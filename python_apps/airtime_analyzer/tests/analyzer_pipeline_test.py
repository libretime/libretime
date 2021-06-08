import datetime
import os
import shutil
from queue import Queue

import pytest
from airtime_analyzer.analyzer_pipeline import AnalyzerPipeline

from .conftest import AUDIO_FILENAME, AUDIO_IMPORT_DEST


def test_run_analysis(src_dir, dest_dir):
    queue = Queue()
    AnalyzerPipeline.run_analysis(
        queue,
        os.path.join(src_dir, AUDIO_FILENAME),
        dest_dir,
        AUDIO_FILENAME,
        "file",
        "",
    )
    metadata = queue.get()

    assert metadata["track_title"] == "Test Title"
    assert metadata["artist_name"] == "Test Artist"
    assert metadata["album_title"] == "Test Album"
    assert metadata["year"] == "1999"
    assert metadata["genre"] == "Test Genre"
    assert metadata["mime"] == "audio/mp3"
    assert abs(metadata["length_seconds"] - 3.9) < 0.1
    assert metadata["length"] == str(
        datetime.timedelta(seconds=metadata["length_seconds"])
    )
    assert os.path.exists(os.path.join(dest_dir, AUDIO_IMPORT_DEST))


@pytest.mark.parametrize(
    "params,exception",
    [
        ((Queue(), "", "", ""), TypeError),
        ((Queue(), "", "", ""), TypeError),
        ((Queue(), "", "", ""), TypeError),
        ((Queue(), "", "", ""), TypeError),
    ],
)
def test_run_analysis_wrong_params(params, exception):
    with pytest.raises(exception):
        AnalyzerPipeline.run_analysis(*params)
