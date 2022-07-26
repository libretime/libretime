import pytest

from libretime_api_client._client import Session


@pytest.mark.parametrize(
    "base_url, url, expected",
    [
        (None, "/path", "/path"),
        (None, "http://host/path", "http://host/path"),
        ("http://host", "path", "http://host/path"),
        ("http://host", "/path", "http://host/path"),
        ("http://host/", "path", "http://host/path"),
        ("http://host/", "/path", "http://host/path"),
    ],
)
def test_session_create_url(base_url, url, expected):
    session = Session(base_url=base_url)
    assert session.create_url(url) == expected
