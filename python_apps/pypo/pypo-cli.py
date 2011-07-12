#!/usr/bin/env python
# -*- coding: utf-8 -*-

"""
Python part of radio playout (pypo)
"""
import time
from optparse import *
import sys
import os
import signal
import logging
import logging.config
import logging.handlers
from Queue import Queue

from pypopush import PypoPush
from pypofetch import PypoFetch

from configobj import ConfigObj

# custom imports
from util import CueFile
from api_clients import api_client

PYPO_VERSION = '1.1'

# Set up command-line options
parser = OptionParser()

# help screen / info
usage = "%prog [options]" + " - python playout system"
parser = OptionParser(usage=usage)

# Options
parser.add_option("-v", "--compat", help="Check compatibility with server API version", default=False, action="store_true", dest="check_compat")

parser.add_option("-t", "--test", help="Do a test to make sure everything is working properly.", default=False, action="store_true", dest="test")
parser.add_option("-b", "--cleanup", help="Cleanup", default=False, action="store_true", dest="cleanup")
parser.add_option("-c", "--check", help="Check the cached schedule and exit", default=False, action="store_true", dest="check")

# parse options
(options, args) = parser.parse_args()

# configure logging
logging.config.fileConfig("logging.cfg")

# loading config file
try:
    config = ConfigObj('/etc/airtime/pypo.cfg')
except Exception, e:
    logger = logging.getLogger()
    logger.error('Error loading config file: %s', e)
    sys.exit()

class Global:
    def __init__(self):
        self.api_client = api_client.api_client_factory(config)
        self.cue_file = CueFile()
        self.set_export_source('scheduler')
        
    def selfcheck(self):
        self.api_client = api_client.api_client_factory(config)
        return self.api_client.is_server_compatible()

    def set_export_source(self, export_source):
        self.export_source = export_source
        self.cache_dir = config["cache_dir"] + self.export_source + '/'
        self.schedule_file = self.cache_dir + 'schedule.pickle'
        self.schedule_tracker_file = self.cache_dir + "schedule_tracker.pickle"
        
    def test_api(self):
        self.api_client.test()

"""
    def check_schedule(self, export_source):
        logger = logging.getLogger()

        try:
            schedule_file = open(self.schedule_file, "r")
            schedule = pickle.load(schedule_file)
            schedule_file.close()

        except Exception, e:
            logger.error("%s", e)
            schedule = None

        for pkey in sorted(schedule.iterkeys()):
            playlist = schedule[pkey]
            print '*****************************************'
            print '\033[0;32m%s %s\033[m' % ('scheduled at:', str(pkey))
            print 'cached at :   ' + self.cache_dir + str(pkey)
            print 'played:       ' + str(playlist['played'])
            print 'schedule id:  ' + str(playlist['schedule_id'])
            print 'duration:     ' + str(playlist['duration'])
            print 'source id:    ' + str(playlist['x_ident'])
            print '-----------------------------------------'

            for media in playlist['medias']:
                print media
"""

def keyboardInterruptHandler(signum, frame):
    logger = logging.getLogger()
    logger.info('\nKeyboard Interrupt\n')
    sys.exit(0)


if __name__ == '__main__':
    logger = logging.getLogger()
    logger.info('###########################################')
    logger.info('#             *** pypo  ***               #')
    logger.info('#   Liquidsoap Scheduled Playout System   #')
    logger.info('###########################################')

    signal.signal(signal.SIGINT, keyboardInterruptHandler)

    # initialize
    g = Global()

    while not g.selfcheck(): time.sleep(5)
    
    logger = logging.getLogger()

    if options.test:
        g.test_api()
        sys.exit()

    q = Queue()

    pp = PypoPush(q)
    pp.start()

    pf = PypoFetch(q)
    pf.start()

    while True: time.sleep(3600)

    #pp.join()
    #pf.join()
"""
    if options.check:
        try: g.check_schedule()
        except Exception, e:
            print e

    if options.cleanup:
        try: pf.cleanup('scheduler')
        except Exception, e:
            print e
"""
