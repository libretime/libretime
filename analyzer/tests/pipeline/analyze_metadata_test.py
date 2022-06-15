from pathlib import Path

import pytest

from libretime_analyzer.pipeline.analyze_metadata import analyze_metadata

from ..fixtures import FILE_INVALID_DRM, FILE_INVALID_TXT, FILES_TAGGED


@pytest.mark.parametrize(
    "filepath,metadata",
    map(lambda i: (i.path, i.metadata), FILES_TAGGED),
)
def test_analyze_metadata(filepath: Path, metadata: dict):
    found = analyze_metadata(str(filepath), {})

    assert len(found["md5"]) == 32
    del found["md5"]

    # Handle filesize
    assert found["filesize"] < 3e6  # ~3Mb
    assert found["filesize"] > 1e5  # 100Kb
    del found["filesize"]

    # Handle track formatted length
    assert metadata["length"] in found["length"]
    del metadata["length"]
    del found["length"]

    # mp3,ogg,flac files does not support comments yet
    if not filepath.suffix == ".m4a":
        if "comment" in metadata:
            del metadata["comment"]

    assert found == metadata


def test_analyze_metadata_invalid_wma():
    metadata = analyze_metadata(str(FILE_INVALID_DRM), {})
    assert metadata["mime"] == "audio/x-ms-wma"


def test_analyze_metadata_unparsable_file():
    metadata = analyze_metadata(str(FILE_INVALID_TXT), {})
    assert metadata == {
        "filesize": 10,
        "ftype": "audioclip",
        "hidden": False,
        "md5": "4d5e4b1c8e8febbd31fa9ce7f088beae",
    }
