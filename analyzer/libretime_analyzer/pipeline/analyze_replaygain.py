from subprocess import CalledProcessError
from typing import Any, Dict

from ._ffmpeg import compute_replaygain, probe_replaygain


def analyze_replaygain(filepath: str, metadata: Dict[str, Any]):
    """
    Extracts the Replaygain loudness normalization factor of a track using ffmpeg.
    """
    try:
        # First probe for existing replaygain metadata.
        track_gain = probe_replaygain(filepath)
        if track_gain is not None:
            metadata["replay_gain"] = track_gain
            return metadata
    except (CalledProcessError, OSError):
        pass

    try:
        track_gain = compute_replaygain(filepath)
        if track_gain is not None:
            metadata["replay_gain"] = track_gain
    except (CalledProcessError, OSError):
        pass

    return metadata
