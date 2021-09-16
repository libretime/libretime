import sys
from copy import deepcopy
from pathlib import Path
from typing import TYPE_CHECKING, NamedTuple, Optional

from loguru import logger

if TYPE_CHECKING:
    from loguru import Logger

logger.remove()


class LogLevel(NamedTuple):
    name: str
    no: int

    def is_debug(self) -> bool:
        return self.no <= 10


# See https://loguru.readthedocs.io/en/stable/api/logger.html#levels
ERROR = LogLevel(name="error", no=40)
WARNING = LogLevel(name="warning", no=30)
INFO = LogLevel(name="info", no=20)
DEBUG = LogLevel(name="debug", no=10)
TRACE = LogLevel(name="trace", no=5)


def level_from_verbosity(verbosity: int) -> LogLevel:
    """
    Find logging level, depending on the verbosity requested.

    -q         -1   =>  ERROR
    default     0   =>  WARNING
    -v          1   =>  INFO
    -vv         2   =>  DEBUG
    -vvv        3   =>  TRACE

    :param verbosity: verbosity (between -1 and 3) of the logger
    :returns: log level guessed from the verbosity
    """
    if verbosity < 0:
        return ERROR
    return [WARNING, INFO, DEBUG, TRACE][min(3, verbosity)]


def setup_logger(
    verbosity: int,
    filepath: Optional[Path] = None,
    serialize: bool = False,
) -> LogLevel:
    """
    Configure the logger and return the computed log level.

    See https://loguru.readthedocs.io/en/stable/overview.html

    :param verbosity: verbosity (between -1 and 3) of the logger
    :param filepath: write logs to filepath
    :param serialize: generate JSON formatted log records
    :returns: log level guessed from the verbosity
    """
    level = level_from_verbosity(verbosity)

    handlers = [dict(sink=sys.stderr, level=level.no, serialize=serialize)]

    if filepath is not None:
        handlers.append(
            dict(
                sink=filepath,
                enqueue=True,
                level=level.no,
                serialize=serialize,
                rotation="12:00",
                retention="7 days",
            )
        )

    logger.configure(handlers=handlers)

    return level


_empty_logger = deepcopy(logger)


def create_task_logger(
    verbosity: int,
    filepath: Path,
    serialize: bool = False,
) -> "Logger":
    """
    Create and configure an independent logger for a task, return the new logger.

    See #creating-independent-loggers-with-separate-set-of-handlers in
    https://loguru.readthedocs.io/en/stable/resources/recipes.html

    :returns: new logger
    """
    task_logger = deepcopy(_empty_logger)

    level = level_from_verbosity(verbosity)

    task_logger.configure(
        handlers=[
            dict(
                sink=filepath,
                enqueue=True,
                level=level.no,
                serialize=serialize,
                rotation="12:00",
                retention="7 days",
            )
        ],
    )

    return task_logger
