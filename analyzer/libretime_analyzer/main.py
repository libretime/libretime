import logging
import os
from pathlib import Path
from typing import Optional

import click
from libretime_shared.cli import cli_config_options, cli_logging_options
from libretime_shared.config import DEFAULT_ENV_PREFIX
from libretime_shared.logging import setup_logger

from . import PACKAGE, VERSION
from .config import Config
from .message_listener import MessageListener
from .status_reporter import StatusReporter

logger = logging.getLogger(__name__)

VERSION = "1.0"

DEFAULT_RETRY_QUEUE_FILEPATH = Path("retry_queue")


@click.command(context_settings={"auto_envvar_prefix": DEFAULT_ENV_PREFIX})
@cli_logging_options()
@cli_config_options()
@click.option(
    "--retry-queue-filepath",
    type=click.Path(path_type=Path),
    help="Path to the retry queue file.",
    default=DEFAULT_RETRY_QUEUE_FILEPATH,
)
def cli(
    log_level: str,
    log_filepath: Optional[Path],
    config_filepath: Optional[Path],
    retry_queue_filepath: Path,
):
    """
    Run analyzer.
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

    # Start up the StatusReporter process
    StatusReporter.start_thread(retry_queue_filepath)

    # Start listening for RabbitMQ messages telling us about newly
    # uploaded files. This blocks until we receive a shutdown signal.
    MessageListener(config)

    StatusReporter.stop_thread()
