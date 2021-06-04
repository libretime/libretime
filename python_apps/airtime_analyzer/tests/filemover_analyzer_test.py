import os
import shutil
import tempfile
import time

import mock
import pytest
from airtime_analyzer.filemover_analyzer import FileMoverAnalyzer

AUDIO_FILE = "tests/test_data/44100Hz-16bit-mono.mp3"
AUDIO_FILENAME = os.path.basename(AUDIO_FILE)


def test_dont_use_analyze():
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


@pytest.fixture()
def dest_dir():
    with tempfile.TemporaryDirectory(prefix="dest") as tmpdir:
        yield tmpdir


@pytest.fixture()
def src_dir():
    with tempfile.TemporaryDirectory(prefix="src") as tmpdir:
        shutil.copy(AUDIO_FILE, tmpdir)
        yield tmpdir


def test_basic(src_dir, dest_dir):
    FileMoverAnalyzer.move(
        os.path.join(src_dir, AUDIO_FILENAME),
        dest_dir,
        AUDIO_FILENAME,
        dict(),
    )
    assert os.path.exists(os.path.join(dest_dir, AUDIO_FILENAME))


def test_basic_samefile(src_dir):
    FileMoverAnalyzer.move(
        os.path.join(src_dir, AUDIO_FILENAME),
        src_dir,
        AUDIO_FILENAME,
        dict(),
    )
    assert os.path.exists(os.path.join(src_dir, AUDIO_FILENAME))


def import_and_restore(src_dir, dest_dir) -> dict:
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


def test_duplicate_file(src_dir, dest_dir):
    # Import the file once
    import_and_restore(src_dir, dest_dir)

    # Import it again. It shouldn't overwrite the old file and instead create a new
    metadata = import_and_restore(src_dir, dest_dir)

    assert metadata["full_path"] != os.path.join(dest_dir, AUDIO_FILENAME)
    assert os.path.exists(metadata["full_path"])
    assert os.path.exists(os.path.join(dest_dir, AUDIO_FILENAME))


def test_double_duplicate_files(src_dir, dest_dir):
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

    assert os.path.exists(metadata1["full_path"])
    # Check if filename is <original>_<date>.<ext>
    assert len(metadata1["full_path"].split("_")) == 2
    assert os.path.exists(metadata2["full_path"])
    # Check if filename is <original>_<date>_<uuid>.<ext>
    assert len(metadata2["full_path"].split("_")) == 3


def test_bad_permissions_destination_dir(src_dir):
    with pytest.raises(OSError):
        # /sys is using sysfs on Linux, which is unwritable
        FileMoverAnalyzer.move(
            os.path.join(src_dir, AUDIO_FILENAME),
            "/sys/foobar",
            AUDIO_FILENAME,
            dict(),
        )
