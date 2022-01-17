from unittest.mock import patch

import distro
import pytest

from libretime_analyzer.steps.analyze_cuepoint import analyze_cuepoint

from ..fixtures import FILE_INVALID_DRM, FILES, Fixture


@pytest.mark.parametrize(
    "filepath,length,cuein,cueout",
    map(lambda i: (str(i.path), i.length, i.cuein, i.cueout), FILES),
)
def test_analyze(filepath, length, cuein, cueout):
    metadata = analyze_cuepoint(filepath, dict())

    assert metadata["length_seconds"] == pytest.approx(length, abs=0.1)

    # Silan does not work with m4a files yet
    if filepath.endswith("m4a"):
        return

    # Silan does not work with mp3 on buster, bullseye, focal
    if filepath.endswith("mp3") and distro.codename() in (
        "buster",
        "bullseye",
        "focal",
    ):
        return

    assert float(metadata["cuein"]) == pytest.approx(cuein, abs=0.5)
    assert float(metadata["cueout"]) == pytest.approx(cueout, abs=0.5)


def test_analyze_missing_silan():
    with patch(
        "libretime_analyzer.steps.analyze_cuepoint.SILAN_EXECUTABLE",
        "foobar",
    ):
        analyze_cuepoint(str(FILES[0].path), dict())


def test_analyze_invalid_filepath():
    with pytest.raises(KeyError):
        test_analyze("non-existent-file", None, None, None)


def test_analyze_invalid_wma():
    with pytest.raises(KeyError):
        test_analyze(FILE_INVALID_DRM, None, None, None)
