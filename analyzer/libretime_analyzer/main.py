from pathlib import Path
from typing import Optional

import click
from kombu import Connection
from libretime_shared.cli import cli_config_options, cli_logging_options
from libretime_shared.logging import level_from_name, setup_logger
from loguru import logger

from .config import Config
from .message_handler import MessageHandler


@click.command()
@cli_logging_options()
@cli_config_options()
def cli(
    log_level: str,
    log_filepath: Optional[Path],
    config_filepath: Optional[Path],
):
    """
    Run analyzer.
    """
    setup_logger(level_from_name(log_level), log_filepath)
    config = Config(filepath=config_filepath)

    with Connection(config.rabbitmq.url) as connection:
        logger.info("starting message handler")
        message_handler = MessageHandler(connection)
        message_handler.run()
