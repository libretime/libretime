import logging
import os
from pathlib import Path
from typing import Optional

import click
from libretime_api_client.v2 import ApiClient
from libretime_shared.cli import cli_config_options, cli_logging_options
from libretime_shared.config import DEFAULT_ENV_PREFIX
from libretime_shared.logging import setup_logger

from ..config import Config
from .entrypoint import generate_entrypoint
from .models import Info, StreamPreferences
from .version import get_liquidsoap_version

logger = logging.getLogger(__name__)

here = Path(__file__).parent


@click.command(context_settings={"auto_envvar_prefix": DEFAULT_ENV_PREFIX})
@cli_logging_options()
@cli_config_options()
def cli(log_level: str, log_filepath: Optional[Path], config_filepath: Optional[Path]):
    """
    Run liquidsoap.
    """
    setup_logger(log_level, log_filepath)
    config = Config(config_filepath)

    api_client = ApiClient(
        base_url=config.general.public_url,
        api_key=config.general.api_key,
    )

    version = get_liquidsoap_version()

    info = Info(**api_client.get_info().json())
    preferences = StreamPreferences(**api_client.get_stream_preferences().json())

    entrypoint_filepath = Path.cwd() / "radio.liq"
    generate_entrypoint(
        entrypoint_filepath,
        log_filepath,
        config,
        preferences,
        info,
        version,
    )

    exec_args = [
        "/usr/bin/liquidsoap",
        "libretime-liquidsoap",
        "--verbose",
        str(entrypoint_filepath),
    ]
    if log_level == "debug":
        exec_args.append("--debug")

    logger.debug(f"liquidsoap {version} using script: {entrypoint_filepath}")
    os.execl(*exec_args)
