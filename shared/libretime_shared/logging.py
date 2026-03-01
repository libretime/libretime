import logging
from logging.handlers import TimedRotatingFileHandler
from pathlib import Path
from typing import List, Optional, Tuple

logger = logging.getLogger(__name__)


def setup_logger(
    level: str,
    filepath: Optional[Path] = None,
    serialize: bool = False,  # pylint: disable=unused-argument
    rotate: bool = True,
) -> Tuple[str, Optional[Path]]:
    """
    Configure the logger and return the log level and log filepath.
    """
    level = level.upper()

    root = logging.getLogger()
    root.setLevel(level)

    formatter = logging.Formatter(
        "%(asctime)s | %(levelname)-8s | %(name)s:%(funcName)s:%(lineno)s - %(message)s"
    )
    handlers: List[logging.Handler] = [logging.StreamHandler()]

    if filepath is not None:
        if rotate:
            handlers.append(TimedRotatingFileHandler(filepath, when="midnight"))
        else:
            handlers.append(logging.FileHandler(filepath))

    for handler in handlers:
        handler.setFormatter(formatter)
        root.addHandler(handler)

    return level, filepath
