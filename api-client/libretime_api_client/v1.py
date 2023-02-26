import json
import logging
import time
import urllib.parse

import requests
from libretime_shared.config import BaseConfig, GeneralConfig

from ._utils import ApiRequest, RequestProvider

logger = logging.getLogger(__name__)


class Config(BaseConfig):
    general: GeneralConfig


AIRTIME_API_VERSION = "1.1"


api_endpoints = {}

# URL to get the version number of the server API
api_endpoints["version_url"] = "version/api_key/{api_key}"
# URL to register a components IP Address with the central web server
api_endpoints[
    "register_component"
] = "register-component/format/json/api_key/{api_key}/component/{component}"

# media-monitor
api_endpoints[
    "upload_recorded"
] = "upload-recorded/format/json/api_key/{api_key}/fileid/{fileid}/showinstanceid/{showinstanceid}"
# show-recorder
api_endpoints["show_schedule_url"] = "recorded-shows/format/json/api_key/{api_key}"
api_endpoints["upload_file_url"] = "rest/media"
# pypo
api_endpoints[
    "update_start_playing_url"
] = "notify-media-item-start-play/api_key/{api_key}/media_id/{media_id}/"
api_endpoints[
    "get_stream_setting"
] = "get-stream-setting/format/json/api_key/{api_key}/"
api_endpoints[
    "update_liquidsoap_status"
] = "update-liquidsoap-status/format/json/api_key/{api_key}/msg/{msg}/stream_id/{stream_id}/boot_time/{boot_time}"
api_endpoints[
    "update_source_status"
] = "update-source-status/format/json/api_key/{api_key}/sourcename/{sourcename}/status/{status}"
api_endpoints[
    "check_live_stream_auth"
] = "check-live-stream-auth/format/json/api_key/{api_key}/username/{username}/password/{password}/djtype/{djtype}"
api_endpoints["get_bootstrap_info"] = "get-bootstrap-info/format/json/api_key/{api_key}"
api_endpoints[
    "notify_webstream_data"
] = "notify-webstream-data/api_key/{api_key}/media_id/{media_id}/format/json"
api_endpoints[
    "notify_liquidsoap_started"
] = "rabbitmq-do-push/api_key/{api_key}/format/json"
api_endpoints[
    "get_stream_parameters"
] = "get-stream-parameters/api_key/{api_key}/format/json"
api_endpoints["push_stream_stats"] = "push-stream-stats/api_key/{api_key}/format/json"
api_endpoints[
    "update_stream_setting_table"
] = "update-stream-setting-table/api_key/{api_key}/format/json"
api_endpoints[
    "update_metadata_on_tunein"
] = "update-metadata-on-tunein/api_key/{api_key}"


