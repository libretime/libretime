import os
import json
import urllib2
import soundcloud
from celery import Celery
from celery.utils.log import get_task_logger

celery = Celery()
logger = get_task_logger(__name__)


@celery.task(name='upload-to-soundcloud')
def upload_to_soundcloud(data, token, file_path):
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
