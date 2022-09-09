import json
from cgi import parse_header
from pathlib import Path
from tempfile import NamedTemporaryFile
from typing import Any, Dict, Optional
from urllib.parse import urlsplit

import mutagen
import requests
from celery import Celery
from celery.utils.log import get_task_logger
from mutagen import MutagenError
from requests import RequestException, Response

from .config import config

worker = Celery()
logger = get_task_logger(__name__)


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
            logger.exception(f"could not download podcast episode {episode_id}")
            raise exception

        # Save metadata to podcast episode file
        try:
            metadata = mutagen.File(tmp_file.name, easy=True)
            if metadata is None:
                raise MutagenError(
                    f"could not determine episode {episode_id} file type"
                )

            if override_album:
                logger.debug(f"overriding album name with podcast name {podcast_name}")
                metadata["artist"] = podcast_name
                metadata["album"] = podcast_name
                metadata["title"] = episode_title

            elif "album" not in metadata:
                logger.debug(f"setting album name to podcast name {podcast_name}")
                metadata["album"] = podcast_name

            metadata.save()
            logger.debug(f"saved metadata {metadata}")

        except MutagenError as exception:
            logger.exception(f"could not save episode {episode_id} metadata")
            raise exception

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
            logger.exception(f"could not upload episode {episode_id}")
            raise exception

    except (RequestException, MutagenError) as exception:
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
    if "Content-Disposition" in response.headers:
        _, params = parse_header(response.headers["Content-Disposition"])
        if "filename" in params:
            return params["filename"]

    return Path(urlsplit(response.url).path).name
