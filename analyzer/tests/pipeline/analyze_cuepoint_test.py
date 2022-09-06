import distro
import pytest

from libretime_analyzer.pipeline.analyze_cuepoint import analyze_cuepoint

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
    metadata = analyze_cuepoint(filepath, {})

    # On bionic, large file duration is a wrong.
    if distro.codename() == "bionic" and str(filepath).endswith("s1-large.flac"):
        return

    assert metadata["length_seconds"] == pytest.approx(length, abs=0.1)
    assert float(metadata["cuein"]) == pytest.approx(float(cuein), abs=1)
    assert float(metadata["cueout"]) == pytest.approx(float(cueout), abs=1)
