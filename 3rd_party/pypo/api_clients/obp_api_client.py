#!/usr/bin/env python
# -*- coding: utf-8 -*-

# author Jonas Ohrstrom <jonas@digris.ch>

import sys
import time
import urllib
import logging
from util import json
import os

OBP_MIN_VERSION = 2010100101 # required obp version
        
class ObpApiClient():

    def __init__(self, config):
		self.config = config
        #self.api_url = api_url
        #self.api_auth = api_auth

    def check_version(self):
        obp_version = self.get_obp_version()
        
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
            print 'OBP API: ' + str(API_BASE)
            print 'OBP version: ' + str(obp_version)
            print 'OBP min-version: ' + str(OBP_MIN_VERSION)
            print 'pypo is compatible with this version of OBP'
            print
	
	
    def get_obp_version(self):
        logger = logging.getLogger("ApiClient.get_obp_version")
        # lookup OBP version
        
        url = self.api_url + 'api/pypo/status/json'
        
        try:    
            logger.debug("Trying to contact %s", url)
            response = urllib.urlopen(url, self.api_auth)
            response_json = json.read(response.read())
            obp_version = int(response_json['version'])
            logger.debug("OBP Version %s detected", obp_version)

    
        except Exception, e:
            try:
                if e[1] == 401:
                    print '#####################################'
                    print '# YOUR API KEY SEEMS TO BE INVALID'
                    print '# ' + self.api_auth
                    print '#####################################'
                    sys.exit()
                    
            except Exception, e:
                pass
            
            try:
                if e[1] == 404:
                    print '#####################################'
                    print '# Unable to contact the OBP-API'
                    print '# ' + url
                    print '#####################################'
                    sys.exit()
                    
            except Exception, e:
                pass
            
            obp_version = 0
            logger.error("Unable to detect OBP Version - %s", e)

        
        return obp_version


    def update_scheduled_item(self, item_id, value):
        logger = logging.getLogger("ApiClient.update_shedueled_item")
        # lookup OBP version
        
        url = self.api_url + 'api/pypo/update_scheduled_item/' + str(item_id) + '?played=' + str(value)
        
        try:
            response = urllib.urlopen(url, self.api_auth)
            response = json.read(response.read())
            logger.info("API-Status %s", response['status'])
            logger.info("API-Message %s", response['message'])
    
        except Exception, e:
            print e
            api_status = False
            logger.critical("Unable to connect to the OBP API - %s", e)
    
        
        return response


    def update_start_playing(self, playlist_type, export_source, media_id, playlist_id, transmission_id):

        logger = logging.getLogger("ApiClient.update_shedueled_item")
    
        url = self.api_url + 'api/pypo/update_start_playing/' \
        + '?playlist_type=' + str(playlist_type) \
        + '&export_source=' + str(export_source) \
        + '&export_source=' + str(export_source) \
        + '&media_id=' + str(media_id) \
        + '&playlist_id=' + str(playlist_id) \
        + '&transmission_id=' + str(transmission_id)
        
        print url
        
        try:
            response = urllib.urlopen(url, self.api_auth)
            response = json.read(response.read())
            logger.info("API-Status %s", response['status'])
            logger.info("API-Message %s", response['message'])
            logger.info("TXT %s", response['str_dls'])
    
        except Exception, e:
            print e
            api_status = False
            logger.critical("Unable to connect to the OBP API - %s", e)
    
        
        return response
    
    
    def generate_range_dp(self):
        logger = logging.getLogger("ApiClient.generate_range_dp")
    
        url = self.api_url + 'api/pypo/generate_range_dp/'
        
        try:
            response = urllib.urlopen(url, self.api_auth)
            response = json.read(response.read())
            logger.debug("Trying to contact %s", url)
            logger.info("API-Status %s", response['status'])
            logger.info("API-Message %s", response['message'])
    
        except Exception, e:
            print e
            api_status = False
            logger.critical("Unable to handle the OBP API request - %s", e)
        
        
        return response
    
    
    
    
    