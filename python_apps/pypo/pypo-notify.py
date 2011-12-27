# -*- coding: utf-8 -*-

"""
Python part of radio playout (pypo)

This function acts as a gateway between liquidsoap and the server API.
Mainly used to tell the platform what pypo/liquidsoap does.

Main case: 
 - whenever LS starts playing a new track, its on_metadata callback calls
   a function in ls (notify(m)) which then calls the python script here
   with the currently starting filename as parameter 
 - this python script takes this parameter, tries to extract the actual
   media id from it, and then calls back to the API to tell about it about it.

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
import urllib
import urllib2
import string
import json

# additional modules (should be checked)
from configobj import ConfigObj

# custom imports
from util import *
from api_clients import *

# Set up command-line options
parser = OptionParser()

# help screeen / info
usage = "%prog [options]" + " - notification gateway"
parser = OptionParser(usage=usage)

# Options
parser.add_option("-d", "--data", help="Pass JSON data from liquidsoap into this script.", metavar="data")
parser.add_option("-m", "--media-id", help="ID of the file that is currently playing.", metavar="media_id")
parser.add_option("-e", "--error", action="store", dest="error", type="string", help="liquidsoap error msg.", metavar="error_msg")
parser.add_option("-s", "--stream-id", help="ID stream", metavar="stream_id")
parser.add_option("-c", "--connect", help="liquidsoap connected", action="store_true", metavar="connect")
parser.add_option("-t", "--time", help="liquidsoap boot up time", action="store", dest="time", metavar="time", type="string")

# parse options
(options, args) = parser.parse_args()

# configure logging
logging.config.fileConfig("logging.cfg")

# loading config file
try:
    config = ConfigObj('/etc/airtime/pypo.cfg')
    
except Exception, e:
    print 'error: ', e
    sys.exit()
    
        
class Notify:
    def __init__(self):
        self.api_client = api_client.api_client_factory(config)
    
    def notify_media_start_playing(self, data, media_id):
        logger = logging.getLogger()
        
        logger.debug('#################################################')
        logger.debug('# Calling server to update about what\'s playing #')
        logger.debug('#################################################')
        logger.debug('data = '+ str(data))
        response = self.api_client.notify_media_item_start_playing(data, media_id) 
        logger.debug("Response: "+json.dumps(response))
    
    # @pram time: time that LS started
    def notify_liquidsoap_status(self, msg, stream_id, time):
        logger = logging.getLogger()
        
        logger.debug('#################################################')
        logger.debug('# Calling server to update liquidsoap status    #')
        logger.debug('#################################################')
        logger.debug('msg = '+ str(msg))
        response = self.api_client.notify_liquidsoap_status(msg, stream_id, time) 
        logger.debug("Response: "+json.dumps(response))
        
if __name__ == '__main__':
    print
    print '#########################################'
    print '#           *** pypo  ***               #'
    print '#     pypo notification gateway         #'
    print '#########################################'
    
    # initialize
    logger = logging.getLogger()
    
    if options.error and options.stream_id:
        try:
            n = Notify()
            n.notify_liquidsoap_status(options.error, options.stream_id, options.time)
        except Exception, e:
            print e
    elif options.connect and options.stream_id:
        try:
            n = Notify()
            n.notify_liquidsoap_status("OK", options.stream_id, options.time)
        except Exception, e:
            print e
    else:
        if not options.data:
            print "NOTICE: 'data' command-line argument not given."
            sys.exit()
        
        if not options.media_id:
            print "NOTICE: 'media_id' command-line argument not given."
            sys.exit()
        
        try:
            n = Notify()
            n.notify_media_start_playing(options.data, options.media_id)
        except Exception, e:
            print e
