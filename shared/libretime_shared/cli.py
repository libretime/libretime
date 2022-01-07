from typing import Callable

import click

from .config import DEFAULT_ENV_PREFIX
from .logging import INFO, LOG_LEVEL_MAP


def cli_logging_options(func: Callable) -> Callable:
    """
    Decorator function to add logging options to a click application.

    This decorator add the following arguments:
    - log_level: str
    - log_filepath: Optional[Path]
    """
    func = click.option(
        "--log-level",
        "log_level",
        envvar=f"{DEFAULT_ENV_PREFIX}_LOG_LEVEL",
        type=click.Choice(list(LOG_LEVEL_MAP.keys())),
        default=INFO.name,
        help="Name of the logging level.",
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
