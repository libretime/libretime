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
import urllib.request, urllib.error, urllib.parse
import requests
import socket 
import logging
import json
import base64
import traceback
from configobj import ConfigObj

AIRTIME_API_VERSION = "1.1"


api_config = {}

# URL to get the version number of the server API
api_config['version_url'] = 'version/api_key/%%api_key%%'
#URL to register a components IP Address with the central web server
api_config['register_component'] = 'register-component/format/json/api_key/%%api_key%%/component/%%component%%'

#media-monitor
api_config['media_setup_url'] = 'media-monitor-setup/format/json/api_key/%%api_key%%'
api_config['upload_recorded'] = 'upload-recorded/format/json/api_key/%%api_key%%/fileid/%%fileid%%/showinstanceid/%%showinstanceid%%'
api_config['update_media_url'] = 'reload-metadata/format/json/api_key/%%api_key%%/mode/%%mode%%'
api_config['list_all_db_files'] = 'list-all-files/format/json/api_key/%%api_key%%/dir_id/%%dir_id%%/all/%%all%%'
api_config['list_all_watched_dirs'] = 'list-all-watched-dirs/format/json/api_key/%%api_key%%'
api_config['add_watched_dir'] = 'add-watched-dir/format/json/api_key/%%api_key%%/path/%%path%%'
api_config['remove_watched_dir'] = 'remove-watched-dir/format/json/api_key/%%api_key%%/path/%%path%%'
api_config['set_storage_dir'] = 'set-storage-dir/format/json/api_key/%%api_key%%/path/%%path%%'
api_config['update_fs_mount'] = 'update-file-system-mount/format/json/api_key/%%api_key%%'
api_config['reload_metadata_group'] = 'reload-metadata-group/format/json/api_key/%%api_key%%'
api_config['handle_watched_dir_missing'] = 'handle-watched-dir-missing/format/json/api_key/%%api_key%%/dir/%%dir%%'
#show-recorder
api_config['show_schedule_url'] = 'recorded-shows/format/json/api_key/%%api_key%%'
api_config['upload_file_url'] = 'rest/media'
api_config['upload_retries'] = '3'
api_config['upload_wait'] = '60'
#pypo
api_config['export_url'] = 'schedule/api_key/%%api_key%%'
api_config['get_media_url'] = 'get-media/file/%%file%%/api_key/%%api_key%%'
api_config['update_item_url'] = 'notify-schedule-group-play/api_key/%%api_key%%/schedule_id/%%schedule_id%%'
api_config['update_start_playing_url'] = 'notify-media-item-start-play/api_key/%%api_key%%/media_id/%%media_id%%/'
api_config['get_stream_setting'] = 'get-stream-setting/format/json/api_key/%%api_key%%/'
api_config['update_liquidsoap_status'] = 'update-liquidsoap-status/format/json/api_key/%%api_key%%/msg/%%msg%%/stream_id/%%stream_id%%/boot_time/%%boot_time%%'
api_config['update_source_status'] = 'update-source-status/format/json/api_key/%%api_key%%/sourcename/%%sourcename%%/status/%%status%%'
api_config['check_live_stream_auth'] = 'check-live-stream-auth/format/json/api_key/%%api_key%%/username/%%username%%/password/%%password%%/djtype/%%djtype%%'
api_config['get_bootstrap_info'] = 'get-bootstrap-info/format/json/api_key/%%api_key%%'
api_config['get_files_without_replay_gain'] = 'get-files-without-replay-gain/api_key/%%api_key%%/dir_id/%%dir_id%%'
api_config['update_replay_gain_value'] = 'update-replay-gain-value/format/json/api_key/%%api_key%%'
api_config['notify_webstream_data'] = 'notify-webstream-data/api_key/%%api_key%%/media_id/%%media_id%%/format/json'
api_config['notify_liquidsoap_started'] = 'rabbitmq-do-push/api_key/%%api_key%%/format/json'
api_config['get_stream_parameters'] = 'get-stream-parameters/api_key/%%api_key%%/format/json'
api_config['push_stream_stats'] = 'push-stream-stats/api_key/%%api_key%%/format/json'
api_config['update_stream_setting_table'] = 'update-stream-setting-table/api_key/%%api_key%%/format/json'
api_config['get_files_without_silan_value'] = 'get-files-without-silan-value/api_key/%%api_key%%'
api_config['update_cue_values_by_silan'] = 'update-cue-values-by-silan/api_key/%%api_key%%'
api_config['api_base'] = 'api'
api_config['bin_dir'] = '/usr/lib/airtime/api_clients/'
api_config['update_metadata_on_tunein'] = 'update-metadata-on-tunein/api_key/%%api_key%%'



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
        for k, v in params.items():
            wrapped_param = "%%" + k + "%%"
            if wrapped_param in temp_url:
                temp_url = temp_url.replace(wrapped_param, str(v))
            else: raise UrlBadParam(self.base_url, k)
        return ApcUrl(temp_url)

    def url(self):
        if '%%' in self.base_url: raise IncompleteUrl(self.base_url)
        else: return self.base_url

