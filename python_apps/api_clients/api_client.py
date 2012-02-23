#!/usr/bin/env python2.6
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
import string
import hashlib

AIRTIME_VERSION = "2.1.0"

def api_client_factory(config, logger=None):
    if logger != None:
        temp_logger = logger
    else:
        temp_logger = logging.getLogger()
    if config["api_client"] == "airtime":
        return AirTimeApiClient(temp_logger)
    elif config["api_client"] == "obp":
        return ObpApiClient()
    else:
        temp_logger.info('API Client "'+config["api_client"]+'" not supported.  Please check your config file.\n')
        sys.exit()
        
def to_unicode(obj, encoding='utf-8'):
    if isinstance(obj, basestring):
        if not isinstance(obj, unicode):
            obj = unicode(obj, encoding)
    return obj

def encode_to(obj, encoding='utf-8'):
    if isinstance(obj, unicode):
        obj = obj.encode(encoding)
    return obj
    
def convert_dict_value_to_utf8(md):
    #list comprehension to convert all values of md to utf-8
    return dict([(item[0], encode_to(item[1], "utf-8")) for item in md.items()])

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

    def register_component(self):
        pass

    def notify_liquidsoap_error(self, error_msg, stream_id):
        pass
    
    def notify_liquidsoap_connection(self, stream_id):
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

    def __init__(self, logger=None):
        if logger != None:
            self.logger = logger
        else:
            self.logger = logging.getLogger()
        # loading config file
        try:
            self.config = ConfigObj('/etc/airtime/api_client.cfg')
        except Exception, e:
            self.logger.error('Error loading config file: %s', e)
            sys.exit(1)
            
    def get_response_from_server(self, url):
        logger = self.logger
        successful_response = False
        
        while not successful_response:
            try:
                response = urllib.urlopen(url)
                data = response.read()
                successful_response = True
            except IOError, e:
                logger.error('Error Authenticating with remote server: %s', e)
            except Exception, e:
                logger.error('Couldn\'t connect to remote server. Is it running?')
                logger.error("%s" % e)
            if not successful_response:
                time.sleep(5)
            
        return data
        

    def __get_airtime_version(self, verbose = True):
        logger = self.logger
        url = "http://%s:%s/%s/%s" % (self.config["base_url"], str(self.config["base_port"]), self.config["api_base"], self.config["version_url"])
        logger.debug("Trying to contact %s", url)
        url = url.replace("%%api_key%%", self.config["api_key"])

        version = -1
        response = None
        try:
            data = self.get_response_from_server(url)
            logger.debug("Data: %s", data)
            response_json = json.loads(data)
            version = response_json['version']
            logger.debug("Airtime Version %s detected", version)
        except Exception, e:
            logger.error("Unable to detect Airtime Version - %s", e)
            return -1

        return version

    def test(self):
        logger = self.logger
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
        logger = self.logger
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
        logger = self.logger

        # Construct the URL
        export_url = "http://%s:%s/%s/%s" % (self.config["base_url"], str(self.config["base_port"]), self.config["api_base"], self.config["export_url"])

        logger.info("Fetching schedule from %s", export_url)
        export_url = export_url.replace('%%api_key%%', self.config["api_key"])

        response = ""
        try:
            response_json = self.get_response_from_server(export_url)
            response = json.loads(response_json)
            success = True
        except Exception, e:
            logger.error(e)
            success = False

        return success, response


    def get_media(self, uri, dst):
        logger = self.logger

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
    This is a callback from liquidsoap, we use this to notify about the
    currently playing *song*.  We get passed a JSON string which we handed to
    liquidsoap in get_liquidsoap_data().
    """
    def notify_media_item_start_playing(self, data, media_id):
        logger = self.logger
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
        logger = self.logger
        playlist = schedule[pkey]
        data = dict()
        try:
            data["schedule_id"] = playlist['id']
        except Exception, e:
            data["schedule_id"] = 0
        return data

    def get_shows_to_record(self):
        logger = self.logger
        response = None
        try:
            url = "http://%s:%s/%s/%s" % (self.config["base_url"], str(self.config["base_port"]), self.config["api_base"], self.config["show_schedule_url"])
            logger.debug(url)
            url = url.replace("%%api_key%%", self.config["api_key"])
            response = self.get_response_from_server(url)

            response = json.loads(response)
            logger.info("shows %s", response)

        except Exception, e:
            logger.error("Exception: %s", e)
            response = None

        return response

    def upload_recorded_show(self, data, headers):
        logger = self.logger
        response = ''

        retries = int(self.config["upload_retries"])
        retries_wait = int(self.config["upload_wait"])

        url = "http://%s:%s/%s/%s" % (self.config["base_url"], str(self.config["base_port"]), self.config["api_base"], self.config["upload_file_url"])

        logger.debug(url)
        url = url.replace("%%api_key%%", self.config["api_key"])

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
    
    def check_live_stream_auth(self, username, password):
        #logger = logging.getLogger()
        response = ''
        try:
            url = "http://%s:%s/%s/%s" % (self.config["base_url"], str(self.config["base_port"]), self.config["api_base"], self.config["check_live_stream_auth"])
    
            url = url.replace("%%api_key%%", self.config["api_key"])
            url = url.replace("%%username%%", username)
            url = url.replace("%%password%%", password)
    
            req = urllib2.Request(url)
            response = urllib2.urlopen(req).read()
            response = json.loads(response)
        except Exception, e:
            import traceback
            top = traceback.format_exc()
            print "Exception: %s", e
            print "traceback: %s", top
            response = None
            
        return response

    def setup_media_monitor(self):
        logger = self.logger

        response = None
        try:
            url = "http://%s:%s/%s/%s" % (self.config["base_url"], str(self.config["base_port"]), self.config["api_base"], self.config["media_setup_url"])
            url = url.replace("%%api_key%%", self.config["api_key"])
            
            response = self.get_response_from_server(url)
            response = json.loads(response)
            logger.info("Connected to Airtime Server. Json Media Storage Dir: %s", response)
        except Exception, e:
            response = None
            logger.error("Exception: %s", e)

        return response

    def update_media_metadata(self, md, mode, is_record=False):
        logger = self.logger
        response = None
        try:

            start = time.time()

            url = "http://%s:%s/%s/%s" % (self.config["base_url"], str(self.config["base_port"]), self.config["api_base"], self.config["update_media_url"])
            url = url.replace("%%api_key%%", self.config["api_key"])
            url = url.replace("%%mode%%", mode)
           
            md = convert_dict_value_to_utf8(md)
            
            data = urllib.urlencode(md)
            req = urllib2.Request(url, data)

            response = urllib2.urlopen(req).read()
            logger.info("update media %s, filepath: %s, mode: %s", response, md['MDATA_KEY_FILEPATH'], mode)
            response = json.loads(response)

            elapsed = (time.time() - start)
            logger.info("time taken to get response %s", elapsed)

            if("error" not in response and is_record):
                url = "http://%s:%s/%s/%s" % (self.config["base_url"], str(self.config["base_port"]), self.config["api_base"], self.config["upload_recorded"])
                url = url.replace("%%fileid%%", str(response[u'id']))
                url = url.replace("%%showinstanceid%%", str(md['MDATA_KEY_TRACKNUMBER']))
                url = url.replace("%%api_key%%", self.config["api_key"])

                req = urllib2.Request(url)
                response = urllib2.urlopen(req).read()
                response = json.loads(response)
                logger.info("associate recorded %s", response)


        except Exception, e:
            response = None
            logger.error("Exception with file %s: %s", md, e)

        return response

    #returns a list of all db files for a given directory in JSON format:
    #{"files":["path/to/file1", "path/to/file2"]}
    #Note that these are relative paths to the given directory. The full
    #path is not returned.
    def list_all_db_files(self, dir_id):
        logger = self.logger
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
        logger = self.logger
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
        logger = self.logger
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
        logger = self.logger
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
        logger = self.logger
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
    
    def get_stream_setting(self):
        logger = self.logger
        try:
            url = "http://%s:%s/%s/%s" % (self.config["base_url"], str(self.config["base_port"]), self.config["api_base"], self.config["get_stream_setting"])
            
            url = url.replace("%%api_key%%", self.config["api_key"])
            req = urllib2.Request(url)
            response = urllib2.urlopen(req).read()
            response = json.loads(response)
        except Exception, e:
            response = None
            logger.error("Exception: %s", e)

        return response

    """
    Purpose of this method is to contact the server with a "Hey its me!" message.
    This will allow the server to register the component's (component = media-monitor, pypo etc.)
    ip address, and later use it to query monit via monit's http service, or download log files
    via a http server.
    """
    def register_component(self, component):
        logger = self.logger
        try:
            url = "http://%s:%s/%s/%s" % (self.config["base_url"], str(self.config["base_port"]), self.config["api_base"], self.config["register_component"])
            
            url = url.replace("%%api_key%%", self.config["api_key"])
            url = url.replace("%%component%%", component)
            req = urllib2.Request(url)
            response = urllib2.urlopen(req).read()
        except Exception, e:
            logger.error("Exception: %s", e)
    
    def notify_liquidsoap_status(self, msg, stream_id, time):
        logger = self.logger
        try:
            url = "http://%s:%s/%s/%s" % (self.config["base_url"], str(self.config["base_port"]), self.config["api_base"], self.config["update_liquidsoap_status"])
            
            url = url.replace("%%api_key%%", self.config["api_key"])
            msg = msg.replace('/', ' ')
            encoded_msg = urllib.quote(msg, '')
            url = url.replace("%%msg%%", encoded_msg)
            url = url.replace("%%stream_id%%", stream_id)
            url = url.replace("%%boot_time%%", time)
            
            req = urllib2.Request(url)
            response = urllib2.urlopen(req).read()
        except Exception, e:
            logger.error("Exception: %s", e)
    
    """
    This function updates status of mounted file system information on airtime
    """
    def update_file_system_mount(self, added_dir, removed_dir):
        logger = logging.getLogger()
        try:
            url = "http://%s:%s/%s/%s" % (self.config["base_url"], str(self.config["base_port"]), self.config["api_base"], self.config["update_fs_mount"])
            
            url = url.replace("%%api_key%%", self.config["api_key"])

            added_data_string = string.join(added_dir, ',')
            removed_data_string = string.join(removed_dir, ',')
            
            map = [("added_dir", added_data_string),("removed_dir",removed_data_string)]
            
            data = urllib.urlencode(map)
            
            req = urllib2.Request(url, data)
            response = urllib2.urlopen(req).read()
            logger.info("update file system mount: %s", response)
        except Exception, e:
            import traceback
            top = traceback.format_exc()
            logger.error('Exception: %s', e)
            logger.error("traceback: %s", top)
    
    """
        When watched dir is missing(unplugged or something) on boot up, this function will get called
        and will call appropriate function on Airtime.
    """
    def handle_watched_dir_missing(self, dir):
        logger = logging.getLogger()
        try:
            url = "http://%s:%s/%s/%s" % (self.config["base_url"], str(self.config["base_port"]), self.config["api_base"], self.config["handle_watched_dir_missing"])
            
            url = url.replace("%%api_key%%", self.config["api_key"])
            url = url.replace("%%dir%%", base64.b64encode(dir))
            
            req = urllib2.Request(url)
            response = urllib2.urlopen(req).read()
            logger.info("update file system mount: %s", response)
        except Exception, e:
            import traceback
            top = traceback.format_exc()
            logger.error('Exception: %s', e)
            logger.error("traceback: %s", top)
        
