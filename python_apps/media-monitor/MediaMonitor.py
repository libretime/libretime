#!/usr/local/bin/python
import time
import logging
import logging.config
import sys
import os
import signal

from api_clients import api_client

from multiprocessing import Process, Queue as mpQueue

from pyinotify import WatchManager

from airtimefilemonitor.airtimenotifier import AirtimeNotifier
from airtimefilemonitor.airtimeprocessevent import AirtimeProcessEvent
from airtimefilemonitor.mediaconfig import AirtimeMediaConfig
from airtimefilemonitor.workerprocess import MediaMonitorWorkerProcess
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
    api_client = api_client.api_client_factory(config.cfg)
    
    logger.info("Setting up monitor")
    response = None
    while response is None:
        response = api_client.setup_media_monitor()
        time.sleep(5)
        
    storage_directory = response["stor"].encode('utf-8')
    logger.info("Storage Directory is: %s", storage_directory)
    config.storage_directory = os.path.normpath(storage_directory)
    config.imported_directory = os.path.normpath(storage_directory + '/imported')
    config.organize_directory = os.path.normpath(storage_directory + '/organize')
    
    multi_queue = mpQueue()
    logger.info("Initializing event processor")
except Exception, e:
    logger.error('Exception: %s', e)    
    
try:
    wm = WatchManager()
    pe = AirtimeProcessEvent(queue=multi_queue, airtime_config=config, wm=wm)

    bootstrap = AirtimeMediaMonitorBootstrap(logger, pe, api_client)
    bootstrap.scan()
    
    notifier = AirtimeNotifier(wm, pe, read_freq=1, timeout=0, airtime_config=config, api_client=api_client, bootstrap=bootstrap)
    notifier.coalesce_events()
        
    #create 5 worker processes
    wp = MediaMonitorWorkerProcess()
    for i in range(5):
        p = Process(target=wp.process_file_events, args=(multi_queue, notifier))
        processes.append(p)
        p.start()
        
    wdd = pe.watch_directory(storage_directory)
    logger.info("Added watch to %s", storage_directory)
    logger.info("wdd result %s", wdd[storage_directory])

    signal.signal(signal.SIGTERM, handleSigTERM)
    notifier.loop(callback=pe.notifier_loop_callback)
        
except KeyboardInterrupt:
    notifier.stop()
    logger.info("Keyboard Interrupt")
except Exception, e:
    logger.error('Exception: %s', e)
