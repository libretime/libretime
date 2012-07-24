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
from urlparse import urlparse
import base64
from configobj import ConfigObj
import string
import traceback

AIRTIME_VERSION = "2.1.3"

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

################################################################################
# Airtime API Client
################################################################################

class AirtimeApiClient():

    def __init__(self, logger=None,config_path='/etc/airtime/api_client.cfg'):
        if logger is None:
            self.logger = logging
        else:
            self.logger = logger

        # loading config file
        try:
            self.config = ConfigObj(config_path)
        except Exception, e:
            self.logger.error('Error loading config file: %s', e)
            sys.exit(1)

    def get_response_from_server(self, url):
        logger = self.logger
        successful_response = False

        while not successful_response:
            try:
                response = urllib2.urlopen(url).read()
                successful_response = True
            except IOError, e:
                logger.error('Error Authenticating with remote server: %s', e)
            except Exception, e:
                logger.error('Couldn\'t connect to remote server. Is it running?')
                logger.error("%s" % e)

            if not successful_response:
                logger.error("Error connecting to server, waiting 5 seconds and trying again.")
                time.sleep(5)

        return response

    def get_response_into_file(self, url, block=True):
        """
        This function will query the server and download its response directly
        into a temporary file. This is useful in the situation where the response
        from the server can be huge and we don't want to store it into memory (potentially
        causing Python to use hundreds of MB's of memory). By writing into a file we can
        then open this file later, and read data a little bit at a time and be very mem
        efficient.

        The return value of this function is the path of the temporary file. Unless specified using
        block = False, this function will block until a successful HTTP 200 response is received.
        """

        logger = self.logger
        successful_response = False

        while not successful_response:
            try:
                path = urllib.urlretrieve(url)[0]
                successful_response = True
            except IOError, e:
                logger.error('Error Authenticating with remote server: %s', e)
                if not block:
                    raise
            except Exception, e:
                logger.error('Couldn\'t connect to remote server. Is it running?')
                logger.error("%s" % e)
                if not block:
                    raise

            if not successful_response:
                logger.error("Error connecting to server, waiting 5 seconds and trying again.")
                time.sleep(5)

        return path



    def __get_airtime_version(self):
        logger = self.logger
        url = "http://%s:%s/%s/%s" % (self.config["base_url"], str(self.config["base_port"]), self.config["api_base"], self.config["version_url"])
        logger.debug("Trying to contact %s", url)
        url = url.replace("%%api_key%%", self.config["api_key"])

        version = -1
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
        items = self.get_schedule()[1]
        schedule = items["playlists"]
        logger.debug("Number of playlists found: %s", str(len(schedule)))
        count = 1
        for pkey in sorted(schedule.iterkeys()):
            logger.debug("Playlist #%s", str(count))
            count += 1
            playlist = schedule[pkey]
            for item in playlist["medias"]:
                filename = urlparse(item["uri"])
                filename = filename.query[5:]
                self.get_media(item["uri"], filename)


    def is_server_compatible(self, verbose=True):
        logger = self.logger
        version = self.__get_airtime_version()
        if (version == -1):
            if (verbose):
                logger.info('Unable to get Airtime version number.\n')
            return False
        elif (version[0:3] != AIRTIME_VERSION[0:3]):
            if (verbose):
                logger.info('Airtime version found: ' + str(version))
                logger.info('pypo is at version ' + AIRTIME_VERSION + ' and is not compatible with this version of Airtime.\n')
            return False
        else:
            if (verbose):
                logger.info('Airtime version: ' + str(version))
                logger.info('pypo is at version ' + AIRTIME_VERSION + ' and is compatible with this version of Airtime.')
            return True


    def get_schedule(self):
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
            headers = urllib.urlretrieve(src, dst)[1]
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

            response = self.get_response_from_server(url)
            response = json.loads(response)
            logger.info("API-Status %s", response['status'])
            logger.info("API-Message %s", response['message'])

        except Exception, e:
            logger.error("Exception: %s", e)

        return response

    def get_liquidsoap_data(self, pkey, schedule):
        playlist = schedule[pkey]
        data = dict()
        try:
            data["schedule_id"] = playlist['id']
        except Exception:
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
            logger.debug("Upload attempt: %s", i + 1)

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

    def check_live_stream_auth(self, username, password, dj_type):
        """
        TODO: Why are we using print statements here? Possibly use logger that
        is directed to stdout. -MK
        """

        response = ''
        try:
            url = "http://%s:%s/%s/%s" % (self.config["base_url"], str(self.config["base_port"]), self.config["api_base"], self.config["check_live_stream_auth"])

            url = url.replace("%%api_key%%", self.config["api_key"])
            url = url.replace("%%username%%", username)
            url = url.replace("%%djtype%%", dj_type)
            url = url.replace("%%password%%", password)

            response = self.get_response_from_server(url)
            response = json.loads(response)
        except Exception, e:
            print "Exception: %s", e
            print "traceback: %s", traceback.format_exc()
            response = None

        return response

    def construct_url(self,config_action_key):
        """Constructs the base url for every request"""
        # TODO : Make other methods in this class use this this method.
        url = "http://%s:%s/%s/%s" % (self.config["base_url"], str(self.config["base_port"]), self.config["api_base"], self.config[config_action_key])
        url = url.replace("%%api_key%%", self.config["api_key"])
        return url

    def setup_media_monitor(self):
        logger = self.logger
        response = None
        try:
            url = self.construct_url("media_setup_url")
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
            url = self.construct_url("update_media_url")
            url = url.replace("%%mode%%", mode)

            self.logger.info("Requesting url %s" % url)

            md = convert_dict_value_to_utf8(md)

            data = urllib.urlencode(md)
            req = urllib2.Request(url, data)

            response = self.get_response_from_server(req)
            logger.info("update media %s, filepath: %s, mode: %s", response, md['MDATA_KEY_FILEPATH'], mode)
            self.logger.info("Received response:")
            self.logger.info(response)
            try: response = json.loads(response)
            except ValueError:
                logger.info("Could not parse json from response: '%s'" % response)

            if("error" not in response and is_record):
                url = "http://%s:%s/%s/%s" % (self.config["base_url"], str(self.config["base_port"]), self.config["api_base"], self.config["upload_recorded"])
                url = url.replace("%%fileid%%", str(response[u'id']))
                url = url.replace("%%showinstanceid%%", str(md['MDATA_KEY_TRACKNUMBER']))
                url = url.replace("%%api_key%%", self.config["api_key"])

                response = self.get_response_from_server(url)
                response = json.loads(response)
                logger.info("associate recorded %s", response)
        except Exception, e:
            response = None
            logger.error('Exception: %s', e)
            logger.error("traceback: %s", traceback.format_exc())

        return response

    def send_media_monitor_requests(self, action_list, dry=False):
        """
        Send a gang of media monitor events at a time. actions_list is a list of dictionaries
        where every dictionary is representing an action. Every action dict must contain a 'mode'
        key that says what kind of action it is and an optional 'is_record' key that says whether
        the show was recorded or not. The value of this key does not matter, only if it's present
        or not.
        """
        logger = self.logger
        try:
            url = self.construct_url('reload_metadata_group')
            # We are assuming that action_list is a list of dictionaries such
            # that every dictionary represents the metadata of a file along
            # with a special mode key that is the action to be executed by the
            # controller.
            valid_actions = []
            # We could get a list of valid_actions in a much shorter way using
            # filter but here we prefer a little more verbosity to help
            # debugging
            for action in action_list:
                if not 'mode' in action:
                    self.logger.debug("Warning: Sending a request element without a 'mode'")
                    self.logger.debug("Here is the the request: '%s'" % str(action) )
                else:
                    # We alias the value of is_record to true or false no
                    # matter what it is based on if it's absent in the action
                    if 'is_record' in action:
                        self.logger.debug("Sending a 'recorded' action")
                        action['is_record'] = 1
                    else: action['is_record'] = 0
                    valid_actions.append(action)
            # Note that we must prefix every key with: mdX where x is a number
            # Is there a way to format the next line a little better? The
            # parenthesis make the code almost unreadable
            md_list = dict((("md%d" % i), json.dumps(convert_dict_value_to_utf8(md))) \
                    for i,md in enumerate(valid_actions))
            # For testing we add the following "dry" parameter to tell the
            # controller not to actually do any changes
            if dry: md_list['dry'] = 1
            self.logger.info("Pumping out %d requests..." % len(valid_actions))
            data = urllib.urlencode(md_list)
            req = urllib2.Request(url, data)
            response = self.get_response_from_server(req)
            response = json.loads(response)
            return response
        except Exception, e:
            logger.error('Exception: %s', e)
            logger.error("traceback: %s", traceback.format_exc())
            raise

    #returns a list of all db files for a given directory in JSON format:
    #{"files":["path/to/file1", "path/to/file2"]}
    #Note that these are relative paths to the given directory. The full
    #path is not returned.
    def list_all_db_files(self, dir_id):
        logger = self.logger
        try:
            url = self.construct_url("list_all_db_files")
            url = url.replace("%%dir_id%%", dir_id)
            response = self.get_response_from_server(url)
            response = json.loads(response)
        except Exception, e:
            response = {}
            logger.error("Exception: %s", e)

        try:
            return response["files"]
        except KeyError:
            self.logger.error("Could not find index 'files' in dictionary: %s", str(response))
            return []

    def list_all_watched_dirs(self):
        # Does this include the stor directory as well?
        logger = self.logger
        try:
            url = "http://%s:%s/%s/%s" % (self.config["base_url"], str(self.config["base_port"]), self.config["api_base"], self.config["list_all_watched_dirs"])

            url = url.replace("%%api_key%%", self.config["api_key"])

            response = self.get_response_from_server(url)
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

            response = self.get_response_from_server(url)
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

            response = self.get_response_from_server(url)
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

            response = self.get_response_from_server(url)
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
            response = self.get_response_from_server(url)
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
            self.get_response_from_server(url)
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

            self.get_response_from_server(url)
        except Exception, e:
            logger.error("Exception: %s", e)

    def notify_source_status(self, sourcename, status):
        logger = self.logger
        try:
            url = "http://%s:%s/%s/%s" % (self.config["base_url"], str(self.config["base_port"]), self.config["api_base"], self.config["update_source_status"])

            url = url.replace("%%api_key%%", self.config["api_key"])
            url = url.replace("%%sourcename%%", sourcename)
            url = url.replace("%%status%%", status)

            self.get_response_from_server(url)
        except Exception, e:
            logger.error("Exception: %s", e)

    """
    This function updates status of mounted file system information on airtime
    """
    def update_file_system_mount(self, added_dir, removed_dir):
        logger = self.logger
        try:
            url = "http://%s:%s/%s/%s" % (self.config["base_url"], str(self.config["base_port"]), self.config["api_base"], self.config["update_fs_mount"])

            url = url.replace("%%api_key%%", self.config["api_key"])

            added_data_string = string.join(added_dir, ',')
            removed_data_string = string.join(removed_dir, ',')

            map = [("added_dir", added_data_string), ("removed_dir", removed_data_string)]

            data = urllib.urlencode(map)

            req = urllib2.Request(url, data)
            response = self.get_response_from_server(req)

            logger.info("update file system mount: %s", json.loads(response))
        except Exception, e:
            logger.error('Exception: %s', e)
            logger.error("traceback: %s", traceback.format_exc())

    """
        When watched dir is missing(unplugged or something) on boot up, this function will get called
        and will call appropriate function on Airtime.
    """
    def handle_watched_dir_missing(self, dir):
        logger = self.logger
        try:
            url = "http://%s:%s/%s/%s" % (self.config["base_url"], str(self.config["base_port"]), self.config["api_base"], self.config["handle_watched_dir_missing"])

            url = url.replace("%%api_key%%", self.config["api_key"])
            url = url.replace("%%dir%%", base64.b64encode(dir))

            response = self.get_response_from_server(url)
            logger.info("update file system mount: %s", json.loads(response))
        except Exception, e:
            logger.error('Exception: %s', e)
            logger.error("traceback: %s", traceback.format_exc())

    def get_bootstrap_info(self):
        """
        Retrive infomations needed on bootstrap time
        """
        logger = self.logger
        try:
            url = self.construct_url("get_bootstrap_info")
            response = self.get_response_from_server(url)
            response = json.loads(response)
            logger.info("Bootstrap info retrieved %s", response)
        except Exception, e:
            response = None
            logger.error('Exception: %s', e)
            logger.error("traceback: %s", traceback.format_exc())
        return response

    def get_files_without_replay_gain_value(self, dir_id):
        """
        Download a list of files that need to have their ReplayGain value calculated. This list
        of files is downloaded into a file and the path to this file is the return value.
        """

        #http://localhost/api/get-files-without-replay-gain/dir_id/1

        logger = self.logger
        try:
            url = "http://%(base_url)s:%(base_port)s/%(api_base)s/%(get_files_without_replay_gain)s/" % (self.config)
            url = url.replace("%%api_key%%", self.config["api_key"])
            url = url.replace("%%dir_id%%", dir_id)
            response = self.get_response_from_server(url)

            logger.info("update file system mount: %s", response)
            response = json.loads(response)
            #file_path = self.get_response_into_file(url)
        except Exception, e:
            response = None
            logger.error('Exception: %s', e)
            logger.error("traceback: %s", traceback.format_exc())

        return response

    def update_replay_gain_values(self, pairs):
        """
        'pairs' is a list of pairs in (x, y), where x is the file's database row id
        and y is the file's replay_gain value in dB
        """

        #http://localhost/api/update-replay-gain-value/
        try:
            url = "http://%(base_url)s:%(base_port)s/%(api_base)s/%(update_replay_gain_value)s/" % (self.config)
            url = url.replace("%%api_key%%", self.config["api_key"])
            data = urllib.urlencode({'data': json.dumps(pairs)})
            request = urllib2.Request(url, data)

            self.get_response_from_server(request)
        except Exception, e:
            self.logger.error("Exception: %s", e)
            raise