class ApiRequest(object):

    API_HTTP_REQUEST_TIMEOUT = 30 # 30 second HTTP request timeout

    def __init__(self, name, url, logger=None):
        self.name = name
        self.url  = url
        self.__req = None
        if logger is None: self.logger = logging
        else: self.logger = logger

    def __call__(self,_post_data=None, **kwargs):
        final_url = self.url.params(**kwargs).url()
        if _post_data is not None: _post_data = urllib.parse.urlencode(_post_data)
        self.logger.debug(final_url)
        try:
            req = urllib.request.Request(final_url, _post_data)
            f = urllib.request.urlopen(req, timeout=ApiRequest.API_HTTP_REQUEST_TIMEOUT)
            content_type = f.info().getheader('Content-Type')
            response = f.read()
        #Everything that calls an ApiRequest should be catching URLError explicitly
        #(according to the other comments in this file and a cursory grep through the code)
        #Note that URLError can occur for timeouts as well as socket.timeout
        except socket.timeout:
            self.logger.error('HTTP request to %s timed out', final_url)
            raise
        except Exception as e:
            #self.logger.error('Exception: %s', e)
            #self.logger.error("traceback: %s", traceback.format_exc())
            raise

        try:
            if content_type == 'application/json':
                data = json.loads(response)
                return data
            else:
                raise InvalidContentType()
        except Exception:
            #self.logger.error(response)
            #self.logger.error("traceback: %s", traceback.format_exc())
            raise

    def req(self, *args, **kwargs):
        self.__req = lambda : self(*args, **kwargs)
        return self

    def retry(self, n, delay=5):
        """Try to send request n times. If after n times it fails then
        we finally raise exception"""
        for i in range(0,n-1):
            try: return self.__req()
            except Exception: time.sleep(delay)
        return self.__req()

class RequestProvider(object):
    """ Creates the available ApiRequest instance that can be read from
    a config file """
    def __init__(self, cfg):
        self.config = cfg
        self.requests = {}
        if self.config["general"]["base_dir"].startswith("/"):
            self.config["general"]["base_dir"] = self.config["general"]["base_dir"][1:]
        self.url = ApcUrl("%s://%s:%s/%s%s/%s" \
            % (str(("http", "https")[int(self.config["general"]["base_port"]) == 443]),
               self.config["general"]["base_url"], str(self.config["general"]["base_port"]),
               self.config["general"]["base_dir"], self.config["api_base"],
               '%%action%%'))
        # Now we must discover the possible actions
        actions = dict( (k,v) for k,v in cfg.items() if '%%api_key%%' in v)
        for action_name, action_value in actions.items():
            new_url = self.url.params(action=action_value).params(
                api_key=self.config["general"]['api_key'])
            self.requests[action_name] = ApiRequest(action_name, new_url)

    def available_requests(self)    : return list(self.requests.keys())
    def __contains__(self, request) : return request in self.requests

    def __getattr__(self, attr):
        if attr in self: return self.requests[attr]
        else: return super(RequestProvider, self).__getattribute__(attr)


