from threading import Thread
from collections import deque
from datetime import datetime

import traceback
import sys

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
                    media_schedule = self.queue.get(block=True)
                else:
                    media_schedule = self.queue.get(block=True, timeout=time_until_next_play)
            except Empty, e:
                #Time to push a scheduled item.
                media_item = schedule_deque.popleft()
                self.telnet_to_liquidsoap(media_item)
                if len(schedule_deque):
                    time_until_next_play = \
                            self.date_interval_to_seconds(
                                    schedule_deque[0]['start'] - datetime.utcnow())
                else:
                    time_until_next_play = None
            else:
                #new schedule received. Replace old one with this.
                schedule_deque.clear()

                keys = sorted(media_schedule.keys())
                for i in keys:
                    schedule_deque.append(media_schedule[i])

                time_until_next_play = self.date_interval_to_seconds(\
                        keys[0] - datetime.utcnow())

    def is_media_item_finished(self, media_item):
        return datetime.utcnow() > media_item['end']

    def telnet_to_liquidsoap(self, media_item):
        """
        telnets to liquidsoap and pushes the media_item to its queue. Push the
        show name of every media_item as well, just to keep Liquidsoap up-to-date
        about which show is playing.
        """
        
        available_queue = None
        for i in self.liq_queue_tracker:
            mi = self.liq_queue_tracker[i]
            if mi == None or self.is_media_item_finished(mi):
                #queue "i" is available. Push to this queue
                available_queue = i

        if available_queue == None:
            raise NoQueueAvailableException()

        try:
            self.telnet_liquidsoap.queue_push(available_queue, media_item)
            self.liq_queue_tracker[available_queue] = media_item
        except Exception as e:
            self.logger.error(e)
            raise

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
