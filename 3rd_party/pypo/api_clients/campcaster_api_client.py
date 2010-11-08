#!/usr/bin/env python
# -*- coding: utf-8 -*-

import sys
import time
import urllib
import logging
from util import json
import os
        
class CampcasterApiClient():

    def __init__(self, config):
        self.config = config
        #self.api_auth = api_auth

    def check_version(self):
        version = self.get_campcaster_version()
        if (version == 0):
            print 'Unable to get Campcaster version number.'
            print
            sys.exit()     
        elif (version[0:4] != "1.6."): 
            print 'Campcaster version: ' + str(version)
            print 'pypo not compatible with this version of Campcaster'
            print
            sys.exit()     
        else:
            print 'Campcaster version: ' + str(version)
            print 'pypo is compatible with this version of OBP'
            print

	def get_campcaster_version(self):
		logger = logging.getLogger("ApiClient.get_campcaster_version")
		url = self.config["base_url"] + self.config["api_base"] + self.config["version_url"]
		url = url.replace("%%api_key%%", self.config["api_key"])

		try:
			logger.debug("Trying to contact %s", url)
			response = urllib.urlopen(url)
			data = response.read()
			response_json = json.read(data)
			version = response_json['version']
			logger.debug("Campcaster Version %s detected", version)    
		except Exception, e:
			try:
				if e[1] == 401:
					print '#####################################'
					print '# YOUR API KEY SEEMS TO BE INVALID:'
					print '# ' + self.config["api_key"]
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

			version = 0
			logger.error("Unable to detect Campcaster Version - %s", e)

        return version


    def update_scheduled_item(self, item_id, value):
        logger = logging.getLogger("ApiClient.update_scheduled_item")
        url = self.api_url + 'schedule/schedule.php?item_id=' + str(item_id) + '&played=' + str(value)
        
        try:
            response = urllib.urlopen(url, self.api_auth)
            response = json.read(response.read())
            logger.info("API-Status %s", response['status'])
            logger.info("API-Message %s", response['message'])
    
        except Exception, e:
            print e
            api_status = False
            logger.critical("Unable to connect - %s", e)
    
        return response


    def update_start_playing(self, playlist_type, export_source, media_id, playlist_id, transmission_id):
        logger = logging.getLogger("ApiClient.update_scheduled_item")
    
        url = self.api_url + 'schedule/update_start_playing.php' \
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
            logger.critical("Unable to connect - %s", e)
    
        return response
    
    
    def generate_range_dp(self):
        logger = logging.getLogger("ApiClient.generate_range_dp")
    
        url = self.api_url + 'schedule/generate_range_dp.php'
        
        try:
            response = urllib.urlopen(url, self.api_auth)
            response = json.read(response.read())
            logger.debug("Trying to contact %s", url)
            logger.info("API-Status %s", response['status'])
            logger.info("API-Message %s", response['message'])
    
        except Exception, e:
            print e
            api_status = False
            logger.critical("Unable to handle the request - %s", e)
            
        return response
    