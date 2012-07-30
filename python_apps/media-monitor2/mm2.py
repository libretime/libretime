# -*- coding: utf-8 -*-
import sys
import os

from media.monitor.manager import Manager
from media.monitor.bootstrap import Bootstrapper
from media.monitor.log import get_logger
from media.monitor.events import PathChannel
from media.monitor.config import MMConfig
from media.monitor.toucher import ToucherThread
from media.monitor.syncdb import SyncDB
from media.monitor.exceptions import FailedToObtainLocale, FailedToSetLocale, NoConfigFile, FailedToCreateDir
from media.monitor.airtime import AirtimeNotifier, AirtimeMessageReceiver
from media.monitor.watchersyncer import WatchSyncer
from media.monitor.handler import ProblemFileHandler
from media.monitor.eventdrainer import EventDrainer
import media.monitor.pure as mmp

from api_clients import api_client as apc

# Execution consists of the following steps (for now)
# 1. Initialize logging
# 2. Create MMConfig from the config file
# 3. Configure the locale
# 4. Initialize all event handlers (WatchManager, OrganizeListener, etc.)
# 5. Get bootstrap db from airtime
# 6. Sync the db according to the filesystem (and vice versa in some cases)
# 7. Initialize listeners for watched and organize directories
# 8. Initialize kombu listener for receiving messages from airtime
# 9. Start the toucher thread that updates the last modified time of the index
#    file as the program is running

# Rewrite to use manager.Manager

log = get_logger()
global_config = u'/home/rudi/Airtime/python_apps/media-monitor2/tests/live_client.cfg'
# MMConfig is a proxy around ConfigObj instances. it does not allow itself
# users of MMConfig instances to modify any config options directly through the
# dictionary. Users of this object muse use the correct methods designated for
# modification
config = None
try: config = MMConfig(global_config)
except NoConfigFile as e:
    log.info("Cannot run mediamonitor2 without configuration file.")
    log.info("Current config path: '%s'" % global_config)
    sys.exit(1)
except Exception as e:
    log.info("Unknown error reading configuration file: '%s'" % global_config)
    log.info(str(e))

log.info("Attempting to set the locale...")
try:
    mmp.configure_locale(mmp.get_system_locale())
except FailedToSetLocale as e:
    log.info("Failed to set the locale...")
    sys.exit(1)
except FailedToObtainLocale as e:
    log.info("Failed to obtain the locale form the default path: '/etc/default/locale'")
    sys.exit(1)
except Exception as e:
    log.info("Failed to set the locale for unknown reason. Logging exception.")
    log.info(str(e))

watch_syncer = WatchSyncer(signal='watch',
                           chunking_number=config['chunking_number'],
                           timeout=config['request_max_wait'])
try:
    problem_handler = ProblemFileHandler( PathChannel(signal='badfile',path='/srv/airtime/stor/problem_files/') )
except FailedToCreateDir as e:
    log.info("Failed to create problem directory: '%s'" % e.path)

apiclient = apc.AirtimeApiClient.create_right_config(log=log,config_path=global_config)


# TODO : Need to do setup_media_monitor call somewhere around here to get
# import/organize dirs
sdb = SyncDB(apiclient)

manager = Manager()

store = apiclient.setup_media_monitor()
store = store[u'stor']

organize_dir, import_dir  = mmp.import_organize(store)
# Order matters here:
manager.set_store_path(import_dir)
manager.set_organize_path(organize_dir)

for watch_dir in sdb.list_directories():
    if not os.path.exists(watch_dir):
        # Create the watch_directory here
        try: os.makedirs(watch_dir)
        except Exception as e:
            log.error("Could not create watch directory: '%s' (given from the database)." % watch_dir)
    if os.path.exists(watch_dir):
        manager.add_watch_directory(watch_dir)

last_ran=config.last_ran()
bs = Bootstrapper( db=sdb, watch_signal='watch' )

bs.flush_all( config.last_ran() )

airtime_receiver = AirtimeMessageReceiver(config,manager)
airtime_notifier = AirtimeNotifier(config, airtime_receiver)

ed = EventDrainer(airtime_notifier.connection,interval=1)

# Launch the toucher that updates the last time when the script was ran every
# n seconds.
tt = ToucherThread(path=config['index_path'], interval=int(config['touch_interval']))

pyi = manager.pyinotify()
pyi.loop()
