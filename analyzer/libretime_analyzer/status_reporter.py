import collections
import json
import logging
import pickle
import queue
import threading
import time
from urllib.parse import urlparse

import requests
from requests.exceptions import HTTPError

logger = logging.getLogger(__name__)


class PicklableHttpRequest:
    def __init__(self, method, url, api_key, data):
        self.method = method
        self.url = url
        self.api_key = api_key
        self.data = data

    def create_request(self):
        return requests.Request(
            method=self.method,
            url=self.url,
            data=self.data,
            auth=requests.auth.HTTPBasicAuth(self.api_key, ""),
        )


def process_http_requests(ipc_queue, http_retry_queue_path):
    """Runs in a separate thread and performs all the HTTP requests where we're
    reporting extracted audio file metadata or errors back to the Airtime web application.

    This process also checks every 5 seconds if there's failed HTTP requests that we
    need to retry. We retry failed HTTP requests so that we don't lose uploads if the
    web server is temporarily down.

    """

    # Store any failed requests (eg. due to web server errors or downtime) to be
    # retried later:
    retry_queue = collections.deque()
    shutdown = False

    # Unpickle retry_queue from disk so that we won't have lost any uploads
    # if airtime_analyzer is shut down while the web server is down or unreachable,
    # and there were failed HTTP requests pending, waiting to be retried.
    try:
        with open(http_retry_queue_path, "rb") as pickle_file:
            retry_queue = pickle.load(pickle_file)
    except OSError as exception:
        if exception.errno != 2:
            raise exception
    except Exception:
        # If we fail to unpickle a saved queue of failed HTTP requests, then we'll just log an error
        # and continue because those HTTP requests are lost anyways. The pickled file will be
        # overwritten the next time the analyzer is shut down too.
        logger.error("Failed to unpickle %s. Continuing...", http_retry_queue_path)

    while True:
        try:
            while not shutdown:
                try:
                    request = ipc_queue.get(block=True, timeout=5)
                    if (
                        isinstance(request, str) and request == "shutdown"
                    ):  # Bit of a cheat
                        shutdown = True
                        break
                except queue.Empty:
                    request = None

                # If there's no new HTTP request we need to execute, let's check our "retry
                # queue" and see if there's any failed HTTP requests we can retry:
                if request:
                    send_http_request(request, retry_queue)
                else:
                    # Using a for loop instead of while so we only iterate over all the requests once!
                    for _ in range(len(retry_queue)):
                        request = retry_queue.popleft()
                        send_http_request(request, retry_queue)

            logger.info("Shutting down status_reporter")
            # Pickle retry_queue to disk so that we don't lose uploads if we're shut down while
            # while the web server is down or unreachable.
            with open(http_retry_queue_path, "wb") as pickle_file:
                pickle.dump(retry_queue, pickle_file)
            return
        except (
            Exception
        ) as exception:  # Terrible top-level exception handler to prevent the thread from dying, just in case.
            if shutdown:
                return
            logger.exception("Unhandled exception in StatusReporter %s", exception)
            logger.info("Restarting StatusReporter thread")
            time.sleep(2)  # Throttle it


def send_http_request(picklable_request: PicklableHttpRequest, retry_queue):
    try:
        bare_request = picklable_request.create_request()
        session = requests.Session()
        prepared_request = session.prepare_request(bare_request)
        resp = session.send(
            prepared_request, timeout=StatusReporter._HTTP_REQUEST_TIMEOUT
        )
        resp.raise_for_status()  # Raise an exception if there was an http error code returned
        logger.info("HTTP request sent successfully.")
    except requests.exceptions.HTTPError as exception:
        if exception.response.status_code == 422:
            # Do no retry the request if there was a metadata validation error
            logger.exception(
                f"HTTP request failed due to an HTTP exception: {exception}"
            )
        else:
            # The request failed with an error 500 probably, so let's check if Airtime and/or
            # the web server are broken. If not, then our request was probably causing an
            # error 500 in the media API (ie. a bug), so there's no point in retrying it.
            logger.exception("HTTP request failed: %s", exception)
            parsed_url = urlparse(exception.response.request.url)
            if is_web_server_broken(parsed_url.scheme + "://" + parsed_url.netloc):
                # If the web server is having problems, retry the request later:
                retry_queue.append(picklable_request)
                # Otherwise, if the request was bad, the request is never retried.
                # You will have to find these bad requests in logs or you'll be
                # notified by sentry.
    except requests.exceptions.ConnectionError as exception:
        logger.exception(
            "HTTP request failed due to a connection error,  retrying later: %s",
            exception,
        )
        retry_queue.append(picklable_request)  # Retry it later
    except Exception as exception:
        logger.exception("HTTP request failed with unhandled exception. %s", exception)
        # Don't put the request into the retry queue, just give up on this one.
        # I'm doing this to protect against us getting some pathological request
        # that breaks our code. I don't want us pickling data that potentially
        # breaks airtime_analyzer.


def is_web_server_broken(url):
    """Do a naive test to check if the web server we're trying to access is down.
    We use this to try to differentiate between error 500s that are coming
    from (for example) a bug in the Airtime Media REST API and error 500s
    caused by Airtime or the webserver itself being broken temporarily.
    """
    try:
        test_req = requests.get(url)
        test_req.raise_for_status()
    except HTTPError:
        return True
    return False


class StatusReporter:
    """Reports the extracted audio file metadata and job status back to the
    Airtime web application.
    """

    _HTTP_REQUEST_TIMEOUT = 30

    _ipc_queue = queue.Queue()
    _http_thread = None

    @classmethod
    def start_thread(cls, http_retry_queue_path):
        StatusReporter._http_thread = threading.Thread(
            target=process_http_requests,
            args=(StatusReporter._ipc_queue, http_retry_queue_path),
        )
        StatusReporter._http_thread.start()

    @classmethod
    def stop_thread(cls):
        logger.info("Terminating status_reporter process")
        StatusReporter._ipc_queue.put("shutdown")
        StatusReporter._http_thread.join()

    @classmethod
    def _send_http_request(cls, request):
        StatusReporter._ipc_queue.put(request)

    @classmethod
    def report_success(
        cls,
        callback_url: str,
        callback_api_key: str,
        metadata: dict,
    ):
        """Report the extracted metadata and status of the successfully imported file
        to the callback URL (which should be the Airtime File Upload API)
        """
        put_payload = json.dumps(metadata)
        StatusReporter._send_http_request(
            PicklableHttpRequest(
                method="PUT",
                url=callback_url,
                api_key=callback_api_key,
                data=put_payload,
            )
        )

    @classmethod
    def report_failure(
        cls,
        callback_url,
        callback_api_key,
        import_status: int,
        reason,
    ):
        logger.debug("Reporting import failure to Airtime REST API...")
        audio_metadata = {}
        audio_metadata["import_status"] = import_status
        audio_metadata["comment"] = reason  # hack attack
        put_payload = json.dumps(audio_metadata)
        # logger.debug("sending http put with payload: %s", put_payload)

        StatusReporter._send_http_request(
            PicklableHttpRequest(
                method="PUT",
                url=callback_url,
                api_key=callback_api_key,
                data=put_payload,
            )
        )
