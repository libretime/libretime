import shutil
from pathlib import Path

import pytest

from libretime_analyzer.pipeline import Context
from libretime_analyzer.pipeline.organise_file import organise_file

from ..conftest import AUDIO_FILENAME


def context_factory_with_storage(filepath, dest_dir):
    return Context(
        filepath=filepath,
        original_filename=AUDIO_FILENAME,
        storage_url=str(dest_dir),
        callback_api_key="",
        callback_url="",
    )


def test_organise_file(src_dir: Path, dest_dir: Path):
    organise_file(context_factory_with_storage(src_dir / AUDIO_FILENAME, dest_dir))
    assert (dest_dir / AUDIO_FILENAME).exists()


def test_organise_file_samefile(src_dir: Path):
    organise_file(context_factory_with_storage(src_dir / AUDIO_FILENAME, src_dir))
    assert (src_dir / AUDIO_FILENAME).exists()


def test_organise_file_duplicate_file(src_dir: Path, dest_dir: Path):
    for i in range(1, 4):
        # Make a copy so we can reuse the file
        filename = f"{i}_{AUDIO_FILENAME}"
        shutil.copy(src_dir / AUDIO_FILENAME, src_dir / filename)

        ctx = organise_file(context_factory_with_storage(src_dir / filename, dest_dir))

        full_path = Path(ctx.metadata["full_path"])
        assert full_path.exists()
        if i == 1:
            assert full_path.name == AUDIO_FILENAME
        else:
            assert len(full_path.name) == len(AUDIO_FILENAME) + 1 + 36  # _ + UUID size


def test_organise_file_bad_permissions_dest_dir(src_dir: Path):
    with pytest.raises(OSError):
        # /sys is using sysfs on Linux, which is unwritable
        organise_file(
            context_factory_with_storage(
                src_dir / AUDIO_FILENAME,
                "/sys/foobar",
            )
        )
