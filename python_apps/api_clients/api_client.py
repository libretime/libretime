#!/usr/bin/env python
# -*- coding: utf-8 -*-

###############################################################################
# This file holds the implementations for all the API clients.
#
# If you want to develop a new client, here are some suggestions:
# Get the fetch methods working first, then the push, then the liquidsoap notifier.
# You will probably want to create a script on your server side to automatically
# schedule a playlist one minute from the current time.
###############################################################################

import sys
import time
import urllib
import urllib2
import logging
import json
import os
from urlparse import urlparse
import base64
from configobj import ConfigObj

AIRTIME_VERSION = "1.9.0-devel"

def api_client_factory(config):
    logger = logging.getLogger()
    if config["api_client"] == "airtime":
        return AirTimeApiClient()
    elif config["api_client"] == "obp":
        return ObpApiClient()
    else:
        logger.info('API Client "'+config["api_client"]+'" not supported.  Please check your config file.\n')
        sys.exit()

class ApiClientInterface:

    # Implementation: optional
    #
    # Called from: beginning of all scripts
    #
    # Should exit the program if this version of pypo is not compatible with
    # 3rd party software.
    def is_server_compatible(self, verbose = True):
        pass

    # Implementation: Required
    #
    # Called from: fetch loop
    #
    # This is the main method you need to implement when creating a new API client.
    # start and end are for testing purposes.
    # start and end are strings in the format YYYY-DD-MM-hh-mm-ss
    def get_schedule(self, start=None, end=None):
        return 0, []

    # Implementation: Required
    #
    # Called from: fetch loop
    #
    # This downloads the media from the server.
    def get_media(self, src, dst):
        pass

    # Implementation: optional
    #
    # Called from: push loop
    #
    # Tell server that the scheduled *playlist* has started.
    def notify_scheduled_item_start_playing(self, pkey, schedule):
        pass

    # Implementation: optional
    # You dont actually have to implement this function for the liquidsoap playout to work.
    #
    # Called from: pypo_notify.py
    #
    # This is a callback from liquidsoap, we use this to notify about the
    # currently playing *song*.  We get passed a JSON string which we handed to
    # liquidsoap in get_liquidsoap_data().
    def notify_media_item_start_playing(self, data, media_id):
        pass

    # Implementation: optional
    # You dont actually have to implement this function for the liquidsoap playout to work.
    def generate_range_dp(self):
        pass

    # Implementation: optional
    #
    # Called from: push loop
    #
    # Return a dict of extra info you want to pass to liquidsoap
    # You will be able to use this data in update_start_playing
    def get_liquidsoap_data(self, pkey, schedule):
        pass

    def get_shows_to_record(self):
        pass

    def upload_recorded_show(self):
        pass

    def check_media_status(self, md5):
        pass

    def update_media_metadata(self, md):
        pass    
        
    def list_all_db_files(self, dir_id):
        pass    
        
    def list_all_watched_dirs(self):
        pass
    
    def add_watched_dir(self):
        pass
    
    def remove_watched_dir(self):
        pass
    
    def set_storage_dir(self):
        pass

    # Put here whatever tests you want to run to make sure your API is working
    def test(self):
        pass


    #def get_media_type(self, playlist):
    #   nil

################################################################################
# Airtime API Client
################################################################################

