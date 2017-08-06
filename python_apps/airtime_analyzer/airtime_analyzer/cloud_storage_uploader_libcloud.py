import os
import logging
import uuid
import ConfigParser
from libcloud.storage.providers import get_driver
from libcloud.storage.types import Provider, ContainerDoesNotExistError, ObjectDoesNotExistError


CLOUD_CONFIG_PATH = os.path.join(os.getenv('LIBRETIME_CONF_DIR', '/etc/airtime'), 'airtime.conf')
STORAGE_BACKEND_FILE = "file"

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

        config = ConfigParser.SafeConfigParser()
        try:
            config.readfp(open(CLOUD_CONFIG_PATH))
            cloud_storage_config_section = config.get("current_backend", "storage_backend")
            self._storage_backend = cloud_storage_config_section
        except IOError as e:
            print "Failed to open config file at " + CLOUD_CONFIG_PATH + ": " + e.strerror
            print "Defaulting to file storage"
            self._storage_backend = STORAGE_BACKEND_FILE
        except Exception as e:
            print e
            print "Defaulting to file storage"
            self._storage_backend = STORAGE_BACKEND_FILE

        if self._storage_backend == STORAGE_BACKEND_FILE:
            self._provider = ""
            self._bucket = ""
            self._api_key = ""
            self._api_key_secret = ""
        else:
            self._provider = config.get(cloud_storage_config_section, 'provider')
            self._bucket = config.get(cloud_storage_config_section, 'bucket')
            self._api_key = config.get(cloud_storage_config_section, 'api_key')
            self._api_key_secret = config.get(cloud_storage_config_section, 'api_key_secret')

    def enabled(self):
        if self._storage_backend == "file":
            return False
        else:
            return True


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
        object_name = "%s_%s%s" % (file_name, str(uuid.uuid4()), extension)

        provider_driver_class = get_driver(getattr(Provider, self._provider))
        driver = provider_driver_class(self._api_key, self._api_key_secret)
        
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

