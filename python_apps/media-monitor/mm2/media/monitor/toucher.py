# -*- coding: utf-8 -*-
import pure         as mmp
import os
from log            import Loggable
from exceptions     import CouldNotCreateIndexFile
from ..saas.thread  import InstanceInheritingThread

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

import time

class RepeatTimer(InstanceInheritingThread):
    def __init__(self, interval, callable, *args, **kwargs):
        super(RepeatTimer, self).__init__()
        self.interval = interval
        self.callable = callable
        self.args = args
        self.kwargs = kwargs
    def run(self):
        while True:
            time.sleep(self.interval)
            self.callable(*self.args, **self.kwargs)

class ToucherThread(Loggable):
    """ Creates a thread that touches a file 'path' every 'interval'
    seconds """
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

