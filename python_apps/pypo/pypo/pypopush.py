# -*- coding: utf-8 -*-

from datetime import datetime
from datetime import timedelta
from configobj import ConfigObj

import sys
import time
import logging.config
import telnetlib
import calendar
import math
import traceback
import os

from .pypofetch import PypoFetch
from .pypoliqqueue import PypoLiqQueue

from queue import Empty, Queue

from threading import Thread

from api_clients import api_client
from .timeout import ls_timeout

logging.captureWarnings(True)

PUSH_INTERVAL = 2


def is_stream(media_item):
    return media_item['type'] == 'stream_output_start'

def is_file(media_item):
    return media_item['type'] == 'file'

class PypoPush(Thread):
    def __init__(self, q, telnet_lock, pypo_liquidsoap, config):
        Thread.__init__(self)
        self.api_client = api_client.AirtimeApiClient()
        self.queue = q

        self.telnet_lock = telnet_lock
        self.config = config

        self.pushed_objects = {}
        self.logger = logging.getLogger('push')
        self.current_prebuffering_stream_id = None
        self.queue_id = 0

        self.future_scheduled_queue = Queue()
        self.pypo_liquidsoap = pypo_liquidsoap

        self.plq = PypoLiqQueue(self.future_scheduled_queue, \
                self.pypo_liquidsoap, \
                self.logger)
        self.plq.daemon = True
        self.plq.start()


    def main(self):
        loops = 0
        heartbeat_period = math.floor(30 / PUSH_INTERVAL)

        media_schedule = None

        while True:
            try:
                media_schedule = self.queue.get(block=True)
            except Exception as e:
                self.logger.error(str(e))
                raise
            else:
                self.logger.debug(media_schedule)
                #separate media_schedule list into currently_playing and
                #scheduled_for_future lists
                currently_playing, scheduled_for_future = \
                        self.separate_present_future(media_schedule)

                self.pypo_liquidsoap.verify_correct_present_media(currently_playing)
                self.future_scheduled_queue.put(scheduled_for_future)

            if loops % heartbeat_period == 0:
                self.logger.info("heartbeat")
                loops = 0
            loops += 1


    def separate_present_future(self, media_schedule):
        tnow = datetime.utcnow()

        present = []
        future = {}

        sorted_keys = sorted(media_schedule.keys())
        for mkey in sorted_keys:
            media_item = media_schedule[mkey]

            diff_td = tnow - media_item['start']
            diff_sec = self.date_interval_to_seconds(diff_td)

            if diff_sec >= 0:
                present.append(media_item)
            else:
                future[mkey] = media_item

        return present, future

    def date_interval_to_seconds(self, interval):
        """
        Convert timedelta object into int representing the number of seconds. If
        number of seconds is less than 0, then return 0.
        """
        seconds = (interval.microseconds + \
                   (interval.seconds + interval.days * 24 * 3600) * 10 ** 6) / float(10 ** 6)

        return seconds

    @ls_timeout
    def stop_web_stream_all(self):
        try:
            self.telnet_lock.acquire()
            tn = telnetlib.Telnet(self.config['LS_HOST'], self.config['LS_PORT'])

            #msg = 'dynamic_source.read_stop_all xxx\n'
            msg = 'http.stop\n'
            self.logger.debug(msg)
            tn.write(msg)

            msg = 'dynamic_source.output_stop\n'
            self.logger.debug(msg)
            tn.write(msg)

            msg = 'dynamic_source.id -1\n'
            self.logger.debug(msg)
            tn.write(msg)

            tn.write("exit\n")
            self.logger.debug(tn.read_all())

        except Exception as e:
            self.logger.error(str(e))
        finally:
            self.telnet_lock.release()

    def run(self):
        while True:
            try: self.main()
            except Exception as e:
                top = traceback.format_exc()
                self.logger.error('Pypo Push Exception: %s', top)
                time.sleep(5)
        self.logger.info('PypoPush thread exiting')