class AirTimeApiClient(ApiClientInterface):

    def __init__(self):
        # loading config file
        try:
            self.config = ConfigObj('/etc/airtime/api_client.cfg')
        except Exception, e:
            logger = logging.getLogger()
            logger.error('Error loading config file: %s', e)
            sys.exit(1)

    def __get_airtime_version(self, verbose = True):
        logger = logging.getLogger()
        url = "http://%s:%s/%s/%s" % (self.config["base_url"], str(self.config["base_port"]), self.config["api_base"], self.config["version_url"])
        logger.debug("Trying to contact %s", url)
        url = url.replace("%%api_key%%", self.config["api_key"])

        version = -1
        response = None
        try:
            response = urllib.urlopen(url)
            data = response.read()
            logger.debug("Data: %s", data)
            response_json = json.loads(data)
            version = response_json['version']
            logger.debug("Airtime Version %s detected", version)
        except IOError, e:
            logger.error("Unable to detect Airtime Version - %s, Response: %s", e, data)
            if e[1] == 401:
                if (verbose):
                    logger.info('#####################################')
                    logger.info('# YOUR API KEY SEEMS TO BE INVALID:')
                    logger.info('# ' + self.config["api_key"])
                    logger.info('#####################################')

            if e[1] == 404:
                if (verbose):
                    logger.info('#####################################')
                    logger.info('# Unable to contact the Airtime-API')
                    logger.info('# ' + url)
                    logger.info('#####################################')
            return -1
        except Exception, e:
            logger.error("Unable to detect Airtime Version - %s, Response: %s", e, data)
            return -1

        return version

    def test(self):
        logger = logging.getLogger()
        status, items = self.get_schedule('2010-01-01-00-00-00', '2011-01-01-00-00-00')
        schedule = items["playlists"]
        logger.debug("Number of playlists found: %s", str(len(schedule)))
        count = 1
        for pkey in sorted(schedule.iterkeys()):
            logger.debug("Playlist #%s",str(count))
            count+=1
            playlist = schedule[pkey]
            for item in playlist["medias"]:
                filename = urlparse(item["uri"])
                filename = filename.query[5:]
                self.get_media(item["uri"], filename)


    def is_server_compatible(self, verbose = True):
        logger = logging.getLogger()
        version = self.__get_airtime_version(verbose)
        if (version == -1):
            if (verbose):
                logger.info('Unable to get Airtime version number.\n')
            return False
        elif (version[0:3] != AIRTIME_VERSION[0:3]):
            if (verbose):
                logger.info('Airtime version found: ' + str(version))
                logger.info('pypo is at version ' +AIRTIME_VERSION+' and is not compatible with this version of Airtime.\n')
            return False
        else:
            if (verbose):
                logger.info('Airtime version: ' + str(version))
                logger.info('pypo is at version ' +AIRTIME_VERSION+' and is compatible with this version of Airtime.')
            return True


    def get_schedule(self, start=None, end=None):
        logger = logging.getLogger()

        # Construct the URL
        export_url = "http://%s:%s/%s/%s" % (self.config["base_url"], str(self.config["base_port"]), self.config["api_base"], self.config["export_url"])

        logger.info("Fetching schedule from %s", export_url)
        export_url = export_url.replace('%%api_key%%', self.config["api_key"])

        response = ""
        status = 0
        try:
            response_json = urllib.urlopen(export_url).read()
            response = json.loads(response_json)
            status = response['check']
        except Exception, e:
            logger.error(e)

        return status, response


    def get_media(self, uri, dst):
        logger = logging.getLogger()

        try:
            src = uri + "/api_key/%%api_key%%"
            logger.info("try to download from %s to %s", src, dst)
            src = src.replace("%%api_key%%", self.config["api_key"])
            # check if file exists already before downloading again
            filename, headers = urllib.urlretrieve(src, dst)
            logger.info(headers)
        except Exception, e:
            logger.error("%s", e)


    """
    Tell server that the scheduled *playlist* has started.
    """
    def notify_scheduled_item_start_playing(self, pkey, schedule):
        logger = logging.getLogger()
        playlist = schedule[pkey]
        schedule_id = playlist["schedule_id"]
        url = "http://%s:%s/%s/%s" % (self.config["base_url"], str(self.config["base_port"]), self.config["api_base"], self.config["update_item_url"])

        url = url.replace("%%schedule_id%%", str(schedule_id))
        logger.debug(url)
        url = url.replace("%%api_key%%", self.config["api_key"])

        try:
            response = urllib.urlopen(url)
            response = json.loads(response.read())
            logger.info("API-Status %s", response['status'])
            logger.info("API-Message %s", response['message'])

        except Exception, e:
            logger.error("Unable to connect - %s", e)

        return response


    """
    This is a callback from liquidsoap, we use this to notify about the
    currently playing *song*.  We get passed a JSON string which we handed to
    liquidsoap in get_liquidsoap_data().
    """
    def notify_media_item_start_playing(self, data, media_id):
        logger = logging.getLogger()
        response = ''
        try:
            schedule_id = data
            url = "http://%s:%s/%s/%s" % (self.config["base_url"], str(self.config["base_port"]), self.config["api_base"], self.config["update_start_playing_url"])
            url = url.replace("%%media_id%%", str(media_id))
            url = url.replace("%%schedule_id%%", str(schedule_id))
            logger.debug(url)
            url = url.replace("%%api_key%%", self.config["api_key"])
            response = urllib.urlopen(url)
            response = json.loads(response.read())
            logger.info("API-Status %s", response['status'])
            logger.info("API-Message %s", response['message'])

        except Exception, e:
            logger.error("Exception: %s", e)

        return response

    def get_liquidsoap_data(self, pkey, schedule):
        logger = logging.getLogger()
        playlist = schedule[pkey]
        data = dict()
        try:
            data["schedule_id"] = playlist['id']
        except Exception, e:
            data["schedule_id"] = 0
        return data

    def get_shows_to_record(self):
        logger = logging.getLogger()
        response = None
        try:
            url = "http://%s:%s/%s/%s" % (self.config["base_url"], str(self.config["base_port"]), self.config["api_base"], self.config["show_schedule_url"])
            logger.debug(url)
            url = url.replace("%%api_key%%", self.config["api_key"])

            response = urllib.urlopen(url)
            response = json.loads(response.read())
            logger.info("shows %s", response)

        except Exception, e:
            logger.error("Exception: %s", e)
            response = None

        return response

    def upload_recorded_show(self, data, headers):
        logger = logging.getLogger()
        response = ''

        retries = int(self.config["upload_retries"])
        retries_wait = int(self.config["upload_wait"])

        url = "http://%s:%s/%s/%s" % (self.config["base_url"], str(self.config["base_port"]), self.config["api_base"], self.config["upload_file_url"])

        logger.debug(url)
        url = url.replace("%%api_key%%", self.config["api_key"])
        logger.debug(url)

        for i in range(0, retries):
            logger.debug("Upload attempt: %s", i+1)

            try:
                request = urllib2.Request(url, data, headers)
                response = urllib2.urlopen(request).read().strip()

                logger.info("uploaded show result %s", response)
                break

            except urllib2.HTTPError, e:
                logger.error("Http error code: %s", e.code)
            except urllib2.URLError, e:
                logger.error("Server is down: %s", e.args)
            except Exception, e:
                logger.error("Exception: %s", e)

            #wait some time before next retry
            time.sleep(retries_wait)

        return response

    def setup_media_monitor(self):
        logger = logging.getLogger()

        response = None
        try:
            url = "http://%s:%s/%s/%s" % (self.config["base_url"], str(self.config["base_port"]), self.config["api_base"], self.config["media_setup_url"])
            url = url.replace("%%api_key%%", self.config["api_key"])

            response = urllib.urlopen(url)
            response = json.loads(response.read())
            logger.info("Connected to Airtime Server. Json Media Storage Dir: %s", response)
        except IOError:
            #this should be a common exception when media-monitor daemon
            #has started before apache on bootup and apache isn't accepting
            #connections yet.
            response = None
        except Exception, e:
            response = None
            logger.error("Exception: %s", e)

        return response

    def update_media_metadata(self, md, mode, is_record=False):
        logger = logging.getLogger()
        response = None
        try:

            start = time.time()

            url = "http://%s:%s/%s/%s" % (self.config["base_url"], str(self.config["base_port"]), self.config["api_base"], self.config["update_media_url"])
            url = url.replace("%%api_key%%", self.config["api_key"])
            url = url.replace("%%mode%%", mode)

            data = urllib.urlencode(md)
            req = urllib2.Request(url, data)

            response = urllib2.urlopen(req).read()
            logger.info("update media %s", response)
            response = json.loads(response)

            elapsed = (time.time() - start)
            logger.info("time taken to get response %s", elapsed)

            if(is_record):
                url = "http://%s:%s/%s/%s" % (self.config["base_url"], str(self.config["base_port"]), self.config["api_base"], self.config["upload_recorded"])
                url = url.replace("%%fileid%%", str(response[u'id']))
                url = url.replace("%%showinstanceid%%", str(md['MDATA_KEY_TRACKNUMBER']))
                logger.debug(url)
                url = url.replace("%%api_key%%", self.config["api_key"])

                req = urllib2.Request(url)
                response = urllib2.urlopen(req).read()
                response = json.loads(response)
                logger.info("associate recorded %s", response)


        except Exception, e:
            response = None
            logger.error("Exception: %s", e)

        return response
        
    #returns a list of all db files for a given directory in JSON format:
    #{"files":["path/to/file1", "path/to/file2"]}
    #Note that these are relative paths to the given directory. The full 
    #path is not returned.
    def list_all_db_files(self, dir_id):
        logger = logging.getLogger()
        try:
            url = "http://%s:%s/%s/%s" % (self.config["base_url"], str(self.config["base_port"]), self.config["api_base"], self.config["list_all_db_files"])
            
            url = url.replace("%%api_key%%", self.config["api_key"])
            url = url.replace("%%dir_id%%", dir_id)
            
            req = urllib2.Request(url)
            response = urllib2.urlopen(req).read()
            response = json.loads(response)
        except Exception, e:
            response = None
            logger.error("Exception: %s", e)
            
        return response
        
    def list_all_watched_dirs(self):
        logger = logging.getLogger()
        try:
            url = "http://%s:%s/%s/%s" % (self.config["base_url"], str(self.config["base_port"]), self.config["api_base"], self.config["list_all_watched_dirs"])
            
            url = url.replace("%%api_key%%", self.config["api_key"])
            
            req = urllib2.Request(url)
            response = urllib2.urlopen(req).read()
            response = json.loads(response)
        except Exception, e:
            response = None
            logger.error("Exception: %s", e)
            
        return response
    
    def add_watched_dir(self, path):
        logger = logging.getLogger()
        try:
            url = "http://%s:%s/%s/%s" % (self.config["base_url"], str(self.config["base_port"]), self.config["api_base"], self.config["add_watched_dir"])
            
            url = url.replace("%%api_key%%", self.config["api_key"])
            url = url.replace("%%path%%", base64.b64encode(path))
            
            req = urllib2.Request(url)
            response = urllib2.urlopen(req).read()
            response = json.loads(response)
        except Exception, e:
            response = None
            logger.error("Exception: %s", e)
            
        return response
    
    def remove_watched_dir(self, path):
        logger = logging.getLogger()
        try:
            url = "http://%s:%s/%s/%s" % (self.config["base_url"], str(self.config["base_port"]), self.config["api_base"], self.config["remove_watched_dir"])
            
            url = url.replace("%%api_key%%", self.config["api_key"])
            url = url.replace("%%path%%", base64.b64encode(path))
            
            req = urllib2.Request(url)
            response = urllib2.urlopen(req).read()
            response = json.loads(response)
        except Exception, e:
            response = None
            logger.error("Exception: %s", e)
            
        return response
    
    def set_storage_dir(self, path):
        logger = logging.getLogger()
        try:
            url = "http://%s:%s/%s/%s" % (self.config["base_url"], str(self.config["base_port"]), self.config["api_base"], self.config["set_storage_dir"])
            
            url = url.replace("%%api_key%%", self.config["api_key"])
            url = url.replace("%%path%%", base64.b64encode(path))
            
            req = urllib2.Request(url)
            response = urllib2.urlopen(req).read()
            response = json.loads(response)
        except Exception, e:
            response = None
            logger.error("Exception: %s", e)
            
        return response


