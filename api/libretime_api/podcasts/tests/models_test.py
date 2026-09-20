from datetime import timedelta

import pytest
from django.db import connection
from django.utils import timezone

from ..models import PodcastEpisode
from .tasks_test import podcast_fixture  # pylint: disable=unused-import


@pytest.mark.django_db
def test_podcast_episode_created_at_default_is_utc(podcast):
    """
    The legacy application inserts the podcast episodes without setting the created_at
    column, whatever the timezone of its database session is.
    """
    with connection.cursor() as cursor:
        cursor.execute("SET LOCAL TIME ZONE 'Pacific/Auckland'")
        cursor.execute(
            """
            INSERT INTO podcast_episodes (
                podcast_id, publication_date, download_url,
                episode_guid, episode_title, episode_description
            )
            VALUES (%s, now(), 'https://example.org/a.mp3', 'guid', 'title', '')
            RETURNING id
            """,
            [podcast.pk],
        )
        (episode_id,) = cursor.fetchone()

    created_at = PodcastEpisode.objects.get(pk=episode_id).created_at
    assert abs(timezone.now().replace(tzinfo=None) - created_at) < timedelta(minutes=1)
