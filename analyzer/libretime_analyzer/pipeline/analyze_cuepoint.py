import logging
from datetime import timedelta
from math import isclose
from subprocess import CalledProcessError
from typing import Any, Dict

from ._ffmpeg import compute_silences, probe_duration

logger = logging.getLogger(__name__)


def analyze_duration(filepath: str, metadata: Dict[str, Any]) -> Dict[str, Any]:
    """
    Extracts the file duration using ffmpeg.
    """
    try:
        duration = probe_duration(filepath)

        if "length_seconds" in metadata and not isclose(
            metadata["length_seconds"],
            duration,
            abs_tol=0.1,
        ):
            logger.warning(
                f"existing duration {metadata['length_seconds']} differs "
                f"from the probed duration {duration}."
            )

        metadata["length_seconds"] = duration
        metadata["length"] = str(timedelta(seconds=duration))
        metadata["cuein"] = 0.0
        metadata["cueout"] = duration
    except (CalledProcessError, OSError):
        pass

    return metadata


def analyze_cuepoint(filepath: str, metadata: Dict[str, Any]) -> Dict[str, Any]:
    """
    Extracts the cuein and cueout times using ffmpeg.

    This step must run after the 'analyze_duration' step.
    """

    # Duration has been computed in the 'analyze_duration' step
    duration = metadata["length_seconds"]

    try:
        silences = compute_silences(filepath)

        if len(silences) > 2:
            # Only keep first and last silence
            silences = silences[:: len(silences) - 1]

        for silence in silences:
            # Sanity check
            if silence[0] >= silence[1]:
                raise ValueError(
                    f"silence starts ({silence[0]}) after ending ({silence[1]})"
                )

            # Is this really the first silence ?
            if isclose(
                0.0,
                max(0.0, silence[0]),  # Clamp negative value
                abs_tol=0.1,
            ):
                metadata["cuein"] = max(0.0, silence[1])

            # Is this really the last silence ?
            elif isclose(
                min(silence[1], duration),  # Clamp infinity value
                duration,
                abs_tol=0.1,
            ):
                metadata["cueout"] = min(silence[0], duration)

        metadata["cuein"] = format(metadata["cuein"], "f")
        metadata["cueout"] = format(metadata["cueout"], "f")

    except (CalledProcessError, OSError):
        pass

    return metadata