################################################################################
# OpenBroadcast API Client
################################################################################
# Also check out the php counterpart that handles the api requests:
# https://lab.digris.ch/svn/elgg/trunk/unstable/mod/medialibrary/application/controllers/api/pypo.php

OBP_MIN_VERSION = 2010100101 # required obp version

class ObpApiClient():

    def __init__(self, config):
        self.config = config
        self.api_auth = urllib.urlencode({'api_key': self.config["api_key"]})

    def is_server_compatible(self, verbose = True):
        logger = logging.getLogger()
        obp_version = self.get_obp_version()

        if obp_version == 0:
            if (verbose):
                logger.error('Unable to get OBP version. Is OBP up and running?\n')
            return False
        elif obp_version < OBP_MIN_VERSION:
            if (verbose):
                logger.warn('OBP version: ' + str(obp_version))
                logger.warn('OBP min-version: ' + str(OBP_MIN_VERSION))
                logger.warn('pypo not compatible with this version of OBP\n')
            return False
        else:
            if (verbose):
                logger.warn('OBP API: ' + str(API_BASE))
                logger.warn('OBP version: ' + str(obp_version))
                logger.warn('OBP min-version: ' + str(OBP_MIN_VERSION))
                logger.warn('pypo is compatible with this version of OBP\n')
            return True


    def get_obp_version(self):
        logger = logging.getLogger()

        # lookup OBP version
        #url = self.config["base_url"] + self.config["api_base"]+ self.config["version_url"]
        url = "http://%s:%s/%s/%s" % (self.config["base_url"], str(self.config["base_port"]), self.config["api_base"], self.config["version_url"])


        try:
            logger.debug("Trying to contact %s", url)
            response = urllib.urlopen(url, self.api_auth)
            response_json = json.loads(response.read())
            obp_version = int(response_json['version'])
            logger.debug("OBP Version %s detected", obp_version)

        except Exception, e:
            try:
                if e[1] == 401:
                    logger.error('#####################################')
                    logger.error('# YOUR API KEY SEEMS TO BE INVALID')
                    logger.error('# ' + self.config["api_auth"])
                    logger.error('#####################################')
                    sys.exit()

            except Exception, e:
                pass

            try:
                if e[1] == 404:
                    logger.error('#####################################')
                    logger.error('# Unable to contact the OBP-API')
                    logger.error('# ' + url)
                    logger.error('#####################################')
                    sys.exit()

            except Exception, e:
                pass

            obp_version = 0
            logger.error("Unable to detect OBP Version - %s", e)

        return obp_version


    def get_schedule(self, start=None, end=None):
        logger = logging.getLogger()

        """
        calculate start/end time range (format: YYYY-DD-MM-hh-mm-ss,YYYY-DD-MM-hh-mm-ss)
        (seconds are ignored, just here for consistency)
        """
        tnow = time.localtime(time.time())
        if (not start):
            tstart = time.localtime(time.time() - 3600 * int(self.config["cache_for"]))
            start = "%04d-%02d-%02d-%02d-%02d" % (tstart[0], tstart[1], tstart[2], tstart[3], tstart[4])

        if (not end):
            tend = time.localtime(time.time() + 3600 * int(self.config["prepare_ahead"]))
            end = "%04d-%02d-%02d-%02d-%02d" % (tend[0], tend[1], tend[2], tend[3], tend[4])

        range = {}
        range['start'] = start
        range['end'] = end

        # Construct the URL
        #export_url = self.config["base_url"] + self.config["api_base"] + self.config["export_url"]
        export_url = "http://%s:%s/%s/%s" % (self.config["base_url"], str(self.config["base_port"]), self.config["api_base"], self.config["export_url"])

        # Insert the start and end times into the URL
        export_url = export_url.replace('%%api_key%%', self.config["api_key"])
        export_url = export_url.replace('%%from%%', range['start'])
        export_url = export_url.replace('%%to%%', range['end'])
        logger.info("export from %s", export_url)

        response = ""
        status = 0
        try:
            response_json = urllib.urlopen(export_url).read()
            logger.debug("%s", response_json)
            response = json.loads(response_json)
            logger.info("export status %s", response['check'])
            status = response['check']
        except Exception, e:
            logger.error(e)

        return status, response


    def get_media(self, src, dest):
        try:
            logger.info('** urllib auth with: ' + self.api_auth)
            urllib.urlretrieve(src, dst, False, self.api_auth)
            logger.info("downloaded %s to %s", src, dst)
        except Exception, e:
            logger.error("%s", e)


    """
    Tell server that the scheduled *playlist* has started.
    """
    def notify_scheduled_item_start_playing(self, pkey, schedule):
    #def update_scheduled_item(self, item_id, value):
        logger = logging.getLogger()
        #url = self.config["base_url"] + self.config["api_base"] + self.config["update_item_url"]
        url = "http://%s:%s/%s/%s" % (self.config["base_url"], str(self.config["base_port"]), self.config["api_base"], self.config["update_item_url"])
        url = url.replace("%%item_id%%", str(schedule[pkey]["id"]))
        url = url.replace("%%played%%", "1")

        try:
            response = urllib.urlopen(url, self.api_auth)
            response = json.loads(response.read())
            logger.info("API-Status %s", response['status'])
            logger.info("API-Message %s", response['message'])

        except Exception, e:
            api_status = False
            logger.error("Unable to connect to the OBP API - %s", e)

        return response

    """
    This is a callback from liquidsoap, we use this to notify about the
    currently playing *song*.  We get passed a JSON string which we handed to
    liquidsoap in get_liquidsoap_data().
    """
    def notify_media_item_start_playing(self, data, media_id):