class ApiClient:
    API_BASE = "/api"
    UPLOAD_RETRIES = 3
    UPLOAD_WAIT = 60

    def __init__(self, config_path="/etc/libretime/config.yml"):
        config = Config(config_path)
        self.base_url = config.general.public_url
        self.api_key = config.general.api_key

        self.services = RequestProvider(
            base_url=self.base_url + self.API_BASE,
            api_key=self.api_key,
            endpoints=api_endpoints,
        )

    def __get_api_version(self):
        try:
            return self.services.version_url()["api_version"]
        except Exception as exception:
            logger.exception(exception)
            return -1

    def is_server_compatible(self, verbose=True):
        api_version = self.__get_api_version()
        if api_version == -1:
            if verbose:
                logger.info("Unable to get Airtime API version number.\n")
            return False

        if api_version[0:3] != AIRTIME_API_VERSION[0:3]:
            if verbose:
                logger.info("Airtime API version found: " + str(api_version))
                logger.info(
                    "pypo is only compatible with API version: " + AIRTIME_API_VERSION
                )
            return False

        if verbose:
            logger.info("Airtime API version found: " + str(api_version))
            logger.info(
                "pypo is only compatible with API version: " + AIRTIME_API_VERSION
            )
        return True

    def notify_liquidsoap_started(self):
        try:
            self.services.notify_liquidsoap_started()
        except Exception as exception:
            logger.exception(exception)

    def notify_media_item_start_playing(self, media_id):
        """
        This is a callback from liquidsoap, we use this to notify
        about the currently playing *song*. We get passed a JSON string
        which we handed to liquidsoap in get_liquidsoap_data().
        """
        try:
            return self.services.update_start_playing_url(media_id=media_id)
        except Exception as exception:
            logger.exception(exception)
            return None

    def get_shows_to_record(self):
        try:
            return self.services.show_schedule_url()
        except Exception as exception:
            logger.exception(exception)
            return None

    def upload_recorded_show(self, files, show_id):
        response = ""

        retries = self.UPLOAD_RETRIES
        retries_wait = self.UPLOAD_WAIT

        url = self.construct_rest_url("upload_file_url")

        logger.debug(url)

        for i in range(0, retries):
            logger.debug("Upload attempt: %s", i + 1)
            logger.debug(files)
            logger.debug(ApiRequest.API_HTTP_REQUEST_TIMEOUT)

            try:
                request = requests.post(
                    url, files=files, timeout=float(ApiRequest.API_HTTP_REQUEST_TIMEOUT)
                )
                response = request.json()
                logger.debug(response)

                # FIXME: We need to tell LibreTime that the uploaded track was recorded
                # for a specific show
                #
                # My issue here is that response does not yet have an id. The id gets
                # generated at the point where analyzer is done with it's work. We
                # probably need to do what is below in analyzer and also make sure that
                # the show instance id is routed all the way through.
                #
                # It already gets uploaded by this but the RestController does not seem
                # to care about it. In the end analyzer doesn't have the info in it's
                # rabbitmq message and imports the show as a regular track.
                #
                # logger.info("uploaded show result as file id %s", response.id)
                #
                # url = self.construct_url("upload_recorded") url =
                # url.replace('%%fileid%%', response.id) url =
                # url.replace('%%showinstanceid%%', show_id) request.get(url)
                # logger.info("associated uploaded file %s with show instance %s",
                # response.id, show_id)
                break

            except requests.exceptions.HTTPError as exception:
                logger.error(f"Http error code: {exception.response.status_code}")
                logger.exception(exception)

            except requests.exceptions.ConnectionError as exception:
                logger.exception(f"Server is down: {exception}")

            except Exception as exception:
                logger.exception(exception)

            # wait some time before next retry
            time.sleep(retries_wait)

        return response

    def check_live_stream_auth(self, username, password, dj_type):
        try:
            return self.services.check_live_stream_auth(
                username=username, password=password, djtype=dj_type
            )
        except Exception as exception:
            logger.exception(exception)
            return {}

    def construct_rest_url(self, action_key):
        """
        Constructs the base url for RESTful requests
        """
        url = urllib.parse.urlsplit(self.base_url)
        url.username = self.api_key
        return f"{url.geturl()}/{api_endpoints[action_key]}"

    def get_stream_setting(self):
        return self.services.get_stream_setting()

    def register_component(self, component):
        """
        Purpose of this method is to contact the server with a "Hey its
        me!" message. This will allow the server to register the component's
        (component = media-monitor, pypo etc.) ip address, and later use it
        to query monit via monit's http service, or download log files via a
        http server.
        """
        return self.services.register_component(component=component)

    def notify_liquidsoap_status(self, msg, stream_id, time):
        try:
            # encoded_msg is no longer used server_side!!
            encoded_msg = urllib.parse.quote("dummy")

            self.services.update_liquidsoap_status.req(
                _post_data={"msg_post": msg},
                msg=encoded_msg,
                stream_id=stream_id,
                boot_time=time,
            ).retry(5)
        except Exception as exception:
            logger.exception(exception)

    def notify_source_status(self, sourcename, status):
        try:
            return self.services.update_source_status.req(
                sourcename=sourcename, status=status
            ).retry(5)
        except Exception as exception:
            logger.exception(exception)

    def get_bootstrap_info(self):
        """
        Retrieve infomations needed on bootstrap time.
        """
        return self.services.get_bootstrap_info()

    def notify_webstream_data(self, data, media_id):
        """
        Update the server with the latest metadata we've received from the
        external webstream
        """
        logger.info(
            self.services.notify_webstream_data.req(
                _post_data={"data": data}, media_id=str(media_id)
            ).retry(5)
        )

    def get_stream_parameters(self):
        response = self.services.get_stream_parameters()
        logger.debug(response)
        return response

    def push_stream_stats(self, data):
        # TODO : users of this method should do their own error handling
        response = self.services.push_stream_stats(
            _post_data={"data": json.dumps(data)}
        )
        return response

    def update_stream_setting_table(self, data):
        try:
            response = self.services.update_stream_setting_table(
                _post_data={"data": json.dumps(data)}
            )
            return response
        except Exception as exception:
            logger.exception(exception)

    def update_metadata_on_tunein(self):
        self.services.update_metadata_on_tunein()


class InvalidContentType(Exception):
    pass
