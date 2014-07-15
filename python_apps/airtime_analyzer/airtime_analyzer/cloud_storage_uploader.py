import os
import logging
import uuid

from libcloud.storage.providers import get_driver
from libcloud.storage.types import Provider, ContainerDoesNotExistError

class CloudStorageUploader:
    def __init__(self, provider, bucket, api_key, api_key_secret):
        self._provider = provider
        self._bucket = bucket
        self._api_key = api_key
        self._api_key_secret = api_key_secret

    def upload_obj(self, audio_file_path, metadata):
        file_base_name = os.path.basename(audio_file_path)
        file_name, extension = os.path.splitext(file_base_name)
        object_name = "%s_%s%s" % (file_name, str(uuid.uuid4()), extension)
        
        cls = get_driver(getattr(Provider, self._provider))
        driver = cls(self._api_key, self._api_key_secret)
        
        try:
            container = driver.get_container(self._bucket)
        except ContainerDoesNotExistError:
            container = driver.create_container(self._bucket)
        
        extra = {'meta_data': {'filename': file_base_name}}
        
        with open(audio_file_path, 'rb') as iterator:
            obj = driver.upload_object_via_stream(iterator=iterator,
                                                  container=container,
                                                  object_name=object_name,
                                                  extra=extra)

        '''remove file from organize directory'''
        try:
            os.remove(audio_file_path)
        except OSError:
            logging.info("Could not remove %s" % audio_file_path)
        
        metadata["s3_object_name"] = object_name
        return metadata

    def delete_obj(self, object_name):
        pass