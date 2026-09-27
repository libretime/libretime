import logging
from subprocess import CalledProcessError
from typing import Any

from ._ffmpeg import _ffmpeg

logger = logging.getLogger(__name__)


class UnplayableFileError(Exception):
    pass


def analyze_playability(filename: str, metadata: dict[str, Any]):
    """
    Checks if a file can be played by ffmpeg.
    """
    try:
        _ffmpeg(
            *("-v", "error"),
            *("-i", filename),
        )
    except CalledProcessError as exception:
        logger.warning(exception)
        raise UnplayableFileError() from exception

    except OSError as exception:  # ffmpeg was not found
        logger.warning("Failed to run: %s. Is ffmpeg installed?", exception)

    return metadata
