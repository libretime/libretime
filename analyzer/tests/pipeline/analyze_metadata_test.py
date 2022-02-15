from pathlib import Path

import pytest

from libretime_analyzer.pipeline.analyze_metadata import analyze_metadata, compute_md5

from ..conftest import context_factory
from ..fixtures import FILE_INVALID_DRM, FILE_INVALID_TXT, FILES_TAGGED


@pytest.mark.parametrize(
    "filepath,metadata",
    map(lambda i: (i.path, i.metadata), FILES_TAGGED),
)
def test_analyze_metadata(filepath: Path, metadata: dict):
    found = analyze_metadata(context_factory(filepath))

    assert len(found.metadata["md5"]) == 32
    del found.metadata["md5"]

    # Handle filesize
    assert found.metadata["filesize"] < 3e6  # ~3Mb
    assert found.metadata["filesize"] > 1e5  # 100Kb
    del found.metadata["filesize"]

    # Handle track formatted length
    assert metadata["length"] in found.metadata["length"]
    del metadata["length"]
    del found.metadata["length"]

    # mp3,ogg,flac files does not support comments yet
    if not filepath.suffix == ".m4a":
        if "comment" in metadata:
            del metadata["comment"]

    assert found.metadata == metadata


def test_analyze_metadata_invalid_wma():
    found = analyze_metadata(context_factory(FILE_INVALID_DRM))
    assert found.metadata["mime"] == "audio/x-ms-wma"


def test_analyze_metadata_unparsable_file():
    found = analyze_metadata(context_factory(FILE_INVALID_TXT))
    assert found.metadata == {
        "filesize": 10,
        "ftype": "audioclip",
        "hidden": False,
        "md5": "4d5e4b1c8e8febbd31fa9ce7f088beae",
    }


def test_compute_md5():
    assert compute_md5(FILE_INVALID_TXT) == "4d5e4b1c8e8febbd31fa9ce7f088beae"
