from abc import ABC, abstractmethod
from os import PathLike
from pathlib import Path
from typing import Optional

from loguru import logger

from .logging import LogLevel, setup_logger


# pylint: disable=too-few-public-methods
class AbstractApp(ABC):
    """
    Abstracts the creation of an application to reduce boilerplate code such
    as logging setup.
    """

    log_level: LogLevel

    @property
    @abstractmethod
    def name(self) -> str:
        ...

    def __init__(
        self,
        *,
        verbosity: int,
        log_filepath: Optional[PathLike] = None,
    ):
        if log_filepath is not None:
            log_filepath = Path(log_filepath)

        self.log_level = setup_logger(verbosity=verbosity, filepath=log_filepath)

        logger.info(f"Starting {self.name}...")
