import shutil
from pathlib import Path

import pytest

from .fixtures import fixtures_path

AUDIO_FILENAME = "s1-stereo-tagged.mp3"
AUDIO_FILE = fixtures_path / AUDIO_FILENAME
AUDIO_IMPORT_DEST = f"Test Artist/Test Album/{AUDIO_FILENAME}"


@pytest.fixture()
def dest_dir(tmp_path: Path):
    dest = tmp_path / "dest"
    dest.mkdir()
    yield dest


@pytest.fixture()
def src_dir(tmp_path: Path):
    src = tmp_path / "src"
    src.mkdir()
    shutil.copy(AUDIO_FILE, src)
    yield src
