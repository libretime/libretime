"""
Python part of radio playout (pypo)
"""

import logging
import sys
import time
from datetime import datetime
from pathlib import Path
from queue import Queue
from typing import Any, Dict, Optional, Union

import click
from libretime_api_client.v1 import ApiClient as LegacyClient
from libretime_api_client.v2 import ApiClient
from libretime_shared.cli import cli_config_options, cli_logging_options
from libretime_shared.config import DEFAULT_ENV_PREFIX
from libretime_shared.logging import setup_logger

from .config import CACHE_DIR, RECORD_DIR, Config
from .history.stats import StatsCollectorThread
from .liquidsoap.client import LiquidsoapClient
from .liquidsoap.version import LIQUIDSOAP_MIN_VERSION
from .message_handler import MessageListener
from .player.events import Events, FileEvents
from .player.fetch import PypoFetch
from .player.file import PypoFile
from .player.liquidsoap import PypoLiquidsoap
from .player.push import PypoPush
from .recorder import Recorder

logger = logging.getLogger(__name__)


@click.command(context_settings={"auto_envvar_prefix": DEFAULT_ENV_PREFIX})
@cli_logging_options()
@cli_config_options()
def cli(log_level: str, log_filepath: Optional[Path], config_filepath: Optional[Path]):
    """
    Run playout.
    """
    setup_logger(log_level, log_filepath)
    config = Config(config_filepath)

    try:
        for dir_path in [CACHE_DIR, RECORD_DIR]:
            dir_path.mkdir(exist_ok=True)
    except OSError as exception:
        logger.error(exception)
        sys.exit(1)

    # Although all of our calculations are in UTC, it is useful to know what timezone
    # the local machine is, so that we have a reference for what time the actual
    # log entries were made
    logger.info("Timezone: %s", time.tzname)
    logger.info("UTC time: %s", datetime.utcnow())

    legacy_client = LegacyClient()
    api_client = ApiClient(
        base_url=config.general.public_url,
        api_key=config.general.api_key,
    )

    while not legacy_client.is_server_compatible():
        time.sleep(5)

    success = False
    while not success:
        try:
            legacy_client.register_component("pypo")
            success = True
        except Exception as exception:
            logger.exception(exception)
            time.sleep(10)

    liq_client = LiquidsoapClient(
        host=config.playout.liquidsoap_host,
        port=config.playout.liquidsoap_port,
    )

    logger.debug("Checking if Liquidsoap is running")
    liq_version = liq_client.wait_for_version()
    if not LIQUIDSOAP_MIN_VERSION <= liq_version:
        raise RuntimeError(f"Invalid liquidsoap version {liq_version}")

    fetch_queue: Queue[Union[str, bytes]] = Queue()
    recorder_queue: Queue[Dict[str, Any]] = Queue()
    push_queue: Queue[Events] = Queue()
    # This queue is shared between pypo-fetch and pypo-file, where pypo-file
    # is the consumer. Pypo-fetch will send every schedule it gets to pypo-file
    # and pypo will parse this schedule to determine which file has the highest
    # priority, and retrieve it.
    file_queue: Queue[FileEvents] = Queue()

    pypo_liquidsoap = PypoLiquidsoap(liq_client)

    file_thread = PypoFile(file_queue, api_client)
    file_thread.start()

    fetch_thread = PypoFetch(
        fetch_queue,
        push_queue,
        file_queue,
        liq_client,
        pypo_liquidsoap,
        config,
        api_client,
        legacy_client,
    )
    fetch_thread.start()

    push_thread = PypoPush(push_queue, pypo_liquidsoap, config)
    push_thread.start()

    recorder_thread = Recorder(recorder_queue, config, legacy_client)
    recorder_thread.start()

    stats_collector_thread = StatsCollectorThread(config, legacy_client)
    stats_collector_thread.start()

    message_listener = MessageListener(config, fetch_queue, recorder_queue)
    message_listener.run_forever()
