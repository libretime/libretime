from datetime import timedelta
from unittest import mock

import mutagen
import pytest
from airtime_analyzer.metadata_analyzer import MetadataAnalyzer

from .fixtures import FILE_INVALID_DRM, FILE_INVALID_TXT, FILES_TAGGED, FixtureMeta


@pytest.mark.parametrize(
    "params,exception",
    [
        ((42, dict()), TypeError),
        (("foo", 3), TypeError),
    ],
)
def test_analyze_wrong_params(params, exception):
    with pytest.raises(exception):
        MetadataAnalyzer.analyze(*params)


@pytest.mark.parametrize(
    "filepath,metadata",
    map(lambda i: (str(i.path), i.metadata), FILES_TAGGED),
)
def test_analyze(filepath: str, metadata: dict):
    found = MetadataAnalyzer.analyze(filepath, dict())

    # Mutagen does not support wav files yet
    if filepath.endswith("wav"):
        return

    assert len(found["md5"]) == 32
    del found["md5"]

    # Handle track formatted length/cueout
    assert metadata["length"] in found["length"]
    assert metadata["length"] in found["cueout"]
    del metadata["length"]
    del found["length"]
    del found["cueout"]

    # mp3,ogg,flac files does not support comments yet
    if not filepath.endswith("m4a"):
        del metadata["comment"]

    assert found == metadata


def test_invalid_wma():
    metadata = MetadataAnalyzer.analyze(str(FILE_INVALID_DRM), dict())
    assert metadata["mime"] == "audio/x-ms-wma"


def test_unparsable_file():
    metadata = MetadataAnalyzer.analyze(str(FILE_INVALID_TXT), dict())
    assert metadata == {
        "filesize": 10,
        "ftype": "audioclip",
        "hidden": False,
        "md5": "4d5e4b1c8e8febbd31fa9ce7f088beae",
        "mime": "text/plain",
    }
