import json
import logging
from functools import wraps
from time import sleep

from requests.exceptions import RequestException

from ._client import AbstractApiClient, Response

logger = logging.getLogger(__name__)


def retry_decorator(max_retries: int = 5):
    def retry_request(func):
        @wraps(func)
        def wrapper(*args, **kwargs):
            retries = max_retries
            while True:
                try:
                    return func(*args, **kwargs)
                except RequestException as exception:
                    logger.warning(exception)

                    retries -= 1
                    if retries <= 0:
                        break

                    sleep(2.0)

            return None

        return wrapper

    return retry_request


class BaseApiClient(AbstractApiClient):
    def __init__(self, base_url: str, api_key: str):
        super().__init__(base_url=base_url)
        self.session.headers.update({"Authorization": f"Api-Key {api_key}"})
        self.session.params.update({"format": "json"})  # type: ignore[union-attr]

    def version(self, **kwargs) -> Response:
        return self._request(
            "GET",
            "/api/version",
            **kwargs,
        )

    def register_component(self, component: str, **kwargs) -> Response:
        return self._request(
            "GET",
            "/api/register-component",
            params={"component": component},
            **kwargs,
        )

    def notify_media_item_start_play(self, media_id, **kwargs) -> Response:
        return self._request(
            "GET",
            "/api/notify-media-item-start-play",
            params={"media_id": media_id},
            **kwargs,
        )

    def get_shows_to_record(self, **kwargs) -> Response:
        return self._request(
            "GET",
            "/api/recorded-shows",
            **kwargs,
        )

    def update_liquidsoap_status(self, msg, stream_id, boot_time, **kwargs) -> Response:
        return self._request(
            "POST",
            "/api/update-liquidsoap-status",
            params={"stream_id": stream_id, "boot_time": boot_time},
            data={"msg_post": msg},
            **kwargs,
        )

    def update_source_status(self, sourcename, status, **kwargs) -> Response:
        return self._request(
            "GET",
            "/api/update-source-status",
            params={"sourcename": sourcename, "status": status},
            **kwargs,
        )

    def check_live_stream_auth(self, username, password, djtype, **kwargs) -> Response:
        return self._request(
            "GET",
            "/api/check-live-stream-auth",
            params={"username": username, "password": password, "djtype": djtype},
            **kwargs,
        )

    def get_shows_to_record(self, **kwargs) -> Response:
        return self._request(
            "GET",
            "/api/recorded-shows",
            **kwargs,
        )

    def notify_webstream_data(self, media_id, data, **kwargs) -> Response:
        return self._request(
            "POST",
            "/api/notify-webstream-data",
            params={"media_id": media_id},
            data={"data": data},  # Data is already a json formatted string
            **kwargs,
        )

    def rabbitmq_do_push(self, **kwargs) -> Response:
        return self._request(
            "GET",
            "/api/rabbitmq-do-push",
            **kwargs,
        )

    def push_stream_stats(self, data, **kwargs) -> Response:
        return self._request(
            "POST",
            "/api/push-stream-stats",
            data={"data": json.dumps(data)},
            **kwargs,
        )

    def update_stream_setting_table(self, data, **kwargs) -> Response:
        return self._request(
            "POST",
            "/api/update-stream-setting-table",
            data={"data": json.dumps(data)},
            **kwargs,
        )

    def update_metadata_on_tunein(self, **kwargs) -> Response:
        return self._request(
            "GET",
            "/api/update-metadata-on-tunein",
            **kwargs,
        )


class ApiClient:
    def __init__(self, base_url: str, api_key: str):
        self._base_client = BaseApiClient(base_url=base_url, api_key=api_key)

    def version(self):
        try:
            resp = self._base_client.version()
            payload = resp.json()
            return payload["api_version"]
        except RequestException:
            return -1

    def notify_liquidsoap_started(self):
        try:
            self._base_client.rabbitmq_do_push()
        except RequestException:
            pass

    def notify_media_item_start_playing(self, media_id):
        """
        This is a callback from liquidsoap, we use this to notify
        about the currently playing *song*. We get passed a JSON string
        which we handed to liquidsoap in get_liquidsoap_data().
        """
        try:
            return self._base_client.notify_media_item_start_play(media_id=media_id)
        except RequestException:
            return None

    def check_live_stream_auth(self, username, password, dj_type):
        try:
            return self._base_client.check_live_stream_auth(
                username=username,
                password=password,
                djtype=dj_type,
            )
        except RequestException:
            return {}

    def get_shows_to_record(self):
        try:
            resp = self._base_client.get_shows_to_record()
            payload = resp.json()
            return payload
        except Exception as exception:
            logger.exception(exception)
            return None

    def register_component(self, component):
        """
        Purpose of this method is to contact the server with a "Hey its
        me!" message. This will allow the server to register the component's
        (component = media-monitor, pypo etc.) ip address, and later use it
        to query monit via monit's http service, or download log files via a
        http server.
        """
        return self._base_client.register_component(component=component)

    @retry_decorator()
    def notify_liquidsoap_status(self, msg, stream_id, time):
        self._base_client.update_liquidsoap_status(
            msg=msg,
            stream_id=stream_id,
            boot_time=time,
        )

    @retry_decorator()
    def notify_source_status(self, sourcename, status):
        return self._base_client.update_source_status(
            sourcename=sourcename,
            status=status,
        )

    @retry_decorator()
    def notify_webstream_data(self, data, media_id):
        """
        Update the server with the latest metadata we've received from the
        external webstream
        """
        return self._base_client.notify_webstream_data(
            data=data,
            media_id=str(media_id),
        )

    def push_stream_stats(self, data):
        return self._base_client.push_stream_stats(data=data)

    def update_stream_setting_table(self, data):
        try:
            return self._base_client.update_stream_setting_table(data=data)
        except RequestException:
            return None

    def update_metadata_on_tunein(self):
        self._base_client.update_metadata_on_tunein()
