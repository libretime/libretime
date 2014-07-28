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
        object_name = "%s_%s%s" % (file_name, str(uuid.uuid4()), extension)
        
        driver = self.get_cloud_driver()
        
        try:
            container = driver.get_container(self._bucket)
        except ContainerDoesNotExistError:
            container = driver.create_container(self._bucket)
        
        extra = {'meta_data': {'filename': file_base_name},
                 'acl': 'public-read-write'}
        
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

    def delete_obj(self, obj_name):
        driver = self.get_cloud_driver()
        
        try:
            cloud_obj = driver.get_object(container_name=self._bucket,
                                    object_name=obj_name)
            filesize = getattr(cloud_obj, 'size')
            driver.delete_object(obj=cloud_obj)
            return filesize
        except ObjectDoesNotExistError:
            raise Exception("Could not find object on %s" % self._provider)

    def get_cloud_driver(self):
        cls = get_driver(getattr(Provider, self._provider))
        return cls(self._api_key, self._api_key_secret)
