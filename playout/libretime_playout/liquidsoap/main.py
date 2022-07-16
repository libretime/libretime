""" Runs Airtime liquidsoap
"""
import os
import subprocess
from pathlib import Path
from typing import Optional

import click
from libretime_shared.cli import cli_logging_options
from libretime_shared.config import DEFAULT_ENV_PREFIX
from libretime_shared.logging import level_from_name, setup_logger
from loguru import logger

from . import generate_liquidsoap_cfg


@click.command(context_settings={"auto_envvar_prefix": DEFAULT_ENV_PREFIX})
@cli_logging_options()
def cli(log_level: int, log_filepath: Optional[Path]):
    """
    Run liquidsoap.
    """
    log_level = level_from_name(log_level)
    setup_logger(log_level, log_filepath)

    generate_liquidsoap_cfg.run(log_filepath)
    # check liquidsoap version so we can run a scripts matching the liquidsoap minor version
    liquidsoap_version = subprocess.check_output(
        "liquidsoap 'print(liquidsoap.version) shutdown()'",
        shell=True,
        universal_newlines=True,
    )[0:3]
    script_path = os.path.join(
        os.path.dirname(__file__), liquidsoap_version, "ls_script.liq"
    )
    exec_args = [
        "/usr/bin/liquidsoap",
        "libretime-liquidsoap",
        "--verbose",
        script_path,
    ]
    if log_level.is_debug():
        exec_args.append("--debug")

    logger.debug(f"Liquidsoap {liquidsoap_version} using script: {script_path}")
    os.execl(*exec_args)
