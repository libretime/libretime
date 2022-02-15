from pathlib import Path
from unittest.mock import patch

import distro
import pytest

from libretime_analyzer.pipeline.analyze_playability import analyze_playability
from libretime_analyzer.pipeline.exceptions import PipelineError

from ..conftest import context_factory
from ..fixtures import FILE_INVALID_DRM, FILES


@pytest.mark.parametrize(
    "filepath",
    map(lambda i: i.path, FILES),
)
def test_analyze_playability(filepath: Path):
    analyze_playability(context_factory(filepath))


def test_analyze_playability_missing_liquidsoap():
    with patch(
        "libretime_analyzer.pipeline.analyze_playability.LIQUIDSOAP_EXECUTABLE",
        "foobar",
    ):
        with pytest.raises(FileNotFoundError):
            analyze_playability(context_factory(FILES[0].path))


def test_analyze_playability_invalid_filepath():
    with pytest.raises(PipelineError):
        test_analyze_playability(Path("non-existent-file"))


def test_analyze_playability_invalid_wma():
    # Liquisoap does not fail with wma files on buster, bullseye, focal, jammy
    if distro.codename() in ("buster", "bullseye", "focal", "jammy"):
        return

    with pytest.raises(PipelineError):
        test_analyze_playability(FILE_INVALID_DRM)


def test_analyze_playability_unknown():
    with pytest.raises(PipelineError):
        test_analyze_playability(Path("https://www.google.com"))
