import shutil
from pathlib import Path

import pytest

from libretime_analyzer.pipeline.organise_file import organise_file

from ..conftest import AUDIO_FILENAME


def organise_file_args_factory(filepath: Path, dest_dir: Path):
    return (
        str(filepath),
        str(dest_dir),
        AUDIO_FILENAME,
        {},
    )


def test_organise_file(src_dir: Path, dest_dir: Path):
    organise_file(*organise_file_args_factory(src_dir / AUDIO_FILENAME, dest_dir))
    assert (dest_dir / AUDIO_FILENAME).exists()


def test_organise_file_samefile(src_dir: Path):
    organise_file(*organise_file_args_factory(src_dir / AUDIO_FILENAME, src_dir))
    assert (src_dir / AUDIO_FILENAME).exists()


def test_organise_file_duplicate_file(src_dir: Path, dest_dir: Path):
    for i in range(1, 4):
        # Make a copy so we can reuse the file
        filename = f"{i}_{AUDIO_FILENAME}"
        shutil.copy(src_dir / AUDIO_FILENAME, src_dir / filename)

        metadata = organise_file(
            *organise_file_args_factory(src_dir / filename, dest_dir)
        )

        full_path = Path(metadata["full_path"])
        assert full_path.exists()
        if i == 1:
            assert full_path.name == AUDIO_FILENAME
        else:
            assert len(full_path.name) == len(AUDIO_FILENAME) + 1 + 36  # _ + UUID size


def test_organise_file_bad_permissions_dest_dir(src_dir: Path):
    with pytest.raises(OSError):
        # /sys is using sysfs on Linux, which is unwritable
        organise_file(
            *organise_file_args_factory(
                src_dir / AUDIO_FILENAME,
                Path("/sys/foobar"),
            )
        )
