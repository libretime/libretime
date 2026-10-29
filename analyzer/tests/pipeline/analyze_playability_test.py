from unittest.mock import patch

import pytest

from libretime_analyzer.pipeline.analyze_playability import (
    UnplayableFileError,
    analyze_playability,
)

from ..fixtures import FILE_INVALID_DRM, FILE_INVALID_TXT, FILES


@pytest.mark.parametrize(
    "filepath",
    [str(i.path) for i in FILES],
)
def test_analyze_playability(filepath):
    analyze_playability(filepath, {})


@pytest.mark.parametrize(
    "filepath",
    [str(i) for i in [FILE_INVALID_DRM, FILE_INVALID_TXT]],
)
def test_analyze_playability_invalid_file(filepath):
    with pytest.raises(UnplayableFileError):
        analyze_playability(filepath, {})


def test_analyze_playability_missing_ffmpeg():
    with patch(
        "libretime_analyzer.pipeline._ffmpeg.FFMPEG",
        "foobar",
    ):
        analyze_playability(str(FILES[0].path), {})


def test_analyze_playability_invalid_filepath():
    with pytest.raises(UnplayableFileError):
        analyze_playability("non-existent-file", {})


def test_analyze_playability_unknown():
    with pytest.raises(UnplayableFileError):
        analyze_playability("https://www.google.com", {})
