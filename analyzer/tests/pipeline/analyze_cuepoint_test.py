import pytest

from libretime_analyzer.pipeline.analyze_cuepoint import (
    analyze_cuepoint,
    analyze_duration,
)

from ..fixtures import FILES


@pytest.mark.parametrize(
    "filepath,length,cuein,cueout",
    map(
        lambda i: pytest.param(
            str(i.path), i.length, i.cuein, i.cueout, id=i.path.name
        ),
        FILES,
    ),
)
def test_analyze_cuepoint(filepath, length, cuein, cueout):
    metadata = analyze_duration(filepath, {})
    metadata = analyze_cuepoint(filepath, metadata)

    assert metadata["length_seconds"] == pytest.approx(length, abs=0.1)
    assert float(metadata["cuein"]) == pytest.approx(float(cuein), abs=1)
    assert float(metadata["cueout"]) == pytest.approx(float(cueout), abs=1)
