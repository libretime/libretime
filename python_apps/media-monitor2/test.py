# testing ground for the script
import pyinotify
from pydispatch import dispatcher
from media.monitor.listeners import OrganizeListener, StoreWatchListener
from media.monitor.organizer import Organizer


wm = pyinotify.WatchManager()
o1 = OrganizeListener(signal='org')
o2 = StoreWatchListener(signal='watch')
notifier = pyinotify.Notifier(wm)
wdd1 = wm.add_watch('/home/rudi/throwaway/fucking_around/organize', pyinotify.ALL_EVENTS, rec=True, auto_add=True, proc_fun=o1)
wdd2 = wm.add_watch('/home/rudi/throwaway/fucking_around/watch', pyinotify.ALL_EVENTS, rec=True, auto_add=True, proc_fun=o2)

def watch_event(sender, event):
    print("Watch: Was sent by %s with %s" % (sender, event))

org = Organizer(signal='org', target='/home/rudi/throwaway/fucking_around/watch')
dispatcher.connect(watch_event, signal='watch', sender=dispatcher.Any)

notifier.loop()

