#!/usr/bin/env python
# -*- coding: utf-8 -*-

# author Jonas Ohrstrom <jonas@digris.ch>

"""
Python part of radio playout (pypo)

This function acts as a gateway between liquidsoap and the obp-api.
Mainliy used to tell the plattform what pypo/LS does.

Main case: 
 - whenever LS starts playing a new track, its on_metadata callback calls
   a function in ls (notify(m)) which then calls the pythin script here
   with the currently starting filename as parameter 
 - this python script takes this parameter, tries to extract the actual
   media id from it, and then calls back to obp via api to tell about


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

# additional modules (should be checked)
from configobj import ConfigObj

# custom imports
from util import *
from obp import *
from dls import *



PYPO_VERSION = '0.9'
OBP_MIN_VERSION = 2010040501 # required obp version


#set up command-line options
parser = OptionParser()

# help screeen / info
usage = "%prog [options]" + " - notification gateway"
parser = OptionParser(usage=usage)

#options
#parser.add_option("-p", "--playing", help="Tell daddy what is playing right now", dest="playing", default=False, metavar=False)

parser.add_option("-p", "--playing", help="Tell daddy what is playing right now", default=False, action="store_true", dest="playing")


parser.add_option("-t", "--playlist-type", help="Tell daddy what is playing right now", metavar="playlist_type")

parser.add_option("-M", "--media-id", help="Tell daddy what is playing right now", metavar="media_id")
parser.add_option("-U", "--user-id", help="Tell daddy what is playing right now", metavar="user_id")
parser.add_option("-P", "--playlist-id", help="Tell daddy what is playing right now", metavar="playlist_id")
parser.add_option("-T", "--transmission-id", help="Tell daddy what is playing right now", metavar="transmission_id")
parser.add_option("-E", "--export-source", help="Tell daddy what is playing right now", metavar="export_source")

# parse options
(options, args) = parser.parse_args()

# configure logging
logging.config.fileConfig("logging.cfg")

# loading config file
try:
    config = ConfigObj('config.cfg')
    TMP_DIR = config['tmp_dir']
    BASE_URL = config['base_url']
    OBP_API_BASE = BASE_URL + 'mod/medialibrary/'
    
    OBP_STATUS_URL = OBP_API_BASE + 'status/version/json'
    OBP_API_KEY = config['obp_api_key']
    
except Exception, e:
    print 'error: ', e
    sys.exit()
    
    
class Global:
    def __init__(self):
        print
        
    def selfcheck(self):
        
        self.api_auth = urllib.urlencode({'api_key': OBP_API_KEY})
        self.api_client = ApiClient(OBP_API_BASE, self.api_auth)
        
        obp_version = self.api_client.get_obp_version()
        
        if obp_version == 0:
            print '#################################################'
            print 'Unable to get OBP version. Is OBP up and running?'
            print '#################################################'
            print
            sys.exit()
         
        elif obp_version < OBP_MIN_VERSION:
            print 'OBP version: ' + str(obp_version)
            print 'OBP min-version: ' + str(OBP_MIN_VERSION)
            print 'pypo not compatible with this version of OBP'
            print
            sys.exit()
         
        else:
            print 'OBP API: ' + str(OBP_API_BASE)
            print 'OBP version: ' + str(obp_version)
            print 'OBP min-version: ' + str(OBP_MIN_VERSION)
            print 'pypo is compatible with this version of OBP'
            print

class Notify:
    def __init__(self):

        self.tmp_dir = TMP_DIR 
        
        self.api_auth = urllib.urlencode({'api_key': OBP_API_KEY})
        self.api_client = ApiClient(OBP_API_BASE, self.api_auth)
        
        self.dls_client = DlsClient('127.0.0.128', 50008, 'myusername', 'mypass')

    

    
    def start_playing(self, options):
        logger = logging.getLogger("start_playing")

        tnow = time.localtime(time.time())
        
        #print options



        print '#################################################'
        print '# calling obp to tell about what\'s playing     #'
        print '#################################################'      

        if int(options.playlist_type) < 5:
            print 'seems to be a playlist'
            
            try:
                media_id = int(options.media_id)
            except Exception, e:
                media_id = 0
            
            response = self.api_client.update_start_playing(options.playlist_type, options.export_source, media_id, options.playlist_id, options.transmission_id) 

            print response

        if int(options.playlist_type) == 6:
            print 'seems to be a couchcast'
            
            try:
                media_id = int(options.media_id)
            except Exception, e:
                media_id = 0
            
            response = self.api_client.update_start_playing(options.playlist_type, options.export_source, media_id, options.playlist_id, options.transmission_id) 

            print response




        
        
        sys.exit()  

        
        
            
    
    def start_playing_legacy(self, options):
        logger = logging.getLogger("start_playing")

        tnow = time.localtime(time.time())
        

        print '#################################################'
        print '# calling obp to tell about what\'s playing     #'
        print '#################################################'

        path = options
        
        print
        print path
        print 
                
        if 'pl_id' in path:
            print 'seems to be a playlist'
            type = 'playlist'
            id = path[5:] 
                
        elif 'text' in path:
            print 'seems to be a playlist'
            type = 'text'
            id = path[4:]
            print id
        
        else:
            print 'seems to be a single track (media)'
            type = 'media'
            try:
                file = path.split("/")[-1:][0]
                if file.find('_cue_') > 0:
                    id = file.split("_cue_")[0]
                else:
                    id = file.split(".")[-2:][0]
                
            except Exception, e:
                #print e
                id = False
            
        try:
            id = id
        except Exception, e:
            #print e
            id = False
        
        print 
        print type + " id: ", 
        print id            
          
        
        response = self.api_client.update_start_playing(type, id, self.export_source, path) 
        
        print 'DONE' 
        
        try:
            txt = response['txt']
            print '#######################################'
            print txt
            print '#######################################'
            #self.dls_client.set_txt(txt)

        except Exception, e:
            print e
        
         

     
            

if __name__ == '__main__':
  
    print
    print '#########################################'
    print '#           *** pypo  ***               #'
    print '#     pypo notification gateway         #'
    print '#########################################'
    print
    
    # initialize
    g = Global()
    g.selfcheck()
    n = Notify()


run = True
while run == True:
    
    logger = logging.getLogger("pypo notify")
            
    if options.playing:
        try: n.start_playing(options)
        except Exception, e:
            print e
        sys.exit()  
        

            

    sys.exit()
