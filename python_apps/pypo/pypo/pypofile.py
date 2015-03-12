# -*- coding: utf-8 -*-

from threading import Thread
from Queue import Empty

import logging
import shutil
import os
import sys
import stat

from std_err_override import LogWriter

# configure logging
logging_cfg = "/etc/airtime/pypo_logging.cfg"
logging.config.fileConfig(logging_cfg)
logger = logging.getLogger()
LogWriter.override_std_err(logger)

#need to wait for Python 2.7 for this..
#logging.captureWarnings(True)


class PypoFile(Thread):

    def __init__(self, schedule_queue, config):
        Thread.__init__(self)
        self.logger = logging.getLogger()
        self.media_queue = schedule_queue
        self.media = None
        self.cache_dir = os.path.join(config["cache_dir"], "scheduler")

    def copy_file(self, media_item):
        """
        Copy media_item from local library directory to local cache directory.
        """
        src = media_item['uri']
        dst = media_item['dst']

        try:
            src_size = os.path.getsize(src)
        except Exception, e:
            self.logger.error("Could not get size of source file: %s", src)
            return

        dst_exists = True
        try:
            dst_size = os.path.getsize(dst)
        except Exception, e:
            dst_exists = False

        do_copy = False
        if dst_exists:
            if src_size != dst_size:
                do_copy = True
            else:
                self.logger.debug("file %s already exists in local cache as %s, skipping copying..." % (src, dst))
        else:
            do_copy = True

        media_item['file_ready'] = not do_copy

        if do_copy:
            self.logger.debug("copying from %s to local cache %s" % (src, dst))
            try:

                """
                copy will overwrite dst if it already exists
                """
                shutil.copy(src, dst)

                #make file world readable
                os.chmod(dst, stat.S_IRUSR | stat.S_IRGRP | stat.S_IROTH)

                media_item['file_ready'] = True
            except Exception, e:
                self.logger.error("Could not copy from %s to %s" % (src, dst))
                self.logger.error(e)

    def get_highest_priority_media_item(self, schedule):
        """
        Get highest priority media_item in the queue. Currently the highest
        priority is decided by how close the start time is to "now".
        """
        if schedule is None or len(schedule) == 0:
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
                if media_item is not None:
                    self.copy_file(media_item)
            except Exception, e:
                import traceback
                top = traceback.format_exc()
                self.logger.error(str(e))
                self.logger.error(top)
                raise

    def run(self):
        """
        Entry point of the thread
        """
        self.main()
