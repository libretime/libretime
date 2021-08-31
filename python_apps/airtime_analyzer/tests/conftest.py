import os
import shutil
from tempfile import TemporaryDirectory

import pytest

from .fixtures import fixtures_path

AUDIO_FILENAME = "s1-stereo-tagged.mp3"
AUDIO_FILE = fixtures_path / AUDIO_FILENAME
AUDIO_IMPORT_DEST = f"Test Artist/Test Album/{AUDIO_FILENAME}"

# TODO: Use pathlib for file manipulation


@pytest.fixture()
def dest_dir():
    with TemporaryDirectory(prefix="dest") as tmpdir:
        yield tmpdir


@pytest.fixture()
def src_dir():
    with TemporaryDirectory(prefix="src") as tmpdir:
        shutil.copy(AUDIO_FILE, tmpdir)
        yield tmpdir
