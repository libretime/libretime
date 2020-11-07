import requests
import json
import logging
import collections
import queue
import time
import traceback
import pickle
import threading 
from urllib.parse import urlparse

# Disable urllib3 warnings because these can cause a rare deadlock due to Python 2's crappy internal non-reentrant locking
# around POSIX stuff. See SAAS-714. The hasattr() is for compatibility with older versions of requests.
if hasattr(requests, 'packages'):
    requests.packages.urllib3.disable_warnings()

class PicklableHttpRequest:
    def __init__(self, method, url, data, api_key):
        self.method = method
        self.url = url
        self.data = data
        self.api_key = api_key

    def create_request(self):
        return requests.Request(method=self.method, url=self.url, data=self.data,
                                auth=requests.auth.HTTPBasicAuth(self.api_key, ''))

def process_http_requests(ipc_queue, http_retry_queue_path):
    ''' Runs in a separate thread and performs all the HTTP requests where we're
        reporting extracted audio file metadata or errors back to the Airtime web application.

        This process also checks every 5 seconds if there's failed HTTP requests that we 
        need to retry. We retry failed HTTP requests so that we don't lose uploads if the 
        web server is temporarily down.

    '''

    # Store any failed requests (eg. due to web server errors or downtime) to be
    # retried later:
    retry_queue = collections.deque()
    shutdown = False

    # Unpickle retry_queue from disk so that we won't have lost any uploads
    # if airtime_analyzer is shut down while the web server is down or unreachable,
    # and there were failed HTTP requests pending, waiting to be retried.
    try:
        with open(http_retry_queue_path, 'rb') as pickle_file:
            retry_queue = pickle.load(pickle_file)
    except IOError as e:
        if e.errno == 2:
            pass
        else:
            raise e
    except Exception as e:
        # If we fail to unpickle a saved queue of failed HTTP requests, then we'll just log an error
        # and continue because those HTTP requests are lost anyways. The pickled file will be
        # overwritten the next time the analyzer is shut down too.
        logging.error("Failed to unpickle %s. Continuing..." % http_retry_queue_path)
        pass

    while True:
        try:
            while not shutdown:
                try:
                    request = ipc_queue.get(block=True, timeout=5)
                    if isinstance(request, str) and request == "shutdown": # Bit of a cheat
                        shutdown = True
                        break
                    if not isinstance(request, PicklableHttpRequest):
                        raise TypeError("request must be a PicklableHttpRequest. Was of type " + type(request).__name__)
                except queue.Empty:
                    request = None

                # If there's no new HTTP request we need to execute, let's check our "retry
                # queue" and see if there's any failed HTTP requests we can retry:
                if request:
                    send_http_request(request, retry_queue)
                else:
                    # Using a for loop instead of while so we only iterate over all the requests once!
                    for i in range(len(retry_queue)):
                        request = retry_queue.popleft()
                        send_http_request(request, retry_queue)

            logging.info("Shutting down status_reporter")
            # Pickle retry_queue to disk so that we don't lose uploads if we're shut down while
            # while the web server is down or unreachable.
            with open(http_retry_queue_path, 'wb') as pickle_file:
                pickle.dump(retry_queue, pickle_file)
            return
        except Exception as e: # Terrible top-level exception handler to prevent the thread from dying, just in case.
            if shutdown:
                return
            logging.exception("Unhandled exception in StatusReporter")
            logging.exception(e)
            logging.info("Restarting StatusReporter thread")
            time.sleep(2) # Throttle it


def send_http_request(picklable_request, retry_queue):
    if not isinstance(picklable_request, PicklableHttpRequest):
        raise TypeError("picklable_request must be a PicklableHttpRequest. Was of type " + type(picklable_request).__name__)
    try: 
        bare_request = picklable_request.create_request()
        s = requests.Session()
        prepared_request = s.prepare_request(bare_request)
        r = s.send(prepared_request, timeout=StatusReporter._HTTP_REQUEST_TIMEOUT, verify=False) # SNI is a pain in the ass
        r.raise_for_status() # Raise an exception if there was an http error code returned
        logging.info("HTTP request sent successfully.")
    except requests.exceptions.HTTPError as e:
        if e.response.status_code == 422:
            # Do no retry the request if there was a metadata validation error
            logging.error("HTTP request failed due to an HTTP exception. Exception was: %s" % str(e))
        else:
            # The request failed with an error 500 probably, so let's check if Airtime and/or
            # the web server are broken. If not, then our request was probably causing an
            # error 500 in the media API (ie. a bug), so there's no point in retrying it.
            logging.error("HTTP request failed. Exception was: %s" % str(e))
            parsed_url = urlparse(e.response.request.url)
            if is_web_server_broken(parsed_url.scheme + "://" + parsed_url.netloc):
                # If the web server is having problems, retry the request later:
                retry_queue.append(picklable_request)
                # Otherwise, if the request was bad, the request is never retried.
                # You will have to find these bad requests in logs or you'll be
                # notified by sentry.
    except requests.exceptions.ConnectionError as e:
        logging.error("HTTP request failed due to a connection error. Retrying later. %s" % str(e))
        retry_queue.append(picklable_request) # Retry it later
    except Exception as e:
        logging.error("HTTP request failed with unhandled exception. %s" % str(e))
        logging.error(traceback.format_exc())
        # Don't put the request into the retry queue, just give up on this one.
        # I'm doing this to protect against us getting some pathological request
        # that breaks our code. I don't want us pickling data that potentially
        # breaks airtime_analyzer.

