import pytest
from airtime_analyzer.cuepoint_analyzer import CuePointAnalyzer


@pytest.mark.parametrize(
    "filepath",
    [
        ("tests/test_data/44100Hz-16bit-mono.mp3"),
        ("tests/test_data/44100Hz-16bit-dualmono.mp3"),
        ("tests/test_data/44100Hz-16bit-stereo.mp3"),
        ("tests/test_data/44100Hz-16bit-stereo-utf8.mp3"),
        ("tests/test_data/44100Hz-16bit-simplestereo.mp3"),
        ("tests/test_data/44100Hz-16bit-jointstereo.mp3"),
        # ("tests/test_data/44100Hz-16bit-mp3-missingid3header.mp3"),
        ("tests/test_data/44100Hz-16bit-mono.ogg"),
        ("tests/test_data/44100Hz-16bit-stereo.ogg"),
        # ("tests/test_data/44100Hz-16bit-stereo-invalid.wma"),
        ("tests/test_data/44100Hz-16bit-stereo.m4a"),
        ("tests/test_data/44100Hz-16bit-stereo.wav"),
    ],
)
def test_analyze(filepath):
    metadata = CuePointAnalyzer.analyze(filepath, dict())

    # We give silan some leeway here by specifying a tolerance
    tolerance_seconds = 0.1
    length_seconds = 3.9
    assert abs(metadata["length_seconds"] - length_seconds) < tolerance_seconds
    assert abs(float(metadata["cuein"])) < tolerance_seconds
    assert abs(float(metadata["cueout"]) - length_seconds) < tolerance_seconds


def test_analyze_missing_silan():
    old = CuePointAnalyzer.SILAN_EXECUTABLE
    CuePointAnalyzer.SILAN_EXECUTABLE = "foobar"
    CuePointAnalyzer.analyze("tests/test_data/44100Hz-16bit-mono.mp3", dict())
    CuePointAnalyzer.SILAN_EXECUTABLE = old


def test_analyze_invalid_filepath():
    with pytest.raises(KeyError):
        test_analyze("non-existent-file")


def test_analyze_invalid_wma():
    with pytest.raises(KeyError):
        test_analyze("tests/test_data/44100Hz-16bit-stereo-invalid.wma")
