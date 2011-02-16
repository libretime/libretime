#!/usr/bin/env python
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
from dls import *

PYPO_VERSION = '0.9'


# Set up command-line options
parser = OptionParser()

# help screeen / info
usage = "%prog [options]" + " - notification gateway"
parser = OptionParser(usage=usage)

# Options
parser.add_option("-d", "--data", help="Pass JSON data from liquidsoap into this script.", metavar="data")
#parser.add_option("-p", "--playing", help="Tell server what is playing right now.", default=False, action="store_true", dest="playing")
#parser.add_option("-t", "--playlist-type", help="", metavar="playlist_type")
parser.add_option("-m", "--media-id", help="ID of the file that is currently playing.", metavar="media_id")
#parser.add_option("-U", "--user-id", help="", metavar="user_id")
#parser.add_option("-P", "--playlist-id", help="", metavar="playlist_id")
#parser.add_option("-T", "--transmission-id", help="", metavar="transmission_id")
#parser.add_option("-E", "--export-source", help="", metavar="export_source")

# parse options
(options, args) = parser.parse_args()

# configure logging
logging.config.fileConfig("logging.cfg")

# loading config file
try:
    config = ConfigObj('config.cfg')
    
except Exception, e:
    print 'error: ', e
    sys.exit()
    
    
class Global:
    def __init__(self):
        print
        
    def selfcheck(self):
        pass
        #self.api_client = api_client.api_client_factory(config)
        #self.api_client.check_version()
        
class Notify:
    def __init__(self):
        self.api_client = api_client.api_client_factory(config)
        #self.dls_client = DlsClient('127.0.0.128', 50008, 'myusername', 'mypass')

    
    def notify_media_start_playing(self, data, media_id):
        logger = logging.getLogger()
        #tnow = time.localtime(time.time())
        
        logger.debug('#################################################')
        logger.debug('# Calling server to update about what\'s playing #')
        logger.debug('#################################################')
        logger.debug('data = '+ str(data))
        #print 'options.data = '+ options.data
        #data = json.loads(options.data)
        response = self.api_client.notify_media_item_start_playing(data, media_id) 
        logger.debug("Response: "+str(response))

    #def start_playing(self, options):
    #    logger = logging.getLogger("start_playing")
    #    tnow = time.localtime(time.time())
    #    
    #    #print options
    #
    #    logger.debug('#################################################')
    #    logger.debug('# Calling server to update about what\'s playing #')
    #    logger.debug('#################################################')
    #
    #    if int(options.playlist_type) < 5:
    #        logger.debug('seems to be a playlist')
    #        
    #        try:
    #            media_id = int(options.media_id)
    #        except Exception, e:
    #            media_id = 0
    #        
    #        response = self.api_client.update_start_playing(options.playlist_type, options.export_source, media_id, options.playlist_id, options.transmission_id) 
    #
    #        logger.debug(response)
    #
    #    if int(options.playlist_type) == 6:
    #        logger.debug('seems to be a couchcast')
    #        
    #        try:
    #            media_id = int(options.media_id)
    #        except Exception, e:
    #            media_id = 0
    #        
    #        response = self.api_client.update_start_playing(options.playlist_type, options.export_source, media_id, options.playlist_id, options.transmission_id) 
    #
    #        logger.debug(response)
    #    
    #    sys.exit()  
    
    #def start_playing_legacy(self, options):
    #    logger = logging.getLogger("start_playing")
    #    tnow = time.localtime(time.time())
    #
    #    print '#################################################'
    #    print '# Calling server to update about what\'s playing     #'
    #    print '#################################################'
    #
    #    path = options
    #    
    #    print
    #    print path
    #    print 
    #            
    #    if 'pl_id' in path:
    #        print 'seems to be a playlist'
    #        type = 'playlist'
    #        id = path[5:] 
    #            
    #    elif 'text' in path:
    #        print 'seems to be a playlist'
    #        type = 'text'
    #        id = path[4:]
    #        print id
    #    
    #    else:
    #        print 'seems to be a single track (media)'
    #        type = 'media'
    #        try:
    #            file = path.split("/")[-1:][0]
    #            if file.find('_cue_') > 0:
    #                id = file.split("_cue_")[0]
    #            else:
    #                id = file.split(".")[-2:][0]
    #            
    #        except Exception, e:
    #            #print e
    #            id = False
    #        
    #    try:
    #        id = id
    #    except Exception, e:
    #        #print e
    #        id = False
    #    
    #    print 
    #    print type + " id: ", 
    #    print id            
    #      
    #    
    #    response = self.api_client.update_start_playing(type, id, self.export_source, path) 
    #    
    #    print 'DONE' 
    #    
    #    try:
    #        txt = response['txt']
    #        print '#######################################'
    #        print txt
    #        print '#######################################'
    #        #self.dls_client.set_txt(txt)
    #
    #    except Exception, e:
    #        print e
        

if __name__ == '__main__':
    print
    print '#########################################'
    print '#           *** pypo  ***               #'
    print '#     pypo notification gateway         #'
    print '#########################################'
    
    # initialize
    g = Global()
    logger = logging.getLogger()
    #if options.playing:
    #    try: n.start_playing(options)
    #    except Exception, e:
    #        print e
    #    sys.exit()  
    if not options.data:
        print "NOTICE: 'data' command-line argument not given."
        sys.exit()
    
    if not options.media_id:
        print "NOTICE: 'media_id' command-line argument not given."
        sys.exit()
    
    try:
        g.selfcheck()
        n = Notify()
        n.notify_media_start_playing(options.data, options.media_id)
    except Exception, e:
        print e