class AirtimeApiClient(object):
    def __init__(self, logger=None,config_path='/etc/airtime/airtime.conf'):
        if logger is None: self.logger = logging
        else: self.logger = logger

        # loading config file
        try:
            self.config = ConfigObj(config_path)
            self.config.update(api_config)
            self.services = RequestProvider(self.config)
        except Exception as e:
            self.logger.error('Error loading config file: %s', config_path)
            self.logger.error("traceback: %s", traceback.format_exc())
            sys.exit(1)

    def __get_airtime_version(self):
        try: return self.services.version_url()['airtime_version']
        except Exception: return -1
    
    def __get_api_version(self):
        try: return self.services.version_url()['api_version']
        except Exception: return -1

    def is_server_compatible(self, verbose=True):
        logger = self.logger
        api_version = self.__get_api_version()
        # logger.info('Airtime version found: ' + str(version))
        if api_version == -1:
            if verbose:
                logger.info('Unable to get Airtime API version number.\n')
            return False
        elif api_version[0:3] != AIRTIME_API_VERSION[0:3]:
            if verbose:
                logger.info('Airtime API version found: ' + str(api_version))
                logger.info('pypo is only compatible with API version: ' + AIRTIME_API_VERSION)
            return False
        else:
            if verbose:
                logger.info('Airtime API version found: ' + str(api_version))
                logger.info('pypo is only compatible with API version: ' + AIRTIME_API_VERSION)
            return True


    def get_schedule(self):
        # TODO : properly refactor this routine
        # For now the return type is a little messed up for compatibility reasons
        try: return (True, self.services.export_url())
        except: return (False, None)

    def notify_liquidsoap_started(self):
        try:
            self.services.notify_liquidsoap_started()
        except Exception as e:
            self.logger.error(str(e))

    def notify_media_item_start_playing(self, media_id):
        """ This is a callback from liquidsoap, we use this to notify
        about the currently playing *song*. We get passed a JSON string
        which we handed to liquidsoap in get_liquidsoap_data(). """
        try:
            return self.services.update_start_playing_url(media_id=media_id)
        except Exception as e:
            self.logger.error(str(e))
            return None

    def get_shows_to_record(self):
        try:
            return self.services.show_schedule_url()
        except Exception as e:
            self.logger.error(str(e))
            return None

    def upload_recorded_show(self, files, show_id):
        logger = self.logger
        response = ''

        retries = int(self.config["upload_retries"])
        retries_wait = int(self.config["upload_wait"])

        url = self.construct_rest_url("upload_file_url")

        logger.debug(url)

        for i in range(0, retries):
            logger.debug("Upload attempt: %s", i + 1)
            logger.debug(files)
            logger.debug(ApiRequest.API_HTTP_REQUEST_TIMEOUT)

            try:
                request  = requests.post(url, files=files, timeout=float(ApiRequest.API_HTTP_REQUEST_TIMEOUT))
                response = request.json()
                logger.debug(response)

                """
                FIXME: We need to tell LibreTime that the uploaded track was recorded for a specific show

                My issue here is that response does not yet have an id. The id gets generated at the point
                where analyzer is done with it's work. We probably need to do what is below in analyzer
                and also make sure that the show instance id is routed all the way through.

                It already gets uploaded by this but the RestController does not seem to care about it. In
                the end analyzer doesn't have the info in it's rabbitmq message and imports the show as a
                regular track.

                logger.info("uploaded show result as file id %s", response.id)

                url = self.construct_url("upload_recorded")
                url = url.replace('%%fileid%%', response.id)
                url = url.replace('%%showinstanceid%%', show_id)
                request.get(url)
                logger.info("associated uploaded file %s with show instance %s", response.id, show_id)
                """
                break

            except requests.exceptions.HTTPError as e:
                logger.error("Http error code: %s", e.code)
                logger.error("traceback: %s", traceback.format_exc())
            except requests.exceptions.ConnectionError as e:
                logger.error("Server is down: %s", e.args)
                logger.error("traceback: %s", traceback.format_exc())
            except Exception as e:
                logger.error("Exception: %s", e)
                logger.error("traceback: %s", traceback.format_exc())

            #wait some time before next retry
            time.sleep(retries_wait)

        return response

    def check_live_stream_auth(self, username, password, dj_type):
        try:
            return self.services.check_live_stream_auth(
                username=username, password=password, djtype=dj_type)
        except Exception as e:
            self.logger.error(str(e))
            return {}

    def construct_url(self,config_action_key):
        """Constructs the base url for every request"""
        # TODO : Make other methods in this class use this this method.
        if self.config["general"]["base_dir"].startswith("/"):
            self.config["general"]["base_dir"] = self.config["general"]["base_dir"][1:]
        url = "%s://%s:%s/%s%s/%s" %  \
            (str(("http", "https")[int(self.config["general"]["base_port"]) == 443]),
             self.config["general"]["base_url"], str(self.config["general"]["base_port"]),
             self.config["general"]["base_dir"], self.config["api_base"],
             self.config[config_action_key])
        url = url.replace("%%api_key%%", self.config["general"]["api_key"])
        return url

    def construct_rest_url(self,config_action_key):
        """Constructs the base url for RESTful requests"""
        if self.config["general"]["base_dir"].startswith("/"):
            self.config["general"]["base_dir"] = self.config["general"]["base_dir"][1:]
        url = "%s://%s:@%s:%s/%s/%s" %  \
            (str(("http", "https")[int(self.config["general"]["base_port"]) == 443]),
             self.config["general"]["api_key"],
             self.config["general"]["base_url"], str(self.config["general"]["base_port"]),
             self.config["general"]["base_dir"],
             self.config[config_action_key])
        return url


    """
    Caller of this method needs to catch any exceptions such as
    ValueError thrown by json.loads or URLError by urllib2.urlopen
    """
    def setup_media_monitor(self):
        return self.services.media_setup_url()

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
        md_list = dict((("md%d" % i), json.dumps(md)) \
                for i,md in enumerate(valid_actions))
        # For testing we add the following "dry" parameter to tell the
        # controller not to actually do any changes
        if dry: md_list['dry'] = 1
        self.logger.info("Pumping out %d requests..." % len(valid_actions))
        return self.services.reload_metadata_group(_post_data=md_list)

    #returns a list of all db files for a given directory in JSON format:
    #{"files":["path/to/file1", "path/to/file2"]}
    #Note that these are relative paths to the given directory. The full
    #path is not returned.
    def list_all_db_files(self, dir_id, all_files=True):
        logger = self.logger
        try:
            all_files = "1" if all_files else "0"
            response = self.services.list_all_db_files(dir_id=dir_id,
                                                       all=all_files)
        except Exception as e:
            response = {}
            logger.error("Exception: %s", e)
        try:
            return response["files"]
        except KeyError:
            self.logger.error("Could not find index 'files' in dictionary: %s",
                    str(response))
            return []
    """
    Caller of this method needs to catch any exceptions such as
    ValueError thrown by json.loads or URLError by urllib2.urlopen
    """
    def list_all_watched_dirs(self):
        return self.services.list_all_watched_dirs()

    """
    Caller of this method needs to catch any exceptions such as
    ValueError thrown by json.loads or URLError by urllib2.urlopen
    """
    def add_watched_dir(self, path):
        return self.services.add_watched_dir(path=base64.b64encode(path))

    """
    Caller of this method needs to catch any exceptions such as
    ValueError thrown by json.loads or URLError by urllib2.urlopen
    """
    def remove_watched_dir(self, path):
        return self.services.remove_watched_dir(path=base64.b64encode(path))

    """
    Caller of this method needs to catch any exceptions such as
    ValueError thrown by json.loads or URLError by urllib2.urlopen
    """
    def set_storage_dir(self, path):
        return self.services.set_storage_dir(path=base64.b64encode(path))

    """
    Caller of this method needs to catch any exceptions such as
    ValueError thrown by json.loads or URLError by urllib2.urlopen
    """
    def get_stream_setting(self):
        return self.services.get_stream_setting()

    def register_component(self, component):
        """ Purpose of this method is to contact the server with a "Hey its
        me!" message. This will allow the server to register the component's
        (component = media-monitor, pypo etc.) ip address, and later use it
        to query monit via monit's http service, or download log files via a
        http server. """
        return self.services.register_component(component=component)

    def notify_liquidsoap_status(self, msg, stream_id, time):
        logger = self.logger
        try:
            post_data = {"msg_post": msg}

            #encoded_msg is no longer used server_side!!
            encoded_msg = urllib.parse.quote('dummy')
            self.services.update_liquidsoap_status.req(post_data,
                                            msg=encoded_msg,
                                            stream_id=stream_id,
                                            boot_time=time).retry(5)
        except Exception as e:
            #TODO
            logger.error("Exception: %s", e)

    def notify_source_status(self, sourcename, status):
        try:
            logger = self.logger
            return self.services.update_source_status.req(sourcename=sourcename,
                                                      status=status).retry(5)
        except Exception as e:
            #TODO
            logger.error("Exception: %s", e)

    def get_bootstrap_info(self):
        """ Retrieve infomations needed on bootstrap time """
        return self.services.get_bootstrap_info()

    def get_files_without_replay_gain_value(self, dir_id):
        """
        Download a list of files that need to have their ReplayGain value
        calculated. This list of files is downloaded into a file and the path
        to this file is the return value.
        """
        #http://localhost/api/get-files-without-replay-gain/dir_id/1
        try:
            return self.services.get_files_without_replay_gain(dir_id=dir_id)
        except Exception as e:
            self.logger.error(str(e))
            return []

    def get_files_without_silan_value(self):
        """
        Download a list of files that need to have their cue in/out value
        calculated. This list of files is downloaded into a file and the path
        to this file is the return value.
        """
        try:
            return self.services.get_files_without_silan_value()
        except Exception as e:
            self.logger.error(str(e))
            return []

    def update_replay_gain_values(self, pairs):
        """
        'pairs' is a list of pairs in (x, y), where x is the file's database
        row id and y is the file's replay_gain value in dB
        """
        self.logger.debug(self.services.update_replay_gain_value(
            _post_data={'data': json.dumps(pairs)}))


    def update_cue_values_by_silan(self, pairs):
        """
        'pairs' is a list of pairs in (x, y), where x is the file's database
        row id and y is the file's cue values in dB
        """
        return self.services.update_cue_values_by_silan(_post_data={'data': json.dumps(pairs)})


    def notify_webstream_data(self, data, media_id):
        """
        Update the server with the latest metadata we've received from the
        external webstream
        """
        self.logger.info( self.services.notify_webstream_data.req(
            _post_data={'data':data}, media_id=str(media_id)).retry(5))

    def get_stream_parameters(self):
        response = self.services.get_stream_parameters()
        self.logger.debug(response)
        return response

    def push_stream_stats(self, data):
        # TODO : users of this method should do their own error handling
        response = self.services.push_stream_stats(_post_data={'data': json.dumps(data)})
        return response

    def update_stream_setting_table(self, data):
        try:
            response = self.services.update_stream_setting_table(_post_data={'data': json.dumps(data)})
            return response
        except Exception as e:
            #TODO
            self.logger.error(str(e))

    def update_metadata_on_tunein(self):
        self.services.update_metadata_on_tunein()


class InvalidContentType(Exception):
    pass
