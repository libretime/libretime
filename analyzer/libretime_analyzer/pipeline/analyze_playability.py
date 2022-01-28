__author__ = "asantoni"

import subprocess
from typing import Any, Dict

from loguru import logger


class UnplayableFileError(Exception):
    pass


LIQUIDSOAP_EXECUTABLE = "liquidsoap"


def analyze_playability(filename: str, metadata: Dict[str, Any]):
    """Checks if a file can be played by Liquidsoap.
    :param filename: The full path to the file to analyzer
    :param metadata: A metadata dictionary where the results will be put
    :return: The metadata dictionary
    """
    command = [
        LIQUIDSOAP_EXECUTABLE,
        "-v",
        "-c",
        "output.dummy(audio_to_stereo(single(argv(1))))",
        "--",
        filename,
    ]
    try:
        subprocess.check_output(command, stderr=subprocess.STDOUT, close_fds=True)

    except OSError as e:  # liquidsoap was not found
        logger.warning(
            "Failed to run: %s - %s. %s"
            % (command[0], e.strerror, "Do you have liquidsoap installed?")
        )
    except (
        subprocess.CalledProcessError,
        Exception,
    ) as e:  # liquidsoap returned an error code
        logger.warning(e)
        raise UnplayableFileError()

    return metadata
