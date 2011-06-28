#!/usr/local/bin/python
import json
import time
import logging
import logging.config
import sys
import os
import signal

from pyinotify import WatchManager, Notifier, ProcessEvent
from multiprocessing import Process, Lock, Queue as mpQueue

from airtimefilemonitor.airtimenotifier import AirtimeNotifier
from airtimefilemonitor.airtimeprocessevent import AirtimeProcessEvent
from airtimefilemonitor.mediaconfig import AirtimeMediaConfig

def handleSigTERM(signum, frame):
    logger = logging.getLogger()
    logger.info("Main Process Shutdown, TERM signal caught. %d")
    for p in processes:
        p.terminate()
        logger.info("Killed process. %d", p.pid)

    notifier_daemon_pid = open('/var/run/airtime-notifier.pid', 'r').read()
    os.kill(int(notifier_daemon_pid), 9)
    logger.info("Killed process. %d", int(notifier_daemon_pid))

    sys.exit(0)


# configure logging
try:
    logging.config.fileConfig("logging.cfg")
except Exception, e:
    print 'Error configuring logging: ', e
    sys.exit()

logger = logging.getLogger()
processes = []

try:
    config = AirtimeMediaConfig()
    logger.info("Initializing event processor")
    pe = AirtimeProcessEvent(airtime_config=config)

    notifier = AirtimeNotifier(pe.wm, pe, read_freq=0.1, timeout=0.1, airtime_config=config)
    notifier.coalesce_events()

    #create 5 worker processes
    for i in range(5):
        p = Process(target=notifier.process_file_events, args=(pe.multi_queue,))
        processes.append(p)
        p.start()

    signal.signal(signal.SIGTERM, handleSigTERM)

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

    notifier.loop(daemonize=True, callback=pe.notifier_loop_callback, pid_file='/var/run/airtime-notifier.pid', stdout='/var/log/airtime/media-monitor/media-monitor.log')

except KeyboardInterrupt:
    notifier.stop()
except Exception, e:
    notifier.stop()
    logger.error('Exception: %s', e)

