import pytest
import os
import tempfile
import shutil

AUDIO_FILE = "tests/test_data/44100Hz-16bit-mono.mp3"
AUDIO_FILENAME = os.path.basename(AUDIO_FILE)


@pytest.fixture()
def dest_dir():
    with tempfile.TemporaryDirectory(prefix="dest") as tmpdir:
        yield tmpdir


@pytest.fixture()
def src_dir():
    with tempfile.TemporaryDirectory(prefix="src") as tmpdir:
        shutil.copy(AUDIO_FILE, tmpdir)
        yield tmpdir
