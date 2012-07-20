# -*- coding: utf-8 -*-
# testing ground for the script
import pyinotify
import time
import os
from media.monitor.listeners import OrganizeListener, StoreWatchListener
from media.monitor.organizer import Organizer
from media.monitor.events import PathChannel
from media.monitor.watchersyncer import WatchSyncer
from media.monitor.handler import ProblemFileHandler
from media.monitor.bootstrap import Bootstrapper
from media.monitor.log import get_logger
from media.monitor.syncdb import SyncDB
from api_clients import api_client as apc

# TODO : we should configure locale before doing anything here

channels = {
    # note that org channel still has a 'watch' path because that is the path
    # it supposed to be moving the organized files to. it doesn't matter where
    # are all the "to organize" files are coming from
    'org' : PathChannel('org', '/home/rudi/throwaway/fucking_around/organize'),
    'watch' : [],
    'badfile' : PathChannel('badfile', '/home/rudi/throwaway/fucking_around/problem_dir'),
}

log = get_logger()
apiclient = apc.AirtimeApiClient(log)
# We initialize sdb before anything because we must know what our watched
# directories are.
sdb = SyncDB(apc)
for watch_dir in sdb.list_directories():
    if not os.path.exists(watch_dir):
        # Create the watch_directory here
        try: os.makedirs(watch_dir)
        except Exception as e:
            log.error("Could not create watch directory: '%s' (given from the database)." % watch_dir)
    # We must do another existence check for the watched directory because we
    # the creation of it could have failed above
    if os.path.exists(watch_dir):
        channels['watch'].append(PathChannel('watch', watch_dir))

org = Organizer(channel=channels['org'],target_path=channels['watch'].path)
watches = [ WatchSyncer(channel=pc) for pc in channels['watch'] ]
problem_files = ProblemFileHandler(channel=channels['badfile'])

raw_bootstrap = apiclient.get_bootstrap_info()
print(raw_bootstrap)
# A slight incosistency here, channels['watch'] is already a list while the
# other items are single elements. For consistency we should make all the
# values in channels lists later on
# TODO : get the actual last running time instead of using the current time
# like now
bs = Bootstrapper(db=sdb, last_run=int(time.time()), org_channels=[channels['org']], watch_channels=channels['watch'])

bs.flush_organize()
bs.flush_watch()

# do the bootstrapping before any listening is going one
#conn = Connection('localhost', 'more', 'shit', 'here')
#db = DBDumper(conn).dump_block()
#bs = Bootstrapper(db, [channels['org']], [channels['watch']])
#bs.flush_organize()
#bs.flush_watch()

wm = pyinotify.WatchManager()

# Listeners don't care about which directory they're related to. All they care
# about is which signal they should respond to
o1 = OrganizeListener(signal=channels['org'].signal)
o2 = StoreWatchListener(signal=channels['watch'].signal)

notifier = pyinotify.Notifier(wm)
wdd1 = wm.add_watch(channels['org'].path, pyinotify.ALL_EVENTS, rec=True, auto_add=True, proc_fun=o1)
for pc in channels['watch']:
    wdd2 = wm.add_watch(pc.path, pyinotify.ALL_EVENTS, rec=True, auto_add=True, proc_fun=o2)

notifier.loop()

