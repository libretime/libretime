import re
from math import inf
from os import getenv
from pathlib import Path
from typing import List, Optional, Tuple

from ._utils import run_

FFPROBE = getenv("FFPROBE_PATH", "ffprobe")
FFMPEG = getenv("FFMPEG_PATH", "ffmpeg")


def _ffmpeg(*args, **kwargs):
    return run_(
        FFMPEG,
        *args,
        "-f",
        "null",
        "/dev/null",
        "-hide_banner",
        "-nostats",
        **kwargs,
    )


def _ffprobe(*args, **kwargs):
    return run_(FFPROBE, *args, **kwargs)


_PROBE_REPLAYGAIN_RE = re.compile(
    r".*REPLAYGAIN_TRACK_GAIN: ([-+]?[0-9]+\.[0-9]+) dB.*",
)


def probe_replaygain(filepath: Path) -> Optional[float]:
    """
    Probe replaygain will probe the given audio file and return the replaygain if available.
    """
    cmd = _ffprobe("-i", filepath, errors="backslashreplace")

    track_gain_match = _PROBE_REPLAYGAIN_RE.search(cmd.stderr)

    if track_gain_match:
        return float(track_gain_match.group(1))
    return None


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
    return None


_SILENCE_DETECT_RE = re.compile(
    r"\[silencedetect.*\] silence_(start|end): (-?\d+(?:\.\d+)?)(?: \| silence_duration: (\d+(?:\.\d+)?))?"
)


def compute_silences(filepath: Path) -> List[Tuple[float, float]]:
    """
    Compute silence will analyse the given audio file and return a list of silences.
    """
    cmd = _ffmpeg(
        *("-i", filepath),
        "-vn",
        *("-filter", "highpass=frequency=80,silencedetect=noise=-60dB:duration=0.9"),
    )

    starts, ends = [], []
    for line in cmd.stderr.splitlines():
        match = _SILENCE_DETECT_RE.search(line)
        if match is None:
            continue

        kind = match.group(1)
        if kind == "start":
            start = float(match.group(2))
            start = max(start, 0.0)
            starts.append(start)
        elif kind == "end":
            end = float(match.group(2))
            ends.append(end)

    # If one end is missing, set the last silence ending to infinity, and
    # clamp it to the track duration before using this value.
    if len(starts) - 1 == len(ends):
        ends.append(inf)

    return list(zip(starts, ends))


def probe_duration(filepath: Path) -> float:
    """
    Probe duration will probe the given audio file and return the duration.
    """
    cmd = _ffprobe(
        *("-i", filepath),
        *("-show_entries", "format=duration"),
        *("-v", "quiet"),
        *("-of", "csv=p=0"),
    )
    return float(cmd.stdout.strip("\n"))
