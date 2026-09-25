from datetime import timedelta
from email.message import EmailMessage
from pathlib import Path
from tempfile import NamedTemporaryFile
from typing import Any
from urllib.parse import urlsplit

import mutagen
import requests
from celery import shared_task
from celery.utils.log import get_task_logger
from django.conf import settings
from django.utils import timezone
from mutagen import MutagenError
from requests import RequestException, Response

from .. import VERSION
from ..storage.models import File
from .models import PodcastEpisode

logger = get_task_logger(__name__)


class ImportEpisodeException(Exception):
    """
    An error occurred during the podcast import task.
    """


# The podcast episode placeholder is created right before the import task is queued,
# and we cannot know if a queued task is still waiting for a worker. Instead, we
# guarantee an upper bound on the lifetime of a placeholder that is still legitimately
# being imported:
#
# - a task that starts more than IMPORT_EPISODE_MAX_QUEUE_TIME after the placeholder
#   was created gives up,
# - a task that starts in time is stopped after IMPORT_EPISODE_TIME_LIMIT.
#
# Once IMPORT_EPISODE_MAX_AGE has passed, a placeholder without a file is orphaned.
IMPORT_EPISODE_SOFT_TIME_LIMIT = timedelta(minutes=15).seconds
IMPORT_EPISODE_TIME_LIMIT = timedelta(minutes=20).seconds
IMPORT_EPISODE_MAX_QUEUE_TIME = timedelta(hours=1)
IMPORT_EPISODE_MAX_AGE = (
    IMPORT_EPISODE_MAX_QUEUE_TIME
    + timedelta(seconds=IMPORT_EPISODE_TIME_LIMIT)
    + timedelta(minutes=5)
)


# pylint: disable=too-many-locals,too-many-statements
@shared_task(
    acks_late=True,
    soft_time_limit=IMPORT_EPISODE_SOFT_TIME_LIMIT,
    time_limit=IMPORT_EPISODE_TIME_LIMIT,
)
def import_episode(
    episode_id: int,
    episode_url: str,
    episode_title: str | None,
    podcast_name: str,
    override_album: bool,
):
    """
    Download a podcast episode and upload to our storage.

    If the import failed for any reason, deletes the podcast episode object.

    Args:
        episode_id: Episode ID.
        episode_url: Episode download url.
        episode_title: Episode title to override the title metadata.
        podcast_name: Podcast name to save to the metadata.
        override_album: Whether to override the album metadata.

    Returns:
        Status of the podcast download.
    """
    result: dict[str, Any] = {"episode_id": episode_id}

    try:
        episode = PodcastEpisode.objects.get(pk=episode_id)
    except PodcastEpisode.DoesNotExist:
        logger.warning("podcast episode %s does not exist anymore", episode_id)
        return result

    tmp_file = None

    try:
        # Give up if the task waited too long in the queue, the episode might already be
        # considered orphaned. The comparison is done by the database, since django reads
        # the created_at naive timestamp as a naive datetime.
        if PodcastEpisode.objects.filter(
            pk=episode_id,
            created_at__lt=timezone.now() - IMPORT_EPISODE_MAX_QUEUE_TIME,
        ).exists():
            raise ImportEpisodeException(
                f"podcast episode {episode_id} import was queued for too long"
            )

        # Download podcast episode file
        try:
            with requests.get(
                episode_url,
                stream=True,
                timeout=30,
                headers={"User-Agent": f"LibreTime/{VERSION}"},
            ) as resp:
                resp.raise_for_status()

                filename = extract_filename(resp)

                # The filename extension helps to determine the file type using mutagen.
                suffix = Path(filename).suffix
                with NamedTemporaryFile(suffix=suffix, delete=False) as tmp_file:
                    for chunk in resp.iter_content(chunk_size=2048):
                        tmp_file.write(chunk)

        except RequestException as exc:
            raise ImportEpisodeException(
                f"could not download podcast episode: {exc}"
            ) from exc

        # Save metadata to podcast episode file
        try:
            metadata = mutagen.File(tmp_file.name, easy=True)
            if metadata is None:
                raise ImportEpisodeException(
                    f"could not determine podcast episode {episode_id} file type"
                )

            if override_album:
                logger.debug("overriding album name with podcast name %s", podcast_name)
                metadata["artist"] = podcast_name
                metadata["album"] = podcast_name
                metadata["title"] = episode_title

            elif "album" not in metadata:
                logger.debug("setting album name to podcast name %s", podcast_name)
                metadata["album"] = podcast_name

            metadata.save()
            logger.debug("saved metadata %s", metadata)

        except (MutagenError, TypeError) as exc:
            raise ImportEpisodeException(
                f"could not save podcast episode {episode_id} metadata: {exc}"
            ) from exc

        # Upload podcast episode file
        try:
            with open(tmp_file.name, "rb") as fd:
                with requests.post(
                    f"{settings.CONFIG.general.public_url}/rest/media",
                    files={"file": (filename, fd)},
                    auth=(settings.CONFIG.general.api_key, ""),
                    timeout=30,
                ) as upload_resp:
                    upload_resp.raise_for_status()
                    upload_payload = upload_resp.json()

                    file_id = upload_payload["id"]
                    result["file_id"] = file_id

        except RequestException as exc:
            raise ImportEpisodeException(
                f"could not upload podcast episode {episode_id}: {exc}"
            ) from exc

        file = File.objects.get(pk=file_id)
        episode.file = file
        episode.save(update_fields=["file"])

    # Clean up after unexpected errors and after the soft time limit exception.
    except Exception as exc:
        logger.exception(exc)
        episode.delete()
        raise

    finally:
        if tmp_file is not None:
            Path(tmp_file.name).unlink()

    return result


def extract_filename(response: Response) -> str:
    """
    Extract the filename from a download request.

    Args:
        response: Download request response.

    Returns:
        Extracted filename.
    """
    content_disposition = "Content-Disposition"
    value = response.headers.get(content_disposition)
    if value and "filename" in value:
        parser = EmailMessage()
        parser[content_disposition] = value
        params = parser[content_disposition].params
        return params["filename"]

    return Path(urlsplit(response.url).path).name


@shared_task()
def clean_failed_imports():
    """
    Clean any podcast episodes that failed to import.

    The import task cleans up after itself, this is a safety net for the cases where it
    couldn't (killed worker, lost message, ...). Only the episodes older than
    IMPORT_EPISODE_MAX_AGE are considered, since no import can still be running or
    waiting in the queue for them.

    Returns:
        Number of deleted episodes.
    """
    failed_episodes = PodcastEpisode.objects.filter(
        file__isnull=True,
        created_at__lt=timezone.now() - IMPORT_EPISODE_MAX_AGE,
    )

    failed_episode_ids = list(failed_episodes.values_list("id", flat=True))
    if not failed_episode_ids:
        return 0

    logger.info("deleting podcast episodes: %s", failed_episode_ids)
    count, _ = failed_episodes.delete()
    return count
