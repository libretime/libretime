import os
import json
import urllib2
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

@celery.task(name='soundcloud-delete', acks_late=True)
def soundcloud_delete(token, track_id):
    """
    Delete a file from SoundCloud

    :param token: OAuth2 client access token

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
