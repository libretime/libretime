import logging
from subprocess import CalledProcessError
from typing import Any, Dict

from ._liquidsoap import _liquidsoap

logger = logging.getLogger(__name__)


class UnplayableFileError(Exception):
    pass


def analyze_playability(filename: str, metadata: Dict[str, Any]):
    """
    Checks if a file can be played by Liquidsoap.
    """
    try:
        _liquidsoap(
            "-v",
            *("-c", "output.dummy(audio_to_stereo(single(argv(1))))"),
            "--",
            filename,
        )
    except CalledProcessError as exception:
        logger.warning(exception)
        raise UnplayableFileError() from exception

    except OSError as exception:  # liquidsoap was not found
        logger.warning(f"Failed to run: {exception}. Is liquidsoap installed?")

    return metadata
