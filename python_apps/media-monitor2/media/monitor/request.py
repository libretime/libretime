# -*- coding: utf-8 -*-

import threading

from media.monitor.exceptions import BadSongFile
from media.monitor.log        import Loggable
import api_clients.api_client as ac

class ThreadedRequestSync(threading.Thread, Loggable):
    def __init__(self, rs):
        threading.Thread.__init__(self)
        self.rs = rs
        self.daemon = True
        self.start()

    def run(self):
        self.rs.run_request()

class RequestSync(Loggable):
    """
    This class is responsible for making the api call to send a request
    to airtime. In the process it packs the requests and retries for
    some number of times
    """
    @classmethod
    def create_with_api_client(cls, watcher, requests):
        apiclient = ac.AirtimeApiClient.create_right_config()
        self = cls(watcher, requests, apiclient)
        return self

    def __init__(self, watcher, requests, apiclient):
        self.watcher   = watcher
        self.requests  = requests
        self.apiclient = apiclient

    def run_request(self):
        self.logger.info("Attempting request with %d items." %
                len(self.requests))
        packed_requests = []
        for request_event in self.requests:
            try:
                for request in request_event.safe_pack():
                    if isinstance(request, BadSongFile):
                        self.logger.info("Bad song file: '%s'" % request.path)
                    else: packed_requests.append(request)
            except Exception as e:
                self.unexpected_exception( e )
                if hasattr(request_event, 'path'):
                    self.logger.info("Possibly related to path: '%s'" %
                            request_event.path)
        try: self.apiclient.send_media_monitor_requests( packed_requests )
        # most likely we did not get json response as we expected
        except ValueError:
            self.logger.info("ApiController.php probably crashed, we \
                    diagnose this from the fact that it did not return \
                    valid json")
            self.logger.info("Trying again after %f seconds" %
                    self.request_wait)
        except Exception as e: self.unexpected_exception(e)
        else: self.logger.info("Request was successful")
        self.watcher.flag_done() # poor man's condition variable

