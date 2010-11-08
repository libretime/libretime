#!/usr/bin/env python
# -*- coding: utf-8 -*-

import sys
import time
import urllib
import logging
from util import json
import os

def api_client_factory(config):
	if config["api_client"] == "campcaster":	
		return CampcasterApiClient(config)
	elif config["api_client"] == "obp":
		return ObpApiClient(config)
	else:
		print 'API Client "'+config["api_client"]+'" not supported.  Please check your config file.'
		print
		sys.exit()

class ApiClientInterface:

	# This is optional.
	# Should exit the program if this version of pypo is not compatible with
	# 3rd party software.
	def check_version(self):
		nil
		
	# This is the main method you need to implement when creating a new API client.
	def get_schedule(self):
		return 0, []
		
	# This is optional.
	# You dont actually have to implement this function for the liquidsoap playout to work.
	def update_scheduled_item(self, item_id, value):
		nil
	
	# This is optional.
	# You dont actually have to implement this function for the liquidsoap playout to work.
	def update_start_playing(self, playlist_type, export_source, media_id, playlist_id, transmission_id):
		nil
	
	def generate_range_dp(self):
		nil


class CampcasterApiClient(ApiClientInterface):

	def __init__(self, config):
		self.config = config
		#self.api_auth = api_auth

	def __get_campcaster_version(self):
		logger = logging.getLogger("CampcasterApiClient.get_campcaster_version")
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

	def check_version(self):
		version = self.__get_campcaster_version()
		if (version == 0):
			print 'Unable to get Campcaster version number.'
			print
			sys.exit()     
		elif (version[0:4] != "1.6."): 
			print 'Campcaster version: ' + str(version)
			print 'pypo not compatible with this version of Campcaster.'
			print
			sys.exit()     
		else:
			print 'Campcaster version: ' + str(version)
			print 'pypo is compatible with this version of Campcaster.'
			print

	def get_schedule(self):
		logger = logging.getLogger("CampcasterApiClient.get_schedule")
		
		"""
		calculate start/end time range (format: YYYY-DD-MM-hh-mm-ss,YYYY-DD-MM-hh-mm-ss)
		(seconds are ignored, just here for consistency)
		"""
		tnow = time.localtime(time.time())
		tstart = time.localtime(time.time() - 3600 * int(self.config["cache_for"]))
		tend = time.localtime(time.time() + 3600 * int(self.config["prepare_ahead"]))
	
		range = {}
		range['start'] = "%04d-%02d-%02d-%02d-%02d" % (tstart[0], tstart[1], tstart[2], tstart[3], tstart[4])
		range['end'] = "%04d-%02d-%02d-%02d-%02d" % (tend[0], tend[1], tend[2], tend[3], tend[4])
		
		# Construct the URL
		export_url = self.config["base_url"] + self.config["api_base"] + self.config["export_url"]
		
		# Insert the start and end times into the URL        
		export_url = export_url.replace('%%api_key%%', self.config["api_key"])
		export_url = export_url.replace('%%from%%', range['start'])
		export_url = export_url.replace('%%to%%', range['end'])
		logger.info("export from %s", export_url)
	
		response = ""
		status = 0
		try:
			response_json = urllib.urlopen(export_url).read()
			response = json.read(response_json)
			logger.info("export status %s", response['check'])
			status = response['check']
		except Exception, e:
			print e

		return status, response			

	def update_scheduled_item(self, item_id, value):
		logger = logging.getLogger("CampcasterApiClient.update_scheduled_item")
		
		url = self.config["base_url"] + self.config["api_base"] + self.config["update_item_url"]

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
		return
		logger = logging.getLogger("ApiClient.update_scheduled_item")
	
		url = self.api_url + 'schedule/update_start_playing.php' \
		+ '?playlist_type=' + str(playlist_type) \
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

    
################################################################################

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
