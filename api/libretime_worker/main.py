from pathlib import Path

import click
from libretime_shared.cli import cli_logging_options
from libretime_shared.config import DEFAULT_ENV_PREFIX

from libretime_api.worker import app


@click.command(context_settings={"auto_envvar_prefix": DEFAULT_ENV_PREFIX})
@cli_logging_options()
def cli(log_level: str, log_filepath: Path | None):
    """
    Run celery.
    """
    args = [
        "--app=libretime_api.worker",
        "worker",
        "--beat",
        f"--loglevel={log_level}",
    ]
    if log_filepath is not None:
        args.append(f"--logfile={log_filepath}")

    app.worker_main(args)
