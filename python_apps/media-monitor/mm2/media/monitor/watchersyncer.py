# -*- coding: utf-8 -*-
import time
import copy

from handler         import ReportHandler
from log             import Loggable
from exceptions      import BadSongFile
from eventcontractor import EventContractor
from events          import EventProxy
from request         import ThreadedRequestSync, RequestSync
from ..saas.thread   import InstanceInheritingThread, getsig

class TimeoutWatcher(InstanceInheritingThread,Loggable):
    """
    The job of this thread is to keep an eye on WatchSyncer and force a
    request whenever the requests go over time out
    """
    def __init__(self, watcher, timeout=5):
        self.logger.info("Created timeout thread...")
        super(TimeoutWatcher, self).__init__()
        self.watcher = watcher
        self.timeout = timeout

    def run(self):
        # We try to launch a new thread every self.timeout seconds
        # so that the people do not have to wait for the queue to fill up
        while True:
            time.sleep(self.timeout)
            # If there is any requests left we launch em. Note that this
            # isn't strictly necessary since RequestSync threads already
            # chain themselves
            if self.watcher.requests_in_queue():
                self.logger.info("We have %d requests waiting to be launched" %
                        self.watcher.requests_left_count())
                self.watcher.request_do()
            # Same for events, this behaviour is mandatory however.
            if self.watcher.events_in_queue():
                self.logger.info("We have %d events that are unflushed" %
                        self.watcher.events_left_count())
                self.watcher.flush_events()

class WatchSyncer(ReportHandler,Loggable):
    def __init__(self, signal, chunking_number = 100, timeout=15):
        self.timeout          = float(timeout)
        self.chunking_number  = int(chunking_number)
        self.request_running  = False
        self.__current_thread = None
        self.__requests       = []
        self.contractor       = EventContractor()
        self.__reset_queue()

        tc = TimeoutWatcher(self, self.timeout)
        tc.daemon = True
        tc.start()
        super(WatchSyncer, self).__init__(signal=getsig(signal))

    def handle(self, sender, event):
        """
        We implement this abstract method from ReportHandler
        """
        if hasattr(event, 'pack'):
            # We push this event into queue
            self.logger.info("Received event '%s'. Path: '%s'" % \
                    ( event.__class__.__name__,
                      getattr(event,'path','No path exists') ))
            try:
                # If there is a strange bug anywhere in the code the next line
                # should be a suspect
                ev = EventProxy(event)
                if self.contractor.register(ev): self.push_queue(ev)
                #self.push_queue( event )
            except BadSongFile as e:
                self.fatal_exception("Received bas song file '%s'" % e.path, e)
            except Exception as e: self.unexpected_exception(e)
        else:
            self.logger.info("Received event that does not implement packing.\
                    Printing its representation:")
            self.logger.info( repr(event) )

    def requests_left_count(self):
        """
        returns the number of requests left in the queue. requests are
        functions that create RequestSync threads
        """
        return len(self.__requests)
    def events_left_count(self):
        """
        Returns the number of events left in the queue to create a request
        """
        return len(self.__queue)

    def push_queue(self, elem):
        """
        Added 'elem' to the event queue and launch a request if we are
        over the the chunking number
        """
        self.logger.info("Added event into queue")
        if self.events_left_count() >= self.chunking_number:
            self.push_request()
            self.request_do() # Launch the request if nothing is running
        self.__queue.append(elem)

    def flush_events(self):
        """
        Force flush the current events held in the queue
        """
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
        """
        Returns true if there are any requests in the queue. False otherwise.
        """
        return len(self.__requests) > 0

    def flag_done(self):
        """
        called by request thread when it finishes operating
        """
        self.request_running = False
        self.__current_thread = None
        # This call might not be necessary but we would like to get the
        # ball running with the requests as soon as possible
        if self.requests_in_queue() > 0: self.request_do()

    def request_do(self):
        """
        launches a request thread only if one is not running right now
        """
        if not self.request_running:
            self.request_running = True
            self.__requests.pop()()

    def push_request(self):
        """
        Create a request from the current events in the queue and schedule it
        """
        self.logger.info("WatchSyncer : Unleashing request")
        # want to do request asyncly and empty the queue
        requests = copy.copy(self.__queue)
        def launch_request():
            # Need shallow copy here
            t = ThreadedRequestSync( RequestSync.create_with_api_client(
                watcher=self, requests=requests) )
            self.__current_thread = t
        self.__requests.append(launch_request)
        self.__reset_queue()

    def __reset_queue(self): self.__queue = []

    def __del__(self):
        #this destructor is completely untested and it's unclear whether
        #it's even doing anything useful. consider removing it
        if self.events_in_queue():
            self.logger.warn("Terminating with events still in the queue...")
        if self.requests_in_queue():
            self.logger.warn("Terminating with http requests still pending...")

