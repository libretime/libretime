import os
import json
import urllib2
import requests
import soundcloud
import cgi
import urlparse
import posixpath
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

    :return: the SoundCloud response object
    :rtype: dict
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
def soundcloud_download(token, callback_url, api_key, track_id=None):
    """
    This is in stasis 

    :param token:        OAuth2 client access token
    :param callback_url: callback URL to send the downloaded file to
    :param api_key:      API key for callback authentication
    :param track_id:     SoundCloud track identifier
    :rtype: None
    """
    client = soundcloud.Client(access_token=token)
    try:
        tracks = client.get('/me/tracks') if track_id is None else {client.get('/tracks/%s' % track_id)}
        for track in tracks:
            if track.downloadable:
                track_file = client.get(track.download_url)
                with track_file as f:
                    requests.post(callback_url, data=f, auth=requests.auth.HTTPBasicAuth(api_key, ''))
    except Exception as e:
        logger.info('Error during file download: {0}'.format(e.message))
        logger.info(str(e))
        raise e


@celery.task(name='soundcloud-delete', acks_late=True)
def soundcloud_delete(token, track_id):
    """
    Delete a file from SoundCloud

    :param token:       OAuth2 client access token
    :param track_id:    SoundCloud track identifier

    :return: the SoundCloud response object
    :rtype: dict
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
def podcast_download(id, url, callback_url, api_key):
    """
    Download a batch of podcast episodes

    :param id:              episode unique ID
    :param url:             download url for the episode
    :param callback_url:    callback URL to send the downloaded file to
    :param api_key:         API key for callback authentication
    :rtype: None
    """
    # Object to store file IDs, episode IDs, and download status
    # (important if there's an error before the file is posted)
    obj = { 'episodeid': id }
    try:
        re = None
        with closing(requests.get(url, stream=True)) as r:
            filename = get_filename(r)
            re = requests.post(callback_url, files={'file': (filename, r.content)}, auth=requests.auth.HTTPBasicAuth(api_key, ''))
        re.raise_for_status()
        f = json.loads(re.content)  # Read the response from the media API to get the file id
        obj['fileid'] = f['id']
        obj['status'] = 1
    except Exception as e:
        obj['error'] = e.message
        logger.info('Error during file download: {0}'.format(e.message))
        obj['status'] = 0
    return json.dumps(obj)


def get_filename(r):
    """
    Given a request object to a file resource, get the name of the file to be downloaded
    by parsing either the content disposition or the request URL

    :param r: request object
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
