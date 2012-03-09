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
        self.push_ahead = 10
        self.last_end_time = 0
        
        self.logger = logging.getLogger('push')
        
    def push(self):
        """
        The Push Loop - the push loop periodically checks if there is a playlist 
        that should be scheduled at the current time.
        If yes, the current liquidsoap playlist gets replaced with the corresponding one,
        then liquidsoap is asked (via telnet) to reload and immediately play it.
        """

        timenow = time.time()
        # get a new schedule from pypo-fetch
        if not self.queue.empty():
            # make sure we get the latest schedule
            while not self.queue.empty():
                self.media = self.queue.get()
            self.logger.debug("Received data from pypo-fetch")          
            self.logger.debug('media %s' % json.dumps(self.media))

        media = self.media
        
        currently_on_air = False
        if media:
            tnow = time.gmtime(timenow)
            tcoming = time.gmtime(timenow + self.push_ahead)
            str_tnow_s = "%04d-%02d-%02d-%02d-%02d-%02d" % (tnow[0], tnow[1], tnow[2], tnow[3], tnow[4], tnow[5])
            str_tcoming_s = "%04d-%02d-%02d-%02d-%02d-%02d" % (tcoming[0], tcoming[1], tcoming[2], tcoming[3], tcoming[4], tcoming[5])
                        
            for key in media.keys():
                media_item = media[key]
                item_start = media_item['start'][0:19]
                
                if str_tnow_s <= item_start and item_start < str_tcoming_s:
                    """
                    If the media item starts in the next 30 seconds, push it to the queue.
                    """
                    self.logger.debug('Preparing to push media item scheduled at: %s', key)
                              
                    if self.push_to_liquidsoap(media_item):
                        self.logger.debug("Pushed to liquidsoap, updating 'played' status.")
                        
                        """
                        Temporary solution to make sure we don't push the same track multiple times.
                        """
                        del media[key]
                        
                        currently_on_air = True
                        self.liquidsoap_state_play = True
                        
    def push_to_liquidsoap(self, media_item):
        """
        This function looks at the media item, and either pushes it to the Liquidsoap
        queue immediately, or if the queue is empty - waits until the start time of the
        media item before pushing it. 
        """        
        try:
            if media_item["start"] == self.last_end_time:
                """
                this media item is attached to the end of the last
                track, so let's push it now so that Liquidsoap can start playing
                it immediately after (and prepare crossfades if need be).
                """
                self.logger.debug("Push track immediately.")
                self.telnet_to_liquidsoap(media_item)
                self.last_end_time = media_item["end"]
            else:
                """
                this media item does not start right after a current playing track.
                We need to sleep, and then wake up when this track starts.
                """
                self.logger.debug("sleep until track start.")
                self.sleep_until_start(media_item)
                
                self.telnet_to_liquidsoap(media_item)
                self.last_end_time = media_item["end"]
        except Exception, e:
            self.logger.error('Pypo Push Exception: %s', e)
            return False
            
        return True

    def sleep_until_start(self, media_item):
        """
        The purpose of this function is to look at the difference between
        "now" and when the media_item starts, and sleep for that period of time.
        After waking from sleep, this function returns.
        """
        
        mi_start = media_item['start'][0:19]
        
        #strptime returns struct_time in local time
        epoch_start = calendar.timegm(time.strptime(mi_start, '%Y-%m-%d-%H-%M-%S'))
        
        #Return the time as a floating point number expressed in seconds since the epoch, in UTC.
        epoch_now = time.time()
        
        self.logger.debug("Epoch start: %s" % epoch_start)
        self.logger.debug("Epoch now: %s" % epoch_now)

        sleep_time = epoch_start - epoch_now

        if sleep_time < 0:
            sleep_time = 0

        self.logger.debug('sleeping for %s s' % (sleep_time))
        time.sleep(sleep_time)

    def telnet_to_liquidsoap(self, media_item):
        """
        telnets to liquidsoap and pushes the media_item to its queue. Push the
        show name of every media_item as well, just to keep Liquidsoap up-to-date
        about which show is playing.
        """
        
        tn = telnetlib.Telnet(LS_HOST, LS_PORT)
        
        #tn.write(("vars.pypo_data %s\n"%liquidsoap_data["schedule_id"]).encode('utf-8'))
        
        annotation = media_item['annotation']
        msg = 'queue.push %s\n' % annotation.encode('utf-8')
        tn.write(msg)
        self.logger.debug(msg)
        
        show_name = media_item['show_name']
        msg = 'vars.show_name %s\n' % show_name.encode('utf-8')
        tn.write(msg)
        self.logger.debug(msg)
        
        tn.write("exit\n")
        self.logger.debug(tn.read_all())
                     
    def run(self):
        loops = 0
        heartbeat_period = math.floor(30/PUSH_INTERVAL)
        
        while True:
            if loops % heartbeat_period == 0:
                self.logger.info("heartbeat")
                loops = 0
            try: self.push()
            except Exception, e:
                self.logger.error('Pypo Push Exception: %s', e)
            time.sleep(PUSH_INTERVAL)
            loops += 1
