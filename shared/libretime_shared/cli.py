from pathlib import Path
from typing import Any, Callable, Optional

import click


def cli_logging_options() -> Callable:
    def decorator(func: Callable) -> Callable:
        """
        Decorator function to add logging options to a click application.

        This decorator add the following arguments:
        - log_level: str
        - log_filepath: Optional[Path]
        """
        func = click.option(
            "--log-level",
            "log_level",
            type=click.Choice(["error", "warning", "info", "debug"]),
            default="info",
            help="Name of the logging level.",
        )(func)

        func = click.option(
            "--log-filepath",
            "log_filepath",
            type=click.Path(path_type=Path),
            help="Path to the logging file.",
            default=None,
        )(func)

        return func

    return decorator


def cli_config_options(
    required: bool = False,
    default: Optional[Any] = None,
) -> Callable:
    def decorator(func: Callable) -> Callable:
        """
        Decorator function to add config file options to a click application.

        This decorator add the following arguments:
        - config_filepath: Optional[Path] or Path
        """

        func = click.option(
            "--c",
            "--config",
            "config_filepath",
            type=click.Path(path_type=Path),
            help="Path to the config file.",
            required=required,
            default=default,
        )(func)

        return func

    return decorator
