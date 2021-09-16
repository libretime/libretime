from typing import Callable

import click

from .config import DEFAULT_ENV_PREFIX


def cli_logging_options(func: Callable) -> Callable:
    """
    Decorator function to add logging options to a click application.

    This decorator add the following arguments:
    - verbosity: int
    - log_filepath: Path
    """
    func = click.option(
        "-v",
        "--verbose",
        "verbosity",
        envvar=f"{DEFAULT_ENV_PREFIX}_VERBOSITY",
        count=True,
        default=0,
        help="Increase logging verbosity (use -vvv to debug).",
    )(func)

    func = click.option(
        "-q",
        "--quiet",
        "verbosity",
        is_flag=True,
        flag_value=-1,
        help="Decrease logging verbosity.",
    )(func)

    func = click.option(
        "--log-filepath",
        "log_filepath",
        envvar=f"{DEFAULT_ENV_PREFIX}_LOG_FILEPATH",
        type=click.Path(),
        help="Path to the logging file.",
        default=None,
    )(func)

    return func


def cli_config_options(func: Callable) -> Callable:
    """
    Decorator function to add config file options to a click application.

    This decorator add the following arguments:
    - config_filepath: Path
    """

    func = click.option(
        "--c",
        "--config",
        "config_filepath",
        envvar=f"{DEFAULT_ENV_PREFIX}_CONFIG_FILEPATH",
        type=click.Path(),
        help="Path to the config file.",
        default=None,
    )(func)

    return func
