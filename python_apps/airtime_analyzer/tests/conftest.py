import os
import shutil
import tempfile

import pytest

AUDIO_FILE = "tests/test_data/44100Hz-16bit-mono.mp3"
AUDIO_FILENAME = os.path.basename(AUDIO_FILE)
AUDIO_IMPORT_DEST = "Test Artist/Test Album/44100Hz-16bit-mono.mp3"


@pytest.fixture()
def dest_dir():
    with tempfile.TemporaryDirectory(prefix="dest") as tmpdir:
        yield tmpdir


@pytest.fixture()
def src_dir():
    with tempfile.TemporaryDirectory(prefix="src") as tmpdir:
        shutil.copy(AUDIO_FILE, tmpdir)
        yield tmpdir
