# -*- coding: utf-8 -*-

from datetime import datetime
from datetime import timedelta

import sys
import time
import logging.config
import telnetlib
import calendar
import math
import traceback
import os

from pypofetch import PypoFetch
from pypoliqqueue import PypoLiqQueue

from Queue import Empty, Queue

from threading import Thread

from api_clients import api_client
from std_err_override import LogWriter
from configobj import ConfigObj


# configure logging
logging_cfg = os.path.join(os.path.dirname(__file__), "logging.cfg")
logging.config.fileConfig(logging_cfg)
logger = logging.getLogger()
LogWriter.override_std_err(logger)

#need to wait for Python 2.7 for this..
#logging.captureWarnings(True)

# loading config file
try:
    config = ConfigObj('/etc/airtime/pypo.cfg')
    LS_HOST = config['ls_host']
    LS_PORT = config['ls_port']
    PUSH_INTERVAL = 2
except Exception, e:
    logger.error('Error loading config file %s', e)
    sys.exit()

def is_stream(media_item):
    return media_item['type'] == 'stream_output_start'

def is_file(media_item):
    return media_item['type'] == 'file'

class PypoPush(Thread):
    def __init__(self, q, telnet_lock, pypo_liquidsoap):
        Thread.__init__(self)
        self.api_client = api_client.AirtimeApiClient()
        self.queue = q

        self.telnet_lock = telnet_lock

        self.pushed_objects = {}
        self.logger = logging.getLogger('push')
        self.current_prebuffering_stream_id = None
        self.queue_id = 0

        self.future_scheduled_queue = Queue()

        #self.pypo_liquidsoap = PypoLiquidsoap(self.logger, telnet_lock,\
                #LS_HOST, LS_PORT)
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
            except Exception, e:
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
                future[media_item['start']] = media_item

        return present, future

    def get_current_stream_id_from_liquidsoap(self):
        response = "-1"
        try:
            self.telnet_lock.acquire()
            tn = telnetlib.Telnet(LS_HOST, LS_PORT)

            msg = 'dynamic_source.get_id\n'
            tn.write(msg)
            response = tn.read_until("\r\n").strip(" \r\n")
            tn.write('exit\n')
            tn.read_all()
        except Exception, e:
            self.logger.error("Error connecting to Liquidsoap: %s", e)
            response = []
        finally:
            self.telnet_lock.release()

        return response

    #def is_correct_current_item(self, media_item, liquidsoap_queue_approx, liquidsoap_stream_id):
        #correct = False
        #if media_item is None:
            #correct = (len(liquidsoap_queue_approx) == 0 and liquidsoap_stream_id == "-1")
        #else:
            #if is_file(media_item):
                #if len(liquidsoap_queue_approx) == 0:
                    #correct = False
                #else:
                    #correct = liquidsoap_queue_approx[0]['start'] == media_item['start'] and \
                            #liquidsoap_queue_approx[0]['row_id'] == media_item['row_id'] and \
                            #liquidsoap_queue_approx[0]['end'] == media_item['end'] and \
                            #liquidsoap_queue_approx[0]['replay_gain'] == media_item['replay_gain']
            #elif is_stream(media_item):
                #correct = liquidsoap_stream_id == str(media_item['row_id'])

        #self.logger.debug("Is current item correct?: %s", str(correct))
        #return correct

    def date_interval_to_seconds(self, interval):
        """
        Convert timedelta object into int representing the number of seconds. If
        number of seconds is less than 0, then return 0.
        """
        seconds = (interval.microseconds + \
                   (interval.seconds + interval.days * 24 * 3600) * 10 ** 6) / float(10 ** 6)

        return seconds

    def stop_web_stream_all(self):
        try:
            self.telnet_lock.acquire()
            tn = telnetlib.Telnet(LS_HOST, LS_PORT)

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

        except Exception, e:
            self.logger.error(str(e))
        finally:
            self.telnet_lock.release()

    def run(self):
        try: self.main()
        except Exception, e:
            top = traceback.format_exc()
            self.logger.error('Pypo Push Exception: %s', top)

