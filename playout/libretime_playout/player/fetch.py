import copy
import logging
import os
import time
from pathlib import Path
from queue import Empty, Queue
from subprocess import DEVNULL, PIPE, run
from threading import Thread, Timer
from typing import Any, Dict, Union

from libretime_api_client.v1 import ApiClient as LegacyClient
from libretime_api_client.v2 import ApiClient
from requests import RequestException

from ..config import CACHE_DIR, POLL_INTERVAL, Config
from ..liquidsoap.client import LiquidsoapClient
from ..liquidsoap.models import Info, MessageFormatKind, StreamPreferences, StreamState
from .events import Events, FileEvent, FileEvents
from .liquidsoap import Liquidsoap
from .schedule import get_schedule

logger = logging.getLogger(__name__)

here = Path(__file__).parent


# pylint: disable=too-many-instance-attributes
class PypoFetch(Thread):
    name = "fetch"
    daemon = True

    # pylint: disable=too-many-positional-arguments
    # pylint: disable=too-many-arguments
    def __init__(
        self,
        fetch_queue: "Queue[Dict[str, Any]]",
        push_queue: "Queue[Events]",
        file_queue: "Queue[FileEvents]",
        liq_client: LiquidsoapClient,
        liquidsoap: Liquidsoap,
        config: Config,
        api_client: ApiClient,
        legacy_client: LegacyClient,
    ):
        Thread.__init__(self)

        self.api_client = api_client
        self.legacy_client = legacy_client
        self.fetch_queue = fetch_queue
        self.push_queue = push_queue
        self.media_prepare_queue = file_queue
        self.last_update_schedule_timestamp = time.time()
        self.config = config
        self.listener_timeout = POLL_INTERVAL

        self.liq_client = liq_client
        self.liquidsoap = liquidsoap

        self.cache_dir = CACHE_DIR
        logger.debug("Cache dir %s", self.cache_dir)

        self.schedule_data: Events = {}
        logger.info("PypoFetch: init complete")

    # Handle a message from RabbitMQ, put it into our yucky global var.
    # Hopefully there is a better way to do this.

    def handle_message(self, message: Dict[str, Any]) -> None:
        try:
            command = message["event_type"]
            logger.debug("handling event %s: %s", command, message)

            if command == "update_schedule":
                self.schedule_data = get_schedule(self.api_client)
                self.process_schedule(self.schedule_data)
            elif command == "reset_liquidsoap_bootstrap":
                self.set_bootstrap_variables()
            elif command == "update_stream_format":
                logger.info("Updating stream format...")
                self.update_liquidsoap_stream_format(message["stream_format"])
            elif command == "update_message_offline":
                logger.info("Updating message offline...")
                self.update_liquidsoap_message_offline(message["message_offline"])
            elif command == "update_station_name":
                logger.info("Updating station name...")
                self.update_liquidsoap_station_name(message["station_name"])
            elif command == "update_transition_fade":
                logger.info("Updating transition_fade...")
                self.update_liquidsoap_transition_fade(message["transition_fade"])
            elif command == "switch_source":
                logger.info("switch_on_source show command received...")
                self.liquidsoap.telnet_liquidsoap.switch_source(
                    message["sourcename"], message["status"]
                )
            elif command == "disconnect_source":
                logger.info("disconnect_on_source show command received...")
                self.liquidsoap.telnet_liquidsoap.disconnect_source(
                    message["sourcename"]
                )
            else:
                logger.info("Unknown command: %s", command)

            # update timeout value
            if command == "update_schedule":
                self.listener_timeout = POLL_INTERVAL
            else:
                self.listener_timeout = max(
                    self.last_update_schedule_timestamp - time.time() + POLL_INTERVAL,
                    0,
                )
            logger.info("New timeout: %s", self.listener_timeout)
        except Exception as exception:  # pylint: disable=broad-exception-caught
            logger.exception(exception)

    # Initialize Liquidsoap environment
    def set_bootstrap_variables(self) -> None:
        logger.debug("Getting information needed on bootstrap from Airtime")
        try:
            info = Info(**self.api_client.get_info().json())
            preferences = StreamPreferences(
                **self.api_client.get_stream_preferences().json()
            )
            state = StreamState(**self.api_client.get_stream_state().json())

        except RequestException as exception:
            logger.exception("Unable to get stream settings: %s", exception)
            return

        logger.debug("info: %s", info)
        logger.debug("preferences: %s", preferences)
        logger.debug("state: %s", state)

        try:
            self.liquidsoap.liq_client.settings_update(
                station_name=info.station_name,
                message_format=preferences.message_format,
                message_offline=preferences.message_offline,
                input_fade_transition=preferences.input_fade_transition,
            )

            self.liquidsoap.liq_client.source_switch_status(
                name="master_dj",
                streaming=state.input_main_streaming,
            )
            self.liquidsoap.liq_client.source_switch_status(
                name="live_dj",
                streaming=state.input_show_streaming,
            )
            self.liquidsoap.liq_client.source_switch_status(
                name="scheduled_play",
                streaming=state.schedule_streaming,
            )

        except OSError as exception:
            logger.exception(exception)

        self.liquidsoap.clear_all_queues()
        self.liquidsoap.clear_queue_tracker()

    def update_liquidsoap_stream_format(
        self,
        stream_format: Union[MessageFormatKind, int],
    ) -> None:
        try:
            self.liq_client.settings_update(message_format=stream_format)
        except OSError as exception:
            logger.exception(exception)

    def update_liquidsoap_message_offline(self, message_offline: str) -> None:
        try:
            self.liq_client.settings_update(message_offline=message_offline)
        except OSError as exception:
            logger.exception(exception)

    def update_liquidsoap_transition_fade(self, fade: float) -> None:
        try:
            self.liq_client.settings_update(input_fade_transition=fade)
        except OSError as exception:
            logger.exception(exception)

    def update_liquidsoap_station_name(self, station_name: str) -> None:
        try:
            self.liq_client.settings_update(station_name=station_name)
        except OSError as exception:
            logger.exception(exception)

    # Process the schedule
    #  - Reads the scheduled entries of a given range (actual time +/- "prepare_ahead" /
    #    "cache_for")
    #  - Saves a serialized file of the schedule
    #  - playlists are prepared. (brought to liquidsoap format) and, if not mounted via
    #    nsf, files are copied to the cache dir
    #    (Folder-structure: cache/YYYY-MM-DD-hh-mm-ss)
    #  - runs the cleanup routine, to get rid of unused cached files

    def process_schedule(self, events: Events) -> None:
        self.last_update_schedule_timestamp = time.time()
        logger.debug(events)
        file_events: FileEvents = {}
        all_events: Events = {}

        # Download all the media and put playlists in liquidsoap "annotate" format
        try:
            for key in events:
                item = events[key]
                if isinstance(item, FileEvent):
                    file_events[key] = item
                all_events[key] = item

            self.media_prepare_queue.put(copy.copy(file_events))
        except Exception as exception:  # pylint: disable=broad-exception-caught
            logger.exception(exception)

        # Send the data to pypo-push
        logger.debug("Pushing to pypo-push")
        self.push_queue.put(all_events)

        # cleanup
        try:
            self.cache_cleanup(events)
        except Exception as exception:  # pylint: disable=broad-exception-caught
            logger.exception(exception)

    def is_file_opened(self, path: str) -> bool:
        result = run(["lsof", "--", path], stdout=PIPE, stderr=DEVNULL, check=False)
        return bool(result.stdout)

    def cache_cleanup(self, events: Events):
        """
        Get list of all files in the cache dir and remove them if they aren't being used
        anymore.
        Input dict() media, lists all files that are scheduled or currently playing. Not
        being in this dict() means the file is safe to remove.
        """
        cached_file_set = set(os.listdir(self.cache_dir))
        scheduled_file_set = set()

        for key in events:
            item = events[key]
            if isinstance(item, FileEvent):
                scheduled_file_set.add(item.local_filepath.name)

        expired_files = cached_file_set - scheduled_file_set

        logger.debug("Files to remove %s", str(expired_files))
        for expired_file in expired_files:
            try:
                expired_filepath = os.path.join(self.cache_dir, expired_file)
                logger.debug("Removing %s", expired_filepath)

                # check if this file is opened (sometimes Liquidsoap is still
                # playing the file due to our knowledge of the track length
                # being incorrect!)
                if not self.is_file_opened(expired_filepath):
                    os.remove(expired_filepath)
                    logger.info("File '%s' removed", expired_filepath)
                else:
                    logger.info("File '%s' not removed. Still busy!", expired_filepath)
            except Exception as exception:  # pylint: disable=broad-exception-caught
                logger.exception(
                    "Problem removing file '%s': %s", expired_file, exception
                )

    def manual_schedule_fetch(self) -> bool:
        try:
            self.schedule_data = get_schedule(self.api_client)
            logger.debug("Received event from API client: %s", self.schedule_data)
            self.process_schedule(self.schedule_data)
            return True
        except Exception as exception:  # pylint: disable=broad-exception-caught
            logger.exception("Unable to fetch schedule: %s", exception)
        return False

    def persistent_manual_schedule_fetch(self, max_attempts=1) -> bool:
        success = False
        num_attempts = 0
        while not success and num_attempts < max_attempts:
            success = self.manual_schedule_fetch()
            num_attempts += 1

        return success

    # This function makes a request to Airtime to see if we need to
    # push metadata to TuneIn. We have to do this because TuneIn turns
    # off metadata if it does not receive a request every 5 minutes.
    def update_metadata_on_tunein(self):
        self.legacy_client.update_metadata_on_tunein()
        Timer(120, self.update_metadata_on_tunein).start()

    def main(self):
        # Make sure all Liquidsoap queues are empty. This is important in the
        # case where we've just restarted the pypo scheduler, but Liquidsoap still
        # is playing tracks. In this case let's just restart everything from scratch
        # so that we can repopulate our dictionary that keeps track of what
        # Liquidsoap is playing much more easily.
        self.liquidsoap.clear_all_queues()

        self.set_bootstrap_variables()

        self.update_metadata_on_tunein()

        # Bootstrap: since we are just starting up, we need to grab the
        # most recent schedule.  After that we fetch the schedule every 8
        # minutes or wait for schedule updates to get pushed.
        success = self.persistent_manual_schedule_fetch(max_attempts=5)

        if success:
            logger.info("Bootstrap schedule received: %s", self.schedule_data)

        loops = 1
        while True:
            logger.info("Loop #%s", loops)
            manual_fetch_needed = False
            try:
                # our simple_queue.get() requires a timeout, in which case we
                # fetch the Airtime schedule manually. It is important to fetch
                # the schedule periodically because if we didn't, we would only
                # get schedule updates via RabbitMq if the user was constantly
                # using the Airtime interface.

                # If the user is not using the interface, RabbitMq messages are not
                # sent, and we will have very stale (or non-existent!) data about the
                # schedule.

                # Currently we are checking every POLL_INTERVAL seconds

                message = self.fetch_queue.get(
                    block=True, timeout=self.listener_timeout
                )
                manual_fetch_needed = False
                self.handle_message(message)
            except Empty:
                logger.info("Queue timeout. Fetching schedule manually")
                manual_fetch_needed = True
            except Exception as exception:  # pylint: disable=broad-exception-caught
                logger.exception(exception)

            try:
                if manual_fetch_needed:
                    self.persistent_manual_schedule_fetch(max_attempts=5)
            except Exception as exception:  # pylint: disable=broad-exception-caught
                logger.exception("Failed to manually fetch the schedule: %s", exception)

            loops += 1

    def run(self):
        """
        Entry point of the thread
        """
        self.main()
