import os
import json
import urllib2
import requests
import soundcloud
import cgi
import urlparse
import posixpath
import shutil
import tempfile
import traceback
from mutagen.mp3 import MP3
from mutagen.easyid3 import EasyID3
import mutagen.id3
from StringIO import StringIO
from celery import Celery
from celery.utils.log import get_task_logger
from contextlib import closing

celery = Celery()
logger = get_task_logger(__name__)


@celery.task(name='soundcloud-upload', acks_late=True)
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
    # Open the file with urllib2 if it's a cloud file
    data['asset_data'] = open(file_path, 'rb') if os.path.isfile(file_path) else urllib2.urlopen(file_path)
    try:
        logger.info('Uploading track: {0}'.format(data))
        track = client.post('/tracks', track=data)
    except Exception as e:
        logger.info('Error uploading track {title}: {0}'.format(e.message, **data))
        raise e
    data['asset_data'].close()
    return json.dumps(track.fields())


@celery.task(name='soundcloud-download', acks_late=True)
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
        track = client.get('/tracks/%s' % track_id)
        obj.update(track.fields())
        if track.downloadable:
            re = None
            with closing(requests.get('%s?oauth_token=%s' % (track.download_url, client.access_token), verify=True, stream=True)) as r:
                filename = get_filename(r)
                re = requests.post(callback_url, files={'file': (filename, r.content)}, auth=requests.auth.HTTPBasicAuth(api_key, ''))
            re.raise_for_status()
            f = json.loads(re.content)  # Read the response from the media API to get the file id
            obj['fileid'] = f['id']
        else:
            # manually update the task state
            self.update_state(
                state = states.FAILURE,
                meta = 'Track %s is not flagged as downloadable!' % track.title
            )
            # ignore the task so no other state is recorded
            raise Ignore()
    except Exception as e:
        logger.info('Error during file download: {0}'.format(e.message))
        raise e
    return json.dumps(obj)


@celery.task(name='soundcloud-update', acks_late=True)
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
        logger.info('Updating track {title}'.format(**data))
        track = client.put('/tracks/%s' % track_id, track=data)
    except Exception as e:
        logger.info('Error updating track {title}: {0}'.format(e.message, **data))
        raise e
    return json.dumps(track.fields())


@celery.task(name='soundcloud-delete', acks_late=True)
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
        logger.info('Deleting track with ID {0}'.format(track_id))
        track = client.delete('/tracks/%s' % track_id)
    except Exception as e:
        logger.info('Error deleting track!')
        raise e
    return json.dumps(track.fields())


@celery.task(name='podcast-download', acks_late=True)
def podcast_download(id, url, callback_url, api_key, podcast_name, album_override):
    """
    Download a podcast episode

    :param id:              episode unique ID
    :param url:             download url for the episode
    :param callback_url:    callback URL to send the downloaded file to
    :param api_key:         API key for callback authentication
    :param podcast_name:    Name of podcast to be added to id3 metadata for smartblock
    :param album_override:  Passing whether to override the album id3 even if it exists

    :return: JSON formatted string of a dictionary of download statuses
             and file identifiers (for successful uploads)
    :rtype: string
    """
    # Object to store file IDs, episode IDs, and download status
    # (important if there's an error before the file is posted)
    obj = { 'episodeid': id }
    try:
        re = None
        with closing(requests.get(url, stream=True)) as r:
            filename = get_filename(r)
            with tempfile.NamedTemporaryFile(mode ='wb+', delete=False) as audiofile:
                r.raw.decode_content = True
                shutil.copyfileobj(r.raw, audiofile)
                # currently hardcoded for mp3s may want to add support for oggs etc
                m = MP3(audiofile.name, ID3=EasyID3)
                logger.debug('podcast_download loaded mp3 {0}'.format(audiofile.name))

                # replace album title as needed
                m = podcast_override_album(m, podcast_name, album_override)

                m.save()
                filetypeinfo = m.pprint()
                logger.info('filetypeinfo is {0}'.format(filetypeinfo.encode('ascii', 'ignore')))
                re = requests.post(callback_url, files={'file': (filename, open(audiofile.name, 'rb'))}, auth=requests.auth.HTTPBasicAuth(api_key, ''))
        re.raise_for_status()
        f = json.loads(re.content)  # Read the response from the media API to get the file id
        obj['fileid'] = f['id']
        obj['status'] = 1
    except Exception as e:
        obj['error'] = e.message
        logger.info('Error during file download: {0}'.format(e))
        logger.debug('Original Traceback: %s' % (traceback.format_exc(e)))
        obj['status'] = 0
    return json.dumps(obj)

def podcast_override_album(m, podcast_name, override):
    """
    Override m['album'] if empty or forced with override arg
    """
    # if the album override option is enabled replace the album id3 tag with the podcast name even if the album tag contains data
    if override is True:
        logger.debug('overriding album name to {0} in podcast'.format(podcast_name.encode('ascii', 'ignore')))
        m['album'] = podcast_name
    else:
        # replace the album id3 tag with the podcast name if the album tag is empty
        try:
            m['album']
        except KeyError:
           logger.debug('setting new album name to {0} in podcast'.format(podcast_name.encode('ascii', 'ignore')))
           m['album'] = podcast_name
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
    d = r.headers.get('Content-Disposition')
    filename = ''
    if d:
        try:
            _, params = cgi.parse_header(d)
            filename = params['filename']
        except Exception as e:
            # We end up here if we get a Content-Disposition header with no filename
            logger.warn("Couldn't find file name in Content-Disposition header, using url")
    if not filename:
        # Since we don't necessarily get the filename back in the response headers,
        # parse the URL and get the filename and extension
        path = urlparse.urlsplit(r.url).path
        filename = posixpath.basename(path)
    return filename
