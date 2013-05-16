"""
    schedule.pypoliqqueue
    ~~~~~~~~~

    This module takes a collection of media_items scheduled in the near future
    and fires off a start event when the item's start time begins.

    :author: (c) 2012 by Martin Konecny.
    :license: GPLv3, see LICENSE for more details.
"""

from threading import Thread
from collections import deque
from datetime import datetime

from schedule import pure

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
    def __init__(self, q, pypo_liquidsoap, logger):
        Thread.__init__(self)
        self.queue = q
        self.logger = logger
        self.pypo_liquidsoap = pypo_liquidsoap

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
                self.pypo_liquidsoap.play(media_item)
                if len(schedule_deque):
                    time_until_next_play = \
                            pure.date_interval_to_seconds(
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
                    time_until_next_play = pure.date_interval_to_seconds(\
                            keys[0] - datetime.utcnow())
                else:
                    time_until_next_play = None

    def run(self):
        try: self.main()
        except Exception, e:
            self.logger.error('PypoLiqQueue Exception: %s', traceback.format_exc())



