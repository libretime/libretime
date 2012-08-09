# -*- coding: utf-8 -*-
import sys
import os

from media.monitor.manager import Manager
from media.monitor.bootstrap import Bootstrapper
from media.monitor.log import get_logger, setup_logging
from media.monitor.config import MMConfig
from media.monitor.toucher import ToucherThread
from media.monitor.syncdb import AirtimeDB
from media.monitor.exceptions import FailedToObtainLocale, FailedToSetLocale, \
                                     NoConfigFile
from media.monitor.airtime import AirtimeNotifier, AirtimeMessageReceiver
from media.monitor.watchersyncer import WatchSyncer
from media.monitor.eventdrainer import EventDrainer
from media.update.replaygainupdater import ReplayGainUpdater
import media.monitor.pure as mmp

from api_clients import api_client as apc

base_path = u'/home/rudi/Airtime/python_apps/media-monitor2/tests'
global_config = os.path.join(base_path,u'live_client.cfg')
api_client_config = global_config

# MMConfig is a proxy around ConfigObj instances. it does not allow itself
# users of MMConfig instances to modify any config options directly through the
# dictionary. Users of this object muse use the correct methods designated for
# modification
try: config = MMConfig(global_config)
except NoConfigFile as e:
    print("Cannot run mediamonitor2 without configuration file.")
    print("Current config path: '%s'" % global_config)
    sys.exit(1)
except Exception as e:
    print("Unknown error reading configuration file: '%s'" % global_config)
    print(str(e))

logfile = unicode( config['logpath'] )
setup_logging(logfile)
log = get_logger()
log.info("Attempting to set the locale...")

try:
    mmp.configure_locale(mmp.get_system_locale())
except FailedToSetLocale as e:
    log.info("Failed to set the locale...")
    sys.exit(1)
except FailedToObtainLocale as e:
    log.info("Failed to obtain the locale form the default path: \
            '/etc/default/locale'")
    sys.exit(1)
except Exception as e:
    log.info("Failed to set the locale for unknown reason. Logging exception.")
    log.info(str(e))

watch_syncer = WatchSyncer(signal='watch',
                           chunking_number=config['chunking_number'],
                           timeout=config['request_max_wait'])

apiclient = apc.AirtimeApiClient.create_right_config(log=log,
        config_path=api_client_config)

ReplayGainUpdater.start_reply_gain(apiclient)

# TODO : Need to do setup_media_monitor call somewhere around here to get
# import/organize dirs
sdb = AirtimeDB(apiclient)

manager = Manager()

airtime_receiver = AirtimeMessageReceiver(config,manager)
airtime_notifier = AirtimeNotifier(config, airtime_receiver)

store = apiclient.setup_media_monitor()
airtime_receiver.change_storage({ 'directory':store[u'stor'] })

for watch_dir in store[u'watched_dirs']:
    if not os.path.exists(watch_dir):
        # Create the watch_directory here
        try: os.makedirs(watch_dir)
        except Exception as e:
            log.error("Could not create watch directory: '%s' \
                    (given from the database)." % watch_dir)
    if os.path.exists(watch_dir):
        airtime_receiver.new_watch({ 'directory':watch_dir })

last_ran=config.last_ran()
bs = Bootstrapper( db=sdb, watch_signal='watch' )

#bs.flush_all( config.last_ran() )

ed = EventDrainer(airtime_notifier.connection,
        interval=float(config['rmq_event_wait']))

# Launch the toucher that updates the last time when the script was ran every n
# seconds.
tt = ToucherThread(path=config['index_path'],
        interval=int(config['touch_interval']))

pyi = manager.pyinotify()
pyi.loop()
