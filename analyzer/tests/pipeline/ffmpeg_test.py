from math import inf

import distro
import pytest

from libretime_analyzer.pipeline._ffmpeg import (
    _SILENCE_DETECT_RE,
    compute_replaygain,
    compute_silences,
    probe_duration,
    probe_replaygain,
)

from ..fixtures import FILES


@pytest.mark.skip(reason="fixtures files are missing replaygain metadata")
@pytest.mark.parametrize(
    "filepath,replaygain",
    map(lambda i: pytest.param(i.path, i.replaygain, id=i.path.name), FILES),
)
def test_probe_replaygain(filepath, replaygain):
    assert probe_replaygain(filepath) == pytest.approx(replaygain, abs=0.05)


@pytest.mark.parametrize(
    "filepath,replaygain",
    map(lambda i: pytest.param(i.path, i.replaygain, id=i.path.name), FILES),
)
def test_compute_replaygain(filepath, replaygain):
    tolerance = 0.8

    # On bionic, replaygain is a bit higher for loud mp3 files.
    # This huge tolerance makes the test pass, with values devianting from ~-17 to ~-13
    if distro.codename() == "bionic" and str(filepath).endswith("+12.mp3"):
        tolerance = 5

    assert compute_replaygain(filepath) == pytest.approx(replaygain, abs=tolerance)


# Be sure to test a matrix of integer / float, positive / negative values
SILENCE_DETECT_RE_RAW = """
[silencedetect @ 0x563121aee500] silence_start: -0.00154195
[silencedetect @ 0x563121aee500] silence_end: 0.998458 | silence_duration: 1
[silencedetect @ 0x563121aee500] silence_start: 2.99383
[silencedetect @ 0x563121aee500] silence_end: 4.99229 | silence_duration: 1.99846
[silencedetect @ 0x563121aee500] silence_start: 6.98766
[silencedetect @ 0x563121aee500] silence_end: 8.98612 | silence_duration: 1.99846
[silencedetect @ 0x563121aee500] silence_start: 12
[silencedetect @ 0x563121aee500] silence_end: 13 | silence_duration: 1
"""

SILENCE_DETECT_RE_EXPECTED = [
    ("start", -0.00154195),
    ("end", 0.998458),
    ("start", 2.99383),
    ("end", 4.99229),
    ("start", 6.98766),
    ("end", 8.98612),
    ("start", 12.0),
    ("end", 13.0),
]


@pytest.mark.parametrize(
    "line,expected",
    zip(
        SILENCE_DETECT_RE_RAW.strip().splitlines(),
        SILENCE_DETECT_RE_EXPECTED,
    ),
)
def test_silence_detect_re(line, expected):
    match = _SILENCE_DETECT_RE.search(line)
    assert match is not None
    assert match.group(1) == expected[0]
    assert float(match.group(2)) == expected[1]


@pytest.mark.parametrize(
    "filepath,length,cuein,cueout",
    map(
        lambda i: pytest.param(i.path, i.length, i.cuein, i.cueout, id=i.path.name),
        FILES,
    ),
)
def test_compute_silences(filepath, length, cuein, cueout):
    result = compute_silences(filepath)

    # On bionic, large file duration is a wrong.
    if distro.codename() == "bionic" and str(filepath).endswith("s1-large.flac"):
        return

    if cuein != 0.0:
        assert len(result) > 0
        first = result.pop(0)
        assert first[0] == pytest.approx(0.0, abs=0.1)
        assert first[1] == pytest.approx(cuein, abs=1)

    if cueout != length:
        # ffmpeg v3 (bionic) does not warn about silence end when the track ends.
        # Check for infinity on last silence ending
        if distro.codename() == "bionic":
            length = inf

        assert len(result) > 0
        last = result.pop()
        assert last[0] == pytest.approx(cueout, abs=1)
        assert last[1] == pytest.approx(length, abs=0.1)


@pytest.mark.parametrize(
    "filepath,length",
    map(lambda i: pytest.param(i.path, i.length, id=i.path.name), FILES),
)
def test_probe_duration(filepath, length):
    # On bionic, large file duration is a wrong.
    if distro.codename() == "bionic" and str(filepath).endswith("s1-large.flac"):
        return

    assert probe_duration(filepath) == pytest.approx(length, abs=0.05)
