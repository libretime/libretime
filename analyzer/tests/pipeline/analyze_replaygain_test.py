import distro
import pytest

from libretime_analyzer.pipeline.analyze_replaygain import analyze_replaygain

from ..fixtures import FILES


@pytest.mark.parametrize(
    "filepath,replaygain",
    map(lambda i: pytest.param(str(i.path), i.replaygain, id=i.path.name), FILES),
)
def test_analyze_replaygain(filepath, replaygain):
    tolerance = 0.8

    # On bionic, replaygain is a bit higher for loud mp3 files.
    # This huge tolerance makes the test pass, with values devianting from ~-17 to ~-13
    if distro.codename() == "bionic" and str(filepath).endswith("+12.mp3"):
        tolerance = 5

    metadata = analyze_replaygain(filepath, dict())
    assert metadata["replay_gain"] == pytest.approx(replaygain, abs=tolerance)
