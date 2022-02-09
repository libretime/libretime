import os
import shutil
import time
from pathlib import Path
from unittest import mock

import pytest

from libretime_analyzer.pipeline import Context
from libretime_analyzer.pipeline.organise_file import organise_file

from ..conftest import AUDIO_FILENAME


def test_organise_file(src_dir: Path, dest_dir: Path):
    organise_file(
        Context(
            filepath=src_dir / AUDIO_FILENAME,
            original_filename=AUDIO_FILENAME,
            storage_url=str(dest_dir),
            callback_api_key="",
            callback_url="",
        )
    )
    assert (dest_dir / AUDIO_FILENAME).exists()


def test_organise_file_samefile(src_dir: Path):
    organise_file(
        Context(
            filepath=src_dir / AUDIO_FILENAME,
            original_filename=AUDIO_FILENAME,
            storage_url=str(src_dir),
            callback_api_key="",
            callback_url="",
        )
    )
    assert (src_dir / AUDIO_FILENAME).exists()


def import_and_restore(src_dir: Path, dest_dir: Path) -> Context:
    """
    Small helper to test the organise_file function.
    Move the file and restore it back to it's origine.
    """
    # Import the file
    ctx = organise_file(
        Context(
            filepath=src_dir / AUDIO_FILENAME,
            original_filename=AUDIO_FILENAME,
            storage_url=str(dest_dir),
            callback_api_key="",
            callback_url="",
        )
    )

    # Copy it back to the original location
    shutil.copy(dest_dir / AUDIO_FILENAME, src_dir / AUDIO_FILENAME)

    return ctx


def test_organise_file_duplicate_file(src_dir: Path, dest_dir: Path):
    # Import the file once
    import_and_restore(src_dir, dest_dir)

    # Import it again. It shouldn't overwrite the old file and instead create a new
    ctx = import_and_restore(src_dir, dest_dir)

    assert ctx.metadata["full_path"] != str(dest_dir / AUDIO_FILENAME)
    assert os.path.exists(ctx.metadata["full_path"])
    assert (dest_dir / AUDIO_FILENAME).exists()


def test_organise_file_triplicate_file(src_dir: Path, dest_dir: Path):
    # Here we use mock to patch out the time.localtime() function so that it
    # always returns the same value. This allows us to consistently simulate this test cases
    # where the last two of the three files are imported at the same time as the timestamp.
    with mock.patch("libretime_analyzer.pipeline.organise_file.time") as mock_time:
        mock_time.localtime.return_value = time.localtime()  # date(2010, 10, 8)
        mock_time.side_effect = time.time

    # Import the file once
    import_and_restore(src_dir, dest_dir)
    # Import it again. It shouldn't overwrite the old file and instead create a new
    ctx1 = import_and_restore(src_dir, dest_dir)

    # Reimport for the third time, which should have the same timestamp as the second one
    # thanks to us mocking out time.localtime()
    ctx2 = import_and_restore(src_dir, dest_dir)

    # Check if file exists and if filename is <original>_<date>.<ext>
    assert os.path.exists(ctx1.metadata["full_path"])
    assert len(os.path.basename(ctx1.metadata["full_path"]).split("_")) == 2

    # Check if file exists and if filename is <original>_<date>_<uuid>.<ext>
    assert os.path.exists(ctx2.metadata["full_path"])
    assert len(os.path.basename(ctx2.metadata["full_path"]).split("_")) == 3


def test_organise_file_bad_permissions_dest_dir(src_dir: Path):
    with pytest.raises(OSError):
        # /sys is using sysfs on Linux, which is unwritable
        organise_file(
            Context(
                filepath=src_dir / AUDIO_FILENAME,
                original_filename=AUDIO_FILENAME,
                storage_url="/sys/foobar",
                callback_api_key="",
                callback_url="",
            )
        )
