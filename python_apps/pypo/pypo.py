"""
Python part of radio playout (pypo)
"""

from datetime import datetime
from configobj import ConfigObj
from Queue import Queue

import time
import sys
import logging
import locale
import os

from schedule.pypopush import PypoPush
from schedule.pypofetch import PypoFetch
from schedule.pypofile import PypoFile
from recorder.recorder import Recorder
from schedule.listenerstat import ListenerStat
from pypomessagehandler import PypoMessageHandler
from schedule.pypoliquidsoap import PypoLiquidsoap

from media.update.replaygainupdater import ReplayGainUpdater
from media.update.silananalyzer import SilanAnalyzer

# custom imports
from api_clients import api_client
from std_err_override import LogWriter



def configure_locale():
    """
    Silly hacks to force Python 2.x to run in UTF-8 mode. Not portable at all,
    however serves our purpose at the moment.

    More information available here:
    http://stackoverflow.com/questions/3828723/why-we-need-sys-setdefaultencodingutf-8-in-a-py-script
    """
    logger.debug("Before %s", locale.nl_langinfo(locale.CODESET))
    current_locale = locale.getlocale()

    if current_locale[1] is None:
        logger.debug("No locale currently set. Attempting to get default locale.")
        default_locale = locale.getdefaultlocale()

        if default_locale[1] is None:
            logger.debug("No default locale exists. Let's try loading from \
                    /etc/default/locale")
            if os.path.exists("/etc/default/locale"):
                locale_config = ConfigObj('/etc/default/locale')
                lang = locale_config.get('LANG')
                new_locale = lang
            else:
                logger.error("/etc/default/locale could not be found! Please \
                        run 'sudo update-locale' from command-line.")
                sys.exit(1)
        else:
            new_locale = default_locale

        logger.info("New locale set to: %s", \
                locale.setlocale(locale.LC_ALL, new_locale))

    reload(sys)
    sys.setdefaultencoding("UTF-8")
    current_locale_encoding = locale.getlocale()[1].lower()
    logger.debug("sys default encoding %s", sys.getdefaultencoding())
    logger.debug("After %s", locale.nl_langinfo(locale.CODESET))

    if current_locale_encoding not in ['utf-8', 'utf8']:
        logger.error("Need a UTF-8 locale. Currently '%s'. Exiting...", 
                current_locale_encoding)
        sys.exit(1)

if __name__ == '__main__':
    # configure logging
    try:
        logging.config.fileConfig("configs/logging.cfg")
        logger = logging.getLogger()
        LogWriter.override_std_err(logger)
    except Exception, e:
        print "Couldn't configure logging"
        sys.exit(1)

    configure_locale()

    # loading config file
    try:
        config = ConfigObj('/etc/airtime/pypo.cfg')
    except Exception, e:
        logger.error('Error loading config file: %s', e)
        sys.exit(1)

    logger.info('###########################################')
    logger.info('#             *** pypo  ***               #')
    logger.info('#   Liquidsoap Scheduled Playout System   #')
    logger.info('###########################################')

    #Although all of our calculations are in UTC, it is useful to know what 
    #timezone the local machine is, so that we have a reference for what time 
    #the actual log entries were made
    logger.info("Timezone: %s" % str(time.tzname))
    logger.info("UTC time: %s" % str(datetime.utcnow()))

    api_client = api_client.AirtimeApiClient()

    while not self.api_client.is_server_compatible():
        time.sleep(5)

    success = False
    while not success:
        try:
            api_client.register_component('pypo')
            success = True
        except Exception, e:
            logger.error(str(e))
            time.sleep(10)

    ls_host = config['ls_host']
    ls_port = config['ls_port']

    pypo_liquidsoap = PypoLiquidsoap(logger, ls_host, ls_port)
    pypo_liquidsoap.liquidsoap_startup_test()

    ReplayGainUpdater.start_reply_gain(api_client)
    SilanAnalyzer.start_silan(api_client, logger)

    pypoFetch_q = Queue()
    recorder_q = Queue()
    pypoPush_q = Queue()

    """
    This queue is shared between pypo-fetch and pypo-file, where pypo-file
    is the consumer. Pypo-fetch will send every schedule it gets to pypo-file
    and pypo will parse this schedule to determine which file has the highest
    priority, and retrieve it.
    """
    media_q = Queue()

    pmh = PypoMessageHandler(pypoFetch_q, recorder_q, config)
    pmh.daemon = True
    pmh.start()

    pfile = PypoFile(media_q, config)
    pfile.daemon = True
    pfile.start()

    pf = PypoFetch(pypoFetch_q, pypoPush_q, media_q, pypo_liquidsoap, config)
    pf.daemon = True
    pf.start()

    pp = PypoPush(pypoPush_q, pypo_liquidsoap)
    pp.daemon = True
    pp.start()

    recorder = Recorder(recorder_q)
    recorder.daemon = True
    recorder.start()

    stat = ListenerStat()
    stat.daemon = True
    stat.start()

    pf.join()

    logger.info("System exit")
