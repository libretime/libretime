import sys
from copy import deepcopy
from pathlib import Path
from typing import TYPE_CHECKING, NamedTuple, Optional, Tuple

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

LOG_LEVEL_MAP = {
    ERROR.name: ERROR,
    WARNING.name: WARNING,
    INFO.name: INFO,
    DEBUG.name: DEBUG,
    TRACE.name: TRACE,
}


def level_from_name(name: str) -> LogLevel:
    """
    Find logging level, depending on the name provided.

    :param name: name (one of "error", "warning", "info", "debug", "trace") of the log level
    :returns: log level guessed from the name
    :raises ValueError: on invalid level name
    """
    name = name.lower()
    if name not in LOG_LEVEL_MAP:
        raise ValueError(f"invalid level name '{name}'")
    return LOG_LEVEL_MAP[name]


def setup_logger(
    level: LogLevel,
    filepath: Optional[Path] = None,
    serialize: bool = False,
    rotate: bool = True,
) -> Tuple[LogLevel, Optional[Path]]:
    """
    Configure the logger and return the computed log level.

    See https://loguru.readthedocs.io/en/stable/overview.html

    :param verbosity: verbosity (between -1 and 3) of the logger
    :param filepath: write logs to filepath
    :param serialize: generate JSON formatted log records
    :param rotate: enable log rotation and retention
    :returns: log level guessed from the verbosity
    """
    handlers = [{"sink": sys.stderr, "level": level.no, "serialize": serialize}]

    if filepath is not None:
        file_handler = {
            "sink": filepath,
            "enqueue": True,
            "level": level.no,
            "serialize": serialize,
            "encoding": "utf-8",
        }
        if rotate:
            file_handler.update(
                {
                    "rotation": "12:00",
                    "retention": "7 days",
                    "compression": "gz",
                }
            )

        handlers.append(file_handler)

    logger.configure(handlers=handlers)

    return level, filepath


_empty_logger = deepcopy(logger)


def create_task_logger(
    level: LogLevel,
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

    task_logger.configure(
        handlers=[
            {
                "sink": filepath,
                "enqueue": True,
                "level": level.no,
                "serialize": serialize,
                "rotation": "12:00",
                "retention": "7 days",
                "encoding": "utf-8",
            }
        ],
    )

    return task_logger
