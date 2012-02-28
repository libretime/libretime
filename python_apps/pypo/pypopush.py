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
        self.queue = q

        self.media = dict()

        self.liquidsoap_state_play = True
        self.push_ahead = 30
        
    """
    The Push Loop - the push loop periodically checks if there is a playlist 
    that should be scheduled at the current time.
    If yes, the current liquidsoap playlist gets replaced with the corresponding one,
    then liquidsoap is asked (via telnet) to reload and immediately play it.
    """
    def push(self):
        logger = logging.getLogger('push')

        timenow = time.time()
        # get a new schedule from pypo-fetch
        if not self.queue.empty():
            # make sure we get the latest schedule
            while not self.queue.empty():
                self.media = self.queue.get()
            logger.debug("Received data from pypo-fetch")          
            logger.debug('media %s' % json.dumps(self.media))

        media = self.media
        
        currently_on_air = False
        if media:
            tnow = time.gmtime(timenow)
            tcoming = time.gmtime(timenow + self.push_ahead)
            str_tnow_s = "%04d-%02d-%02d-%02d-%02d-%02d" % (tnow[0], tnow[1], tnow[2], tnow[3], tnow[4], tnow[5])
            str_tcoming_s = "%04d-%02d-%02d-%02d-%02d-%02d" % (tcoming[0], tcoming[1], tcoming[2], tcoming[3], tcoming[4], tcoming[5])
                        
            
            for media_item in media:
                item_start = media_item['start'][0:19]
                
                if str_tnow_s <= item_start and item_start < str_tcoming_s:
                    """
                    If the media item starts in the next 30 seconds, push it to the queue.
                    """
                    logger.debug('Preparing to push media item scheduled at: %s', pkey)
                              
                    if self.push_to_liquidsoap(media_item):
                        logger.debug("Pushed to liquidsoap, updating 'played' status.")
                        
                        currently_on_air = True
                        self.liquidsoap_state_play = True

                        # Call API to update schedule states
                        logger.debug("Doing callback to server to update 'played' status.")
                        self.api_client.notify_scheduled_item_start_playing(pkey, schedule)
                        
    def push_to_liquidsoap(self, media_item):
        try:
            if media_item["starts"] == self.last_end_time:
                """
                this media item is attached to the end of the last
                track, so let's push it now so that Liquidsoap can start playing
                it immediately after (and prepare crossfades if need be).
                """
                telnet_to_liquidsoap(media_item)
                self.last_end_time = media_item["end"]
            else:
                """
                this media item does not start right after a current playing track.
                We need to sleep, and then wake up when this track starts.
                """
                sleep_until_start(media_item)
                
                telnet_to_liquidsoap(media_item)
                self.last_end_time = media_item["end"]
        except Exception, e:
            return False
            
        return True

    def sleep_until_start(media_item):
        mi_start = media_item['start'][0:19]
        
        #strptime returns struct_time in local time
        epoch_start = calendar.timegm(time.strptime(mi_start, '%Y-%m-%d-%H-%M-%S'))
        
        #Return the time as a floating point number expressed in seconds since the epoch, in UTC.
        epoch_now = time.time()
        
        logger.debug("Epoch start: %s" % epoch_start)
        logger.debug("Epoch now: %s" % epoch_now)

        sleep_time = epoch_start - epoch_now

        if sleep_time < 0:
            sleep_time = 0

        logger.debug('sleeping for %s s' % (sleep_time))
        time.sleep(sleep_time)

    def telnet_to_liquidsoap(media_item):
        tn = telnetlib.Telnet(LS_HOST, LS_PORT)
        
        #tn.write(("vars.pypo_data %s\n"%liquidsoap_data["schedule_id"]).encode('utf-8'))
        
        annotation = media_item['annotation']
        tn.write('queue.push %s\n' % annotation.encode('utf-8'))
        
        show_name = media_item['show_name']
        tn.write('vars.show_name %s\n' % show_name.encode('utf-8'))
        
        tn.write("exit\n")
        logger.debug(tn.read_all())
                     
    def run(self):
        loops = 0
        heartbeat_period = math.floor(30/PUSH_INTERVAL)
        logger = logging.getLogger('push')
        
        while True:
            if loops % heartbeat_period == 0:
                logger.info("heartbeat")
                loops = 0
            try: self.push()
            except Exception, e:
                logger.error('Pypo Push Exception: %s', e)
            time.sleep(PUSH_INTERVAL)
            loops += 1
