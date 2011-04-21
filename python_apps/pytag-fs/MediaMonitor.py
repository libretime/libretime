import os
import pyinotify
from pyinotify import WatchManager, Notifier, ThreadedNotifier, EventsCodes, ProcessEvent

# configure logging
try:
    logging.config.fileConfig("logging.cfg")
except Exception, e:
    print 'Error configuring logging: ', e
    sys.exit()

# loading config file
try:
    config = ConfigObj('/etc/airtime/recorder.cfg')
except Exception, e:
    print 'Error loading config file: ', e
    sys.exit()

# watched events
mask = pyinotify.ALL_EVENTS

wm = WatchManager()
wdd = wm.add_watch('/srv/airtime/stor', mask, rec=True) 

class PTmp(ProcessEvent):
    def process_IN_CREATE(self, event):
        if event.dir :
            global wm
            wdd = wm.add_watch(event.pathname, mask, rec=True)
            #print wdd.keys()
        
        print "%s: %s" %  (event.maskname, os.path.join(event.path, event.name))

    def process_IN_MODIFY(self, event):
        if not event.dir :
            print event.path            

        print "%s: %s" %  (event.maskname, os.path.join(event.path, event.name))

    def process_default(self, event):
        print "%s: %s" %  (event.maskname, os.path.join(event.path, event.name))

if __name__ == '__main__':

    try:
        notifier = Notifier(wm, PTmp(), read_freq=2, timeout=1)
        notifier.coalesce_events()
        notifier.loop()
    except KeyboardInterrupt:
        notifier.stop()


