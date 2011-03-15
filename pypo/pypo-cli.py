#!/usr/bin/env python
# -*- coding: utf-8 -*-

"""
Python part of radio playout (pypo)

The main functions are "fetch" (./pypo_cli.py -f) and "push" (./pypo_cli.py -p)
"""

# python defaults (debian default)
import time
#import calendar


#import traceback
from optparse import *
import sys
#import datetime
import logging
import logging.config
#import shutil
#import urllib
#import urllib2
#import pickle
#import telnetlib
#import random
#import string
#import operator
#import inspect

from pypopush import PypoPush
from pypofetch import PypoFetch

# additional modules (should be checked)
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
parser.add_option("-f", "--fetch-scheduler", help="Fetch the schedule from server.  This is a polling process that runs forever.", default=False, action="store_true", dest="fetch_scheduler")
parser.add_option("-p", "--push-scheduler", help="Push the schedule to Liquidsoap. This is a polling process that runs forever.", default=False, action="store_true", dest="push_scheduler")

parser.add_option("-b", "--cleanup", help="Cleanup", default=False, action="store_true", dest="cleanup")
parser.add_option("-c", "--check", help="Check the cached schedule and exit", default=False, action="store_true", dest="check")

# parse options
(options, args) = parser.parse_args()

# configure logging
logging.config.fileConfig("logging.cfg")

# loading config file
try:
    config = ConfigObj('config.cfg')
    POLL_INTERVAL = float(config['poll_interval'])
    PUSH_INTERVAL = float(config['push_interval'])
    LS_HOST = config['ls_host']
    LS_PORT = config['ls_port']
except Exception, e:
    print 'Error loading config file: ', e
    sys.exit()

class Global:
    def __init__(self):
        self.api_client = api_client.api_client_factory(config)
        self.cue_file = CueFile()
        self.set_export_source('scheduler')
        
    def selfcheck(self):
        self.api_client = api_client.api_client_factory(config)
        if (not self.api_client.is_server_compatible()):
            sys.exit()

    def set_export_source(self, export_source):
        self.export_source = export_source
        self.cache_dir = config["cache_dir"] + self.export_source + '/'
        self.schedule_file = self.cache_dir + 'schedule.pickle'
        self.schedule_tracker_file = self.cache_dir + "schedule_tracker.pickle"
        
    def test_api(self):
        self.api_client.test()

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
            print 'subtype:      ' + str(playlist['subtype'])
            print 'played:       ' + str(playlist['played'])
            print 'schedule id:  ' + str(playlist['schedule_id'])
            print 'duration:     ' + str(playlist['duration'])
            print 'source id:    ' + str(playlist['x_ident'])
            print '-----------------------------------------'

            for media in playlist['medias']:
                print media



if __name__ == '__main__':
    print '###########################################'
    print '#             *** pypo  ***               #'
    print '#      Liquidsoap + External Scheduler    #'
    print '#            Playout System               #'
    print '###########################################'

    # initialize
    g = Global()
    g.selfcheck()
    
    logger = logging.getLogger()
    loops = 0

    if options.test:
        g.test_api()
        sys.exit()

    
    if options.fetch_scheduler:
        pf = PypoFetch()
        while True:
            try: pf.fetch('scheduler')
            except Exception, e:
                print e
                sys.exit()

            if (loops%2 == 0):
                logger.info("heartbeat")
            loops += 1
            time.sleep(POLL_INTERVAL)

    if options.push_scheduler:
        pp = PypoPush()
        while True:
            try: pp.push('scheduler')
            except Exception, e:
                print 'PUSH ERROR!! WILL EXIT NOW:('
                print e
                sys.exit()

            if (loops%60 == 0):
                logger.info("heartbeat")

            loops += 1
            time.sleep(PUSH_INTERVAL)

    if options.check:
        try: g.check_schedule()
        except Exception, e:
            print e

    if options.cleanup:
        try: pf.cleanup('scheduler')
        except Exception, e:
            print e
        sys.exit()
