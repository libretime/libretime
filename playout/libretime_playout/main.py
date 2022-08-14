"""
Python part of radio playout (pypo)
"""

import signal
import sys
import telnetlib
import time
from datetime import datetime
from pathlib import Path
from queue import Queue
from threading import Lock
from typing import Optional, Tuple

import click
from libretime_api_client.v1 import ApiClient as LegacyClient
from libretime_api_client.v2 import ApiClient
from libretime_shared.cli import cli_config_options, cli_logging_options
from libretime_shared.config import DEFAULT_ENV_PREFIX
from libretime_shared.logging import level_from_name, setup_logger
from loguru import logger

from .config import CACHE_DIR, RECORD_DIR, Config
from .history.stats import StatsCollectorThread
from .liquidsoap.version import LIQUIDSOAP_MIN_VERSION, parse_liquidsoap_version
from .message_handler import PypoMessageHandler
from .player.fetch import PypoFetch
from .player.file import PypoFile
from .player.liquidsoap import PypoLiquidsoap
from .player.push import PypoPush
from .recorder import Recorder
from .timeout import ls_timeout


class Global:
    def __init__(self, legacy_client: LegacyClient):
        self.legacy_client = legacy_client

    def selfcheck(self):
        return self.legacy_client.is_server_compatible()


def keyboardInterruptHandler(signum, frame):
    logger.info("\nKeyboard Interrupt\n")
    sys.exit(0)


@ls_timeout
def liquidsoap_get_info(telnet_lock, host, port) -> Optional[Tuple[int, int, int]]:
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

    return parse_liquidsoap_version(response)


def liquidsoap_startup_test(telnet_lock, liquidsoap_host, liquidsoap_port):

    liquidsoap_version = liquidsoap_get_info(
        telnet_lock,
        liquidsoap_host,
        liquidsoap_port,
    )
    while not liquidsoap_version:
        logger.warning("Liquidsoap doesn't appear to be running! Trying again later...")
        time.sleep(1)
        liquidsoap_version = liquidsoap_get_info(
            telnet_lock,
            liquidsoap_host,
            liquidsoap_port,
        )

    while not LIQUIDSOAP_MIN_VERSION <= liquidsoap_version:
        logger.warning(
            f"Found invalid Liquidsoap version! "
            f"Liquidsoap<={LIQUIDSOAP_MIN_VERSION} is required!"
        )
        time.sleep(5)
        liquidsoap_version = liquidsoap_get_info(
            telnet_lock,
            liquidsoap_host,
            liquidsoap_port,
        )

    logger.info(f"Liquidsoap version {liquidsoap_version}")


@click.command(context_settings={"auto_envvar_prefix": DEFAULT_ENV_PREFIX})
@cli_logging_options()
@cli_config_options()
def cli(log_level: str, log_filepath: Optional[Path], config_filepath: Optional[Path]):
    """
    Run playout.
    """
    setup_logger(level_from_name(log_level), log_filepath)
    config = Config(config_filepath)

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

    legacy_client = LegacyClient()
    api_client = ApiClient(
        base_url=config.general.public_url,
        api_key=config.general.api_key,
    )
    g = Global(legacy_client)

    while not g.selfcheck():
        time.sleep(5)

    success = False
    while not success:
        try:
            legacy_client.register_component("pypo")
            success = True
        except Exception as exception:
            logger.exception(exception)
            time.sleep(10)

    telnet_lock = Lock()

    liquidsoap_host = config.playout.liquidsoap_host
    liquidsoap_port = config.playout.liquidsoap_port

    liquidsoap_startup_test(telnet_lock, liquidsoap_host, liquidsoap_port)

    pypoFetch_q = Queue()
    recorder_q = Queue()
    pypoPush_q = Queue()

    pypo_liquidsoap = PypoLiquidsoap(telnet_lock, liquidsoap_host, liquidsoap_port)

    # This queue is shared between pypo-fetch and pypo-file, where pypo-file
    # is the consumer. Pypo-fetch will send every schedule it gets to pypo-file
    # and pypo will parse this schedule to determine which file has the highest
    # priority, and retrieve it.
    media_q = Queue()

    # Pass only the configuration sections needed; PypoMessageHandler only needs rabbitmq settings
    pmh = PypoMessageHandler(pypoFetch_q, recorder_q, config.rabbitmq)
    pmh.daemon = True
    pmh.start()

    pfile = PypoFile(media_q, api_client)
    pfile.daemon = True
    pfile.start()

    pf = PypoFetch(
        pypoFetch_q,
        pypoPush_q,
        media_q,
        telnet_lock,
        pypo_liquidsoap,
        config,
        api_client,
        legacy_client,
    )
    pf.daemon = True
    pf.start()

    pp = PypoPush(pypoPush_q, telnet_lock, pypo_liquidsoap, config)
    pp.daemon = True
    pp.start()

    recorder = Recorder(recorder_q, config, legacy_client)
    recorder.daemon = True
    recorder.start()

    stats_collector = StatsCollectorThread(legacy_client)
    stats_collector.start()

    # Just sleep the main thread, instead of blocking on pf.join().
    # This allows CTRL-C to work!
    while True:
        time.sleep(1)
