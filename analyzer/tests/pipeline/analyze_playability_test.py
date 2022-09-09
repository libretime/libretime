from unittest.mock import patch

import distro
import pytest

from libretime_analyzer.pipeline.analyze_playability import (
    UnplayableFileError,
    analyze_playability,
)

from ..fixtures import FILE_INVALID_DRM, FILES


@pytest.mark.parametrize(
    "filepath",
    map(lambda i: str(i.path), FILES),
)
def test_analyze_playability(filepath):
    analyze_playability(filepath, {})


def test_analyze_playability_missing_liquidsoap():
    with patch(
        "libretime_analyzer.pipeline._liquidsoap.LIQUIDSOAP",
        "foobar",
    ):
        analyze_playability(str(FILES[0].path), {})


def test_analyze_playability_invalid_filepath():
    with pytest.raises(UnplayableFileError):
        test_analyze_playability("non-existent-file")


def test_analyze_playability_invalid_wma():
    # Liquisoap does not fail with wma files on bullseye, focal, jammy
    if distro.codename() in ("bullseye", "focal", "jammy"):
        return

    with pytest.raises(UnplayableFileError):
        test_analyze_playability(FILE_INVALID_DRM)


def test_analyze_playability_unknown():
    with pytest.raises(UnplayableFileError):
        test_analyze_playability("https://www.google.com")
