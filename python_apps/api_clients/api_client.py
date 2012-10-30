###############################################################################
# This file holds the implementations for all the API clients.
#
# If you want to develop a new client, here are some suggestions: Get the fetch
# methods working first, then the push, then the liquidsoap notifier.  You will
# probably want to create a script on your server side to automatically
# schedule a playlist one minute from the current time.
###############################################################################
import sys
import time
import urllib
import urllib2
import logging
import json
import base64
from configobj import ConfigObj
import traceback

AIRTIME_VERSION = "2.2.0"


# TODO : Place these functions in some common module. Right now, media
# monitor uses the same functions and it would be better to reuse them
# instead of copy pasting them around

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

class UrlException(Exception): pass

class IncompleteUrl(UrlException):
    def __init__(self, url): self.url = url
    def __str__(self): return "Incomplete url: '%s'" % self.url

class UrlBadParam(UrlException):
    def __init__(self, url, param):
        self.url = url
        self.param = param
    def __str__(self):
        return "Bad param '%s' passed into url: '%s'" % (self.param, self.url)

class ApcUrl(object):
    """ A safe abstraction and testable for filling in parameters in
    api_client.cfg"""
    def __init__(self, base_url): self.base_url = base_url

    def params(self, **params):
        temp_url = self.base_url
        for k, v in params.iteritems():
            wrapped_param = "%%" + k + "%%"
            if wrapped_param in temp_url:
                temp_url = temp_url.replace(wrapped_param, str(v))
            else: raise UrlBadParam(self.base_url, k)
        return ApcUrl(temp_url)

    def url(self):
        if '%%' in self.base_url: raise IncompleteUrl(self.base_url)
        else: return self.base_url

class ApiRequest(object):
    def __init__(self, name, url):
        self.name = name
        self.url  = url
    def __call__(self,_post_data=None, **kwargs):
        # TODO : get rid of god damn urllib and replace everything with
        # grequests or requests at least
        final_url = self.url.params(**kwargs).url()
        if _post_data is not None: _post_data = urllib.urlencode(_post_data)
        req = urllib2.Request(final_url, _post_data)
        response  = urllib2.urlopen(req).read()
        return json.loads(response)

class RequestProvider(object):
    """ Creates the available ApiRequest instance that can be read from
    a config file """
    def __init__(self, cfg):
        self.config = cfg
        self.requests = {}
        self.url = ApcUrl("http://%s:%s/%s/%s/%s" \
            % (self.config["host"], str(self.config["base_port"]),
               self.config["base_dir"], self.config["api_base"],
               '%%action%%'))
        # Now we must discover the possible actions
        actions = dict( (k,v) for k,v in cfg.iteritems() if '%%api_key%%' in v)
        for action_name, action_value in actions.iteritems():
            new_url = self.url.params(action=action_value).params(
                api_key=self.config['api_key'])
            self.requests[action_name] = ApiRequest(action_name, new_url)

    def available_requests(self)    : return self.requests.keys()
    def __contains__(self, request) : return request in self.requests

    def __getattr__(self, attr):
        if attr in self: return self.requests[attr]
        else: return super(RequestProvider, self).__getattribute__(attr)


