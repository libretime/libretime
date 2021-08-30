import pytest
from airtime_analyzer.playability_analyzer import (
    PlayabilityAnalyzer,
    UnplayableFileError,
)

from .fixtures import FILE_INVALID_DRM, FILES, Fixture


@pytest.mark.parametrize(
    "filepath",
    map(lambda i: str(i.path), FILES),
)
def test_analyze(filepath):
    PlayabilityAnalyzer.analyze(filepath, dict())


def test_analyze_missing_liquidsoap():
    old = PlayabilityAnalyzer.LIQUIDSOAP_EXECUTABLE
    PlayabilityAnalyzer.LIQUIDSOAP_EXECUTABLE = "foobar"
    PlayabilityAnalyzer.analyze(str(FILES[0].path), dict())
    PlayabilityAnalyzer.LIQUIDSOAP_EXECUTABLE = old


def test_analyze_invalid_filepath():
    with pytest.raises(UnplayableFileError):
        test_analyze("non-existent-file")


# This test is not be consistent with all Liquidsoap versions.
def test_analyze_invalid_wma():
    with pytest.raises(UnplayableFileError):
        test_analyze(FILE_INVALID_DRM)


def test_analyze_unknown():
    with pytest.raises(UnplayableFileError):
        test_analyze("https://www.google.com")
