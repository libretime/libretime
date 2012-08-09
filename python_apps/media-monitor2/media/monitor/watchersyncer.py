# -*- coding: utf-8 -*-
import threading
import time
import copy
import traceback

from media.monitor.handler import ReportHandler
from media.monitor.log import Loggable
from media.monitor.exceptions import BadSongFile
from media.monitor.pure import LazyProperty

import api_clients.api_client as ac

class RequestSync(threading.Thread,Loggable):
    def __init__(self, watcher, requests):
        threading.Thread.__init__(self)
        self.watcher = watcher
        self.requests = requests
        self.retries = 1
        self.request_wait = 0.3

    @LazyProperty
    def apiclient(self):
        return ac.AirtimeApiClient.create_right_config()

    def run(self):
        self.logger.info("Attempting request with %d items." %
                len(self.requests))
        # Note that we must attach the appropriate mode to every response. Also
        # Not forget to attach the 'is_record' to any requests that are related
        # to recorded shows
        # TODO : recorded shows aren't flagged right
        # Is this retry shit even necessary? Consider getting rid of this.
        def make_req():
            self.apiclient.send_media_monitor_requests( self.requests )
        for try_index in range(0,self.retries):
            try: make_req()
            # most likely we did not get json response as we expected
            except ValueError:
                self.logger.info("ApiController.php probably crashed, we \
                        diagnose this from the fact that it did not return \
                        valid json")
                self.logger.info("Trying again after %f seconds" %
                        self.request_wait)
                time.sleep( self.request_wait )
            except Exception as e: self.unexpected_exception(e)
            else:
                self.logger.info("Request worked on the '%d' try" %
                        (try_index + 1))
                break
        else: self.logger.info("Failed to send request after '%d' tries..." %
                self.retries)
        self.watcher.flag_done()

class TimeoutWatcher(threading.Thread,Loggable):
    def __init__(self, watcher, timeout=5):
        self.logger.info("Created timeout thread...")
        threading.Thread.__init__(self)
        self.watcher = watcher
        self.timeout = timeout

    def run(self):
        # We try to launch a new thread every self.timeout seconds
        # so that the people do not have to wait for the queue to fill up
        while True:
            time.sleep(self.timeout)
            # If there is any requests left we launch em.
            # Note that this isn't strictly necessary since RequestSync threads
            # already chain themselves
            if self.watcher.requests_in_queue():
                self.logger.info("We got %d requests waiting to be launched" %
                        self.watcher.requests_left_count())
                self.watcher.request_do()
            # Same for events, this behaviour is mandatory however.
            if self.watcher.events_in_queue():
                self.logger.info("We got %d events that are unflushed" %
                        self.watcher.events_left_count())
                self.watcher.flush_events()

class WatchSyncer(ReportHandler,Loggable):
    def __init__(self, signal, chunking_number = 100, timeout=15):
        #self.signal = signal
        self.timeout = float(timeout)
        self.chunking_number = int(chunking_number)
        self.__queue = []
        # Even though we are not blocking on the http requests, we are still
        # trying to send the http requests in order
        self.__requests = []
        self.request_running = False
        # we don't actually use this "private" instance variable anywhere
        self.__current_thread = None
        tc = TimeoutWatcher(self, self.timeout)
        tc.daemon = True
        tc.start()
        super(WatchSyncer, self).__init__(signal=signal)

    def handle(self, sender, event):
        """
        We implement this abstract method from ReportHandler
        """
        if hasattr(event, 'pack'):
            # We push this event into queue
            self.logger.info("Received event '%s'. Path: '%s'" % \
                    ( event.__class__.__name__,
                      getattr(event,'path','No path exists') ))
            try: self.push_queue( event )
            except BadSongFile as e:
                self.fatal_exception("Received bas song file '%s'" % e.path, e)
            except Exception as e:
                self.unexpected_exception(e)
        else:
            self.logger.info("Received event that does not implement packing.\
                    Printing its representation:")
            self.logger.info( repr(event) )

    def requests_left_count(self): return len(self.__requests)
    def events_left_count(self): return len(self.__queue)

    def push_queue(self, elem):
        self.logger.info("Added event into queue")
        if self.events_left_count() >= self.chunking_number:
            self.push_request()
            self.request_do() # Launch the request if nothing is running
        self.__queue.append(elem)

    def flush_events(self):
        self.logger.info("Force flushing events...")
        self.push_request()
        self.request_do()

    def events_in_queue(self):
        """
        returns true if there are events in the queue that haven't been
        processed yet
        """
        return len(self.__queue) > 0

    def requests_in_queue(self):
        return len(self.__requests) > 0

    def flag_done(self):
        """
        called by request thread when it finishes operating
        """
        self.request_running = False
        self.__current_thread = None
        # This call might not be necessary but we would like
        # to get the ball running with the requests as soon as possible
        if self.requests_in_queue() > 0: self.request_do()

    def request_do(self):
        """
        launches a request thread only if one is not running right now
        """
        if not self.request_running:
            self.request_running = True
            self.__requests.pop()()

    def push_request(self):
        self.logger.info("WatchSyncer : Unleashing request")
        # want to do request asyncly and empty the queue
        requests = copy.copy(self.__queue)
        packed_requests = []
        for request_event in requests:
            try:
                for request in request_event.safe_pack():
                    if isinstance(request, BadSongFile):
                        self.logger.info("Bad song file: '%s'" % request.path)
                    else: packed_requests.append(request)
            except BadSongFile as e:
                self.logger.info("This should never occur anymore!!!")
                self.logger.info("Bad song file: '%s'" % e.path)
            except Exception as e:
                self.logger.info("An evil exception occured")
                self.logger.error( traceback.format_exc() )
        def launch_request():
            # Need shallow copy here
            t = RequestSync(watcher=self, requests=packed_requests)
            t.start()
            self.__current_thread = t
        self.__requests.append(launch_request)
        self.__queue = []

    def __del__(self):
        # Ideally we would like to do a little more to ensure safe shutdown
        if self.events_in_queue():
            self.logger.warn("Terminating with events still in the queue...")
        if self.requests_in_queue():
            self.logger.warn("Terminating with http requests still pending...")

