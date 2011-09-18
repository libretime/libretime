import os
import sys
import time
import logging
import logging.config
import logging.handlers
import pickle
import telnetlib
import calendar
import json
import math
from threading import Thread

from api_clients import api_client

from configobj import ConfigObj


# configure logging
logging.config.fileConfig("logging.cfg")

# loading config file
try:
    config = ConfigObj('/etc/airtime/pypo.cfg')
    LS_HOST = config['ls_host']
    LS_PORT = config['ls_port']
    PUSH_INTERVAL = 2
except Exception, e:
    logger = logging.getLogger()
    logger.error('Error loading config file %s', e)
    sys.exit()

class PypoPush(Thread):
    def __init__(self, q):
        Thread.__init__(self)
        self.api_client = api_client.api_client_factory(config)
        self.set_export_source('scheduler')
        self.queue = q

        self.schedule = dict()
        self.playlists = dict()
        self.stream_metadata = dict()

        self.push_ahead = 10

    def set_export_source(self, export_source):
        self.export_source = export_source
        self.cache_dir = config["cache_dir"] + self.export_source + '/'
        self.schedule_tracker_file = self.cache_dir + "schedule_tracker.pickle"
        
    """
    The Push Loop - the push loop periodically checks if there is a playlist 
    that should be scheduled at the current time.
    If yes, the current liquidsoap playlist gets replaced with the corresponding one,
    then liquidsoap is asked (via telnet) to reload and immediately play it.
    """
    def push(self, export_source):
        logger = logging.getLogger('push')

        # get a new schedule from pypo-fetch
        if not self.queue.empty():
            scheduled_data = self.queue.get()
            logger.debug("Received data from pypo-fetch")
            self.schedule = scheduled_data['schedule']
            self.playlists = scheduled_data['liquidsoap_playlists']
            self.stream_metadata = scheduled_data['stream_metadata']
            logger.debug('schedule %s' % json.dumps(self.schedule))
            logger.debug('playlists %s' % json.dumps(self.playlists))

        schedule = self.schedule
        playlists = self.playlists
        
        if schedule:
            timenow = time.time()
            tnow = time.gmtime(timenow)
            tcoming = time.gmtime(timenow + self.push_ahead)
            str_tnow_s = "%04d-%02d-%02d-%02d-%02d-%02d" % (tnow[0], tnow[1], tnow[2], tnow[3], tnow[4], tnow[5])
            str_tcoming_s = "%04d-%02d-%02d-%02d-%02d-%02d" % (tcoming[0], tcoming[1], tcoming[2], tcoming[3], tcoming[4], tcoming[5])
                        
            for pkey in schedule:
                plstart = schedule[pkey]['start'][0:19]
         
                if str_tnow_s <= plstart and plstart < str_tcoming_s:
                    logger.debug('Preparing to push playlist scheduled at: %s', pkey)
                    playlist = schedule[pkey]

                    # We have a match, replace the current playlist and
                    # force liquidsoap to refresh.
                    if (self.push_liquidsoap(pkey, schedule, playlists) == 1):
                        logger.debug("Pushed to liquidsoap, updating 'played' status.")

                        # Call API to update schedule states
                        logger.debug("Doing callback to server to update 'played' status.")
                        self.api_client.notify_scheduled_item_start_playing(pkey, schedule)

                show_start = schedule[pkey]['show_start']
                show_end = schedule[pkey]['show_end']

    def push_liquidsoap(self, pkey, schedule, playlists):
        logger = logging.getLogger('push')

        try:
            playlist = playlists[pkey]

            #strptime returns struct_time in local time
            #mktime takes a time_struct and returns a floating point
            #gmtime Convert a time expressed in seconds since the epoch to a struct_time in UTC
            #mktime: expresses the time in local time, not UTC. It returns a floating point number, for compatibility with time().
            epoch_start = calendar.timegm(time.strptime(pkey, '%Y-%m-%d-%H-%M-%S'))

            #Return the time as a floating point number expressed in seconds since the epoch, in UTC.
            epoch_now = time.time()

            logger.debug("Epoch start: %s" % epoch_start)
            logger.debug("Epoch now: %s" % epoch_now)

            sleep_time = epoch_start - epoch_now;

            if sleep_time < 0:
                sleep_time = 0

            logger.debug('sleeping for %s s' % (sleep_time))
            time.sleep(sleep_time)

            tn = telnetlib.Telnet(LS_HOST, LS_PORT)

            #skip the currently playing song if any.
            logger.debug("source.skip\n")
            tn.write("source.skip\n")

            # Get any extra information for liquidsoap (which will be sent back to us)
            liquidsoap_data = self.api_client.get_liquidsoap_data(pkey, schedule)

            #Sending schedule table row id string.
            logger.debug("vars.pypo_data %s\n"%(liquidsoap_data["schedule_id"]))
            tn.write(("vars.pypo_data %s\n"%liquidsoap_data["schedule_id"]).encode('latin-1'))

            logger.debug('Preparing to push playlist %s' % pkey)
            for item in playlist:
                annotate = item['annotate']
                tn.write(str('queue.push %s\n' % annotate.encode('utf-8')))

                show_name = item['show_name']
                tn.write(str('vars.show_name %s\n' % show_name.encode('utf-8')))

            tn.write("exit\n")
            logger.debug(tn.read_all())

            status = 1
        except Exception, e:
            logger.error('%s', e)
            status = 0
        return status

    def run(self):
        loops = 0
        heartbeat_period = math.floor(30/PUSH_INTERVAL)
        logger = logging.getLogger('push')
        
        while True:
            if loops % heartbeat_period == 0:
                logger.info("heartbeat")
                loops = 0
            try: self.push('scheduler')
            except Exception, e:
                logger.error('Pypo Push Exception: %s', e)
            time.sleep(PUSH_INTERVAL)
            loops += 1
