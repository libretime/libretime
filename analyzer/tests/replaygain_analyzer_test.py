import pytest
from airtime_analyzer.replaygain_analyzer import ReplayGainAnalyzer

from .fixtures import FILE_INVALID_DRM, FILES, Fixture


@pytest.mark.parametrize(
    "filepath,replaygain",
    map(lambda i: (str(i.path), i.replaygain), FILES),
)
def test_analyze(filepath, replaygain):
    metadata = ReplayGainAnalyzer.analyze(filepath, dict())
    assert metadata["replay_gain"] == pytest.approx(replaygain, abs=0.6)


def test_analyze_missing_replaygain():
    old = ReplayGainAnalyzer.REPLAYGAIN_EXECUTABLE
    ReplayGainAnalyzer.REPLAYGAIN_EXECUTABLE = "foobar"
    ReplayGainAnalyzer.analyze(str(FILES[0].path), dict())
    ReplayGainAnalyzer.REPLAYGAIN_EXECUTABLE = old


def test_analyze_invalid_filepath():
    with pytest.raises(KeyError):
        test_analyze("non-existent-file", None)


def test_analyze_invalid_wma():
    with pytest.raises(KeyError):
        test_analyze(FILE_INVALID_DRM, None)
