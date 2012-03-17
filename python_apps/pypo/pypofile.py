from threading import Thread
from Queue import Empty
from configobj import ConfigObj

import logging
import logging.config
import shutil
import os
import sys

# configure logging
logging.config.fileConfig("logging.cfg")

# loading config file
try:
    config = ConfigObj('/etc/airtime/pypo.cfg')
    LS_HOST = config['ls_host']
    LS_PORT = config['ls_port']
    POLL_INTERVAL = int(config['poll_interval'])

except Exception, e:
    logger = logging.getLogger()
    logger.error('Error loading config file: %s', e)
    sys.exit(1)


class PypoFile(Thread):
    
    def __init__(self, schedule_queue):
        Thread.__init__(self)
        self.logger = logging.getLogger()
        self.media_queue = schedule_queue
        self.media = None
        self.cache_dir = os.path.join(config["cache_dir"], "scheduler")
        
    def copy_file(self, media_item):
        """
        Copy media_item from local library directory to local cache directory.
        """
        
        if media_item is None:
            return
        
        dst = media_item['dst']
        
        if not os.path.isfile(dst):
            self.logger.debug("copying from %s to local cache %s" % (media_item['uri'], dst))
            try:
                shutil.copy(media_item['uri'], dst)
            except:
                self.logger.error("Could not copy from %s to %s" % (media_item['uri'], dst))
        else:
            self.logger.debug("Destination %s already exists. Not copying", dst)    
    
    def get_highest_priority_media_item(self, schedule):
        """
        Get highest priority media_item in the queue. Currently the highest
        priority is decided by how close the start time is to "now".
        """
        if schedule is None:
            return None
            
        sorted_keys = sorted(schedule.keys())
        
        if len(sorted_keys) == 0:
            return None
        
        highest_priority = sorted_keys[0]
        media_item = schedule[highest_priority]
        
        self.logger.debug("Highest priority item: %s" % highest_priority)
        
        """
        Remove this media_item from the dictionary. On the next iteration
        (from the main function) we won't consider it for prioritization 
        anymore. If on the next iteration we have received a new schedule,
        it is very possible we will have to deal with the same media_items 
        again. In this situation, the worst possible case is that we try to
        copy the file again and realize we already have it (thus aborting the copy). 
        """
        del schedule[highest_priority]            
        
        return media_item
        
        
    def main(self):
        while True:
            try:
                if self.media is None or len(self.media) == 0:
                    """
                    We have no schedule, so we have nothing else to do. Let's
                    do a blocked wait on the queue
                    """
                    self.media = self.media_queue.get(block=True)
                else:
                    """
                    We have a schedule we need to process, but we also want
                    to check if a newer schedule is available. In this case
                    do a non-blocking queue.get and in either case (we get something
                    or we don't), get back to work on preparing getting files.
                    """
                    try:
                        self.media = self.media_queue.get_nowait()
                    except Empty, e:
                        pass
                                
                media_item = self.get_highest_priority_media_item(self.media)
                self.copy_file(media_item)
            except Exception, e:
                self.logger.error(str(e))
                raise
            
    def run(self):
        """
        Entry point of the thread
        """
        self.main()
