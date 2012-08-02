# -*- coding: utf-8 -*-
import threading
import time
import copy
import traceback

from media.monitor.handler import ReportHandler
from media.monitor.events import NewFile, DeleteFile, ModifyFile
from media.monitor.log import Loggable
from media.monitor.listeners import FileMediator
from media.monitor.exceptions import BadSongFile
from media.monitor.pure import LazyProperty

import api_clients.api_client as ac

class RequestSync(threading.Thread,Loggable):
    def __init__(self, watcher, requests):
        threading.Thread.__init__(self)
        self.watcher = watcher
        self.requests = requests
        self.retries = 3
        self.request_wait = 0.3

    @LazyProperty
    def apiclient(self):
        return ac.AirtimeApiClient.create_right_config()

    def run(self):
        # TODO : implement proper request sending
        self.logger.info("launching request with %d items." % len(self.requests))
        # Note that we must attach the appropriate mode to every response. Also
        # Not forget to attach the 'is_record' to any requests that are related
        # to recorded shows
        # A simplistic request would like:
        # TODO : recorded shows aren't flagged right
        packed_requests = []
        for request in self.requests:
            try: packed_requests.append(request.pack())
            except BadSongFile as e:
                self.logger.info("Bad song file: '%s'" % e.path)
                self.logger.info("TODO : put in ignore list")
            except Exception as e:
                self.logger.info("An evil exception occured to packing '%s'" % request.path)
                self.logger.error( traceback.format_exc() )
        # Remove when finished debugging
        def send_one(x): self.apiclient.send_media_monitor_requests( [x] )
        def make_req(): self.apiclient.send_media_monitor_requests( packed_requests )
        for try_index in range(0,self.retries):
            try: make_req()
            except ValueError:
                self.logger.info("Api Controller is a piece of shit... will fix once I setup the damn debugger")
                self.logger.info("Trying again after %f seconds" % self.request_wait)
                time.sleep( self.request_wait )
            else:
                self.logger.info("Request worked on the '%d' try" % (try_index + 1))
                break
        else: self.logger.info("Failed to send request after '%d' tries..." % self.retries)
        self.logger.info("Now ignoring: %d files" % len(FileMediator.ignored_set))
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
                self.logger.info("We got %d events that are unflushed" % self.watcher.events_left_count())
                self.watcher.flush_events()

class WatchSyncer(ReportHandler,Loggable):
    def __init__(self, signal, chunking_number = 100, timeout=15):
        self.path = '' # TODO : get rid of this attribute everywhere
        #self.signal = signal
        self.timeout = float(timeout)
        self.chunking_number = chunking_number
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

    @property
    def target_path(self): return self.path

    def handle(self, sender, event):
        """We implement this abstract method from ReportHandler"""
        # TODO : more types of events need to be handled here
        if hasattr(event, 'pack'):
            # We push this event into queue
            self.logger.info("Received event '%s'. Path: '%s'" % ( "", getattr(event,'path','No path exists') ))
            try: self.push_queue( event )
            except BadSongFile as e:
                self.logger.info("...")
            except Exception as e:
                self.unexpected_exception(e)
        else:
            self.logger.info("Received event that cannot be packed. Printing its representation:")
            self.logger.info( repr(event) )

    def requests_left_count(self): return len(self.__requests)
    def events_left_count(self): return len(self.__queue)

    def push_queue(self, elem):
        self.logger.info("Added event into queue")
        if self.events_left_count() == self.chunking_number:
            self.push_request()
            self.request_do() # Launch the request if nothing is running
        self.__queue.append(elem)

    def flush_events(self):
        self.logger.info("Force flushing events...")
        self.push_request()
        self.request_do()

    def events_in_queue(self):
        """returns true if there are events in the queue that haven't been processed yet"""
        return len(self.__queue) > 0

    def requests_in_queue(self):
        return len(self.__requests) > 0

    def flag_done(self):
        """ called by request thread when it finishes operating """
        self.request_running = False
        self.__current_thread = None
        # This call might not be necessary but we would like
        # to get the ball running with the requests as soon as possible
        if self.requests_in_queue() > 0: self.request_do()

    def request_do(self):
        """ launches a request thread only if one is not running right now """
        if not self.request_running:
            self.request_running = True
            self.__requests.pop()()

    def push_request(self):
        self.logger.info("'%s' : Unleashing request" % self.target_path)
        # want to do request asyncly and empty the queue
        requests = copy.copy(self.__queue)
        def launch_request():
            # Need shallow copy here
            t = RequestSync(watcher=self, requests=requests)
            t.start()
            self.__current_thread = t
        self.__requests.append(launch_request)
        self.__queue = []

    def __del__(self):
        # Ideally we would like to do a little more to ensure safe shutdown
        if self.events_in_queue(): self.logger.warn("Terminating with events in the queue still pending...")
        if self.requests_in_queue(): self.logger.warn("Terminating with http requests still pending...")

