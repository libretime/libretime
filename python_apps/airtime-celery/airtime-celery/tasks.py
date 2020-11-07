from future.standard_library import install_aliases

install_aliases()

import os
import json
import requests
import soundcloud
import cgi
import posixpath
import shutil
import tempfile
import traceback
import mutagen
from io import StringIO
from celery import Celery
from celery.utils.log import get_task_logger
from contextlib import closing
from urllib.parse import urlsplit


celery = Celery()
logger = get_task_logger(__name__)


@celery.task(name="soundcloud-upload", acks_late=True)
def soundcloud_upload(data, token, file_path):
    """
    Upload a file to SoundCloud

    :param data:      associative array containing SoundCloud metadata
    :param token:     OAuth2 client access token
    :param file_path: path to the file being uploaded

    :return: JSON formatted string of the SoundCloud response object
    :rtype: string
    """
    client = soundcloud.Client(access_token=token)
    # Open the file with requests if it's a cloud file
    data["asset_data"] = (
        open(file_path, "rb")
        if os.path.isfile(file_path)
        else requests.get(file_path).content
    )
    try:
        logger.info("Uploading track: {0}".format(data))
        track = client.post("/tracks", track=data)
    except Exception as e:
        logger.info("Error uploading track {title}: {0}".format(e.message, **data))
        raise e
    data["asset_data"].close()
    return json.dumps(track.fields())


@celery.task(name="soundcloud-download", acks_late=True)
def soundcloud_download(token, callback_url, api_key, track_id):
    """
    Download a file from SoundCloud

    :param token:        OAuth2 client access token
    :param callback_url: callback URL to send the downloaded file to
    :param api_key:      API key for callback authentication
    :param track_id:     SoundCloud track identifier

    :return: JSON formatted string of file identifiers for the downloaded tracks
    :rtype: string
    """
    client = soundcloud.Client(access_token=token)
    obj = {}
    try:
        track = client.get("/tracks/%s" % track_id)
        obj.update(track.fields())
        if track.downloadable:
            re = None
            with closing(
                requests.get(
                    "%s?oauth_token=%s" % (track.download_url, client.access_token),
                    verify=True,
                    stream=True,
                )
            ) as r:
                filename = get_filename(r)
                re = requests.post(
                    callback_url,
                    files={"file": (filename, r.content)},
                    auth=requests.auth.HTTPBasicAuth(api_key, ""),
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
        else:
            # manually update the task state
            self.update_state(
                state=states.FAILURE,
                meta="Track %s is not flagged as downloadable!" % track.title,
            )
            # ignore the task so no other state is recorded
            raise Ignore()
    except Exception as e:
        logger.info("Error during file download: {0}".format(e.message))
        raise e
    return json.dumps(obj)


@celery.task(name="soundcloud-update", acks_late=True)
def soundcloud_update(data, token, track_id):
    """
    Update a file on SoundCloud

    :param data:      associative array containing SoundCloud metadata
    :param token:     OAuth2 client access token
    :param track_id:  SoundCloud ID of the track to be updated

    :return: JSON formatted string of the SoundCloud response object
    :rtype: string
    """
    client = soundcloud.Client(access_token=token)
    try:
        logger.info("Updating track {title}".format(**data))
        track = client.put("/tracks/%s" % track_id, track=data)
    except Exception as e:
        logger.info("Error updating track {title}: {0}".format(e.message, **data))
        raise e
    return json.dumps(track.fields())


@celery.task(name="soundcloud-delete", acks_late=True)
def soundcloud_delete(token, track_id):
    """
    Delete a file from SoundCloud

    :param token:       OAuth2 client access token
    :param track_id:    SoundCloud track identifier

    :return: JSON formatted string of the SoundCloud response object
    :rtype: string
    """
    client = soundcloud.Client(access_token=token)
    try:
        logger.info("Deleting track with ID {0}".format(track_id))
        track = client.delete("/tracks/%s" % track_id)
    except Exception as e:
        logger.info("Error deleting track!")
        raise e
    return json.dumps(track.fields())


@celery.task(name="podcast-download", acks_late=True)
def podcast_download(
    id, url, callback_url, api_key, podcast_name, album_override, track_title
):
    """
    Download a podcast episode

    :param id:              episode unique ID
    :param url:             download url for the episode
    :param callback_url:    callback URL to send the downloaded file to
    :param api_key:         API key for callback authentication
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
            filename = get_filename(r)
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
                    "filetypeinfo is {0}".format(filetypeinfo.encode("ascii", "ignore"))
                )
                re = requests.post(
                    callback_url,
                    files={"file": (filename, open(audiofile.name, "rb"))},
                    auth=requests.auth.HTTPBasicAuth(api_key, ""),
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
        logger.info("Error during file download: {0}".format(e))
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
            "overriding album name to {0} in podcast".format(
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
                "setting new album name to {0} in podcast".format(
                    podcast_name.encode("ascii", "ignore")
                )
            )
            m["album"] = podcast_name
    return m


def get_filename(r):
    """
    Given a request object to a file resource, get the name of the file to be downloaded
    by parsing either the content disposition or the request URL

    :param r: request object

    :return: the file name
    :rtype: string
    """
    # Try to get the filename from the content disposition
    d = r.headers.get("Content-Disposition")
    filename = ""
    if d:
        try:
            _, params = cgi.parse_header(d)
            filename = params["filename"]
        except Exception as e:
            # We end up here if we get a Content-Disposition header with no filename
            logger.warn(
                "Couldn't find file name in Content-Disposition header, using url"
            )
    if not filename:
        # Since we don't necessarily get the filename back in the response headers,
        # parse the URL and get the filename and extension
        path = urlsplit(r.url).path
        filename = posixpath.basename(path)
    return filename
