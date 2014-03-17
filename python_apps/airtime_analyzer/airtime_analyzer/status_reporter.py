import requests
import json
import logging

class StatusReporter():

    _HTTP_REQUEST_TIMEOUT = 30

    # Report the extracted metadata and status of the successfully imported file 
    # to the callback URL (which should be the Airtime File Upload API)
    @classmethod
    def report_success_to_callback_url(self, callback_url, api_key, audio_metadata):

        # Encode the audio metadata as JSON and post it back to the callback_url
        put_payload = json.dumps(audio_metadata)
        logging.debug("Sending HTTP PUT with payload: " + put_payload)
        r = requests.put(callback_url, data=put_payload, 
                         auth=requests.auth.HTTPBasicAuth(api_key, ''),
                         timeout=StatusReporter._HTTP_REQUEST_TIMEOUT)
        logging.debug("HTTP request returned status: " + str(r.status_code))
        logging.debug(r.text) # Log the response body

        #TODO: Queue up failed requests and try them again later.
        r.raise_for_status() # Raise an exception if there was an HTTP error code returned

    @classmethod
    def report_failure_to_callback_url(self, callback_url, api_key, error_status, reason):
        # TODO: Make error_status is an int?
        pass