#   def update_start_playing(self, playlist_type, export_source, media_id, playlist_id, transmission_id):
        logger = logging.getLogger()
        playlist_type = data["playlist_type"]
        export_source = data["export_source"]
        playlist_id = data["playlist_id"]
        transmission_id = data["transmission_id"]

        #url = self.config["base_url"] + self.config["api_base"] + self.config["update_start_playing_url"]
        url = "http://%s:%s/%s/%s" % (self.config["base_url"], str(self.config["base_port"]), self.config["api_base"], self.config["update_start_playing_url"])
        url = url.replace("%%playlist_type%%", str(playlist_type))
        url = url.replace("%%export_source%%", str(export_source))
        url = url.replace("%%media_id%%", str(media_id))
        url = url.replace("%%playlist_id%%", str(playlist_id))
        url = url.replace("%%transmission_id%%", str(transmission_id))
        logger.info(url)

        try:
            response = urllib.urlopen(url, self.api_auth)
            response = json.loads(response.read())
            logger.info("API-Status %s", response['status'])
            logger.info("API-Message %s", response['message'])
            logger.info("TXT %s", response['str_dls'])

        except Exception, e:
            api_status = False
            logger.error("Unable to connect to the OBP API - %s", e)

        return response


    def generate_range_dp(self):
        logger = logging.getLogger()

        #url = self.config["base_url"] + self.config["api_base"] + self.config["generate_range_url"]
        url = "http://%s:%s/%s/%s" % (self.config["base_url"], str(self.config["base_port"]), self.config["api_base"], self.config["generate_range_url"])

        try:
            response = urllib.urlopen(url, self.api_auth)
            response = json.loads(response.read())
            logger.debug("Trying to contact %s", url)
            logger.info("API-Status %s", response['status'])
            logger.info("API-Message %s", response['message'])

        except Exception, e:
            api_status = False
            logger.error("Unable to handle the OBP API request - %s", e)

        return response

    def get_liquidsoap_data(self, pkey, schedule):
        playlist = schedule[pkey]
        data = dict()
        #data["ptype"] = playlist['subtype']
        try:
            data["user_id"] = playlist['user_id']
            data["playlist_id"] = playlist['id']
            data["transmission_id"] = playlist['schedule_id']
        except Exception, e:
            data["playlist_id"] = 0
            data["user_id"] = 0
            data["transmission_id"] = 0
        data = json.dumps(data)
        return data

