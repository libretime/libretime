from unittest.mock import patch

import pytest

from libretime_analyzer.steps.analyze_replaygain import analyze_replaygain

from ..fixtures import FILE_INVALID_DRM, FILES, Fixture


@pytest.mark.parametrize(
    "filepath,replaygain",
    map(lambda i: (str(i.path), i.replaygain), FILES),
)
def test_analyze_replaygain(filepath, replaygain):
    metadata = analyze_replaygain(filepath, dict())
    assert metadata["replay_gain"] == pytest.approx(replaygain, abs=0.6)


def test_analyze_replaygain_missing_replaygain():
    with patch(
        "libretime_analyzer.steps.analyze_replaygain.REPLAYGAIN_EXECUTABLE",
        "foobar",
    ):
        analyze_replaygain(str(FILES[0].path), dict())


def test_analyze_replaygain_invalid_filepath():
    with pytest.raises(KeyError):
        test_analyze_replaygain("non-existent-file", None)


def test_analyze_invalid_wma():
    with pytest.raises(KeyError):
        test_analyze_replaygain(FILE_INVALID_DRM, None)
