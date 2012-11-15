# -*- coding: utf-8 -*-
import sys
import os
import logging
import logging.config

from media.monitor.log              import get_logger, setup_logging
from std_err_override               import LogWriter
from media.saas.launcher import setup_global, launch_instance
from media.saas.airtimeinstance import AirtimeInstance
from media.monitor.config import MMConfig

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

def main(global_config, api_client_config, log_config):
    """ function to run hosted install """
    mm_config = MMConfig(global_config)
    log = setup_logger( log_config, mm_config['logpath'] )
    setup_global(log)
    launch_instance('hosted_install', '/', global_config, api_client_config,
            log_config)

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

