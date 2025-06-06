import logging
import time
from datetime import datetime, timedelta
from typing import Dict, List, Optional, Set

from ..liquidsoap.client import LiquidsoapClient
from ..utils import seconds_between
from .events import ActionEvent, AnyEvent, EventKind, FileEvent, WebStreamEvent

logger = logging.getLogger(__name__)


def create_liquidsoap_annotation(file_event: FileEvent) -> str:
    # We need liq_start_next value in the annotation. That is the value that controls
    # overlap duration of crossfade.
    annotations = {
        "media_id": file_event.id,
        "schedule_table_id": file_event.row_id,
        "liq_start_next": "0",
        "liq_fade_in": file_event.fade_in / 1000,
        "liq_fade_out": file_event.fade_out / 1000,
        "liq_cue_in": file_event.cue_in,
        "liq_cue_out": file_event.cue_out,
    }

    if file_event.replay_gain is not None:
        annotations["replay_gain"] = f"{file_event.replay_gain} dB"

    # Override the the artist/title that Liquidsoap extracts from a file's metadata with
    # the metadata we get from LibreTime. (You can modify metadata in LibreTime's library,
    # which doesn't get saved back to the file.)
    if file_event.artist_name:
        annotations["artist"] = file_event.artist_name.replace('"', '\\"')

    if file_event.track_title:
        annotations["title"] = file_event.track_title.replace('"', '\\"')

    annotations_str = ",".join(f'{key}="{value}"' for key, value in annotations.items()).replace("\n", "")

    return "annotate:" + annotations_str + ":" + str(file_event.local_filepath)


class TelnetLiquidsoap:
    current_prebuffering_stream_id: Optional[int] = None

    def __init__(
        self,
        liq_client: LiquidsoapClient,
        queues: List[int],
    ):
        self.liq_client = liq_client
        self.queues = queues

    def queue_clear_all(self):
        try:
            self.liq_client.queues_remove(*self.queues)
        except OSError as exception:
            logger.exception(exception)

    def queue_remove(self, queue_id: int):
        try:
            self.liq_client.queues_remove(queue_id)
        except OSError as exception:
            logger.exception(exception)

    def queue_push(self, queue_id: int, file_event: FileEvent):
        try:
            annotation = create_liquidsoap_annotation(file_event)
            self.liq_client.queue_push(queue_id, annotation, file_event.show_name)
        except OSError as exception:
            logger.exception(exception)

    def stop_web_stream_buffer(self):
        try:
            self.liq_client.web_stream_stop_buffer()
        except OSError as exception:
            logger.exception(exception)

    def stop_web_stream_output(self):
        try:
            self.liq_client.web_stream_stop()
        except OSError as exception:
            logger.exception(exception)

    def start_web_stream(self):
        try:
            self.liq_client.web_stream_start()
            self.current_prebuffering_stream_id = None
        except OSError as exception:
            logger.exception(exception)

    def start_web_stream_buffer(self, event: WebStreamEvent):
        try:
            self.liq_client.web_stream_start_buffer(event.row_id, event.uri)
            self.current_prebuffering_stream_id = event.row_id
        except OSError as exception:
            logger.exception(exception)

    def get_current_stream_id(self) -> str:
        try:
            return self.liq_client.web_stream_get_id()
        except OSError as exception:
            logger.exception(exception)
            return "-1"

    def disconnect_source(self, sourcename):
        if sourcename not in ("master_dj", "live_dj"):
            raise ValueError(f"invalid source name: {sourcename}")

        try:
            logger.debug("Disconnecting source: %s", sourcename)
            self.liq_client.source_disconnect(sourcename)
        except OSError as exception:
            logger.exception(exception)

    def switch_source(self, sourcename, status):
        if sourcename not in ("master_dj", "live_dj", "scheduled_play"):
            raise ValueError(f"invalid source name: {sourcename}")

        try:
            logger.debug('Switching source: %s to "%s" status', sourcename, status)
            self.liq_client.source_switch_status(sourcename, status == "on")
        except OSError as exception:
            logger.exception(exception)


