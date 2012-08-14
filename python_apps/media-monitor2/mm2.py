# -*- coding: utf-8 -*-
import sys
import os
import logging
import logging.config

from media.monitor.manager          import Manager
from media.monitor.bootstrap        import Bootstrapper
from media.monitor.log              import get_logger, setup_logging
from media.monitor.config           import MMConfig
from media.monitor.toucher          import ToucherThread
from media.monitor.syncdb           import AirtimeDB
from media.monitor.exceptions       import FailedToObtainLocale, \
                                           FailedToSetLocale, \
                                           NoConfigFile
from media.monitor.airtime          import AirtimeNotifier, \
                                           AirtimeMessageReceiver
from media.monitor.watchersyncer    import WatchSyncer
from media.monitor.eventdrainer     import EventDrainer
from media.update.replaygainupdater import ReplayGainUpdater
from std_err_override               import LogWriter

import media.monitor.pure          as mmp
from api_clients import api_client as apc


def main(global_config, api_client_config, log_config,
        index_create_attempt=False):
    for cfg in [global_config, api_client_config]:
        if not os.path.exists(cfg): raise NoConfigFile(cfg)
    # MMConfig is a proxy around ConfigObj instances. it does not allow
    # itself users of MMConfig instances to modify any config options
    # directly through the dictionary. Users of this object muse use the
    # correct methods designated for modification
    try: config = MMConfig(global_config)
    except NoConfigFile as e:
        print("Cannot run mediamonitor2 without configuration file.")
        print("Current config path: '%s'" % global_config)
        sys.exit(1)
    except Exception as e:
        print("Unknown error reading configuration file: '%s'" % global_config)
        print(str(e))


    #logging.config.fileConfig(log_config)

    #need to wait for Python 2.7 for this..
    #logging.captureWarnings(True)

    logger = logging.getLogger()
    LogWriter.override_std_err(logger)
    logfile = unicode( config['logpath'] )
    setup_logging(logfile)
    log = get_logger()

    if not index_create_attempt:
        if not os.path.exists(config['index_path']):
            log.info("Attempting to create index file:...")
            try:
                f = open(config['index_path'])
                f.write(" ")
                f.close()
            except Exception as e:
                log.info("Failed to create index file with exception: %s" % str(e))
            else:
                log.info("Created index file, reloading configuration:")
                main( global_config,  api_client_config, log_config,
                        index_create_attempt=True )
    else:
        log.info("Already tried to create index. Will not try again ")

    if not os.path.exists(config['index_path']):
        log.info("Index file does not exist. Terminating")

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
        log.info("Failed to set the locale for unknown reason. \
                Logging exception.")
        log.info(str(e))

    watch_syncer = WatchSyncer(signal='watch',
                               chunking_number=config['chunking_number'],
                               timeout=config['request_max_wait'])

    apiclient = apc.AirtimeApiClient.create_right_config(log=log,
            config_path=api_client_config)

    ReplayGainUpdater.start_reply_gain(apiclient)

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

    bs = Bootstrapper( db=sdb, watch_signal='watch' )

    ed = EventDrainer(airtime_notifier.connection,
            interval=float(config['rmq_event_wait']))

    # Launch the toucher that updates the last time when the script was
    # ran every n seconds.
    tt = ToucherThread(path=config['index_path'],
                       interval=int(config['touch_interval']))

    pyi = manager.pyinotify()
    pyi.loop()

__doc__ = """
Usage:
    mm2.py --config=<path> --apiclient=<path> --log=<path>

Options:
    -h --help          Show this screen
    --config=<path>    path to mm2 config
    --apiclient=<path> path to apiclient config
    --log=<path>       log config at <path>
"""

    #original debugging paths
    #base_path = u'/home/rudi/Airtime/python_apps/media-monitor2/tests'
    #global_config = os.path.join(base_path, u'live_client.cfg')
    #api_client_config = global_config

if __name__ == '__main__':
    from docopt import docopt
    args = docopt(__doc__,version="mm1.99")
    for k in ['--apiclient','--config','--log']:
        if not os.path.exists(args[k]):
            print("'%s' must exist" % args[k])
            sys.exit(0)
    print("Running mm1.99")
    main(args['--config'],args['--apiclient'],args['--log'])
