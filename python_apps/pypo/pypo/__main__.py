"""
Python part of radio playout (pypo)
"""


import locale
import logging
import os
import re
import signal
import sys
import telnetlib
import time

from api_clients import api_client
from configobj import ConfigObj
from datetime import datetime
from optparse import OptionParser
import importlib
try:
    from queue import Queue
except ImportError:  # Python 2.7.5 (CentOS 7)
    from queue import Queue
from threading import Lock

from .listenerstat import ListenerStat
from .pypofetch import PypoFetch
from .pypofile import PypoFile
from .pypoliquidsoap import PypoLiquidsoap
from .pypomessagehandler import PypoMessageHandler
from .pypopush import PypoPush
from .recorder import Recorder
from .timeout import ls_timeout
from . import pure


LOG_PATH = "/var/log/airtime/pypo/pypo.log"
LOG_LEVEL = logging.INFO
logging.captureWarnings(True)

# Set up command-line options
parser = OptionParser()

# help screen / info
usage = "%prog [options]" + " - python playout system"
parser = OptionParser(usage=usage)

# Options
parser.add_option(
    "-v",
    "--compat",
    help="Check compatibility with server API version",
    default=False,
    action="store_true",
    dest="check_compat",
)

parser.add_option(
    "-t",
    "--test",
    help="Do a test to make sure everything is working properly.",
    default=False,
    action="store_true",
    dest="test",
)

parser.add_option(
    "-b",
    "--cleanup",
    help="Cleanup",
    default=False,
    action="store_true",
    dest="cleanup",
)

parser.add_option(
    "-c",
    "--check",
    help="Check the cached schedule and exit",
    default=False,
    action="store_true",
    dest="check",
)

# parse options
(options, args) = parser.parse_args()

LIQUIDSOAP_MIN_VERSION = "1.1.1"

PYPO_HOME = "/var/tmp/airtime/pypo/"


def configure_environment():
    os.environ["HOME"] = PYPO_HOME
    os.environ["TERM"] = "xterm"


configure_environment()

# need to wait for Python 2.7 for this..
logging.captureWarnings(True)

# configure logging
try:
    # Set up logging
    logFormatter = logging.Formatter(
        "%(asctime)s [%(module)s] [%(levelname)-5.5s]  %(message)s"
    )
    rootLogger = logging.getLogger()
    rootLogger.setLevel(LOG_LEVEL)
    logger = rootLogger

    fileHandler = logging.handlers.RotatingFileHandler(
        filename=LOG_PATH, maxBytes=1024 * 1024 * 30, backupCount=8
    )
    fileHandler.setFormatter(logFormatter)
    rootLogger.addHandler(fileHandler)

    consoleHandler = logging.StreamHandler()
    consoleHandler.setFormatter(logFormatter)
    rootLogger.addHandler(consoleHandler)
except Exception as e:
    print("Couldn't configure logging: {}".format(e))
    sys.exit(1)

# loading config file
try:
    config = ConfigObj("/etc/airtime/airtime.conf")
except Exception as e:
    logger.error("Error loading config file: %s", e)
    sys.exit(1)


class Global:
    def __init__(self, api_client):
        self.api_client = api_client

    def selfcheck(self):
        return self.api_client.is_server_compatible()

    def test_api(self):
        self.api_client.test()


def keyboardInterruptHandler(signum, frame):
    logger = logging.getLogger()
    logger.info("\nKeyboard Interrupt\n")
    sys.exit(0)


@ls_timeout
def liquidsoap_get_info(telnet_lock, host, port, logger):
    logger.debug("Checking to see if Liquidsoap is running")
    try:
        telnet_lock.acquire()
        tn = telnetlib.Telnet(host, port)
        msg = "version\n"
        tn.write(msg)
        tn.write("exit\n")
        response = tn.read_all()
    except Exception as e:
        logger.error(str(e))
        return None
    finally:
        telnet_lock.release()

    return get_liquidsoap_version(response)


