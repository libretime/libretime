from datetime import timedelta
from pathlib import Path

import pytest

from libretime_analyzer.pipeline.pipeline import run_pipeline

from ..conftest import AUDIO_FILENAME, AUDIO_IMPORT_DEST, context_factory


def test_run_analysis(src_dir: Path, dest_dir: Path):
    found = run_pipeline(
        context_factory(
            src_dir / AUDIO_FILENAME,
            original_filename=AUDIO_FILENAME,
            storage_url=dest_dir,
        ),
    )

    assert found.metadata["track_title"] == "Test Title"
    assert found.metadata["artist_name"] == "Test Artist"
    assert found.metadata["album_title"] == "Test Album"
    assert found.metadata["year"] == "1999"
    assert found.metadata["genre"] == "Test Genre"
    assert found.metadata["mime"] == "audio/mp3"
    assert found.metadata["length_seconds"] == pytest.approx(15.0, abs=0.1)
    assert found.metadata["length"] == str(
        timedelta(seconds=found.metadata["length_seconds"])
    )
    assert (dest_dir / AUDIO_IMPORT_DEST).exists()
