import pytest

from libretime_analyzer.pipeline.analyze_metadata import analyze_metadata

from ..conftest import context_factory
from ..fixtures import FILE_INVALID_DRM, FILE_INVALID_TXT, FILES_TAGGED


@pytest.mark.parametrize(
    "filepath,metadata",
    map(lambda i: (i.path, i.metadata), FILES_TAGGED),
)
def test_analyze_metadata(filepath, metadata):
    ctx = analyze_metadata(context_factory(filepath))

    # Mutagen does not support wav files yet
    if filepath.suffix == ".wav":
        return

    assert len(ctx.metadata["md5"]) == 32
    del ctx.metadata["md5"]

    # Handle filesize
    assert ctx.metadata["filesize"] < 2e6  # ~2Mb
    assert ctx.metadata["filesize"] > 1e5  # 100Kb
    del ctx.metadata["filesize"]

    # Handle track formatted length/cueout
    assert metadata["length"] in ctx.metadata["length"]
    assert metadata["length"] in ctx.metadata["cueout"]
    del metadata["length"]
    del ctx.metadata["length"]
    del ctx.metadata["cueout"]

    # mp3,ogg,flac files does not support comments yet
    if not filepath.suffix == ".m4a":
        del metadata["comment"]

    assert ctx.metadata == metadata


def test_analyze_metadata_invalid_wma():
    ctx = analyze_metadata(context_factory(FILE_INVALID_DRM))
    assert ctx.metadata["mime"] == "audio/x-ms-wma"


def test_analyze_metadata_unparsable_file():
    ctx = analyze_metadata(context_factory(FILE_INVALID_TXT))
    assert ctx.metadata == {
        "filesize": 10,
        "ftype": "audioclip",
        "hidden": False,
        "md5": "4d5e4b1c8e8febbd31fa9ce7f088beae",
        "mime": "text/plain",
    }
