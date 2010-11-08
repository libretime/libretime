#!/usr/bin/env python
# -*- coding: utf-8 -*-

# author Jonas Ohrstrom <jonas@digris.ch>

"""
Python part of radio playout (pypo)

The main functionas are "fetch" (./pypo_cli.py -f) and "push" (./pypo_cli.py -p)

Also check out the php counterpart that handles the api requests:
https://lab.digris.ch/svn/elgg/trunk/unstable/mod/medialibrary/application/controllers/api/pypo.php

Attention & ToDos
- liquidsoap does not like mono files! So we have to make sure that only files with 
  2 channels are fed to LS
  (solved: current = audio_to_stereo(current) - maybe not with ultimate performance)


made for python version 2.5!!
should work with 2.6 as well with a bit of adaption. for 
sure the json parsing has to be changed
(2.6 has an parser, pypo brigs it's own -> util/json.py)
"""

# python defaults (debian default)
import time
import os
import traceback
from optparse import *
import sys
import time
import datetime
import logging
import logging.config
import shutil
import urllib
import urllib2
import pickle
import telnetlib
import random
import string
import operator

# additional modules (should be checked)
from configobj import ConfigObj

# custom imports
from util import *
from obp import *



PYPO_VERSION = '0.1'

#set up command-line options
parser = OptionParser()

# help screeen / info
usage = "%prog [options]" + " - python playout system"
parser = OptionParser(usage=usage)

#options
parser.add_option("-f", "--fetch", help="Fetch Schedule (loop, interval in config file)", default=False, action="store_true", dest="fetch")
parser.add_option("-p", "--push", help="Push pl to Liquidsoap (loop, interval in config file)", default=False, action="store_true", dest="push")
parser.add_option("-b", "--cleanup", help="Faeili Butzae aka cleanup", default=False, action="store_true", dest="cleanup")
parser.add_option("-j", "--jingles", help="Get new jungles from obp, comma separated list if jingle-id's as argument", metavar="LIST")
parser.add_option("-c", "--check", help="Check the cached schedule and exit", default=False, action="store_true", dest="check")

parser.add_option("-P", "--pushpkey", help="Push PKEY", metavar="LIST")

# parse options
(options, args) = parser.parse_args()

# configure logging
logging.config.fileConfig("logging.cfg")

# loading config file
try:
    config = ConfigObj('config.cfg')
    CACHE_DIR = config['cache_dir']
    FILE_DIR = config['file_dir']
    TMP_DIR = config['tmp_dir']
    BASE_URL = config['base_url']
    API_BASE = config['api_base']
    EXPORT_SOURCE = config['export_source']
    OBP_API_KEY = config['api_key']
    POLL_INTERVAL = float(config['poll_interval'])
    PUSH_INTERVAL = float(config['push_interval'])
    LS_HOST = config['ls_host']
    LS_PORT = config['ls_port']
    PREPARE_AHEAD = config['prepare_ahead']
    CACHE_FOR = config['cache_for']
    CUE_STYLE = config['cue_style']
    
except Exception, e:
    print 'error: ', e
    sys.exit()
    
#TIME = time.localtime(time.time())
TIME = (2010, 6, 26, 15, 33, 23, 2, 322, 0)
    

class Global:
    def __init__(self):
        #print '#   global initialisation'
        print
        
    def selfcheck(self):
        
        self.api_auth = urllib.urlencode({'api_key': API_KEY})
        self.api_client = ApiClient(API_BASE, self.api_auth)
        self.api_client.check_version()
            
        """
        Uncomment the following lines to let pypo check if
        liquidsoap is running. (checks for a telnet server)
        """
#        while self.status.check_ls(LS_HOST, LS_PORT) == 0:
#            print 'Unable to connect to liquidsoap. Is it up and running?'
#            time.sleep(2)
            
        
  
"""

"""
class Playout:
    def __init__(self):
        #print '#   init fallback' 
        
        self.cache_dir = CACHE_DIR 
        self.file_dir = FILE_DIR 
        self.tmp_dir = TMP_DIR 
        self.export_source = EXPORT_SOURCE
        
        self.api_auth = urllib.urlencode({'api_key': API_KEY})
        self.api_client = ApiClient(API_BASE, self.api_auth)
        self.cue_file = CueFile()
        
        self.schedule_file = CACHE_DIR + 'schedule'
        
        # set initial state
        self.range_updated = False
    
    
    def push_liquidsoap(self,options):
        logger = logging.getLogger("push_liquidsoap")
        print options
        #pkey = '2010-10-26-21-00-00'
        pkey = options
        src = self.cache_dir + str(pkey) + '/list.lsp'
        print src
s        
        if True == os.access(src, os.R_OK):
            print 'OK - Can read'
            
        pl_file = open(src, "r")


        """
        i know this could be wrapped, maybe later..
        """
        tn = telnetlib.Telnet(LS_HOST, 1234)
        #tn.write("\n")
        
        for line in pl_file.readlines():
            print line.strip() 
            #tn.write('q.push ' + pl_entry)
            #tn.write("\n")
            tn.write('scheduler.push %s' % (line.strip()))
            tn.write("\n")
            
        tn.write("scheduler_q0.queue \n")
        tn.write("scheduler_q1.queue \n")
        #time.sleep(2)
        
        #print tn.read_all()
        
        print 'sending "flip"'
        
        tn.write('scheduler.flip')
        tn.write("\n")
        
        #tn.write("live_in.stop\n")
        #tn.write("stream_disable\n")
        #time.sleep(0.2)
        #tn.write("\n")
        #tn.write("reload_current\n")
        #tn.write("current.reload\n")
        #time.sleep(0.2)
        #tn.write("skip_current\n")
        
#                if(int(ptype) == 6):
#                    """
#                    Couchcaster / Recast comming. Stop/Start live input to have ls re-read it's playlist
#                    """
#                    print 'Couchcaster - switching to stream'
#                    tn.write("live_in.start\n")
#                    time.sleep(0.2)
#                    tn.write("stream_enable\n")
#                
#
#                
#                """
#                Pass some extra information to liquidsoap
#                """
#                tn.write("pl.pl_id '%s'\n" % p_id)
#                tn.write("pl.user_id '%s'\n" % user_id)
        
        
        
        tn.write("exit\n")
        print tn.read_all()
        status = 1
        sys.exit()
            
#        except Exception, e:
#            logger.error('%s', e)
#            status = 0

    
            

if __name__ == '__main__':
  
    print
    print '#########################################'
    print '#           *** pypo  ***               #'
    print '#         obp python playout            #'
    print '#########################################'
    print
    
    # initialize
    g = Global()
    g.selfcheck()
    po = Playout()


run = True
while run == True:
    
    logger = logging.getLogger("pypo")

    while options.pushpkey:
        try: po.push_liquidsoap(options.pushpkey)
        except Exception, e:
            print e
        sys.exit()  

    while options.push:
        po.push_liquidsoap()
        sys.exit()
