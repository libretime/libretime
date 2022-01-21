import pytest

from libretime_analyzer.steps.analyze_metadata import analyze_metadata

from ..fixtures import FILE_INVALID_DRM, FILE_INVALID_TXT, FILES_TAGGED


@pytest.mark.parametrize(
    "params,exception",
    [
        ((42, dict()), TypeError),
        (("foo", 3), TypeError),
    ],
)
def test_analyze_metadata_wrong_params(params, exception):
    with pytest.raises(exception):
        analyze_metadata(*params)


@pytest.mark.parametrize(
    "filepath,metadata",
    map(lambda i: (str(i.path), i.metadata), FILES_TAGGED),
)
def test_analyze_metadata(filepath: str, metadata: dict):
    found = analyze_metadata(filepath, dict())

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


def test_analyze_metadata_invalid_wma():
    metadata = analyze_metadata(str(FILE_INVALID_DRM), dict())
    assert metadata["mime"] == "audio/x-ms-wma"


def test_analyze_metadata_unparsable_file():
    metadata = analyze_metadata(str(FILE_INVALID_TXT), dict())
    assert metadata == {
        "filesize": 10,
        "ftype": "audioclip",
        "hidden": False,
        "md5": "4d5e4b1c8e8febbd31fa9ce7f088beae",
        "mime": "text/plain",
    }
