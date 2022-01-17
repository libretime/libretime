from unittest.mock import patch

import distro
import pytest

from libretime_analyzer.steps.analyze_playability import (
    UnplayableFileError,
    analyze_playability,
)

from ..fixtures import FILE_INVALID_DRM, FILES, Fixture


@pytest.mark.parametrize(
    "filepath",
    map(lambda i: str(i.path), FILES),
)
def test_analyze(filepath):
    analyze_playability(filepath, dict())


def test_analyze_missing_liquidsoap():
    with patch(
        "libretime_analyzer.steps.analyze_playability.LIQUIDSOAP_EXECUTABLE",
        "foobar",
    ):
        analyze_playability(str(FILES[0].path), dict())


def test_analyze_invalid_filepath():
    with pytest.raises(UnplayableFileError):
        test_analyze("non-existent-file")


def test_analyze_invalid_wma():
    # Liquisoap does not fail with wma files on buster, bullseye, focal
    if distro.codename() in ("buster", "bullseye", "focal"):
        return

    with pytest.raises(UnplayableFileError):
        test_analyze(FILE_INVALID_DRM)


def test_analyze_unknown():
    with pytest.raises(UnplayableFileError):
        test_analyze("https://www.google.com")
