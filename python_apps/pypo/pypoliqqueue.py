from threading import Thread
from collections import deque
from datetime import datetime
from pypofetch import PypoFetch

import eventtypes

import traceback
import sys
import time

from Queue import Empty

import signal
def keyboardInterruptHandler(signum, frame):
    logger = logging.getLogger()
    logger.info('\nKeyboard Interrupt\n')
    sys.exit(0)
signal.signal(signal.SIGINT, keyboardInterruptHandler)

class PypoLiqQueue(Thread):
    def __init__(self, q, telnet_lock, logger, liq_queue_tracker, \
            telnet_liquidsoap):
        Thread.__init__(self)
        self.queue = q
        self.telnet_lock = telnet_lock
        self.logger = logger
        self.liq_queue_tracker = liq_queue_tracker
        self.telnet_liquidsoap = telnet_liquidsoap

    def main(self):
        time_until_next_play = None
        schedule_deque = deque()
        media_schedule = None

        while True:
            try:
                if time_until_next_play is None:
                    self.logger.info("waiting indefinitely for schedule")
                    media_schedule = self.queue.get(block=True)
                else:
                    self.logger.info("waiting %ss until next scheduled item" % \
                            time_until_next_play)
                    media_schedule = self.queue.get(block=True, \
                            timeout=time_until_next_play)
            except Empty, e:
                #Time to push a scheduled item.
                media_item = schedule_deque.popleft()
                self.telnet_to_liquidsoap(media_item)
                if len(schedule_deque):
                    time_until_next_play = \
                            self.date_interval_to_seconds(
                                    schedule_deque[0]['start'] - datetime.utcnow())
                    if time_until_next_play < 0:
                        time_until_next_play = 0
                else:
                    time_until_next_play = None
            else:
                self.logger.info("New schedule received: %s", media_schedule)

                #new schedule received. Replace old one with this.
                schedule_deque.clear()

                keys = sorted(media_schedule.keys())
                for i in keys:
                    schedule_deque.append(media_schedule[i])

                if len(keys):
                    time_until_next_play = self.date_interval_to_seconds(\
                            keys[0] - datetime.utcnow())
                else:
                    time_until_next_play = None

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

    def telnet_to_liquidsoap(self, media_item):
        """
        telnets to liquidsoap and pushes the media_item to its queue. Push the
        show name of every media_item as well, just to keep Liquidsoap up-to-date
        about which show is playing.
        """

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
            self.telnet_liquidsoap.stop_web_stream_buffer(media_item)
        elif media_item['type'] == eventtypes.STREAM_OUTPUT_END:
            self.telnet_liquidsoap.stop_web_stream_output(media_item)
        else: raise UnknownMediaItemType(str(media_item))

    def handle_event_type(self, media_item):
        if media_item['event_type'] == "kick_out":
            PypoFetch.disconnect_source(self.logger, self.telnet_lock, "live_dj")
        elif media_item['event_type'] == "switch_off":
            PypoFetch.switch_source(self.logger, self.telnet_lock, "live_dj", "off")


    def handle_file_type(self, media_item):
        """
        Wait maximum 5 seconds (50 iterations) for file to become ready, 
        otherwise give up on it.
        """
        iter_num = 0
        while not media_item['file_ready'] and iter_num < 50:
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

    def date_interval_to_seconds(self, interval):
        """
        Convert timedelta object into int representing the number of seconds. If
        number of seconds is less than 0, then return 0.
        """
        seconds = (interval.microseconds + \
                   (interval.seconds + interval.days * 24 * 3600) * 10 ** 6) / float(10 ** 6)
        if seconds < 0: seconds = 0

        return seconds


    def run(self):
        try: self.main()
        except Exception, e:
            self.logger.error('PypoLiqQueue Exception: %s', traceback.format_exc())

class NoQueueAvailableException(Exception):
    pass

class UnknownMediaItemType(Exception):
    pass
