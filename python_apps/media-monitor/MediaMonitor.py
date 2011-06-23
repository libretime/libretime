#!/usr/local/bin/python
import json
import time
import logging
import logging.config
import sys
import os

from pyinotify import WatchManager, Notifier, ProcessEvent
from multiprocessing import Process, Lock, Queue as mpQueue

from airtimefilemonitor.airtimenotifier import AirtimeNotifier
from airtimefilemonitor.airtimeprocessevent import AirtimeProcessEvent
from airtimefilemonitor.mediaconfig import AirtimeMediaConfig

if __name__ == '__main__':

     # configure logging
    try:
        logging.config.fileConfig("logging.cfg")
    except Exception, e:
        print 'Error configuring logging: ', e
        sys.exit()

    logger = logging.getLogger()

    try:
        config = AirtimeMediaConfig()

        logger.info("Initializing event processor")

        pe = AirtimeProcessEvent(airtime_config=config)

        notifier = AirtimeNotifier(pe.wm, pe, read_freq=1, timeout=1, airtime_config=config)
        notifier.coalesce_events()

        p = Process(target=notifier.process_file_events, args=(pe.file_events,))
        p.daemon = True
        p.start()

        logger.info("Setting up monitor")
        response = None
        while response is None:
            response = notifier.api_client.setup_media_monitor()
            time.sleep(5)

        storage_directory = response["stor"].encode('utf-8')
        logger.info("Storage Directory is: %s", storage_directory)
        config.storage_directory = storage_directory

        wdd = pe.watch_directory(storage_directory)
        logger.info("Added watch to %s", storage_directory)
        logger.info("wdd result %s", wdd[storage_directory])

        #notifier.loop(callback=mm.notifier_loop_callback)

        while True:
            if(notifier.check_events(1)):
                notifier.read_events()
                notifier.process_events()
            pe.notifier_loop_callback(notifier)

    except KeyboardInterrupt:
        notifier.stop()
    except Exception, e:
        logger.error('Exception: %s', e)
    finally:
        p.terminate()
