import pytest
from airtime_analyzer.playability_analyzer import (
    PlayabilityAnalyzer,
    UnplayableFileError,
)


@pytest.mark.parametrize(
    "filepath",
    [
        ("tests/test_data/44100Hz-16bit-mono.mp3"),
        ("tests/test_data/44100Hz-16bit-dualmono.mp3"),
        ("tests/test_data/44100Hz-16bit-stereo.mp3"),
        ("tests/test_data/44100Hz-16bit-stereo-utf8.mp3"),
        ("tests/test_data/44100Hz-16bit-simplestereo.mp3"),
        ("tests/test_data/44100Hz-16bit-jointstereo.mp3"),
        ("tests/test_data/44100Hz-16bit-mp3-missingid3header.mp3"),
        ("tests/test_data/44100Hz-16bit-mono.ogg"),
        ("tests/test_data/44100Hz-16bit-stereo.ogg"),
        # ("tests/test_data/44100Hz-16bit-stereo-invalid.wma"),
        ("tests/test_data/44100Hz-16bit-stereo.m4a"),
        ("tests/test_data/44100Hz-16bit-stereo.wav"),
    ],
)
def test_analyze(filepath):
    PlayabilityAnalyzer.analyze(filepath, dict())


def test_analyze_missing_liquidsoap():
    old = PlayabilityAnalyzer.LIQUIDSOAP_EXECUTABLE
    PlayabilityAnalyzer.LIQUIDSOAP_EXECUTABLE = "foobar"
    PlayabilityAnalyzer.analyze("tests/test_data/44100Hz-16bit-mono.mp3", dict())
    PlayabilityAnalyzer.LIQUIDSOAP_EXECUTABLE = old


def test_analyze_invalid_filepath():
    with pytest.raises(UnplayableFileError):
        test_analyze("non-existent-file")


# This test is not be consistent with all Liquidsoap versions.
def test_analyze_invalid_wma():
    with pytest.raises(UnplayableFileError):
        test_analyze("tests/test_data/44100Hz-16bit-stereo-invalid.wma")


def test_analyze_unknown():
    with pytest.raises(UnplayableFileError):
        test_analyze("https://www.google.com")
