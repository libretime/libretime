import os
import json
import urllib2
import requests
import soundcloud
from celery import Celery
from celery.utils.log import get_task_logger

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
def podcast_download(download_urls, callback_url, api_key):
    """
    Download a given podcast episode

    :param download_urls:   array of download URLs for episodes to download
    :param callback_url:    callback URL to send the downloaded file to
    :param api_key:         API key for callback authentication
    :rtype: None
    """
    try:
        for url in download_urls:
            r = requests.get(url, stream=True)
            r.raise_for_status()
            with r as f:
                requests.post(callback_url, data=f, auth=requests.auth.HTTPBasicAuth(api_key, ''))
    except Exception as e:
        logger.info('Error during file download: {0}'.format(e.message))
        logger.info(str(e))
        raise e