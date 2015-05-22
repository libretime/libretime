import os, sys
import logging
import logging.config

from ..monitor               import pure as mmp

from ..monitor.exceptions    import FailedToObtainLocale, FailedToSetLocale
from ..monitor.log           import get_logger, setup_logging
from std_err_override            import LogWriter
from ..saas.thread           import InstanceThread, user, apc, getsig
from ..monitor.log           import Loggable
from ..monitor.exceptions    import CouldNotCreateIndexFile
from ..monitor.toucher       import ToucherThread
from ..monitor.airtime       import AirtimeNotifier, AirtimeMessageReceiver
from ..monitor.watchersyncer import WatchSyncer
from ..monitor.eventdrainer  import EventDrainer
from ..monitor.manager       import Manager
from ..monitor.syncdb        import AirtimeDB
from airtimeinstance  import AirtimeInstance

class MM2(InstanceThread, Loggable):

    def index_create(self, index_create_attempt=False):
        config = user().mm_config
        if not index_create_attempt:
            if not os.path.exists(config['media-monitor']['index_path']):
                self.logger.info("Attempting to create index file:...")
                try:
                    with open(config['media-monitor']['index_path'], 'w') as f: f.write(" ")
                except Exception as e:
                    self.logger.info("Failed to create index file with exception: %s" \
                             % str(e))
                else:
                    self.logger.info("Created index file, reloading configuration:")
                    self.index_create(index_create_attempt=True)
        else:
            self.logger.info("Already tried to create index. Will not try again ")

        if not os.path.exists(config['media-monitor']['index_path']):
            raise CouldNotCreateIndexFile(config['media-monitor']['index_path'])

    def run(self):
        self.index_create()
        manager = Manager()
        apiclient = apc()
        config = user().mm_config
        WatchSyncer(signal=getsig('watch'),
                chunking_number=config['media-monitor']['chunking_number'],
                timeout=config['media-monitor']['request_max_wait'])
        airtime_receiver = AirtimeMessageReceiver(config,manager)
        airtime_notifier = AirtimeNotifier(config, airtime_receiver)


        adb = AirtimeDB(apiclient)
        store = {
                u'stor' : adb.storage_path(),
                u'watched_dirs' : adb.list_watched(),
        }

        self.logger.info("initializing mm with directories: %s" % str(store))

        self.logger.info(
                "Initing with the following airtime response:%s" % str(store))

        airtime_receiver.change_storage({ 'directory':store[u'stor'] })

        for watch_dir in store[u'watched_dirs']:
            if not os.path.exists(watch_dir):
                # Create the watch_directory here
                try: os.makedirs(watch_dir)
                except Exception:
                    self.logger.error("Could not create watch directory: '%s' \
                            (given from the database)." % watch_dir)
            if os.path.exists(watch_dir):
                airtime_receiver.new_watch({ 'directory':watch_dir }, restart=True)
            else: self.logger.info("Failed to add watch on %s" % str(watch_dir))

        EventDrainer(airtime_notifier,
                interval=float(config['media-monitor']['rmq_event_wait']))

        # Launch the toucher that updates the last time when the script was
        # ran every n seconds.
        # TODO : verify that this does not interfere with bootstrapping because the
        # toucher thread might update the last_ran variable too fast
        ToucherThread(path=user().touch_file_path(),
                interval=int(config['media-monitor']['touch_interval']))

        success = False
        while not success:
            try:
                apiclient.register_component('media-monitor')
                success = True
            except Exception, e:
                self.logger.error(str(e))
                import time
                time.sleep(10)

        manager.loop()

def launch_instance(name, root, global_cfg):
    cfg = {
        'api_client'    : global_cfg,
        'media_monitor' : global_cfg,
    }
    ai = AirtimeInstance(name, root, cfg)
    MM2(ai).start()

def setup_global(log):
    """ setup unicode and other stuff """
    log.info("Attempting to set the locale...")
    try: mmp.configure_locale(mmp.get_system_locale())
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

def setup_logger(log_config, logpath):
    logging.config.fileConfig(log_config)
    #need to wait for Python 2.7 for this..
    #logging.captureWarnings(True)
    logger = logging.getLogger()
    LogWriter.override_std_err(logger)
    logfile = unicode(logpath)
    setup_logging(logfile)
    log = get_logger()
    return log
