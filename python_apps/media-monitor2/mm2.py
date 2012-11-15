# -*- coding: utf-8 -*-
import sys
import os
import logging
import logging.config

from media.monitor.log              import get_logger, setup_logging
from media.monitor.exceptions       import FailedToObtainLocale, \
                                           FailedToSetLocale
from std_err_override               import LogWriter
from media.saas.launcher import MM2
from media.saas.airtimeinstance import AirtimeInstance

import media.monitor.pure          as mmp

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

def main(global_config, api_client_config, log_config):
    cfg = {
        'api_client'    : api_client_config,
        'media_monitor' : global_config,
        'logging'       : log_config,
    }
    ai = AirtimeInstance('hosted_install', '/', cfg)
    log = setup_logger( log_config, ai.mm_config['logpath'] )
    setup_global(log)
    run_instance(ai)

def run_instance(airtime_intance):
    MM2(airtime_intance).start()

__doc__ = """
Usage:
    mm2.py --config=<path> --apiclient=<path> --log=<path>

Options:
    -h --help          Show this screen
    --config=<path>    path to mm2 config
    --apiclient=<path> path to apiclient config
    --log=<path>       log config at <path>
"""

if __name__ == '__main__':
    from docopt import docopt
    args = docopt(__doc__,version="mm1.99")
    for k in ['--apiclient','--config','--log']:
        if not os.path.exists(args[k]):
            print("'%s' must exist" % args[k])
            sys.exit(0)
    print("Running mm1.99")
    main(args['--config'],args['--apiclient'],args['--log'])

