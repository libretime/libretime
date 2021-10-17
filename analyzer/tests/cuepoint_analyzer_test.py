import distro
import pytest

from airtime_analyzer.cuepoint_analyzer import CuePointAnalyzer

from .fixtures import FILE_INVALID_DRM, FILES, Fixture


@pytest.mark.parametrize(
    "filepath,length,cuein,cueout",
    map(lambda i: (str(i.path), i.length, i.cuein, i.cueout), FILES),
)
def test_analyze(filepath, length, cuein, cueout):
    metadata = CuePointAnalyzer.analyze(filepath, dict())

    assert metadata["length_seconds"] == pytest.approx(length, abs=0.1)

    # Silan does not work with m4a files yet
    if filepath.endswith("m4a"):
        return

    # Silan does not work with mp3 on debian buster
    if filepath.endswith("mp3") and "buster" == distro.codename():
        return

    assert float(metadata["cuein"]) == pytest.approx(cuein, abs=0.5)
    assert float(metadata["cueout"]) == pytest.approx(cueout, abs=0.5)


def test_analyze_missing_silan():
    old = CuePointAnalyzer.SILAN_EXECUTABLE
    CuePointAnalyzer.SILAN_EXECUTABLE = "foobar"
    CuePointAnalyzer.analyze(str(FILES[0].path), dict())
    CuePointAnalyzer.SILAN_EXECUTABLE = old


def test_analyze_invalid_filepath():
    with pytest.raises(KeyError):
        test_analyze("non-existent-file", None, None, None)


def test_analyze_invalid_wma():
    with pytest.raises(KeyError):
        test_analyze(FILE_INVALID_DRM, None, None, None)
