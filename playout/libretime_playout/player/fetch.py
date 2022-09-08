import copy
import json
import mimetypes
import os
import signal
import sys
import time
from datetime import datetime
from queue import Empty, Queue
from subprocess import PIPE, Popen
from threading import Thread, Timer

from libretime_api_client.v1 import ApiClient as LegacyClient
from libretime_api_client.v2 import ApiClient
from loguru import logger
from requests import RequestException

from ..config import CACHE_DIR, POLL_INTERVAL, Config
from ..liquidsoap.client import LiquidsoapClient
from ..liquidsoap.models import Info, StreamPreferences, StreamState
from ..timeout import ls_timeout
from .liquidsoap import PypoLiquidsoap
from .schedule import get_schedule


def shutdown_handler(signum, frame):
    logger.info("shutting down")
    sys.exit(0)


signal.signal(signal.SIGINT, shutdown_handler)
signal.signal(signal.SIGTERM, shutdown_handler)


class PypoFetch(Thread):
    name = "fetch"

    def __init__(
        self,
        fetch_queue: Queue,
        push_queue: Queue,
        file_queue: Queue,
        liq_client: LiquidsoapClient,
        pypo_liquidsoap: PypoLiquidsoap,
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
        self.pypo_liquidsoap = pypo_liquidsoap

        self.cache_dir = CACHE_DIR
        logger.debug("Cache dir %s", self.cache_dir)

        self.schedule_data = []
        logger.info("PypoFetch: init complete")

    # Handle a message from RabbitMQ, put it into our yucky global var.
    # Hopefully there is a better way to do this.

    def handle_message(self, message):
        try:
            logger.info("Received event from Pypo Message Handler: %s" % message)

            try:
                message = message.decode()
            except (UnicodeDecodeError, AttributeError):
                pass
            m = json.loads(message)
            command = m["event_type"]
            logger.info("Handling command: " + command)

            if command == "update_schedule":
                self.schedule_data = m["schedule"]
                self.process_schedule(self.schedule_data)
            elif command == "reset_liquidsoap_bootstrap":
                self.set_bootstrap_variables()
            elif command == "update_stream_format":
                logger.info("Updating stream format...")
                self.update_liquidsoap_stream_format(m["stream_format"])
            elif command == "update_message_offline":
                logger.info("Updating message offline...")
                self.update_liquidsoap_message_offline(m["message_offline"])
            elif command == "update_station_name":
                logger.info("Updating station name...")
                self.update_liquidsoap_station_name(m["station_name"])
            elif command == "update_transition_fade":
                logger.info("Updating transition_fade...")
                self.update_liquidsoap_transition_fade(m["transition_fade"])
            elif command == "switch_source":
                logger.info("switch_on_source show command received...")
                self.pypo_liquidsoap.telnet_liquidsoap.switch_source(
                    m["sourcename"], m["status"]
                )
            elif command == "disconnect_source":
                logger.info("disconnect_on_source show command received...")
                self.pypo_liquidsoap.telnet_liquidsoap.disconnect_source(
                    m["sourcename"]
                )
            else:
                logger.info("Unknown command: %s" % command)

            # update timeout value
            if command == "update_schedule":
                self.listener_timeout = POLL_INTERVAL
            else:
                self.listener_timeout = (
                    self.last_update_schedule_timestamp - time.time() + POLL_INTERVAL
                )
                if self.listener_timeout < 0:
                    self.listener_timeout = 0
            logger.info("New timeout: %s" % self.listener_timeout)
        except Exception as exception:
            logger.exception(exception)

    # Initialize Liquidsoap environment
    def set_bootstrap_variables(self):
        logger.debug("Getting information needed on bootstrap from Airtime")
        try:
            info = Info(**self.api_client.get_info().json())
            preferences = StreamPreferences(
                **self.api_client.get_stream_preferences().json()
            )
            state = StreamState(**self.api_client.get_stream_state().json())

        except RequestException as exception:
            logger.exception(f"Unable to get stream settings: {exception}")

        logger.debug(f"info: {info}")
        logger.debug(f"preferences: {preferences}")
        logger.debug(f"state: {state}")

        try:
            self.pypo_liquidsoap.liq_client.settings_update(
                station_name=info.station_name,
                message_format=preferences.message_format,
                message_offline=preferences.message_offline,
                input_fade_transition=preferences.input_fade_transition,
            )

            self.pypo_liquidsoap.liq_client.source_switch_status(
                name="master_dj",
                streaming=state.input_main_streaming,
            )
            self.pypo_liquidsoap.liq_client.source_switch_status(
                name="live_dj",
                streaming=state.input_show_streaming,
            )
            self.pypo_liquidsoap.liq_client.source_switch_status(
                name="scheduled_play",
                streaming=state.schedule_streaming,
            )

        except (ConnectionError, TimeoutError) as exception:
            logger.exception(exception)

        self.pypo_liquidsoap.clear_all_queues()
        self.pypo_liquidsoap.clear_queue_tracker()

    @ls_timeout
    def update_liquidsoap_connection_status(self):
        """
        updates the status of Liquidsoap connection to the streaming server
        This function updates the bootup time variable in Liquidsoap script
        """

        try:
            # update the boot up time of Liquidsoap. Since Liquidsoap is not restarting,
            # we are manually adjusting the bootup time variable so the status msg will get
            # updated.
            current_time = time.time()
            self.liq_client.boot_timestamp_update(current_time)

            stream_info = self.liq_client.outputs_connection_status()
        except (ConnectionError, TimeoutError) as exception:
            logger.exception(exception)

        # streamin info is in the form of:
        # eg. s1:true,2:true,3:false
        streams = stream_info.split(",")
        logger.info(streams)

        fake_time = current_time + 1
        for stream in streams:
            info = stream.split(":")
            stream_id = info[0]
            status = info[1]
            if status == "true":
                self.legacy_client.notify_liquidsoap_status(
                    "OK", stream_id, str(fake_time)
                )

    @ls_timeout
    def update_liquidsoap_stream_format(self, stream_format):
        try:
            self.liq_client.settings_update(message_format=stream_format)
        except (ConnectionError, TimeoutError) as exception:
            logger.exception(exception)

    @ls_timeout
    def update_liquidsoap_message_offline(self, message_offline: str):
        try:
            self.liq_client.settings_update(message_offline=message_offline)
        except (ConnectionError, TimeoutError) as exception:
            logger.exception(exception)

    @ls_timeout
    def update_liquidsoap_transition_fade(self, fade):
        try:
            self.liq_client.settings_update(input_fade_transition=fade)
        except (ConnectionError, TimeoutError) as exception:
            logger.exception(exception)

    @ls_timeout
    def update_liquidsoap_station_name(self, station_name):
        try:
            self.liq_client.settings_update(station_name=station_name)
        except (ConnectionError, TimeoutError) as exception:
            logger.exception(exception)

    # Process the schedule
    #  - Reads the scheduled entries of a given range (actual time +/- "prepare_ahead" / "cache_for")
    #  - Saves a serialized file of the schedule
    #  - playlists are prepared. (brought to liquidsoap format) and, if not mounted via nsf, files are copied
    #    to the cache dir (Folder-structure: cache/YYYY-MM-DD-hh-mm-ss)
    #  - runs the cleanup routine, to get rid of unused cached files

    def process_schedule(self, schedule_data):
        self.last_update_schedule_timestamp = time.time()
        logger.debug(schedule_data)
        media = schedule_data["media"]
        media_filtered = {}

        # Download all the media and put playlists in liquidsoap "annotate" format
        try:

            # Make sure cache_dir exists
            download_dir = self.cache_dir
            try:
                os.makedirs(download_dir)
            except Exception:
                pass

            media_copy = {}
            for key in media:
                media_item = media[key]
                if media_item["type"] == "file":
                    fileExt = self.sanity_check_media_item(media_item)
                    dst = os.path.join(download_dir, f'{media_item["id"]}{fileExt}')
                    media_item["dst"] = dst
                    media_item["file_ready"] = False
                    media_filtered[key] = media_item

                media_item["start"] = datetime.strptime(
                    media_item["start"], "%Y-%m-%d-%H-%M-%S"
                )
                media_item["end"] = datetime.strptime(
                    media_item["end"], "%Y-%m-%d-%H-%M-%S"
                )
                media_copy[key] = media_item

            self.media_prepare_queue.put(copy.copy(media_filtered))
        except Exception as exception:
            logger.exception(exception)

        # Send the data to pypo-push
        logger.debug("Pushing to pypo-push")
        self.push_queue.put(media_copy)

        # cleanup
        try:
            self.cache_cleanup(media)
        except Exception as exception:
            logger.exception(exception)

    # do basic validation of file parameters. Useful for debugging
    # purposes
    def sanity_check_media_item(self, media_item):
        start = datetime.strptime(media_item["start"], "%Y-%m-%d-%H-%M-%S")
        end = datetime.strptime(media_item["end"], "%Y-%m-%d-%H-%M-%S")

        mime = media_item["metadata"]["mime"]
        mimetypes.init(["%s/mime.types" % os.path.dirname(os.path.realpath(__file__))])
        mime_ext = mimetypes.guess_extension(mime, strict=False)

        length1 = (end - start).total_seconds()
        length2 = media_item["cue_out"] - media_item["cue_in"]

        if abs(length2 - length1) > 1:
            logger.error("end - start length: %s", length1)
            logger.error("cue_out - cue_in length: %s", length2)
            logger.error("Two lengths are not equal!!!")

        media_item["file_ext"] = mime_ext

        return mime_ext

    def is_file_opened(self, path):
        # Capture stderr to avoid polluting py-interpreter.log
        proc = Popen(["lsof", path], stdout=PIPE, stderr=PIPE)
        out = proc.communicate()[0].strip()
        return bool(out)

    def cache_cleanup(self, media):
        """
        Get list of all files in the cache dir and remove them if they aren't being used anymore.
        Input dict() media, lists all files that are scheduled or currently playing. Not being in this
        dict() means the file is safe to remove.
        """
        cached_file_set = set(os.listdir(self.cache_dir))
        scheduled_file_set = set()

        for mkey in media:
            media_item = media[mkey]
            if media_item["type"] == "file":
                if "file_ext" not in media_item.keys():
                    media_item["file_ext"] = mimetypes.guess_extension(
                        media_item["metadata"]["mime"], strict=False
                    )
                scheduled_file_set.add(
                    "{}{}".format(media_item["id"], media_item["file_ext"])
                )

        expired_files = cached_file_set - scheduled_file_set

        logger.debug("Files to remove " + str(expired_files))
        for f in expired_files:
            try:
                path = os.path.join(self.cache_dir, f)
                logger.debug("Removing %s" % path)

                # check if this file is opened (sometimes Liquidsoap is still
                # playing the file due to our knowledge of the track length
                # being incorrect!)
                if not self.is_file_opened(path):
                    os.remove(path)
                    logger.info("File '%s' removed" % path)
                else:
                    logger.info("File '%s' not removed. Still busy!" % path)
            except Exception as exception:
                logger.exception(f"Problem removing file '{f}': {exception}")

    def manual_schedule_fetch(self):
        try:
            self.schedule_data = get_schedule(self.api_client)
            logger.debug(f"Received event from API client: {self.schedule_data}")
            self.process_schedule(self.schedule_data)
            return True
        except Exception as exception:
            logger.exception(f"Unable to fetch schedule: {exception}")
        return False

    def persistent_manual_schedule_fetch(self, max_attempts=1):
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
        self.pypo_liquidsoap.clear_all_queues()

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
            logger.info(f"Loop #{loops}")
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
            except Exception as exception:
                logger.exception(exception)

            try:
                if manual_fetch_needed:
                    self.persistent_manual_schedule_fetch(max_attempts=5)
            except Exception as exception:
                logger.exception(f"Failed to manually fetch the schedule: {exception}")

            loops += 1

    def run(self):
        """
        Entry point of the thread
        """
        self.main()
