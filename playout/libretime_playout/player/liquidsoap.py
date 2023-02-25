import logging
import time
from datetime import datetime, timedelta
from typing import Dict, List, Optional

from ..liquidsoap.client import LiquidsoapClient
from ..utils import seconds_between
from .events import (
    ActionEvent,
    AnyEvent,
    EventKind,
    FileEvent,
    WebStreamEvent,
    event_key_to_datetime,
)
from .liquidsoap_gateway import TelnetLiquidsoap

logger = logging.getLogger(__name__)


class PypoLiquidsoap:
    def __init__(self, liq_client: LiquidsoapClient):
        self.liq_queue_tracker: Dict[str, Optional[FileEvent]] = {
            "s0": None,
            "s1": None,
            "s2": None,
            "s3": None,
        }

        self.liq_client = liq_client
        self.telnet_liquidsoap = TelnetLiquidsoap(
            liq_client,
            list(self.liq_queue_tracker.keys()),
        )

    def play(self, media_item: AnyEvent):
        if media_item["type"] == EventKind.FILE:
            self.handle_file_type(media_item)
        elif media_item["type"] == EventKind.ACTION:
            self.handle_event_type(media_item)
        elif media_item["type"] == EventKind.WEB_STREAM_BUFFER_START:
            self.telnet_liquidsoap.start_web_stream_buffer(media_item)
        elif media_item["type"] == EventKind.WEB_STREAM_OUTPUT_START:
            if (
                media_item["row_id"]
                != self.telnet_liquidsoap.current_prebuffering_stream_id
            ):
                # this is called if the stream wasn't scheduled sufficiently ahead of
                # time so that the prebuffering stage could take effect. Let's do the
                # prebuffering now.
                self.telnet_liquidsoap.start_web_stream_buffer(media_item)
            self.telnet_liquidsoap.start_web_stream(media_item)
        elif media_item["type"] == EventKind.WEB_STREAM_BUFFER_END:
            self.telnet_liquidsoap.stop_web_stream_buffer()
        elif media_item["type"] == EventKind.WEB_STREAM_OUTPUT_END:
            self.telnet_liquidsoap.stop_web_stream_output()
        else:
            raise UnknownMediaItemType(str(media_item))

    def handle_file_type(self, media_item: FileEvent):
        """
        Wait 200 seconds (2000 iterations) for file to become ready,
        otherwise give up on it.
        """
        iter_num = 0
        while not media_item.get("file_ready", False) and iter_num < 2000:
            time.sleep(0.1)
            iter_num += 1

        if media_item.get("file_ready", False):
            available_queue = self.find_available_queue()

            try:
                self.telnet_liquidsoap.queue_push(available_queue, media_item)
                self.liq_queue_tracker[available_queue] = media_item
            except Exception as exception:
                logger.exception(exception)
                raise exception
        else:
            logger.warning(
                "File %s did not become ready in less than 5 seconds. Skipping...",
                media_item["dst"],
            )

    def handle_event_type(self, media_item: ActionEvent):
        if media_item["event_type"] == "kick_out":
            self.telnet_liquidsoap.disconnect_source("live_dj")
        elif media_item["event_type"] == "switch_off":
            self.telnet_liquidsoap.switch_source("live_dj", "off")

    def is_media_item_finished(self, media_item: Optional[AnyEvent]):
        if media_item is None:
            return True
        return datetime.utcnow() > event_key_to_datetime(media_item["end"])

    def find_available_queue(self) -> str:
        available_queue = None
        for queue_id, item in self.liq_queue_tracker.items():
            if item is None or self.is_media_item_finished(item):
                # queue "i" is available. Push to this queue
                available_queue = queue_id

        if available_queue is None:
            raise NoQueueAvailableException()

        return available_queue

    def verify_correct_present_media(self, scheduled_now: List[AnyEvent]):
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

        try:
            scheduled_now_files: List[FileEvent] = [
                x for x in scheduled_now if x["type"] == EventKind.FILE
            ]

            scheduled_now_webstream: List[WebStreamEvent] = [
                x
                for x in scheduled_now
                if x["type"] == EventKind.WEB_STREAM_OUTPUT_START
            ]

            schedule_ids = {x["row_id"] for x in scheduled_now_files}

            row_id_map = {}
            liq_queue_ids = set()
            for queue_id in self.liq_queue_tracker:
                queue_item = self.liq_queue_tracker[queue_id]
                if queue_item is not None and not self.is_media_item_finished(
                    queue_item
                ):
                    liq_queue_ids.add(queue_item["row_id"])
                    row_id_map[queue_item["row_id"]] = queue_item

            to_be_removed = set()
            to_be_added = set()

            # Iterate over the new files, and compare them to currently scheduled
            # tracks. If already in liquidsoap queue still need to make sure they don't
            # have different attributes. Ff replay gain changes, it shouldn't change the
            # amplification of the currently playing song
            for item in scheduled_now_files:
                if item["row_id"] in row_id_map:
                    queue_item = row_id_map[item["row_id"]]
                    assert queue_item is not None

                    correct = (
                        queue_item["start"] == item["start"]
                        and queue_item["end"] == item["end"]
                        and queue_item["row_id"] == item["row_id"]
                    )

                    if not correct:
                        # need to re-add
                        logger.info("Track %s found to have new attr.", item)
                        to_be_removed.add(item["row_id"])
                        to_be_added.add(item["row_id"])

            to_be_removed.update(liq_queue_ids - schedule_ids)
            to_be_added.update(schedule_ids - liq_queue_ids)

            if to_be_removed:
                logger.info("Need to remove items from Liquidsoap: %s", to_be_removed)

                # remove files from Liquidsoap's queue
                for queue_id in self.liq_queue_tracker:
                    queue_item = self.liq_queue_tracker[queue_id]
                    if queue_item is not None and queue_item["row_id"] in to_be_removed:
                        self.stop(queue_id)

            if to_be_added:
                logger.info("Need to add items to Liquidsoap *now*: %s", to_be_added)

                for item in scheduled_now_files:
                    if item["row_id"] in to_be_added:
                        self.modify_cue_point(item)
                        self.play(item)

            # handle webstreams
            current_stream_id = self.telnet_liquidsoap.get_current_stream_id()
            if current_stream_id is None:
                current_stream_id = "-1"

            logger.debug("scheduled now webstream: %s", scheduled_now_webstream)
            if scheduled_now_webstream:
                if int(current_stream_id) != int(scheduled_now_webstream[0]["row_id"]):
                    self.play(scheduled_now_webstream[0])
            elif current_stream_id != "-1":
                # something is playing and it shouldn't be.
                self.telnet_liquidsoap.stop_web_stream_buffer()
                self.telnet_liquidsoap.stop_web_stream_output()
        except KeyError as exception:
            logger.exception("Malformed event in schedule: %s", exception)

    def stop(self, queue_id: str):
        self.telnet_liquidsoap.queue_remove(queue_id)
        self.liq_queue_tracker[queue_id] = None

    def is_file(self, event: AnyEvent):
        return event["type"] == EventKind.FILE

    def clear_queue_tracker(self):
        for queue_id in self.liq_queue_tracker:
            self.liq_queue_tracker[queue_id] = None

    def modify_cue_point(self, link: FileEvent):
        assert self.is_file(link)

        lateness = seconds_between(
            event_key_to_datetime(link["start"]),
            datetime.utcnow(),
        )

        if lateness > 0:
            logger.debug("media item was supposed to start %ss ago", lateness)
            cue_in_orig = timedelta(seconds=float(link["cue_in"]))
            link["cue_in"] = cue_in_orig.total_seconds() + lateness

    def clear_all_queues(self):
        self.telnet_liquidsoap.queue_clear_all()


class UnknownMediaItemType(Exception):
    pass


class NoQueueAvailableException(Exception):
    pass
