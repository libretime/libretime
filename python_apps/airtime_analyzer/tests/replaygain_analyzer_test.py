import pytest
from airtime_analyzer.replaygain_analyzer import ReplayGainAnalyzer


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
        # ("tests/test_data/44100Hz-16bit-stereo.wav"), # WAV is not supported by rgain3
    ],
)
def test_analyze(filepath):
    metadata = ReplayGainAnalyzer.analyze(filepath, dict())

    # We give rgain3 some leeway here by specifying a tolerance
    tolerance = 0.60
    expected_replaygain = 5.2
    assert abs(metadata["replay_gain"] - expected_replaygain) < tolerance


def test_analyze_missing_replaygain():
    old = ReplayGainAnalyzer.REPLAYGAIN_EXECUTABLE
    ReplayGainAnalyzer.REPLAYGAIN_EXECUTABLE = "foobar"
    ReplayGainAnalyzer.analyze("tests/test_data/44100Hz-16bit-mono.mp3", dict())
    ReplayGainAnalyzer.REPLAYGAIN_EXECUTABLE = old


def test_analyze_invalid_filepath():
    with pytest.raises(KeyError):
        test_analyze("non-existent-file")


def test_analyze_invalid_wma():
    with pytest.raises(KeyError):
        test_analyze("tests/test_data/44100Hz-16bit-stereo-invalid.wma")
