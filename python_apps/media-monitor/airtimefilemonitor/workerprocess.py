# -*- coding: utf-8 -*-

import traceback
import os

class MediaMonitorWorkerProcess:

    def __init__(self, config, mmc):
        self.config = config
        self.mmc = mmc

    #this function is run in its own process, and continuously
    #checks the queue for any new file events.
    def process_file_events(self, queue, notifier):
        while True:
            try:
                event = queue.get()
                notifier.logger.info("received event %s", event)
                notifier.update_airtime(event)
            except Exception, e:
                notifier.logger.error(e)
                notifier.logger.error("traceback: %s", traceback.format_exc())
