""" Runs Airtime liquidsoap
"""
import os
from pathlib import Path
from typing import Optional

import click
from libretime_shared.cli import cli_logging_options
from libretime_shared.config import DEFAULT_ENV_PREFIX
from libretime_shared.logging import level_from_name, setup_logger
from loguru import logger

from .entrypoint import generate_entrypoint
from .version import get_liquidsoap_version


@click.command(context_settings={"auto_envvar_prefix": DEFAULT_ENV_PREFIX})
@cli_logging_options()
def cli(log_level: int, log_filepath: Optional[Path]):
    """
    Run liquidsoap.
    """
    log_level = level_from_name(log_level)
    setup_logger(log_level, log_filepath)

    generate_entrypoint(log_filepath)

    version = get_liquidsoap_version()

    script_path = os.path.join(
        os.path.dirname(__file__), f"{version[0]}.{version[1]}", "ls_script.liq"
    )
    exec_args = [
        "/usr/bin/liquidsoap",
        "libretime-liquidsoap",
        "--verbose",
        script_path,
    ]
    if log_level.is_debug():
        exec_args.append("--debug")

    logger.debug(f"Liquidsoap {version} using script: {script_path}")
    os.execl(*exec_args)
