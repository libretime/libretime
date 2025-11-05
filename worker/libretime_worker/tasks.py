import json
import os
from email.message import EmailMessage
from pathlib import Path
from tempfile import NamedTemporaryFile
from typing import Any, Dict, Optional
from urllib.parse import urlsplit

import mutagen
import requests
from celery import Celery, signals
from celery.schedules import crontab
from celery.utils.log import get_task_logger
from libretime_api_client.v1 import ApiClient as LegacyClient
from mutagen import MutagenError
from requests import RequestException, Response

from . import PACKAGE, VERSION
from .config import config

worker = Celery()
logger = get_task_logger(__name__)

legacy_client = LegacyClient(
    base_url=config.general.public_url,
    api_key=config.general.api_key,
)


@signals.worker_init.connect
def init_sentry(**_kwargs):
    if "SENTRY_DSN" in os.environ:
        logger.info("installing sentry")
        # pylint: disable=import-outside-toplevel
        import sentry_sdk
        from sentry_sdk.integrations.celery import CeleryIntegration

        sentry_sdk.init(
            traces_sample_rate=1.0,
            release=f"{PACKAGE}@{VERSION}",
            integrations=[
                CeleryIntegration(),
            ],
        )


worker.conf.beat_schedule = {
    "legacy-trigger-task-manager": {
        "task": "libretime_worker.tasks.legacy_trigger_task_manager",
        "schedule": crontab(minute="*/5"),
    },
}


@worker.task()
def legacy_trigger_task_manager():
    """
    Trigger the legacy task manager to perform background tasks.
    """
    legacy_client.trigger_task_manager()


class PodcastDownloadException(Exception):
    """
    An error occurred during the podcast download task.
    """


@worker.task(name="podcast-download", acks_late=True)
def podcast_download(
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
        Status of the podcast download as JSON string.
    """
    result: Dict[str, Any] = {"episodeid": episode_id}
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
            raise PodcastDownloadException(
                f"could not download podcast episode {episode_id}: {exception}"
            ) from exception

        # Save metadata to podcast episode file
        try:
            metadata = mutagen.File(tmp_file.name, easy=True)
            if metadata is None:
                raise PodcastDownloadException(
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

        except (MutagenError, TypeError) as exception:
            raise PodcastDownloadException(
                f"could not save podcast episode {episode_id} metadata: {exception}"
            ) from exception

        # Upload podcast episode file
        try:
            with requests.post(
                f"{config.general.public_url}/rest/media",
                files={"file": (filename, open(tmp_file.name, "rb"))},
                auth=(config.general.api_key, ""),
                timeout=30,
            ) as upload_resp:
                upload_resp.raise_for_status()
                upload_payload = upload_resp.json()

                result["fileid"] = upload_payload["id"]
                result["status"] = 1

        except RequestException as exception:
            raise PodcastDownloadException(
                f"could not upload podcast episode {episode_id}: {exception}"
            ) from exception

    except PodcastDownloadException as exception:
        logger.exception(exception)
        result["status"] = 0
        result["error"] = str(exception)

    if tmp_file is not None:
        Path(tmp_file.name).unlink()

    return json.dumps(result)


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
