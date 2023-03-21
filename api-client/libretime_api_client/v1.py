import json
import logging
import urllib.parse

from libretime_shared.config import BaseConfig, GeneralConfig

from ._utils import RequestProvider

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

# pypo
api_endpoints[
    "update_start_playing_url"
] = "notify-media-item-start-play/api_key/{api_key}/media_id/{media_id}/"
api_endpoints[
    "update_liquidsoap_status"
] = "update-liquidsoap-status/format/json/api_key/{api_key}/msg/{msg}/stream_id/{stream_id}/boot_time/{boot_time}"
api_endpoints[
    "update_source_status"
] = "update-source-status/format/json/api_key/{api_key}/sourcename/{sourcename}/status/{status}"
api_endpoints[
    "check_live_stream_auth"
] = "check-live-stream-auth/format/json/api_key/{api_key}/username/{username}/password/{password}/djtype/{djtype}"
api_endpoints[
    "notify_webstream_data"
] = "notify-webstream-data/api_key/{api_key}/media_id/{media_id}/format/json"
api_endpoints[
    "notify_liquidsoap_started"
] = "rabbitmq-do-push/api_key/{api_key}/format/json"
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
                logger.info("Airtime API version found: %s", str(api_version))
                logger.info(
                    "pypo is only compatible with API version: %s", AIRTIME_API_VERSION
                )
            return False

        if verbose:
            logger.info("Airtime API version found: %s", str(api_version))
            logger.info(
                "pypo is only compatible with API version: %s", AIRTIME_API_VERSION
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

    def check_live_stream_auth(self, username, password, dj_type):
        try:
            return self.services.check_live_stream_auth(
                username=username, password=password, djtype=dj_type
            )
        except Exception as exception:
            logger.exception(exception)
            return {}

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
