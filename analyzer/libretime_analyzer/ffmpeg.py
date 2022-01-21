import re
from pathlib import Path
from typing import Optional

from .utils import run_


def _ffmpeg(*args, **kwargs):
    return run_(
        "ffmpeg",
        *args,
        "-f",
        "null",
        "/dev/null",
        "-hide_banner",
        "-nostats",
        **kwargs,
    )


def _ffprobe(*args, **kwargs):
    return run_("ffprobe", *args, **kwargs)


_PROBE_REPLAYGAIN_RE = re.compile(
    r".*REPLAYGAIN_TRACK_GAIN: ([-+]?[0-9]+\.[0-9]+) dB.*",
)


def probe_replaygain(filepath: Path) -> Optional[float]:
    """
    Probe replaygain will probe the given audio file and return the replaygain if available.
    """
    cmd = _ffprobe("-i", filepath)

    track_gain_match = _PROBE_REPLAYGAIN_RE.search(cmd.stderr)

    if track_gain_match:
        return float(track_gain_match.group(1))


_COMPUTE_REPLAYGAIN_RE = re.compile(
    r".* track_gain = ([-+]?[0-9]+\.[0-9]+) dB.*",
)


def compute_replaygain(filepath: Path) -> Optional[float]:
    """
    Compute replaygain will analyse the given audio file and return the replaygain if available.
    """
    cmd = _ffmpeg("-i", filepath, "-vn", "-filter", "replaygain")

    track_gain_match = _COMPUTE_REPLAYGAIN_RE.search(cmd.stderr)

    if track_gain_match:
        return float(track_gain_match.group(1))
