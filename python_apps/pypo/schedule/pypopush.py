# -*- coding: utf-8 -*-

"""
    schedule.pypopush
    ~~~~~~~~~

    Purpose of pypopush is to separate the schedule into items currently
    scheduled and scheduled in the future. Currently scheduled items are 
    handled immediately, and future scheduled items are handed off to 
    PypoLiqQueue

    :author: (c) 2012 by Martin Konecny.
    :license: GPLv3, see LICENSE for more details.
"""

from datetime import datetime
from datetime import timedelta
from threading import Thread
from Queue import Queue

import logging.config
import math
import traceback
import os

from pypoliqqueue import PypoLiqQueue
from schedule import pure
from api_clients import api_client
from std_err_override import LogWriter

# configure logging
logging_cfg = os.path.join(os.path.dirname(__file__), "../configs/logging.cfg")
logging.config.fileConfig(logging_cfg)
logger = logging.getLogger()
LogWriter.override_std_err(logger)

class PypoPush(Thread):
    def __init__(self, q, pypo_liquidsoap):
        Thread.__init__(self)
        self.api_client = api_client.AirtimeApiClient()
        self.queue = q

        self.logger = logging.getLogger('push')

        self.future_scheduled_queue = Queue()
        self.pypo_liquidsoap = pypo_liquidsoap

        self.plq = PypoLiqQueue(self.future_scheduled_queue, \
                self.pypo_liquidsoap, \
                self.logger)
        self.plq.daemon = True
        self.plq.start()


    def main(self):
        media_schedule = None

        while True:
            try:
                media_schedule = self.queue.get(block=True)
            except Exception, e:
                self.logger.error(str(e))
                raise
            else:
                self.logger.debug(media_schedule)
                #separate media_schedule list into currently_playing and
                #scheduled_for_future lists
                currently_playing, scheduled_for_future = \
                        self.separate_present_future(media_schedule)

                self.pypo_liquidsoap.verify_correct_present_media(
                        currently_playing)
                self.future_scheduled_queue.put(scheduled_for_future)

    def separate_present_future(self, media_schedule):
        tnow = datetime.utcnow()

        present = []
        future = {}

        sorted_keys = sorted(media_schedule.keys())
        for mkey in sorted_keys:
            media_item = media_schedule[mkey]

            diff_td = tnow - media_item['start']
            diff_sec = pure.date_interval_to_seconds(diff_td)

            if diff_sec >= 0:
                present.append(media_item)
            else:
                future[media_item['start']] = media_item

        return present, future

    def run(self):
        try: self.main()
        except Exception, e:
            top = traceback.format_exc()
            self.logger.error('Pypo Push Exception: %s', top)

