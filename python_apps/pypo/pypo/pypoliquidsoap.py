from .pypofetch import PypoFetch
from .telnetliquidsoap import TelnetLiquidsoap

from datetime import datetime
from datetime import timedelta

from . import eventtypes
import time

class PypoLiquidsoap():
    def __init__(self, logger, telnet_lock, host, port):
        self.logger = logger
        self.liq_queue_tracker = {
                "s0": None,
                "s1": None,
                "s2": None,
                "s3": None,
                "s4": None,
                }

        self.telnet_liquidsoap = TelnetLiquidsoap(telnet_lock, \
                logger,\
                host,\
                port,\
                list(self.liq_queue_tracker.keys()))

    def get_telnet_dispatcher(self):
        return self.telnet_liquidsoap


    def play(self, media_item):
        if media_item["type"] == eventtypes.FILE:
            self.handle_file_type(media_item)
        elif media_item["type"] == eventtypes.EVENT:
            self.handle_event_type(media_item)
        elif media_item["type"] == eventtypes.STREAM_BUFFER_START:
            self.telnet_liquidsoap.start_web_stream_buffer(media_item)
        elif media_item["type"] == eventtypes.STREAM_OUTPUT_START:
            if media_item['row_id'] != self.telnet_liquidsoap.current_prebuffering_stream_id:
                #this is called if the stream wasn't scheduled sufficiently ahead of time
                #so that the prebuffering stage could take effect. Let's do the prebuffering now.
                self.telnet_liquidsoap.start_web_stream_buffer(media_item)
            self.telnet_liquidsoap.start_web_stream(media_item)
        elif media_item['type'] == eventtypes.STREAM_BUFFER_END:
            self.telnet_liquidsoap.stop_web_stream_buffer()
        elif media_item['type'] == eventtypes.STREAM_OUTPUT_END:
            self.telnet_liquidsoap.stop_web_stream_output()
        else: raise UnknownMediaItemType(str(media_item))

    def handle_file_type(self, media_item):
        """
        Wait 200 seconds (2000 iterations) for file to become ready, 
        otherwise give up on it.
        """
        iter_num = 0
        while not media_item['file_ready'] and iter_num < 2000:
            time.sleep(0.1)
            iter_num += 1

        if media_item['file_ready']:
            available_queue = self.find_available_queue()

            try:
                self.telnet_liquidsoap.queue_push(available_queue, media_item)
                self.liq_queue_tracker[available_queue] = media_item
            except Exception as e:
                self.logger.error(e)
                raise
        else:
            self.logger.warn("File %s did not become ready in less than 5 seconds. Skipping...", media_item['dst'])

    def handle_event_type(self, media_item):
        if media_item['event_type'] == "kick_out":
            self.telnet_liquidsoap.disconnect_source("live_dj")
        elif media_item['event_type'] == "switch_off":
            self.telnet_liquidsoap.switch_source("live_dj", "off")


    def is_media_item_finished(self, media_item):
        if media_item is None:
            return True
        else:
            return datetime.utcnow() > media_item['end']

    def find_available_queue(self):
        available_queue = None
        for i in self.liq_queue_tracker:
            mi = self.liq_queue_tracker[i]
            if mi == None or self.is_media_item_finished(mi):
                #queue "i" is available. Push to this queue
                available_queue = i

        if available_queue == None:
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
        show_name: "Untitled Show",
        independent_event: true
        },
        """

        try:
            scheduled_now_files = \
                    [x for x in scheduled_now if x["type"] == eventtypes.FILE]

            scheduled_now_webstream = \
                    [x for x in scheduled_now if x["type"] == eventtypes.STREAM_OUTPUT_START]

            schedule_ids = set([x["row_id"] for x in scheduled_now_files])

            row_id_map = {}
            liq_queue_ids = set()
            for i in self.liq_queue_tracker:
                mi = self.liq_queue_tracker[i]
                if not self.is_media_item_finished(mi):
                    liq_queue_ids.add(mi["row_id"])
                    row_id_map[mi["row_id"]] = mi

            to_be_removed = set()
            to_be_added = set()

            #Iterate over the new files, and compare them to currently scheduled
            #tracks. If already in liquidsoap queue still need to make sure they don't
            #have different attributes
            #if replay gain changes, it shouldn't change the amplification of the currently playing song
            for i in scheduled_now_files:
                if i["row_id"] in row_id_map:
                    mi = row_id_map[i["row_id"]]
                    correct = mi['start'] == i['start'] and \
                            mi['end'] == i['end'] and \
                            mi['row_id'] == i['row_id']

                    if not correct:
                        #need to re-add
                        self.logger.info("Track %s found to have new attr." % i)
                        to_be_removed.add(i["row_id"])
                        to_be_added.add(i["row_id"])

            to_be_removed.update(liq_queue_ids - schedule_ids)
            to_be_added.update(schedule_ids - liq_queue_ids)

            if to_be_removed:
                self.logger.info("Need to remove items from Liquidsoap: %s" % \
                        to_be_removed)

                #remove files from Liquidsoap's queue
                for i in self.liq_queue_tracker:
                    mi = self.liq_queue_tracker[i]
                    if mi is not None and mi["row_id"] in to_be_removed:
                        self.stop(i)

            if to_be_added:
                self.logger.info("Need to add items to Liquidsoap *now*: %s" % \
                        to_be_added)

                for i in scheduled_now_files:
                    if i["row_id"] in to_be_added:
                        self.modify_cue_point(i)
                        self.play(i)

            #handle webstreams
            current_stream_id = self.telnet_liquidsoap.get_current_stream_id()
            if scheduled_now_webstream:
                if int(current_stream_id) != int(scheduled_now_webstream[0]["row_id"]):
                    self.play(scheduled_now_webstream[0])
            elif current_stream_id != "-1":
                #something is playing and it shouldn't be.
                self.telnet_liquidsoap.stop_web_stream_buffer()
                self.telnet_liquidsoap.stop_web_stream_output()
        except KeyError as e:
            self.logger.error("Error: Malformed event in schedule. " + str(e))


    def stop(self, queue):
        self.telnet_liquidsoap.queue_remove(queue)
        self.liq_queue_tracker[queue] = None

    def is_file(self, media_item):
        return media_item["type"] == eventtypes.FILE

    def clear_queue_tracker(self):
        for i in self.liq_queue_tracker.keys():
            self.liq_queue_tracker[i] = None

    def modify_cue_point(self, link):
        assert self.is_file(link)

        tnow = datetime.utcnow()

        link_start = link['start']

        diff_td = tnow - link_start
        diff_sec = self.date_interval_to_seconds(diff_td)

        if diff_sec > 0:
            self.logger.debug("media item was supposed to start %s ago. Preparing to start..", diff_sec)
            original_cue_in_td = timedelta(seconds=float(link['cue_in']))
            link['cue_in'] = self.date_interval_to_seconds(original_cue_in_td) + diff_sec

    def date_interval_to_seconds(self, interval):
        """
        Convert timedelta object into int representing the number of seconds. If
        number of seconds is less than 0, then return 0.
        """
        seconds = (interval.microseconds + \
                   (interval.seconds + interval.days * 24 * 3600) * 10 ** 6) / float(10 ** 6)
        if seconds < 0: seconds = 0

        return seconds

    def clear_all_queues(self):
        self.telnet_liquidsoap.queue_clear_all()


class UnknownMediaItemType(Exception):
    pass

class NoQueueAvailableException(Exception):
    pass
