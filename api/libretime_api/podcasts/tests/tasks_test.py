import pytest
from django.utils import timezone
from requests import Response

from ...storage.models import File
from ..models import Podcast, PodcastEpisode
from ..tasks import ImportEpisodeException, extract_filename, import_episode
from .fixtures import fixtures_path


@pytest.fixture(name="podcast")
def podcast_fixture(host_user):
    return Podcast.objects.create(
        owner=host_user,
        title="My podcast!",
        url="https://example.org/podcast.rss",
    )


@pytest.fixture(name="podcast_episode")
def podcast_episode_fixture(podcast: Podcast):
    return PodcastEpisode.objects.create(
        podcast=podcast,
        published_at=timezone.now(),
        download_url="https://example.org/episode.mp3",
        episode_guid="893ae17f",
        episode_title="My episode!",
    )


@pytest.mark.parametrize(
    "fixture",
    [
        ("s1-stereo.ogg"),
        ("s1-stereo-tagged.mp3"),
        ("malformed.mp3"),
    ],
)
@pytest.mark.parametrize("override_album", [(True), (False)])
@pytest.mark.django_db
def test_import_episode(
    requests_mock,
    fixture: str,
    podcast: Podcast,
    podcast_episode: PodcastEpisode,
    override_album: bool,
):
    episode_url = f"https://remote.example.org/{fixture}"
    episode_filepath = fixtures_path / fixture

    file = File.objects.create()

    requests_mock.get(episode_url, content=episode_filepath.read_bytes())
    requests_mock.post("http://localhost/rest/media", json={"id": file.pk})

    result = import_episode(
        episode_id=podcast_episode.pk,
        episode_url=episode_url,
        episode_title=podcast_episode.episode_title,
        podcast_name=podcast.title,
        override_album=override_album,
    )

    podcast_episode.refresh_from_db()
    assert podcast_episode.file is not None

    assert result == {
        "episode_id": podcast_episode.pk,
        "file_id": file.pk,
    }


@pytest.mark.django_db
def test_import_episode_invalid_file(
    requests_mock,
    podcast: Podcast,
    podcast_episode: PodcastEpisode,
):
    requests_mock.get(podcast_episode.download_url, content=b"some invalid content")

    try:
        import_episode(
            episode_id=podcast_episode.pk,
            episode_url=podcast_episode.download_url,
            episode_title=podcast_episode.episode_title,
            podcast_name=podcast.title,
            override_album=False,
        )
    except ImportEpisodeException as exc:
        assert str(exc).startswith(
            f"could not save podcast episode {podcast_episode.pk} metadata: "
        )

        assert PodcastEpisode.objects.filter(pk=podcast_episode.pk).first() is None


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
