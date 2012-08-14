# -*- coding: utf-8 -*-
import media.monitor.pure as mmp
import os
from media.monitor.log        import Loggable
from media.monitor.exceptions import CouldNotCreateIndexFile

class Toucher(Loggable):
    """
    Class responsible for touching a file at a certain path when called
    """
    def __init__(self,path):
        self.path = path
    def __call__(self):
        try: mmp.fondle(self.path)
        except Exception as e:
            self.logger.info("Failed to touch file: '%s'. Logging exception." %
                    self.path)
            self.logger.info(str(e))

#http://code.activestate.com/lists/python-ideas/8982/
from datetime import datetime

import threading

class RepeatTimer(threading.Thread):
    def __init__(self, interval, callable, args=[], kwargs={}):
        threading.Thread.__init__(self)
        # interval_current shows number of milliseconds in currently triggered
        # <tick>
        self.interval_current = interval
        # interval_new shows number of milliseconds for next <tick>
        self.interval_new = interval
        self.callable = callable
        self.args = args
        self.kwargs = kwargs
        self.event = threading.Event()
        self.event.set()
        self.activation_dt = None
        self.__timer = None

    def run(self):
        while self.event.is_set():
            self.activation_dt = datetime.utcnow()
            self.__timer = threading.Timer(self.interval_new,
                    self.callable,
                    self.args,
                    self.kwargs)
            self.interval_current = self.interval_new
            self.__timer.start()
            self.__timer.join()

    def cancel(self):
        self.event.clear()
        if self.__timer is not None:
            self.__timer.cancel()

    def trigger(self):
        self.callable(*self.args, **self.kwargs)
        if self.__timer is not None:
            self.__timer.cancel()

    def change_interval(self, value):
        self.interval_new = value


class ToucherThread(Loggable):
    """
    Creates a thread that touches a file 'path' every 'interval' seconds
    """
    def __init__(self, path, interval=5):
        if not os.path.exists(path):
            try:
                # TODO : rewrite using with?
                f = open(path,'w')
                f.write('')
                f.close()
            except Exception as e:
                raise CouldNotCreateIndexFile(path,e)
        cb = Toucher(path)
        t = RepeatTimer(interval, cb)
        t.daemon = True # thread terminates once process is done
        t.start()

