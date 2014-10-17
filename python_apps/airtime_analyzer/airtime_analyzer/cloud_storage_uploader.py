import os
import logging
import uuid

from libcloud.storage.providers import get_driver
from libcloud.storage.types import Provider, ContainerDoesNotExistError, ObjectDoesNotExistError

class CloudStorageUploader:
    def __init__(self, provider, bucket, api_key, api_key_secret):
        self._provider = provider
        self._bucket = bucket
        self._api_key = api_key
        self._api_key_secret = api_key_secret

    def upload_obj(self, audio_file_path, metadata):
        file_base_name = os.path.basename(audio_file_path)
        file_name, extension = os.path.splitext(file_base_name)
        
        '''
        With Amazon S3 you cannot create a signed url if there are spaces 
        in the object name. URL encoding the object name doesn't solve the
        problem. As a solution we will replace spaces with dashes.
        '''
        file_name = file_name.replace(" ", "-")
        object_name = "%s_%s%s" % (file_name, str(uuid.uuid4()), extension)

        cls = get_driver(getattr(Provider, self._provider))
        driver = cls(self._api_key, self._api_key_secret)
        
        try:
            container = driver.get_container(self._bucket)
        except ContainerDoesNotExistError:
            container = driver.create_container(self._bucket)
        
        extra = {'meta_data': {'filename': file_base_name}}
        
        obj = driver.upload_object(file_path=audio_file_path,
                                   container=container,
                                   object_name=object_name,
                                   verify_hash=False,
                                   extra=extra)

        metadata["filesize"] = os.path.getsize(audio_file_path)
        
        '''remove file from organize directory'''
        try:
            os.remove(audio_file_path)
        except OSError:
            logging.info("Could not remove %s from organize directory" % audio_file_path)
        
        '''pass original filename to Airtime so we can store it in the db'''
        metadata["filename"] = file_base_name
        
        metadata["resource_id"] = object_name
        return metadata

