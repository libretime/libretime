"""
Python part of radio playout (pypo)
"""

import re
import signal
import sys
import telnetlib
import time
from datetime import datetime
from pathlib import Path
from queue import Queue
from threading import Lock
from typing import Optional

import click
from libretime_api_client.version1 import AirtimeApiClient as ApiClient
from libretime_shared.cli import cli_config_options, cli_logging_options
from libretime_shared.config import DEFAULT_ENV_PREFIX
from libretime_shared.logging import level_from_name, setup_logger
from loguru import logger

from . import pure
from .config import CACHE_DIR, RECORD_DIR, Config
from .listenerstat import ListenerStat
from .pypofetch import PypoFetch
from .pypofile import PypoFile
from .pypoliquidsoap import PypoLiquidsoap
from .pypomessagehandler import PypoMessageHandler
from .pypopush import PypoPush
from .recorder import Recorder
from .timeout import ls_timeout

LIQUIDSOAP_MIN_VERSION = "1.1.1"


class Global:
    def __init__(self, api_client):
        self.api_client = api_client

    def selfcheck(self):
        return self.api_client.is_server_compatible()

    def test_api(self):
        self.api_client.test()


def keyboardInterruptHandler(signum, frame):
    logger.info("\nKeyboard Interrupt\n")
    sys.exit(0)


@ls_timeout
def liquidsoap_get_info(telnet_lock, host, port):
    logger.debug("Checking to see if Liquidsoap is running")
    try:
        telnet_lock.acquire()
        tn = telnetlib.Telnet(host, port)
        msg = "version\n"
        tn.write(msg.encode("utf-8"))
        tn.write(b"exit\n")
        response = tn.read_all().decode("utf-8")
    except Exception as e:
        logger.error(e)
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


def liquidsoap_startup_test(telnet_lock, liquidsoap_host, liquidsoap_port):

    liquidsoap_version_string = liquidsoap_get_info(
        telnet_lock,
        liquidsoap_host,
        liquidsoap_port,
    )
    while not liquidsoap_version_string:
        logger.warning(
            "Liquidsoap doesn't appear to be running!, " + "Sleeping and trying again"
        )
        time.sleep(1)
        liquidsoap_version_string = liquidsoap_get_info(
            telnet_lock,
            liquidsoap_host,
            liquidsoap_port,
        )

    while pure.version_cmp(liquidsoap_version_string, LIQUIDSOAP_MIN_VERSION) < 0:
        logger.warning(
            "Liquidsoap is running but in incorrect version! "
            + "Make sure you have at least Liquidsoap %s installed"
            % LIQUIDSOAP_MIN_VERSION
        )
        time.sleep(1)
        liquidsoap_version_string = liquidsoap_get_info(
            telnet_lock,
            liquidsoap_host,
            liquidsoap_port,
        )

    logger.info("Liquidsoap version string found %s" % liquidsoap_version_string)


@click.command(context_settings={"auto_envvar_prefix": DEFAULT_ENV_PREFIX})
@cli_logging_options()
@cli_config_options()
def cli(log_level: str, log_filepath: Optional[Path], config_filepath: Optional[Path]):
    """
    Run playout.
    """
    setup_logger(level_from_name(log_level), log_filepath)
    config = Config(filepath=config_filepath)

    try:
        for dir_path in [CACHE_DIR, RECORD_DIR]:
            dir_path.mkdir(exist_ok=True)
    except OSError as exception:
        logger.error(exception)
        sys.exit(1)

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

    api_client = ApiClient()
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

    liquidsoap_host = config.playout.liquidsoap_host
    liquidsoap_port = config.playout.liquidsoap_port

    liquidsoap_startup_test(telnet_lock, liquidsoap_host, liquidsoap_port)

    pypoFetch_q = Queue()
    recorder_q = Queue()
    pypoPush_q = Queue()

    pypo_liquidsoap = PypoLiquidsoap(telnet_lock, liquidsoap_host, liquidsoap_port)

    """
    This queue is shared between pypo-fetch and pypo-file, where pypo-file
    is the consumer. Pypo-fetch will send every schedule it gets to pypo-file
    and pypo will parse this schedule to determine which file has the highest
    priority, and retrieve it.
    """
    media_q = Queue()

    # Pass only the configuration sections needed; PypoMessageHandler only needs rabbitmq settings
    pmh = PypoMessageHandler(pypoFetch_q, recorder_q, config.rabbitmq)
    pmh.daemon = True
    pmh.start()

    pfile = PypoFile(media_q)
    pfile.daemon = True
    pfile.start()

    pf = PypoFetch(
        pypoFetch_q,
        pypoPush_q,
        media_q,
        telnet_lock,
        pypo_liquidsoap,
        config,
    )
    pf.daemon = True
    pf.start()

    pp = PypoPush(pypoPush_q, telnet_lock, pypo_liquidsoap, config)
    pp.daemon = True
    pp.start()

    recorder = Recorder(recorder_q, config)
    recorder.daemon = True
    recorder.start()

    stat = ListenerStat(config)
    stat.daemon = True
    stat.start()

    # Just sleep the main thread, instead of blocking on pf.join().
    # This allows CTRL-C to work!
    while True:
        time.sleep(1)
