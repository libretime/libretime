import os
import logging
import uuid
import airtime_analyzer as aa
from libcloud.storage.providers import get_driver
from libcloud.storage.types import Provider, ContainerDoesNotExistError, ObjectDoesNotExistError


CONFIG_PATH = '/etc/airtime-saas/cloud_storage.conf'

class CloudStorageUploader:
    """ A class that uses Apache Libcloud's Storage API to upload objects into
    a cloud storage backend. For this implementation all files will be uploaded
    into a bucket on Amazon S3.
    
    It is important to note that every file, coming from different Airtime Pro
    stations, will get uploaded into the same bucket on the same Amazon S3
    account.

    Attributes:
        _provider: Storage backend. For exmaple, Amazon S3, Google Storage.
        _bucket: Name of container on provider where files will get uploaded into.
        _api_key: Access key to objects on the provider's storage backend.
        _api_key_secret: Secret access key to objects on the provider's storage backend.
    """

    def __init__(self):
        config = aa.AirtimeAnalyzerServer.read_config_file(CONFIG_PATH)
        
        CLOUD_STORAGE_CONFIG_SECTION = config.get("current_backend", "storage_backend")
        self._storage_backend = CLOUD_STORAGE_CONFIG_SECTION
        self._provider = config.get(CLOUD_STORAGE_CONFIG_SECTION, 'provider')
        self._bucket = config.get(CLOUD_STORAGE_CONFIG_SECTION, 'bucket')
        self._api_key = config.get(CLOUD_STORAGE_CONFIG_SECTION, 'api_key')
        self._api_key_secret = config.get(CLOUD_STORAGE_CONFIG_SECTION, 'api_key_secret')

    def upload_obj(self, audio_file_path, metadata):
        """Uploads a file into Amazon S3 object storage.
        
        Before a file is uploaded onto Amazon S3 we generate a unique object
        name consisting of the filename and a unqiue string using the uuid4
        module.
        
        Keyword arguments:
            audio_file_path: Path on disk to the audio file that is about to be
                             uploaded to Amazon S3 object storage.
            metadata: ID3 tags and other metadata extracted from the audio file.
            
        Returns:
            The metadata dictionary it received with three new keys:
                filesize: The file's filesize in bytes.
                filename: The file's filename.
                resource_id: The unique object name used to identify the objects
                             on Amazon S3 
        """
        
        file_base_name = os.path.basename(audio_file_path)
        file_name, extension = os.path.splitext(file_base_name)
        
        # With Amazon S3 you cannot create a signed url if there are spaces 
        # in the object name. URL encoding the object name doesn't solve the
        # problem. As a solution we will replace spaces with dashes.
        file_name = file_name.replace(" ", "-")
        object_name = "%s/%s_%s%s" % (metadata["station_domain"], file_name, str(uuid.uuid4()), extension)

        provider_driver_class = get_driver(getattr(Provider, self._provider))
        driver = provider_driver_class(self._api_key, self._api_key_secret)
        
        try:
            container = driver.get_container(self._bucket)
        except ContainerDoesNotExistError:
            container = driver.create_container(self._bucket)
        
        extra = {'meta_data': {'filename': file_base_name,
                               'station_domain': metadata["station_domain"]}}
        
        obj = driver.upload_object(file_path=audio_file_path,
                                   container=container,
                                   object_name=object_name,
                                   verify_hash=False,
                                   extra=extra)

        metadata["filesize"] = os.path.getsize(audio_file_path)
        
        # Remove file from organize directory
        try:
            os.remove(audio_file_path)
        except OSError:
            logging.info("Could not remove %s from organize directory" % audio_file_path)
        
        # Pass original filename to Airtime so we can store it in the db
        metadata["filename"] = file_base_name
        
        metadata["resource_id"] = object_name
        metadata["storage_backend"] = self._storage_backend
        return metadata

