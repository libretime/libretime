from datetime import timedelta

import pytest
from celery.exceptions import SoftTimeLimitExceeded
from django.utils import timezone
from requests import Response

from ...storage.models import File
from ..models import Podcast, PodcastEpisode
from ..tasks import (
    IMPORT_EPISODE_MAX_AGE,
    IMPORT_EPISODE_MAX_QUEUE_TIME,
    ImportEpisodeException,
    clean_failed_imports,
    extract_filename,
    import_episode,
)
from .fixtures import fixtures_path


def make_episode(
    podcast: Podcast,
    guid: str,
    age: timedelta = timedelta(0),
    file: File | None = None,
) -> PodcastEpisode:
    episode = PodcastEpisode.objects.create(
        podcast=podcast,
        file=file,
        published_at=timezone.now(),
        download_url=f"https://example.org/{guid}.mp3",
        episode_guid=guid,
        episode_title=f"Episode {guid}",
    )
    # created_at is set on creation, update() is the way to bypass it.
    PodcastEpisode.objects.filter(pk=episode.pk).update(created_at=timezone.now() - age)
    episode.refresh_from_db()
    return episode


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

    with pytest.raises(
        ImportEpisodeException,
        match=f"could not save podcast episode {podcast_episode.pk} metadata: ",
    ):
        import_episode(
            episode_id=podcast_episode.pk,
            episode_url=podcast_episode.download_url,
            episode_title=podcast_episode.episode_title,
            podcast_name=podcast.title,
            override_album=False,
        )

    assert PodcastEpisode.objects.filter(pk=podcast_episode.pk).first() is None


def run_import_episode(podcast: Podcast, episode: PodcastEpisode):
    return import_episode(
        episode_id=episode.pk,
        episode_url=episode.download_url,
        episode_title=episode.episode_title,
        podcast_name=podcast.title,
        override_album=False,
    )


@pytest.mark.django_db
def test_import_episode_missing_episode(podcast: Podcast, podcast_episode):
    episode_id = podcast_episode.pk
    podcast_episode.delete()

    result = import_episode(
        episode_id=episode_id,
        episode_url="https://example.org/episode.mp3",
        episode_title=None,
        podcast_name=podcast.title,
        override_album=False,
    )

    assert result == {"episode_id": episode_id}


@pytest.mark.django_db
def test_import_episode_queued_for_too_long(
    requests_mock,
    podcast: Podcast,
):
    episode = make_episode(
        podcast,
        "old",
        age=IMPORT_EPISODE_MAX_QUEUE_TIME + timedelta(minutes=1),
    )

    with pytest.raises(ImportEpisodeException, match="queued for too long"):
        run_import_episode(podcast, episode)

    assert requests_mock.call_count == 0
    assert PodcastEpisode.objects.filter(pk=episode.pk).first() is None


@pytest.mark.django_db
def test_import_episode_queued_in_time(requests_mock, podcast: Podcast):
    episode = make_episode(
        podcast,
        "recent",
        age=IMPORT_EPISODE_MAX_QUEUE_TIME - timedelta(minutes=1),
    )
    file = File.objects.create()

    requests_mock.get(
        episode.download_url,
        content=(fixtures_path / "s1-stereo.ogg").read_bytes(),
    )
    requests_mock.post("http://localhost/rest/media", json={"id": file.pk})

    run_import_episode(podcast, episode)

    episode.refresh_from_db()
    assert episode.file == file


@pytest.mark.django_db
def test_import_episode_unexpected_error(
    requests_mock,
    podcast: Podcast,
    podcast_episode: PodcastEpisode,
):
    requests_mock.get(
        podcast_episode.download_url,
        content=(fixtures_path / "s1-stereo.ogg").read_bytes(),
    )
    # The upload response is missing the file id
    requests_mock.post("http://localhost/rest/media", json={})

    with pytest.raises(KeyError):
        run_import_episode(podcast, podcast_episode)

    assert PodcastEpisode.objects.filter(pk=podcast_episode.pk).first() is None


@pytest.mark.django_db
def test_import_episode_soft_time_limit(
    requests_mock,
    podcast: Podcast,
    podcast_episode: PodcastEpisode,
):
    requests_mock.get(podcast_episode.download_url, exc=SoftTimeLimitExceeded)

    with pytest.raises(SoftTimeLimitExceeded):
        run_import_episode(podcast, podcast_episode)

    assert PodcastEpisode.objects.filter(pk=podcast_episode.pk).first() is None


@pytest.mark.django_db
def test_clean_failed_imports(podcast: Podcast):
    file = File.objects.create()

    orphaned = make_episode(
        podcast, "orphaned", age=IMPORT_EPISODE_MAX_AGE + timedelta(minutes=1)
    )
    # Old enough to have been abandoned by a task, but a task might still be running.
    in_flight = make_episode(
        podcast, "in-flight", age=IMPORT_EPISODE_MAX_AGE - timedelta(minutes=1)
    )
    # Not started yet, or waiting in the queue.
    queued = make_episode(podcast, "queued")
    imported = make_episode(
        podcast,
        "imported",
        age=IMPORT_EPISODE_MAX_AGE + timedelta(days=30),
        file=file,
    )

    assert clean_failed_imports() == 1

    remaining = set(PodcastEpisode.objects.values_list("pk", flat=True))
    assert remaining == {in_flight.pk, queued.pk, imported.pk}
    assert orphaned.pk not in remaining

    # Nothing left to clean.
    assert clean_failed_imports() == 0


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
