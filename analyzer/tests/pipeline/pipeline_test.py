import datetime
from pathlib import Path

import pytest

from libretime_analyzer.pipeline.context import Context
from libretime_analyzer.pipeline.pipeline import run_pipeline

from ..conftest import AUDIO_FILENAME, AUDIO_IMPORT_DEST


def test_run_pipeline(src_dir: Path, dest_dir: Path):
    ctx = run_pipeline(
        Context(
            filepath=src_dir / AUDIO_FILENAME,
            original_filename=AUDIO_FILENAME,
            storage_url=str(dest_dir),
            callback_api_key="",
            callback_url="",
        )
    )

    assert ctx.metadata["track_title"] == "Test Title"
    assert ctx.metadata["artist_name"] == "Test Artist"
    assert ctx.metadata["album_title"] == "Test Album"
    assert ctx.metadata["year"] == "1999"
    assert ctx.metadata["genre"] == "Test Genre"
    assert ctx.metadata["mime"] == "audio/mp3"
    assert ctx.metadata["length_seconds"] == pytest.approx(15.0, abs=0.1)
    assert ctx.metadata["length"] == str(
        datetime.timedelta(seconds=ctx.metadata["length_seconds"])
    )
    assert (dest_dir / AUDIO_IMPORT_DEST).exists()
