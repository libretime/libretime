import json

import pytest
from requests import Response

from libretime_worker.tasks import extract_filename, podcast_download

from .fixtures import fixtures_path


@pytest.mark.parametrize(
    "file",
    [
        ("s1-stereo.ogg"),
        ("s1-stereo-tagged.mp3"),
        ("malformed.mp3"),
    ],
)
@pytest.mark.parametrize("override_album", [(True), (False)])
def test_podcast_download(requests_mock, file, override_album):
    episode_url = f"https://remote.example.org/{file}"
    episode_filepath = fixtures_path / file

    requests_mock.get(episode_url, content=episode_filepath.read_bytes())
    requests_mock.post("http://localhost/rest/media", json={"id": 1})

    result = podcast_download(
        episode_id=1,
        episode_url=episode_url,
        episode_title="My episode",
        podcast_name="My podcast!",
        override_album=override_album,
    )
    assert json.loads(result) == {
        "episodeid": 1,
        "fileid": 1,
        "status": 1,
    }


def test_podcast_download_invalid_file(requests_mock):
    episode_url = "https://remote.example.org/invalid"
    requests_mock.get(episode_url, content=b"some invalid content")
    requests_mock.post("http://localhost/rest/media", json={"id": 1})

    result = podcast_download(
        episode_id=1,
        episode_url=episode_url,
        episode_title="My episode",
        podcast_name="My podcast!",
        override_album=False,
    )
    assert json.loads(result) == {
        "episodeid": 1,
        "status": 0,
        "error": "could not determine podcast episode 1 file type",
    }


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