def is_web_server_broken(url):
    ''' Do a naive test to check if the web server we're trying to access is down.
        We use this to try to differentiate between error 500s that are coming
        from (for example) a bug in the Airtime Media REST API and error 500s 
        caused by Airtime or the webserver itself being broken temporarily.
    '''
    try:
        test_req = requests.get(url, verify=False)
        test_req.raise_for_status()
    except Exception as e:
        return True
    else:
        # The request worked fine, so the web server and Airtime are still up.
        return False 
    return False


class StatusReporter():
    ''' Reports the extracted audio file metadata and job status back to the
        Airtime web application.
    '''
    _HTTP_REQUEST_TIMEOUT = 30
    
    ''' We use multiprocessing.Process again here because we need a thread for this stuff
        anyways, and Python gives us process isolation for free (crash safety).
    '''
    _ipc_queue = queue.Queue()
    #_http_thread = multiprocessing.Process(target=process_http_requests,
    #                        args=(_ipc_queue,))
    _http_thread = None

    @classmethod
    def start_thread(self, http_retry_queue_path):
        StatusReporter._http_thread = threading.Thread(target=process_http_requests,
                                args=(StatusReporter._ipc_queue,http_retry_queue_path))
        StatusReporter._http_thread.start()

    @classmethod
    def stop_thread(self):
        logging.info("Terminating status_reporter process")
        #StatusReporter._http_thread.terminate() # Triggers SIGTERM on the child process
        StatusReporter._ipc_queue.put("shutdown") # Special trigger
        StatusReporter._http_thread.join()

    @classmethod
    def _send_http_request(self, request):
        StatusReporter._ipc_queue.put(request)

    @classmethod
    def report_success_to_callback_url(self, callback_url, api_key, audio_metadata):
        ''' Report the extracted metadata and status of the successfully imported file 
            to the callback URL (which should be the Airtime File Upload API)
        '''
        put_payload = json.dumps(audio_metadata)
        #r = requests.Request(method='PUT', url=callback_url, data=put_payload, 
        #                     auth=requests.auth.HTTPBasicAuth(api_key, ''))
        '''
        r = requests.Request(method='PUT', url=callback_url, data=put_payload, 
                             auth=requests.auth.HTTPBasicAuth(api_key, ''))

        StatusReporter._send_http_request(r)
        '''

        StatusReporter._send_http_request(PicklableHttpRequest(method='PUT', url=callback_url, 
                                            data=put_payload, api_key=api_key))

        '''
        try:
            r.raise_for_status() # Raise an exception if there was an http error code returned
        except requests.exceptions.RequestException:
            StatusReporter._ipc_queue.put(r.prepare())
        '''

        ''' 
        # Encode the audio metadata as json and post it back to the callback_url
        put_payload = json.dumps(audio_metadata)
        logging.debug("sending http put with payload: " + put_payload)
        r = requests.put(callback_url, data=put_payload, 
                         auth=requests.auth.HTTPBasicAuth(api_key, ''),
                         timeout=StatusReporter._HTTP_REQUEST_TIMEOUT)
        logging.debug("HTTP request returned status: " + str(r.status_code))
        logging.debug(r.text) # log the response body

        #TODO: queue up failed requests and try them again later.
        r.raise_for_status() # Raise an exception if there was an http error code returned
        '''

    @classmethod
    def report_failure_to_callback_url(self, callback_url, api_key, import_status, reason):
        if not isinstance(import_status, int ):
            raise TypeError("import_status must be an integer. Was of type " + type(import_status).__name__)

        logging.debug("Reporting import failure to Airtime REST API...")
        audio_metadata = dict()
        audio_metadata["import_status"] = import_status
        audio_metadata["comment"] = reason  # hack attack
        put_payload = json.dumps(audio_metadata)
        #logging.debug("sending http put with payload: " + put_payload)
        '''
        r = requests.put(callback_url, data=put_payload, 
                         auth=requests.auth.HTTPBasicAuth(api_key, ''),
                         timeout=StatusReporter._HTTP_REQUEST_TIMEOUT)
        '''
        StatusReporter._send_http_request(PicklableHttpRequest(method='PUT', url=callback_url, 
                                          data=put_payload, api_key=api_key))
        '''
        logging.debug("HTTP request returned status: " + str(r.status_code))
        logging.debug(r.text) # log the response body

        #TODO: queue up failed requests and try them again later.
        r.raise_for_status() # raise an exception if there was an http error code returned
        '''

