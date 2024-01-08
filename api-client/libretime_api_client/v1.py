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

    def upload_recorded(self, media_id, fileid, showinstanceid, **kwargs) -> Response:
        return self._request(
            "GET",
            "/api/upload_recorded",
            params={"media_id": media_id,"fileid": fileid, "showinstanceid": showinstanceid},
            **kwargs,
        )

    def get_shows_to_record(self,  **kwargs) -> Response:
        return self._request(
            "GET",
            "/api/recorded-shows",
            **kwargs,
        )

    def upload_file_url(self,  **kwargs) -> Response:
        return self._request(
            "GET",
            "/api/upload_file_url",
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
            resp=self._base_client.get_shows_to_record()
            payload = resp.json()
            return payload
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
                logger.error("Http error code: %s", exception.response.status_code)
                logger.exception(exception)

            except requests.exceptions.ConnectionError as exception:
                logger.exception("Server is down: %s", exception)

            except Exception as exception:
                logger.exception(exception)

            # wait some time before next retry
            time.sleep(retries_wait)

        return response        

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