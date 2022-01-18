import sys
from pathlib import Path
from shutil import which as which_base
from subprocess import CalledProcessError, run

import click


def warning(msg):
    click.secho(f"warning: {msg}", fg="magenta")


def error(msg):
    click.secho(f"error: {msg}", fg="red")


def fatal(msg):
    error(msg)
    sys.exit(1)


def which(cmd: str) -> Path:
    """
    Given a command name, return the path of the command or exit.
    """
    cmd_path = which_base(cmd)
    if cmd_path is None:
        fatal(f"could not find executable '{cmd}'")

    return Path(cmd_path)


def run_(*args, **kwargs):
    try:
        return run(args, check=True, capture_output=True, text=True, **kwargs)
    except CalledProcessError as exception:
        error(exception.stderr)
        fatal(exception)
