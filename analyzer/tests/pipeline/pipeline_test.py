from datetime import timedelta
from pathlib import Path
from queue import Queue

import pytest

from libretime_analyzer.pipeline import Pipeline, PipelineOptions

from ..conftest import AUDIO_FILENAME, AUDIO_IMPORT_DEST


def test_run_analysis(src_dir: Path, dest_dir: Path):
    queue = Queue()
    Pipeline.run_analysis(
        queue,
        str(src_dir / AUDIO_FILENAME),
        str(dest_dir),
        AUDIO_FILENAME,
        PipelineOptions(),
    )
    metadata = queue.get()

    assert metadata["track_title"] == "Test Title"
    assert metadata["artist_name"] == "Test Artist"
    assert metadata["album_title"] == "Test Album"
    assert metadata["year"] == "1999"
    assert metadata["genre"] == "Test Genre"
    assert metadata["mime"] == "audio/mp3"
    assert metadata["length_seconds"] == pytest.approx(15.0, abs=0.1)
    assert metadata["length"] == str(timedelta(seconds=metadata["length_seconds"]))
    assert (dest_dir / AUDIO_IMPORT_DEST).exists()
