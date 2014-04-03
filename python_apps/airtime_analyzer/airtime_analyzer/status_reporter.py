import requests
import json
import logging

class StatusReporter():
    ''' Reports the extracted audio file metadata and job status back to the
        Airtime web application.
    '''
    _HTTP_REQUEST_TIMEOUT = 30

    @classmethod
    def report_success_to_callback_url(self, callback_url, api_key, audio_metadata):
        ''' Report the extracted metadata and status of the successfully imported file 
            to the callback URL (which should be the Airtime File Upload API)
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

    @classmethod
    def report_failure_to_callback_url(self, callback_url, api_key, import_status, reason):
        if not isinstance(import_status, (int, long) ):
            raise TypeError("import_status must be an integer. Was of type " + type(import_status).__name__)

        logging.debug("Reporting import failure to Airtime REST API...")
        audio_metadata = dict()
        audio_metadata["import_status"] = import_status
        audio_metadata["comment"] = reason  # hack attack
        put_payload = json.dumps(audio_metadata)
        logging.debug("sending http put with payload: " + put_payload)
        r = requests.put(callback_url, data=put_payload, 
                         auth=requests.auth.HTTPBasicAuth(api_key, ''),
                         timeout=StatusReporter._HTTP_REQUEST_TIMEOUT)
        logging.debug("HTTP request returned status: " + str(r.status_code))
        logging.debug(r.text) # log the response body

        #TODO: queue up failed requests and try them again later.
        r.raise_for_status() # raise an exception if there was an http error code returned

