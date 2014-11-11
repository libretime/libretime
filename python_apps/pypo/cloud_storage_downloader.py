import os
import logging
import ConfigParser
import sys
import hashlib

from libcloud.storage.types import Provider, ObjectDoesNotExistError
from libcloud.storage.providers import get_driver

CONFIG_PATH = '/etc/airtime-saas/amazon.conf'

class CloudStorageDownloader:
    """ A class that uses Apache Libcloud's Storage API to download objects from
    a cloud storage backend. For this implementation all files are stored on
    Amazon S3 and will be downloaded from there.
    
    This class is used with Airtime's playout engine service, PYPO.

    Attributes:
        _provider: Storage backend. For exmaple, Amazon S3, Google Storage.
        _bucket: Name of container on provider where files will get uploaded into.
        _api_key: Access key to objects on the provider's storage backend.
        _api_key_secret: Secret access key to objects on the provider's storage backend.
    """

    def __init__(self):
        config = self.read_config_file(CONFIG_PATH)
        
        CLOUD_STORAGE_CONFIG_SECTION = "cloud_storage"
        self._provider = config.get(CLOUD_STORAGE_CONFIG_SECTION, 'provider')
        self._bucket = config.get(CLOUD_STORAGE_CONFIG_SECTION, 'bucket')
        self._api_key = config.get(CLOUD_STORAGE_CONFIG_SECTION, 'api_key')
        self._api_key_secret = config.get(CLOUD_STORAGE_CONFIG_SECTION, 'api_key_secret')
    
    def download_obj(self, dst, obj_name):
        """Downloads a file from Amazon S3 object storage to disk.
        
        Downloads an object to PYPO's temporary cache directory on disk.
        If the file already exists in the cache directory the object
        downloading is skipped.
        
        Keyword arguments:
            dst: PYPO's temporary cache directory on disk.
            obj_name: Name of the object to download to disk
        """
        provider_driver_class = get_driver(getattr(Provider, self._provider))
        driver = provider_driver_class(self._api_key, self._api_key_secret)

        try:
            cloud_obj = driver.get_object(container_name=self._bucket,
                                    object_name=obj_name)
        except ObjectDoesNotExistError:
            logging.info("%s does not exist on Amazon S3" % obj_name)

        # If we detect the file path already exists in PYPO's cache directory
        # we need to verify the contents of that file is the same (in case there
        # was file corruption in a previous download for example) as the
        # object's contents by comparing the hash. If the hash values are not
        # equal we need to download the object to disk again.
        dst_exists = False
        if (os.path.isfile(dst)):
            dst_hash = hashlib.md5(open(dst).read()).hexdigest()
            if dst_hash == cloud_obj.hash:
                dst_exists = True

        if dst_exists == False:
            logging.info('Downloading: %s to %s' % (cloud_obj.name, dst))
            cloud_obj.download(destination_path=dst)
        else:
            logging.info("Skipping download because %s already exists" % dst)

    def read_config_file(self, config_path):
        """Parse the application's config file located at config_path."""
        config = ConfigParser.SafeConfigParser()
        try:
            config.readfp(open(config_path))
        except IOError as e:
            logging.debug("Failed to open config file at %s: %s" % (config_path, e.strerror))
            sys.exit()
        except Exception:
            logging.debug(e.strerror) 
            sys.exit()

        return config
