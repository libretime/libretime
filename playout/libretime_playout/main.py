"""
Python part of radio playout (pypo)
"""
import logging
import os
import sys
import time
from datetime import datetime
from pathlib import Path
from queue import Queue
from typing import Any, Dict, Optional

import click
import requests
from libretime_api_client.v1 import ApiClient as LegacyClient
from libretime_api_client.v2 import ApiClient
from libretime_shared.cli import cli_config_options, cli_logging_options
from libretime_shared.config import DEFAULT_ENV_PREFIX
from libretime_shared.logging import setup_logger

from . import PACKAGE, VERSION
from .config import CACHE_DIR, RECORD_DIR, Config
from .history.stats import StatsCollectorThread
from .liquidsoap.client import LiquidsoapClient
from .liquidsoap.version import LIQUIDSOAP_MIN_VERSION
from .message_handler import MessageListener
from .player.events import Events, FileEvents
from .player.fetch import PypoFetch
from .player.file import PypoFile
from .player.liquidsoap import Liquidsoap
from .player.push import PypoPush

logger = logging.getLogger(__name__)

for module in ("amqp",):
    logging.getLogger(module).setLevel(logging.INFO)
    logging.getLogger(module).propagate = False


def wait_for_legacy(legacy_client: LegacyClient) -> None:
    while legacy_client.version() == -1:
        time.sleep(2)

    success = False
    while not success:
        try:
            legacy_client.register_component("pypo")
            success = True
        except (
            requests.exceptions.ConnectionError,
            requests.exceptions.HTTPError,
            requests.exceptions.Timeout,
        ) as exception:
            logger.exception(exception)
            time.sleep(10)


def wait_for_liquidsoap(liq_client: LiquidsoapClient) -> None:
    logger.debug("Checking if Liquidsoap is running")
    liq_version = liq_client.wait_for_version()
    if not LIQUIDSOAP_MIN_VERSION <= liq_version:
        raise RuntimeError(f"Invalid liquidsoap version {liq_version}")


@click.command(context_settings={"auto_envvar_prefix": DEFAULT_ENV_PREFIX})
@cli_logging_options()
@cli_config_options()
def cli(
    log_level: str,
    log_filepath: Optional[Path],
    config_filepath: Optional[Path],
) -> None:
    """
    Run playout.
    """
    setup_logger(log_level, log_filepath)
    config = Config(config_filepath)

    if "SENTRY_DSN" in os.environ:
        logger.info("installing sentry")
        # pylint: disable=import-outside-toplevel
        import sentry_sdk

        sentry_sdk.init(
            traces_sample_rate=1.0,
            release=f"{PACKAGE}@{VERSION}",
        )

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

    api_client = ApiClient(
        base_url=config.general.public_url,
        api_key=config.general.api_key,
    )

    legacy_client = LegacyClient(
        base_url=config.general.public_url,
        api_key=config.general.api_key,
    )
    wait_for_legacy(legacy_client)

    liq_client = LiquidsoapClient(
        host=config.playout.liquidsoap_host,
        port=config.playout.liquidsoap_port,
    )
    wait_for_liquidsoap(liq_client)

    fetch_queue: "Queue[Dict[str, Any]]" = Queue()
    push_queue: "Queue[Events]" = Queue()
    # This queue is shared between pypo-fetch and pypo-file, where pypo-file
    # is the consumer. Pypo-fetch will send every schedule it gets to pypo-file
    # and pypo will parse this schedule to determine which file has the highest
    # priority, and retrieve it.
    file_queue: "Queue[FileEvents]" = Queue()

    liquidsoap = Liquidsoap(liq_client)

    PypoFile(file_queue, api_client).start()

    PypoFetch(
        fetch_queue,
        push_queue,
        file_queue,
        liq_client,
        liquidsoap,
        config,
        api_client,
        legacy_client,
    ).start()

    PypoPush(push_queue, liquidsoap, config).start()

    StatsCollectorThread(config, legacy_client).start()

    message_listener = MessageListener(config, fetch_queue)
    message_listener.run_forever()
