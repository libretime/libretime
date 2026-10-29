from queue import Queue
from unittest.mock import MagicMock

from libretime_playout.player.file import PypoFile


def test_file_thread():
    PypoFile(
        Queue(),
        MagicMock(),
    )


def test_reported_file_size_names_the_field_the_api_has(tmp_path):
    """
    The payload playout sends when it records a show.

    The api's field is "size" ("filesize" is its column name) and a patch carrying an
    unknown key is accepted and ignored, so this asserts the key by name: a rename on
    either side has to fail here rather than leave recordings whose size is never
    stored, which is what happened when the api renamed the field.
    """
    api_client = MagicMock()
    recorded = tmp_path / "recording.mp3"
    recorded.write_bytes(b"0123456789")

    pypo_file = PypoFile(Queue(), api_client)
    size = pypo_file.report_file_size_and_md5_to_api(str(recorded), 42)

    assert size == 10

    _args, kwargs = api_client.update_file.call_args

    assert kwargs["json"] == {
        "size": 10,
        "md5": "781e5e245d69b566979b86e28d23f2c7",
    }
