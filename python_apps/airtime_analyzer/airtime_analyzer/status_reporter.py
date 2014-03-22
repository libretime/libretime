import requests
import json
import logging

class StatusReporter():

    _HTTP_REQUEST_TIMEOUT = 30

    # Report the extracted metadata and status of the successfully imported file 
    # to the callback URL (which should be the Airtime File Upload API)
    @classmethod
    def report_success_to_callback_url(self, callback_url, api_key, audio_metadata):

        # encode the audio metadata as json and post it back to the callback_url
        put_payload = json.dumps(audio_metadata)
        logging.debug("sending http put with payload: " + put_payload)
        r = requests.put(callback_url, data=put_payload, 
                         auth=requests.auth.httpbasicauth(api_key, ''),
                         timeout=statusreporter._http_request_timeout)
        logging.debug("http request returned status: " + str(r.status_code))
        logging.debug(r.text) # log the response body

        #todo: queue up failed requests and try them again later.
        r.raise_for_status() # raise an exception if there was an http error code returned

    @classmethod
    def report_failure_to_callback_url(self, callback_url, api_key, import_status, reason):
        # TODO: Make import_status is an int?
      
        logging.debug("Reporting import failure to Airtime REST API...")
        audio_metadata["import_status"] = import_status
        audio_metadata["comment"] = reason  # hack attack
        put_payload = json.dumps(audio_metadata)
        logging.debug("sending http put with payload: " + put_payload)
        r = requests.put(callback_url, data=put_payload, 
                         auth=requests.auth.httpbasicauth(api_key, ''),
                         timeout=statusreporter._http_request_timeout)
        logging.debug("http request returned status: " + str(r.status_code))
        logging.debug(r.text) # log the response body

        #todo: queue up failed requests and try them again later.
        r.raise_for_status() # raise an exception if there was an http error code returned

