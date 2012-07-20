# -*- coding: utf-8 -*-
# testing ground for the script
import pyinotify
import time
from media.monitor.listeners import OrganizeListener, StoreWatchListener
from media.monitor.organizer import Organizer
from media.monitor.events import PathChannel
from media.monitor.watchersyncer import WatchSyncer
from media.monitor.handler import ProblemFileHandler
from media.monitor.bootstrap import Bootstrapper
from media.monitor.log import get_logger
from media.monitor.syncdb import SyncDB
from api_clients import api_client as apc

channels = {
    # note that org channel still has a 'watch' path because that is the path
    # it supposed to be moving the organized files to. it doesn't matter where
    # are all the "to organize" files are coming from
    'org' : PathChannel('org', '/home/rudi/throwaway/fucking_around/organize'),
    'watch' : PathChannel('watch', '/home/rudi/throwaway/fucking_around/watch'),
    'badfile' : PathChannel('badfile', '/home/rudi/throwaway/fucking_around/problem_dir'),
}

org = Organizer(channel=channels['org'],target_path=channels['watch'].path)
watch = WatchSyncer(channel=channels['watch'])
problem_files = ProblemFileHandler(channel=channels['badfile'])

apiclient = apc.AirtimeApiClient(get_logger())
raw_bootstrap = apiclient.get_bootstrap_info()
print(raw_bootstrap)
bs = Bootstrapper(db=bootstrap_db, last_run=int(time.time()), org_channels=[channels['org']], watch_channels=[channels['watch']])

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
wdd2 = wm.add_watch(channels['watch'].path, pyinotify.ALL_EVENTS, rec=True, auto_add=True, proc_fun=o2)

notifier.loop()

