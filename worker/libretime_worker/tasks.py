import json
import shutil
import tempfile
import traceback
from cgi import parse_header
from contextlib import closing
from pathlib import Path
from urllib.parse import urlsplit

import mutagen
import requests
from celery import Celery
from celery.utils.log import get_task_logger
from requests import Response

from .config import config

worker = Celery()
logger = get_task_logger(__name__)


@worker.task(name="podcast-download", acks_late=True)
def podcast_download(
    id,
    url,
    podcast_name,
    album_override,
    track_title,
):
    """
    Download a podcast episode

    :param id:              episode unique ID
    :param url:             download url for the episode
    :param podcast_name:    Name of podcast to be added to id3 metadata for smartblock
    :param album_override:  Passing whether to override the album id3 even if it exists
    :param track_title:     Passing the title of the episode from feed to override the metadata

    :return: JSON formatted string of a dictionary of download statuses
             and file identifiers (for successful uploads)
    :rtype: string
    """
    # Object to store file IDs, episode IDs, and download status
    # (important if there's an error before the file is posted)
    obj = {"episodeid": id}
    try:
        re = None
        with closing(requests.get(url, stream=True)) as r:
            filename = extract_filename(r)
            with tempfile.NamedTemporaryFile(mode="wb+", delete=False) as audiofile:
                r.raw.decode_content = True
                shutil.copyfileobj(r.raw, audiofile)
                # mutagen should be able to guess the write file type
                metadata_audiofile = mutagen.File(audiofile.name, easy=True)
                # if for some reason this should fail lets try it as a mp3 specific code
                if metadata_audiofile == None:
                    # if this happens then mutagen couldn't guess what type of file it is
                    mp3suffix = ("mp3", "MP3", "Mp3", "mP3")
                    # so we treat it like a mp3 if it has a mp3 file extension and hope for the best
                    if filename.endswith(mp3suffix):
                        metadata_audiofile = mutagen.mp3.MP3(
                            audiofile.name, ID3=mutagen.easyid3.EasyID3
                        )
                # replace track metadata as indicated by album_override setting
                # replace album title as needed
                metadata_audiofile = podcast_override_metadata(
                    metadata_audiofile, podcast_name, album_override, track_title
                )
                metadata_audiofile.save()
                filetypeinfo = metadata_audiofile.pprint()
                logger.info(
                    "filetypeinfo is {}".format(filetypeinfo.encode("ascii", "ignore"))
                )
                callback_url = f"{config.general.public_url}/rest/media"
                callback_api_key = config.general.api_key

                re = requests.post(
                    callback_url,
                    files={"file": (filename, open(audiofile.name, "rb"))},
                    auth=requests.auth.HTTPBasicAuth(callback_api_key, ""),
                )
        re.raise_for_status()
        try:
            response = re.content.decode()
        except (UnicodeDecodeError, AttributeError):
            response = re.content
        f = json.loads(
            response
        )  # Read the response from the media API to get the file id
        obj["fileid"] = f["id"]
        obj["status"] = 1
    except Exception as e:
        obj["error"] = e.message
        logger.info(f"Error during file download: {e}")
        logger.debug("Original Traceback: %s" % (traceback.format_exc(e)))
        obj["status"] = 0
    return json.dumps(obj)


def podcast_override_metadata(m, podcast_name, override, track_title):
    """
    Override m['album'] if empty or forced with override arg
    """
    # if the album override option is enabled replace the album id3 tag with the podcast name even if the album tag contains data
    if override is True:
        logger.debug(
            "overriding album name to {} in podcast".format(
                podcast_name.encode("ascii", "ignore")
            )
        )
        m["album"] = podcast_name
        m["title"] = track_title
        m["artist"] = podcast_name
    else:
        # replace the album id3 tag with the podcast name if the album tag is empty
        try:
            m["album"]
        except KeyError:
            logger.debug(
                "setting new album name to {} in podcast".format(
                    podcast_name.encode("ascii", "ignore")
                )
            )
            m["album"] = podcast_name
    return m


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
