#!/usr/local/bin/python
import time
import logging
import logging.config
import sys
import os
import signal

from multiprocessing import Process

from airtimefilemonitor.airtimenotifier import AirtimeNotifier
from airtimefilemonitor.airtimeprocessevent import AirtimeProcessEvent
from airtimefilemonitor.mediaconfig import AirtimeMediaConfig
from airtimefilemonitor.airtimemediamonitorbootstrap import AirtimeMediaMonitorBootstrap

def handleSigTERM(signum, frame):
    logger = logging.getLogger()
    logger.info("Main Process Shutdown, TERM signal caught.")
    for p in processes:
        logger.info("Killed process. %d", p.pid)
        p.terminate()

    sys.exit(0)


# configure logging
try:
    logging.config.fileConfig("logging.cfg")
except Exception, e:
    print 'Error configuring logging: ', e
    sys.exit(1)

logger = logging.getLogger()
processes = []

try:
    config = AirtimeMediaConfig(logger)
    
    bootstrap = AirtimeMediaMonitorBootstrap(logger)
    bootstrap.scan()
    
    logger.info("Initializing event processor")
    pe = AirtimeProcessEvent(airtime_config=config)

    notifier = AirtimeNotifier(pe.wm, pe, read_freq=0.1, timeout=0.1, airtime_config=config)
    notifier.coalesce_events()

    #create 5 worker processes
    for i in range(5):
        p = Process(target=notifier.process_file_events, args=(pe.multi_queue,))
        processes.append(p)
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


    #register signal before process forks and exits.
    signal.signal(signal.SIGTERM, handleSigTERM)
    notifier.loop(callback=pe.notifier_loop_callback)


        
except KeyboardInterrupt:
    notifier.stop()
    logger.info("Keyboard Interrupt")
except Exception, e:
    #notifier.stop()
    logger.error('Exception: %s', e)
