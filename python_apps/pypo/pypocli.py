"""
Python part of radio playout (pypo)
"""

from optparse import OptionParser
from datetime import datetime

import telnetlib

import time
import sys
import signal
import logging
import locale
import os
from Queue import Queue

from threading import Lock

from pypopush import PypoPush
from pypofetch import PypoFetch
from pypofile import PypoFile
from recorder import Recorder
from pypomessagehandler import PypoMessageHandler

from configobj import ConfigObj

# custom imports
from api_clients import api_client
from std_err_override import LogWriter

PYPO_VERSION = '1.1'

# Set up command-line options
parser = OptionParser()

# help screen / info
usage = "%prog [options]" + " - python playout system"
parser = OptionParser(usage=usage)

# Options
parser.add_option("-v", "--compat", help="Check compatibility with server API version", default=False, action="store_true", dest="check_compat")

parser.add_option("-t", "--test", help="Do a test to make sure everything is working properly.", default=False, action="store_true", dest="test")
parser.add_option("-b", "--cleanup", help="Cleanup", default=False, action="store_true", dest="cleanup")
parser.add_option("-c", "--check", help="Check the cached schedule and exit", default=False, action="store_true", dest="check")

# parse options
(options, args) = parser.parse_args()


#need to wait for Python 2.7 for this..
#logging.captureWarnings(True)

# configure logging
try:
    logging.config.fileConfig("logging.cfg")
    logger = logging.getLogger()
    LogWriter.override_std_err(logger)
except Exception, e:
    print "Couldn't configure logging"
    sys.exit()

def configure_locale():
    logger.debug("Before %s", locale.nl_langinfo(locale.CODESET))
    current_locale = locale.getlocale()

    if current_locale[1] is None:
        logger.debug("No locale currently set. Attempting to get default locale.")
        default_locale = locale.getdefaultlocale()

        if default_locale[1] is None:
            logger.debug("No default locale exists. Let's try loading from /etc/default/locale")
            if os.path.exists("/etc/default/locale"):
                locale_config = ConfigObj('/etc/default/locale')
                lang = locale_config.get('LANG')
                new_locale = lang
            else:
                logger.error("/etc/default/locale could not be found! Please run 'sudo update-locale' from command-line.")
                sys.exit(1)
        else:
            new_locale = default_locale

        logger.info("New locale set to: %s", locale.setlocale(locale.LC_ALL, new_locale))



    reload(sys)
    sys.setdefaultencoding("UTF-8")
    current_locale_encoding = locale.getlocale()[1].lower()
    logger.debug("sys default encoding %s", sys.getdefaultencoding())
    logger.debug("After %s", locale.nl_langinfo(locale.CODESET))

    if current_locale_encoding not in ['utf-8', 'utf8']:
        logger.error("Need a UTF-8 locale. Currently '%s'. Exiting..." % current_locale_encoding)
        sys.exit(1)


configure_locale()

# loading config file
try:
    config = ConfigObj('/etc/airtime/pypo.cfg')
except Exception, e:
    logger.error('Error loading config file: %s', e)
    sys.exit()

class Global:
    def __init__(self):
        self.api_client = api_client.AirtimeApiClient()

    def selfcheck(self):
        self.api_client = api_client.AirtimeApiClient()
        return self.api_client.is_server_compatible()

    def test_api(self):
        self.api_client.test()

def keyboardInterruptHandler(signum, frame):
    logger = logging.getLogger()
    logger.info('\nKeyboard Interrupt\n')
    sys.exit(0)

def liquidsoap_running_test(telnet_lock, host, port, logger):
    logger.debug("Checking to see if Liquidsoap is running")
    success = True
    try:
        telnet_lock.acquire()
        tn = telnetlib.Telnet(host, port)
        msg = "version\n"
        tn.write(msg)
        tn.write("exit\n")
        logger.info("Found: %s", tn.read_all())
    except Exception, e:
        logger.error(str(e))
        success = False
    finally:
        telnet_lock.release()

    return success


if __name__ == '__main__':
    logger.info('###########################################')
    logger.info('#             *** pypo  ***               #')
    logger.info('#   Liquidsoap Scheduled Playout System   #')
    logger.info('###########################################')

    #Although all of our calculations are in UTC, it is useful to know what timezone
    #the local machine is, so that we have a reference for what time the actual
    #log entries were made
    logger.info("Timezone: %s" % str(time.tzname))
    logger.info("UTC time: %s" % str(datetime.utcnow()))

    signal.signal(signal.SIGINT, keyboardInterruptHandler)

    # initialize
    g = Global()

    while not g.selfcheck():
        time.sleep(5)

    telnet_lock = Lock()

    ls_host = config['ls_host']
    ls_port = config['ls_port']
    while not liquidsoap_running_test(telnet_lock, ls_host, ls_port, logger):
        logger.warning("Liquidsoap not started yet. Sleeping one second and trying again")
        time.sleep(1)

    if options.test:
        g.test_api()
        sys.exit()

    api_client = api_client.AirtimeApiClient()
    api_client.register_component("pypo")

    pypoFetch_q = Queue()
    recorder_q = Queue()
    pypoPush_q = Queue()



    """
    This queue is shared between pypo-fetch and pypo-file, where pypo-file
    is the receiver. Pypo-fetch will send every schedule it gets to pypo-file
    and pypo will parse this schedule to determine which file has the highest
    priority, and will retrieve it.
    """
    media_q = Queue()

    pmh = PypoMessageHandler(pypoFetch_q, recorder_q)
    pmh.daemon = True
    pmh.start()

    pfile = PypoFile(media_q)
    pfile.daemon = True
    pfile.start()

    pf = PypoFetch(pypoFetch_q, pypoPush_q, media_q, telnet_lock)
    pf.daemon = True
    pf.start()

    pp = PypoPush(pypoPush_q, telnet_lock)
    pp.daemon = True
    pp.start()

    recorder = Recorder(recorder_q)
    recorder.daemon = True
    recorder.start()

    # all join() are commented out because we want to exit entire pypo
    # if pypofetch is exiting
    #pmh.join()
    #recorder.join()
    #pp.join()
    pf.join()

    logger.info("pypo fetch exit")
    sys.exit()
