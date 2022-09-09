import pytest
from requests import Response

from libretime_worker.tasks import extract_filename


@pytest.mark.parametrize(
    "url, header, expected",
    [
        ("http://example.com/from-url.mp3", None, "from-url.mp3"),
        (
            "http://example.com/from-url.mp3",
            'attachment; filename="from-header.mp3"',
            "from-header.mp3",
        ),
        ("http://example.com/from-url.mp3", "attachment", "from-url.mp3"),
    ],
)
def test_extract_filename(url, header, expected):
    resp = Response()
    resp.url = url
    if header is not None:
        resp.headers["Content-Disposition"] = header

    assert extract_filename(resp) == expected