class AirtimeApiClient(object):

    # This is a little hacky fix so that I don't have to pass the config object
    # everywhere where AirtimeApiClient needs to be initialized
    default_config = None
    # the purpose of this custom constructor is to remember which config file
    # it was called with. So that after the initial call:
    # AirtimeApiClient.create_right_config('/path/to/config')
    # All subsequence calls to create_right_config will be with that config
    # file
    @staticmethod
    def create_right_config(log=None,config_path=None):
        if config_path: AirtimeApiClient.default_config = config_path
        elif (not AirtimeApiClient.default_config):
            raise ValueError("Cannot slip config_path attribute when it has \
                              never been passed yet")
        return AirtimeApiClient( logger=None,
                config_path=AirtimeApiClient.default_config )

    def __init__(self, logger=None,config_path='/etc/airtime/api_client.cfg'):
        if logger is None: self.logger = logging
        else: self.logger = logger

        # loading config file
        try:
            self.config = ConfigObj(config_path)
            self.services = RequestProvider(self.config)
        except Exception, e:
            self.logger.error('Error loading config file: %s', e)
            sys.exit(1)

    def get_response_from_server(self, url, attempts=-1):
        logger = self.logger
        successful_response = False

        while not successful_response:
            try:
                response = urllib2.urlopen(url).read()
                successful_response = True
            except IOError, e:
                logger.error('Error Authenticating with remote server: %s %s', e, url)
                if isinstance(url, urllib2.Request):
                    logger.debug(url.get_full_url())
                else:
                    logger.debug(url)
            except Exception, e:
                logger.error('Couldn\'t connect to remote server. Is it running?')
                logger.error("%s" % e)
                if isinstance(url, urllib2.Request):
                    logger.debug(url.get_full_url())
                else:
                    logger.debug(url)

            #If the user passed in a positive attempts number then that means
            #attempts will roll over 0 and we stop. If attempts was initially negative,
            #then we have unlimited attempts
            if attempts > 0:
                attempts = attempts - 1
                if attempts == 0:
                    successful_response = True

            if not successful_response:
                logger.error("Error connecting to server, waiting 5 seconds and trying again.")
                time.sleep(5)

        return response

    def __get_airtime_version(self):
        logger = self.logger
        url= self.construct_url("version_url")

        logger.debug("Trying to contact %s", url)

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
                logger.info('pypo is at version ' + AIRTIME_VERSION +
                    ' and is not compatible with this version of Airtime.\n')
            return False
        else:
            if (verbose):
                logger.info('Airtime version: ' + str(version))
                logger.info('pypo is at version ' + AIRTIME_VERSION + ' and is compatible with this version of Airtime.')
            return True


    def get_schedule(self):
        # TODO : properly refactor this routine
        logger = self.logger

        # Construct the URL
        export_url = self.construct_url("export_url")
        logger.info("Fetching schedule from %s", export_url)

        response = ""
        try:
            response_json = self.get_response_from_server(export_url)
            response = json.loads(response_json)
            success = True
        except Exception, e:
            logger.error(e)
            success = False

        return success, response

    def notify_liquidsoap_started(self):
        return self.services.notify_liquidsoap_started()

    """
    This is a callback from liquidsoap, we use this to notify about the
    currently playing *song*.  We get passed a JSON string which we handed to
    liquidsoap in get_liquidsoap_data().
    """
    def notify_media_item_start_playing(self, media_id):
        return self.services.update_start_playing_url(media_id=media_id)

    def get_liquidsoap_data(self, pkey, schedule):
        playlist = schedule[pkey]
        data = dict()
        try:
            data["schedule_id"] = playlist['id']
        except Exception:
            data["schedule_id"] = 0
        return data

    def get_shows_to_record(self):
        return self.services.show_schedule_url()

    def upload_recorded_show(self, data, headers):
        logger = self.logger
        response = ''

        retries = int(self.config["upload_retries"])
        retries_wait = int(self.config["upload_wait"])

        url = self.construct_url("upload_file_url")

        logger.debug(url)

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
        return self.services.check_live_stream_auth(
            username=username, password=password, dj_type=dj_type)

    def construct_url(self,config_action_key):
        """Constructs the base url for every request"""
        # TODO : Make other methods in this class use this this method.
        url = "http://%s:%s/%s/%s/%s" %  \
            (self.config["host"], str(self.config["base_port"]),
             self.config["base_dir"], self.config["api_base"],
             self.config[config_action_key])
        url = url.replace("%%api_key%%", self.config["api_key"])
        return url

    def setup_media_monitor(self):
        return self.services.media_setup_url()

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
                url = self.construct_url("upload_recorded")
                url = url.replace("%%fileid%%", str(response[u'id']))
                url = url.replace("%%showinstanceid%%", str(md['MDATA_KEY_TRACKNUMBER']))

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
        Send a gang of media monitor events at a time. actions_list is a
        list of dictionaries where every dictionary is representing an
        action. Every action dict must contain a 'mode' key that says
        what kind of action it is and an optional 'is_record' key that
        says whether the show was recorded or not. The value of this key
        does not matter, only if it's present or not.
        """
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
                self.logger.debug("Warning: Trying to send a request element without a 'mode'")
                self.logger.debug("Here is the the request: '%s'" % str(action) )
            else:
                # We alias the value of is_record to true or false no
                # matter what it is based on if it's absent in the action
                if 'is_record' not in action:
                    action['is_record'] = 0
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
        #response = self.get_response_from_server(req)
        #response = json.loads(response)
        response = self.services.reload_metadata_group(_post_data=md_list)
        return response

    #returns a list of all db files for a given directory in JSON format:
    #{"files":["path/to/file1", "path/to/file2"]}
    #Note that these are relative paths to the given directory. The full
    #path is not returned.
    def list_all_db_files(self, dir_id, all_files=True):
        logger = self.logger
        try:
            all_files = u"1" if all_files else u"0"
            response = self.services.list_all_db_files(dir_id=dir_id,
                                                       all=all_files)
        except Exception, e:
            response = {}
            logger.error("Exception: %s", e)
        try:
            return response["files"]
        except KeyError:
            self.logger.error("Could not find index 'files' in dictionary: %s",
                    str(response))
            return []

    def list_all_watched_dirs(self):
        return self.services.list_all_watched_dirs()

    def add_watched_dir(self, path):
        return self.services.add_watched_dir(path=base64.b64encode(path))

    def remove_watched_dir(self, path):
        return self.services.remove_watched_dir(path=base64.b64encode(path))

    def set_storage_dir(self, path):
        return self.services.set_storage_dir(path=base64.b64encode(path))

    def get_stream_setting(self):
        logger = self.logger
        try:
            url = self.construct_url("get_stream_setting")
            response = self.get_response_from_server(url)
            response = json.loads(response)
        except Exception, e:
            response = None
            logger.error("Exception: %s", e)

        return response

    """
    Purpose of this method is to contact the server with a "Hey its me!"
    message.  This will allow the server to register the component's (component
    = media-monitor, pypo etc.) ip address, and later use it to query monit via
    monit's http service, or download log files via a http server.
    """
    def register_component(self, component):
        return self.services.register_component(component=component)

    def notify_liquidsoap_status(self, msg, stream_id, time):
        logger = self.logger
        try:
            url = self.construct_url("update_liquidsoap_status")
            msg = msg.replace('/', ' ')
            encoded_msg = urllib.quote(msg, '')
            url = url.replace("%%msg%%", encoded_msg)
            url = url.replace("%%stream_id%%", stream_id)
            url = url.replace("%%boot_time%%", time)

            self.get_response_from_server(url, attempts = 5)
        except Exception, e:
            logger.error("Exception: %s", e)

    def notify_source_status(self, sourcename, status):
        try:
            logger = self.logger
            return self.services.update_source_status(sourcename=sourcename,
                                                      status=status)
            #self.get_response_from_server(url, attempts = 5)
        except Exception, e:
            logger.error("Exception: %s", e)

    def get_bootstrap_info(self):
        """ Retrive infomations needed on bootstrap time """
        return self.services.get_bootstrap_info()

    def get_files_without_replay_gain_value(self, dir_id):
        """
        Download a list of files that need to have their ReplayGain value
        calculated. This list of files is downloaded into a file and the path
        to this file is the return value.
        """
        #http://localhost/api/get-files-without-replay-gain/dir_id/1
        return self.services.get_files_without_replay_gain(dir_id=dir_id)

    def update_replay_gain_values(self, pairs):
        """
        'pairs' is a list of pairs in (x, y), where x is the file's database
        row id and y is the file's replay_gain value in dB
        """
        #http://localhost/api/update-replay-gain-value/
        url = self.construct_url("update_replay_gain_value")
        data = urllib.urlencode({'data': json.dumps(pairs)})
        request = urllib2.Request(url, data)

        self.logger.debug(self.get_response_from_server(request))


    def notify_webstream_data(self, data, media_id):
        """
        Update the server with the latest metadata we've received from the
        external webstream
        """
        try:
            url = self.construct_url("notify_webstream_data")
            url = url.replace("%%media_id%%", str(media_id))
            data = urllib.urlencode({'data': data})
            self.logger.debug(url)
            request = urllib2.Request(url, data)

            self.logger.info(self.get_response_from_server(request, attempts = 5))
        except Exception, e:
            self.logger.error("Exception: %s", e)
