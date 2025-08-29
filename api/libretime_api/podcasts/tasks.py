import json
from email.message import EmailMessage
from pathlib import Path
from tempfile import NamedTemporaryFile
from typing import Any, Dict, Optional
from urllib.parse import urlsplit

import mutagen
import requests
from celery import shared_task
from celery.utils.log import get_task_logger
from django.conf import settings
from django_celery_results.models import TaskResult
from mutagen import MutagenError
from requests import RequestException, Response

from ..storage.models import File
from .models import PodcastEpisode

logger = get_task_logger(__name__)


@shared_task(acks_late=True, time_limit=900)
def download_episode(
    episode_id: int,
    episode_url: str,
    episode_title: Optional[str],
    podcast_name: str,
    override_album: bool,
):
    """
    Download a podcast episode.

    Args:
        episode_id: Episode ID.
        episode_url: Episode download url.
        episode_title: Episode title to override the title metadata.
        podcast_name: Podcast name to save to the metadata.
        override_album: Whether to override the album metadata.

    Returns:
        Status of the podcast download.
    """
    result: Dict[str, Any] = {"episode_id": episode_id}

    episode = PodcastEpisode.objects.get(pk=episode_id)
    tmp_file = None

    try:
        # Download podcast episode file
        try:
            with requests.get(episode_url, stream=True, timeout=30) as resp:
                resp.raise_for_status()

                filename = extract_filename(resp)

                # The filename extension helps to determine the file type using mutagen
                with NamedTemporaryFile(suffix=filename, delete=False) as tmp_file:
                    for chunk in resp.iter_content(chunk_size=2048):
                        tmp_file.write(chunk)

        except RequestException as exception:
            logger.exception("could not download podcast episode %s", episode_id)
            raise exception

        # Save metadata to podcast episode file
        try:
            metadata = mutagen.File(tmp_file.name, easy=True)
            if metadata is None:
                raise MutagenError(
                    f"could not determine episode {episode_id} file type"
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

        except MutagenError as exception:
            logger.exception("could not save episode %s metadata", episode_id)
            raise exception

        # Upload podcast episode file
        try:
            with requests.post(
                f"{settings.CONFIG.general.public_url}/rest/media",
                files={"file": (filename, open(tmp_file.name, "rb"))},
                auth=(settings.CONFIG.general.api_key, ""),
                timeout=30,
            ) as upload_resp:
                upload_resp.raise_for_status()
                upload_payload = upload_resp.json()

                file_id = upload_payload["id"]
                result["file_id"] = file_id

        except RequestException as exception:
            logger.exception("could not upload episode %s", episode_id)
            raise exception

        file = File.objects.get(pk=file_id)
        episode.file = file
        episode.save()

    except (RequestException, MutagenError) as exception:
        logger.error(exception)
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
def delete_failed_download():
    """
    Delete any podcast episodes that failed to download.

    Returns:
        Number of deleted episodes.
    """
    pending_tasks = TaskResult.objects.filter(
        task_name=download_episode.name,
        status__in=["PENDING", "STARTED", "RETRY"],
    ).all()

    pending_episode_ids = []
    for task in pending_tasks:
        if not task.task_kwargs:
            continue

        kwargs = json.loads(task.task_kwargs)
        if "episode_id" in kwargs:
            pending_episode_ids.append(kwargs["episode_id"])

    failed_episodes = (
        PodcastEpisode.objects.filter(file__isnull=True)
        .exclude(pk__in=pending_episode_ids)
        .all()
    )
    if failed_episodes.count() > 0:
        logger.info(
            "deleting podcast episodes: %s",
            failed_episodes.values_list("id", flat=True),
        )
        count, _ = failed_episodes.delete()
        return count
