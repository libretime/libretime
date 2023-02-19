import time
from datetime import datetime, timedelta

from loguru import logger

from ..liquidsoap.client import LiquidsoapClient
from ..utils import seconds_between
from .events import EventKind
from .liquidsoap_gateway import TelnetLiquidsoap


class PypoLiquidsoap:
    def __init__(self, liq_client: LiquidsoapClient):
        self.liq_queue_tracker = {
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

    def play(self, media_item):
        if media_item["type"] == EventKind.FILE:
            self.handle_file_type(media_item)
        elif media_item["type"] == EventKind.EVENT:
            self.handle_event_type(media_item)
        elif media_item["type"] == EventKind.STREAM_BUFFER_START:
            self.telnet_liquidsoap.start_web_stream_buffer(media_item)
        elif media_item["type"] == EventKind.STREAM_OUTPUT_START:
            if (
                media_item["row_id"]
                != self.telnet_liquidsoap.current_prebuffering_stream_id
            ):
                # this is called if the stream wasn't scheduled sufficiently ahead of time
                # so that the prebuffering stage could take effect. Let's do the prebuffering now.
                self.telnet_liquidsoap.start_web_stream_buffer(media_item)
            self.telnet_liquidsoap.start_web_stream(media_item)
        elif media_item["type"] == EventKind.STREAM_BUFFER_END:
            self.telnet_liquidsoap.stop_web_stream_buffer()
        elif media_item["type"] == EventKind.STREAM_OUTPUT_END:
            self.telnet_liquidsoap.stop_web_stream_output()
        else:
            raise UnknownMediaItemType(str(media_item))

    def handle_file_type(self, media_item):
        """
        Wait 200 seconds (2000 iterations) for file to become ready,
        otherwise give up on it.
        """
        iter_num = 0
        while not media_item["file_ready"] and iter_num < 2000:
            time.sleep(0.1)
            iter_num += 1

        if media_item["file_ready"]:
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

    def handle_event_type(self, media_item):
        if media_item["event_type"] == "kick_out":
            self.telnet_liquidsoap.disconnect_source("live_dj")
        elif media_item["event_type"] == "switch_off":
            self.telnet_liquidsoap.switch_source("live_dj", "off")

    def is_media_item_finished(self, media_item):
        if media_item is None:
            return True
        else:
            return datetime.utcnow() > media_item["end"]

    def find_available_queue(self):
        available_queue = None
        for queue_id, item in self.liq_queue_tracker.items():
            if item is None or self.is_media_item_finished(item):
                # queue "i" is available. Push to this queue
                available_queue = queue_id

        if available_queue is None:
            raise NoQueueAvailableException()

        return available_queue

    def verify_correct_present_media(self, scheduled_now):
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
            scheduled_now_files = [
                x for x in scheduled_now if x["type"] == EventKind.FILE
            ]

            scheduled_now_webstream = [
                x for x in scheduled_now if x["type"] in (EventKind.STREAM_OUTPUT_START)
            ]

            schedule_ids = {x["row_id"] for x in scheduled_now_files}

            row_id_map = {}
            liq_queue_ids = set()
            for i in self.liq_queue_tracker:
                mi = self.liq_queue_tracker[i]
                if not self.is_media_item_finished(mi):
                    liq_queue_ids.add(mi["row_id"])
                    row_id_map[mi["row_id"]] = mi

            to_be_removed = set()
            to_be_added = set()

            # Iterate over the new files, and compare them to currently scheduled
            # tracks. If already in liquidsoap queue still need to make sure they don't
            # have different attributes
            # if replay gain changes, it shouldn't change the amplification of the currently playing song
            for i in scheduled_now_files:
                if i["row_id"] in row_id_map:
                    mi = row_id_map[i["row_id"]]
                    correct = (
                        mi["start"] == i["start"]
                        and mi["end"] == i["end"]
                        and mi["row_id"] == i["row_id"]
                    )

                    if not correct:
                        # need to re-add
                        logger.info("Track %s found to have new attr." % i)
                        to_be_removed.add(i["row_id"])
                        to_be_added.add(i["row_id"])

            to_be_removed.update(liq_queue_ids - schedule_ids)
            to_be_added.update(schedule_ids - liq_queue_ids)

            if to_be_removed:
                logger.info("Need to remove items from Liquidsoap: %s" % to_be_removed)

                # remove files from Liquidsoap's queue
                for i in self.liq_queue_tracker:
                    mi = self.liq_queue_tracker[i]
                    if mi is not None and mi["row_id"] in to_be_removed:
                        self.stop(i)

            if to_be_added:
                logger.info("Need to add items to Liquidsoap *now*: %s" % to_be_added)

                for i in scheduled_now_files:
                    if i["row_id"] in to_be_added:
                        self.modify_cue_point(i)
                        self.play(i)

            # handle webstreams
            current_stream_id = self.telnet_liquidsoap.get_current_stream_id()
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

    def stop(self, queue):
        self.telnet_liquidsoap.queue_remove(queue)
        self.liq_queue_tracker[queue] = None

    def is_file(self, media_item):
        return media_item["type"] == EventKind.FILE

    def clear_queue_tracker(self):
        for i in self.liq_queue_tracker.keys():
            self.liq_queue_tracker[i] = None

    def modify_cue_point(self, link):
        assert self.is_file(link)

        lateness = seconds_between(link["start"], datetime.utcnow())

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