class Liquidsoap:
    def __init__(self, liq_client: LiquidsoapClient):
        self.liq_queue_tracker: Dict[int, Optional[FileEvent]] = {
            0: None,
            1: None,
            2: None,
            3: None,
        }

        self.liq_client = liq_client
        self.telnet_liquidsoap = TelnetLiquidsoap(
            liq_client,
            list(self.liq_queue_tracker.keys()),
        )

    def play(self, event: AnyEvent) -> None:
        if isinstance(event, FileEvent):
            self.handle_file_type(event)
        elif isinstance(event, ActionEvent):
            self.handle_event_type(event)
        elif isinstance(event, WebStreamEvent):
            self.handle_web_stream_type(event)
        else:
            raise UnknownEvent(str(event))

    def handle_file_type(self, file_event: FileEvent) -> None:
        """
        Wait 200 seconds (2000 iterations) for file to become ready,
        otherwise give up on it.
        """
        iter_num = 0
        while not file_event.file_ready and iter_num < 2000:
            time.sleep(0.1)
            iter_num += 1

        if file_event.file_ready:
            available_queue = self.find_available_queue()

            try:
                self.telnet_liquidsoap.queue_push(available_queue, file_event)
                self.liq_queue_tracker[available_queue] = file_event
            except Exception as exception:
                logger.exception(exception)
                raise exception
        else:
            logger.warning(
                "File %s did not become ready in less than 5 seconds. Skipping...",
                file_event.local_filepath,
            )

    def handle_web_stream_type(self, event: WebStreamEvent) -> None:
        if event.type == EventKind.WEB_STREAM_BUFFER_START:
            self.telnet_liquidsoap.start_web_stream_buffer(event)
        elif event.type == EventKind.WEB_STREAM_OUTPUT_START:
            if event.row_id != self.telnet_liquidsoap.current_prebuffering_stream_id:
                # this is called if the stream wasn't scheduled sufficiently ahead of
                # time so that the prebuffering stage could take effect. Let's do the
                # prebuffering now.
                self.telnet_liquidsoap.start_web_stream_buffer(event)
            self.telnet_liquidsoap.start_web_stream()
        elif event.type == EventKind.WEB_STREAM_BUFFER_END:
            self.telnet_liquidsoap.stop_web_stream_buffer()
        elif event.type == EventKind.WEB_STREAM_OUTPUT_END:
            self.telnet_liquidsoap.stop_web_stream_output()

    def handle_event_type(self, event: ActionEvent) -> None:
        if event.event_type == "kick_out":
            self.telnet_liquidsoap.disconnect_source("live_dj")
        elif event.event_type == "switch_off":
            self.telnet_liquidsoap.switch_source("live_dj", "off")

    def find_available_queue(self) -> int:
        available_queue = None
        for queue_id, item in self.liq_queue_tracker.items():
            if item is None or item.ended():
                # queue "i" is available. Push to this queue
                available_queue = queue_id

        if available_queue is None:
            raise NoQueueAvailableException()

        return available_queue

    # pylint: disable=too-many-branches
    def verify_correct_present_media(self, scheduled_now: List[AnyEvent]) -> None:
        """
        verify whether Liquidsoap is currently playing the correct files.
        if we find an item that Liquidsoap is not playing, then push it
        into one of Liquidsoap's queues. If Liquidsoap is already playing
        it do nothing. If Liquidsoap is playing a track that isn't in
        currently_playing then stop it.

        Check for Liquidsoap media we should source.skip
        get liquidsoap items for each queue. Since each queue can only have one
        item, we should have a max of 8 items.

        2013-03-21-22-56-00_0: {
        id: 1,
        type: "stream_output_start",
        row_id: 41,
        uri: "http://stream2.radioblackout.org:80/blackout.ogg",
        start: "2013-03-21-22-56-00",
        end: "2013-03-21-23-26-00",
        show_name: "Untitled Show"
        },
        """

        scheduled_now_files: List[FileEvent] = [
            x for x in scheduled_now if x.type == EventKind.FILE  # type: ignore
        ]

        scheduled_now_webstream: List[WebStreamEvent] = [
            x  # type: ignore
            for x in scheduled_now
            if x.type == EventKind.WEB_STREAM_OUTPUT_START
        ]

        schedule_ids: Set[int] = {x.row_id for x in scheduled_now_files}

        row_id_map: Dict[int, FileEvent] = {}
        liq_queue_ids: Set[int] = set()
        for queue_item in self.liq_queue_tracker.values():
            if queue_item is not None and not queue_item.ended():
                liq_queue_ids.add(queue_item.row_id)
                row_id_map[queue_item.row_id] = queue_item

        to_be_removed: Set[int] = set()
        to_be_added: Set[int] = set()

        # Iterate over the new files, and compare them to currently scheduled
        # tracks. If already in liquidsoap queue still need to make sure they don't
        # have different attributes. Ff replay gain changes, it shouldn't change the
        # amplification of the currently playing song
        for item in scheduled_now_files:
            if item.row_id in row_id_map:
                queue_item = row_id_map[item.row_id]

                if not (
                    queue_item.start == item.start
                    and queue_item.end == item.end
                    and queue_item.row_id == item.row_id
                ):
                    # need to re-add
                    logger.info("Track %s found to have new attr.", item)
                    to_be_removed.add(item.row_id)
                    to_be_added.add(item.row_id)

        to_be_removed.update(liq_queue_ids - schedule_ids)
        to_be_added.update(schedule_ids - liq_queue_ids)

        if to_be_removed:
            logger.info("Need to remove items from Liquidsoap: %s", to_be_removed)

            # remove files from Liquidsoap's queue
            for queue_id, queue_item in self.liq_queue_tracker.items():
                if queue_item is not None and queue_item.row_id in to_be_removed:
                    self.stop(queue_id)

        if to_be_added:
            logger.info("Need to add items to Liquidsoap *now*: %s", to_be_added)

            for item in scheduled_now_files:
                if item.row_id in to_be_added:
                    self.modify_cue_point(item)
                    self.play(item)

        # handle webstreams
        current_stream_id = self.telnet_liquidsoap.get_current_stream_id()
        if current_stream_id is None:
            current_stream_id = "-1"

        logger.debug("scheduled now webstream: %s", scheduled_now_webstream)
        if scheduled_now_webstream:
            if int(current_stream_id) != int(scheduled_now_webstream[0].row_id):
                self.play(scheduled_now_webstream[0])
        elif current_stream_id != "-1":
            # something is playing and it shouldn't be.
            self.telnet_liquidsoap.stop_web_stream_buffer()
            self.telnet_liquidsoap.stop_web_stream_output()

    def stop(self, queue_id: int) -> None:
        self.telnet_liquidsoap.queue_remove(queue_id)
        self.liq_queue_tracker[queue_id] = None

    def clear_queue_tracker(self) -> None:
        for queue_id in self.liq_queue_tracker:
            self.liq_queue_tracker[queue_id] = None

    def modify_cue_point(self, file_event: FileEvent) -> None:
        assert file_event.type == EventKind.FILE

        lateness = seconds_between(file_event.start, datetime.utcnow())

        if lateness > 0:
            logger.debug("media item was supposed to start %ss ago", lateness)
            cue_in_orig = timedelta(seconds=file_event.cue_in)
            file_event.cue_in = cue_in_orig.total_seconds() + lateness

    def clear_all_queues(self) -> None:
        self.telnet_liquidsoap.queue_clear_all()


class UnknownEvent(Exception):
    pass


class NoQueueAvailableException(Exception):
    pass
