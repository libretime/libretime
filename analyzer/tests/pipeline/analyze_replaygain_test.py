import pytest

from libretime_analyzer.pipeline.analyze_replaygain import analyze_replaygain

from ..fixtures import FILES


@pytest.mark.parametrize(
    "filepath,replaygain",
    [pytest.param(str(i.path), i.replaygain, id=i.path.name) for i in FILES],
)
def test_analyze_replaygain(filepath, replaygain):
    tolerance = 0.8

    metadata = analyze_replaygain(filepath, {})
    assert metadata["replay_gain"] == pytest.approx(replaygain, abs=tolerance)