def get_liquidsoap_version(version_string):
    m = re.match(r"Liquidsoap (\d+.\d+.\d+)", version_string)

    if m:
        return m.group(1)
    else:
        return None

    if m:
        current_version = m.group(1)
        return pure.version_cmp(current_version, LIQUIDSOAP_MIN_VERSION) >= 0
    return False


def liquidsoap_startup_test():

    liquidsoap_version_string = liquidsoap_get_info(
        telnet_lock, ls_host, ls_port, logger
    )
    while not liquidsoap_version_string:
        logger.warning(
            "Liquidsoap doesn't appear to be running!, " + "Sleeping and trying again"
        )
        time.sleep(1)
        liquidsoap_version_string = liquidsoap_get_info(
            telnet_lock, ls_host, ls_port, logger
        )

    while pure.version_cmp(liquidsoap_version_string, LIQUIDSOAP_MIN_VERSION) < 0:
        logger.warning(
            "Liquidsoap is running but in incorrect version! "
            + "Make sure you have at least Liquidsoap %s installed"
            % LIQUIDSOAP_MIN_VERSION
        )
        time.sleep(1)
        liquidsoap_version_string = liquidsoap_get_info(
            telnet_lock, ls_host, ls_port, logger
        )

    logger.info("Liquidsoap version string found %s" % liquidsoap_version_string)


if __name__ == "__main__":
    logger.info("###########################################")
    logger.info("#             *** pypo  ***               #")
    logger.info("#   Liquidsoap Scheduled Playout System   #")
    logger.info("###########################################")

    # Although all of our calculations are in UTC, it is useful to know what timezone
    # the local machine is, so that we have a reference for what time the actual
    # log entries were made
    logger.info("Timezone: %s" % str(time.tzname))
    logger.info("UTC time: %s" % str(datetime.utcnow()))

    signal.signal(signal.SIGINT, keyboardInterruptHandler)

    api_client = api_client.AirtimeApiClient()
    g = Global(api_client)

    while not g.selfcheck():
        time.sleep(5)

    success = False
    while not success:
        try:
            api_client.register_component("pypo")
            success = True
        except Exception as e:
            logger.error(str(e))
            time.sleep(10)

    telnet_lock = Lock()

    ls_host = config["pypo"]["ls_host"]
    ls_port = config["pypo"]["ls_port"]

    liquidsoap_startup_test()

    if options.test:
        g.test_api()
        sys.exit(0)

    pypoFetch_q = Queue()
    recorder_q = Queue()
    pypoPush_q = Queue()

    pypo_liquidsoap = PypoLiquidsoap(logger, telnet_lock, ls_host, ls_port)

    """
    This queue is shared between pypo-fetch and pypo-file, where pypo-file
    is the consumer. Pypo-fetch will send every schedule it gets to pypo-file
    and pypo will parse this schedule to determine which file has the highest
    priority, and retrieve it.
    """
    media_q = Queue()

    # Pass only the configuration sections needed; PypoMessageHandler only needs rabbitmq settings
    pmh = PypoMessageHandler(pypoFetch_q, recorder_q, config["rabbitmq"])
    pmh.daemon = True
    pmh.start()

    pfile = PypoFile(media_q, config["pypo"])
    pfile.daemon = True
    pfile.start()

    pf = PypoFetch(
        pypoFetch_q, pypoPush_q, media_q, telnet_lock, pypo_liquidsoap, config["pypo"]
    )
    pf.daemon = True
    pf.start()

    pp = PypoPush(pypoPush_q, telnet_lock, pypo_liquidsoap, config["pypo"])
    pp.daemon = True
    pp.start()

    recorder = Recorder(recorder_q)
    recorder.daemon = True
    recorder.start()

    stat = ListenerStat(config)
    stat.daemon = True
    stat.start()

    # Just sleep the main thread, instead of blocking on pf.join().
    # This allows CTRL-C to work!
    while True:
        time.sleep(1)

    logger.info("System exit")
