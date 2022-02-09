import pytest

from libretime_analyzer.pipeline.analyze_cuepoint import analyze_cuepoint

from ..conftest import context_factory
from ..fixtures import FILES


@pytest.mark.parametrize(
    "filepath,length,cuein,cueout",
    map(
        lambda i: pytest.param(i.path, i.length, i.cuein, i.cueout, id=i.path.name),
        FILES,
    ),
)
def test_analyze_cuepoint(filepath, length, cuein, cueout):
    ctx = analyze_cuepoint(context_factory(filepath))

    assert ctx.metadata["length_seconds"] == pytest.approx(length, abs=0.1)
    assert float(ctx.metadata["cuein"]) == pytest.approx(float(cuein), abs=1)
    assert float(ctx.metadata["cueout"]) == pytest.approx(float(cueout), abs=1)
