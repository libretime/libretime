import os
import shutil
import tempfile
import time

import mock
import pytest
from airtime_analyzer.filemover_analyzer import FileMoverAnalyzer

from .conftest import AUDIO_FILENAME


def test_analyze():
    with pytest.raises(Exception):
        FileMoverAnalyzer.analyze("foo", dict())


@pytest.mark.parametrize(
    "params,exception",
    [
        ((42, "", "", dict()), TypeError),
        (("", 23, "", dict()), TypeError),
        (("", "", 5, dict()), TypeError),
        (("", "", "", 12345), TypeError),
    ],
)
def test_move_wrong_params(params, exception):
    with pytest.raises(exception):
        FileMoverAnalyzer.move(*params)


def test_move(src_dir, dest_dir):
    FileMoverAnalyzer.move(
        os.path.join(src_dir, AUDIO_FILENAME),
        dest_dir,
        AUDIO_FILENAME,
        dict(),
    )
    assert os.path.exists(os.path.join(dest_dir, AUDIO_FILENAME))


def test_move_samefile(src_dir):
    FileMoverAnalyzer.move(
        os.path.join(src_dir, AUDIO_FILENAME),
        src_dir,
        AUDIO_FILENAME,
        dict(),
    )
    assert os.path.exists(os.path.join(src_dir, AUDIO_FILENAME))


def import_and_restore(src_dir, dest_dir) -> dict:
    """
    Small helper to test the FileMoverAnalyzer.move function.
    Move the file and restore it back to it's origine.
    """
    # Import the file
    metadata = FileMoverAnalyzer.move(
        os.path.join(src_dir, AUDIO_FILENAME),
        dest_dir,
        AUDIO_FILENAME,
        dict(),
    )

    # Copy it back to the original location
    shutil.copy(
        os.path.join(dest_dir, AUDIO_FILENAME),
        os.path.join(src_dir, AUDIO_FILENAME),
    )

    return metadata


def test_move_duplicate_file(src_dir, dest_dir):
    # Import the file once
    import_and_restore(src_dir, dest_dir)

    # Import it again. It shouldn't overwrite the old file and instead create a new
    metadata = import_and_restore(src_dir, dest_dir)

    assert metadata["full_path"] != os.path.join(dest_dir, AUDIO_FILENAME)
    assert os.path.exists(metadata["full_path"])
    assert os.path.exists(os.path.join(dest_dir, AUDIO_FILENAME))


def test_move_triplicate_file(src_dir, dest_dir):
    # Here we use mock to patch out the time.localtime() function so that it
    # always returns the same value. This allows us to consistently simulate this test cases
    # where the last two of the three files are imported at the same time as the timestamp.
    with mock.patch("airtime_analyzer.filemover_analyzer.time") as mock_time:
        mock_time.localtime.return_value = time.localtime()  # date(2010, 10, 8)
        mock_time.side_effect = time.time

    # Import the file once
    import_and_restore(src_dir, dest_dir)
    # Import it again. It shouldn't overwrite the old file and instead create a new
    metadata1 = import_and_restore(src_dir, dest_dir)

    # Reimport for the third time, which should have the same timestamp as the second one
    # thanks to us mocking out time.localtime()
    metadata2 = import_and_restore(src_dir, dest_dir)

    # Check if file exists and if filename is <original>_<date>.<ext>
    assert os.path.exists(metadata1["full_path"])
    assert len(os.path.basename(metadata1["full_path"]).split("_")) == 2

    # Check if file exists and if filename is <original>_<date>_<uuid>.<ext>
    assert os.path.exists(metadata2["full_path"])
    assert len(os.path.basename(metadata2["full_path"]).split("_")) == 3


def test_move_bad_permissions_dest_dir(src_dir):
    with pytest.raises(OSError):
        # /sys is using sysfs on Linux, which is unwritable
        FileMoverAnalyzer.move(
            os.path.join(src_dir, AUDIO_FILENAME),
            "/sys/foobar",
            AUDIO_FILENAME,
            dict(),
        )
