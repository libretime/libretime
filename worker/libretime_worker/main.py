from pathlib import Path
from typing import Optional

import click
from libretime_shared.cli import cli_logging_options
from libretime_shared.config import DEFAULT_ENV_PREFIX

from .config import __name__ as config_module
from .tasks import worker


@click.command(context_settings={"auto_envvar_prefix": DEFAULT_ENV_PREFIX})
@cli_logging_options()
def cli(log_level: str, log_filepath: Optional[Path]):
    """
    Run celery.
    """
    args = [
        f"--config={config_module}",
        "--beat",
        "--time-limit=1800",
        "--concurrency=1",
        f"--loglevel={log_level}",
    ]
    if log_filepath is not None:
        args.append(f"--logfile={log_filepath}")

    worker.worker_main(args)
