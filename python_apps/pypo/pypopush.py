from datetime import datetime
from datetime import timedelta

import sys
import time
import logging
import logging.config
import telnetlib
import calendar
import json
import math

from Queue import Empty

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
    MAX_LIQUIDSOAP_QUEUE_LENGTH = 2
except Exception, e:
    logger = logging.getLogger()
    logger.error('Error loading config file %s', e)
    sys.exit()

class PypoPush(Thread):
    def __init__(self, q, telnet_lock):
        Thread.__init__(self)
        self.api_client = api_client.api_client_factory(config)
        self.queue = q

        self.media = dict()
        
        self.telnet_lock = telnet_lock

        self.push_ahead = 5
        self.last_end_time = 0
                
        self.pushed_objects = {}
                
        self.logger = logging.getLogger('push')
        
        
    def main(self):
        loops = 0
        heartbeat_period = math.floor(30/PUSH_INTERVAL)
        
        next_media_item_chain = None
        media_schedule = None
        time_until_next_play = None
                
        while True:
            try:
                if time_until_next_play is None:
                    media_schedule = self.queue.get(block=True)
                else:
                    media_schedule = self.queue.get(block=True, timeout=time_until_next_play)
                    
                #We get to the following lines only if a schedule was received.
                liquidsoap_queue_approx = self.get_queue_items_from_liquidsoap()
                self.handle_new_media_schedule(media_schedule, liquidsoap_queue_approx)
                next_media_item_chain = self.get_next_schedule_chain(media_schedule)
                self.logger.debug("Next schedule chain: %s", next_media_item_chain)
                
                if next_media_item_chain is not None:
                    tnow = datetime.utcnow()
                    chain_start = datetime.strptime(next_media_item_chain[0]['start'], "%Y-%m-%d-%H-%M-%S")
                    time_until_next_play = self.date_interval_to_seconds(chain_start - tnow)
                    self.logger.debug("Blocking %s seconds until show start", time_until_next_play)
                else:
                    self.logger.debug("Blocking indefinitely since no show scheduled next")
                    time_until_next_play = None
            except Empty, e:
                #We only get here when a new chain of tracks are ready to be played.
                self.push_to_liquidsoap(next_media_item_chain)
                
                #TODO
                time.sleep(2)
                
                next_media_item_chain = self.get_next_schedule_chain(media_schedule)
                if next_media_item_chain is not None:
                    tnow = datetime.utcnow()
                    chain_start = datetime.strptime(next_media_item_chain[0]['start'], "%Y-%m-%d-%H-%M-%S")
                    time_until_next_play = self.date_interval_to_seconds(chain_start - tnow)
                    self.logger.debug("Blocking %s seconds until show start", time_until_next_play)
                else:
                    self.logger.debug("Blocking indefinitely since no show scheduled next")
                    time_until_next_play = None
                
            if loops % heartbeat_period == 0:
                self.logger.info("heartbeat")
                loops = 0
            loops += 1

    def get_queue_items_from_liquidsoap(self):
        """
        This function connects to Liquidsoap to find what media items are in its queue.
        """
        try:
            self.telnet_lock.acquire()
            tn = telnetlib.Telnet(LS_HOST, LS_PORT)
            
            msg = 'queue.queue\n'
            tn.write(msg)
            response = tn.read_until("\r\n").strip(" \r\n")
            tn.write('exit\n')
            tn.read_all()
        except Exception, e:
            self.logger.error(str(e))
        finally:
            self.telnet_lock.release()
        
        liquidsoap_queue_approx = []
        
        if len(response) > 0:
            items_in_queue = response.split(" ")
            
            self.logger.debug("items_in_queue: %s", items_in_queue)
            
            for item in items_in_queue:
                if item in self.pushed_objects:
                    liquidsoap_queue_approx.append(self.pushed_objects[item])
                else:
                    """
                    We should only reach here if Pypo crashed and restarted (because self.pushed_objects was reset). In this case
                    let's clear the entire Liquidsoap queue. 
                    """
                    self.logger.error("ID exists in liquidsoap queue that does not exist in our pushed_objects queue: " + item)
                    self.clear_liquidsoap_queue()
                    liquidsoap_queue_approx = []
                    break
                
        return liquidsoap_queue_approx
            
    def handle_new_media_schedule(self, media, liquidsoap_queue_approx):
        """
        This function's purpose is to gracefully handle situations where
        Liquidsoap already has a track in its queue, but the schedule 
        has changed. If the schedule has changed, this function's job is to
        call other functions that will connect to Liquidsoap and alter its
        queue.
        """
        
        #iterate through the items we got from the liquidsoap queue and 
        #see if they are the same as the newly received schedule
        iteration = 0
        problem_at_iteration = None
        for queue_item in liquidsoap_queue_approx:
            if queue_item['start'] in media.keys():
                if queue_item['id'] == media['start']['id']:
                    #Everything OK for this iteration.
                    pass
                else:
                    #A different item has been scheduled at the same time! Need to remove
                    #all tracks from the Liquidsoap queue starting at this point, and re-add
                    #them. 
                    problem_at_iteration = iteration
                    break
            else:
                #There are no more items scheduled for this time! The user has shortened
                #the playlist, so we simply need to remove tracks from the queue. 
                problem_at_iteration = iteration
                break
            iteration+=1
        
        
        if problem_at_iteration is not None:
            #The first item in the Liquidsoap queue (the one that is currently playing)
            #has changed or been removed from the schedule. We need to clear the entire
            #queue, and push the new schedule
            self.remove_from_liquidsoap_queue(problem_at_iteration, liquidsoap_queue_approx)
        
        
                
    """
    The purpose of this function is to take a look at the last received schedule from
    pypo-fetch and return the next chain of media_items. A chain is defined as a sequence 
    of media_items where the end time of media_item 'n' is the start time of media_item
    'n+1'
    """
    def get_next_schedule_chain(self, media_schedule):
        chains = []
        
        current_chain = []
        for mkey in media_schedule:
            media_item = media_schedule[mkey]
            if len(current_chain) == 0:
                current_chain.append(media_item)
            elif media_item['start'] == current_chain[-1]['end']:
                current_chain.append(media_item)
            else:
                #current item is not a continuation of the chain.
                #Start a new one instead
                chains.append(current_chain)
                current_chain = [media_item]
                
        if len(current_chain) > 0:
            chains.append(current_chain)
                
        self.logger.debug('media_schedule %s', media_schedule)
        self.logger.debug("chains %s", chains)
        
        #all media_items are now divided into chains. Let's find the one that
        #starts closest in the future.
        
        tnow = datetime.utcnow()
        closest_start = None
        closest_chain = None
        for chain in chains:
            chain_start = datetime.strptime(chain[0]['start'], "%Y-%m-%d-%H-%M-%S")
            self.logger.debug("tnow %s, chain_start %s", tnow, chain_start)
            if (closest_start == None or chain_start < closest_start) and chain_start > tnow:
                closest_start = chain_start
                closest_chain = chain
                
        return closest_chain
        
                   
    def date_interval_to_seconds(self, interval):
        return (interval.microseconds + (interval.seconds + interval.days * 24 * 3600) * 10**6) / 10**6
                        
    def push_to_liquidsoap(self, media_item_chain):
        
        try:
            for media_item in media_item_chain:
                self.telnet_to_liquidsoap(media_item)
        except Exception, e:
            self.logger.error('Pypo Push Exception: %s', e)
                                            
    def clear_liquidsoap_queue(self):
        self.logger.debug("Clearing Liquidsoap queue")
        try:
            self.telnet_lock.acquire()
            tn = telnetlib.Telnet(LS_HOST, LS_PORT)
            msg = "source.skip\n"
            tn.write(msg)                
            tn.write("exit\n")
            tn.read_all()
        except Exception, e:
            self.logger.error(str(e))
        finally:
            self.telnet_lock.release()        
                
    def remove_from_liquidsoap_queue(self, problem_at_iteration, liquidsoap_queue_approx):        
        iteration = 0
        
        try:
            self.telnet_lock.acquire()
            tn = telnetlib.Telnet(LS_HOST, LS_PORT)
            
            for queue_item in liquidsoap_queue_approx:
                if iteration >= problem_at_iteration:

                    msg = "queue.remove %s\n" % queue_item['queue_id']
                    self.logger.debug(msg)
                    tn.write(msg)
                    response = tn.read_until("\r\n").strip("\r\n")
                    
                    if "No such request in my queue" in response:
                        """
                        Cannot remove because Liquidsoap started playing the item. Need
                        to use source.skip instead
                        """
                        msg = "source.skip\n"
                        self.logger.debug(msg)
                        tn.write(msg)
                iteration += 1
                        
            tn.write("exit\n")
            tn.read_all()
        except Exception, e:
            self.logger.error(str(e))
        finally:
            self.telnet_lock.release()
                
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
        try:
            self.telnet_lock.acquire()
            tn = telnetlib.Telnet(LS_HOST, LS_PORT)
            
            #tn.write(("vars.pypo_data %s\n"%liquidsoap_data["schedule_id"]).encode('utf-8'))
            
            annotation = self.create_liquidsoap_annotation(media_item)
            msg = 'queue.push %s\n' % annotation.encode('utf-8')
            self.logger.debug(msg)
            tn.write(msg)
            queue_id = tn.read_until("\r\n").strip("\r\n")
            
            #remember the media_item's queue id which we may use
            #later if we need to remove it from the queue.
            media_item['queue_id'] = queue_id
            
            #add media_item to the end of our queue
            self.pushed_objects[queue_id] = media_item
            
            show_name = media_item['show_name']
            msg = 'vars.show_name %s\n' % show_name.encode('utf-8')
            tn.write(msg)
            self.logger.debug(msg)
            
            tn.write("exit\n")
            self.logger.debug(tn.read_all())
        except Exception, e:
            self.logger.error(str(e))
        finally:
            self.telnet_lock.release()
            
    def create_liquidsoap_annotation(self, media):        
        return 'annotate:media_id="%s",liq_cue_in="%s",liq_cue_out="%s",schedule_table_id="%s":%s' \
            % (media['id'], float(media['cue_in']), float(media['cue_out']), media['row_id'], media['dst'])
                     
    def run(self):
        try: self.main()
        except Exception, e:
            self.logger.error('Pypo Push Exception: %s', e)
            
