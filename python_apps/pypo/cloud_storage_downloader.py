import os
import logging
import ConfigParser
import sys
import hashlib

from libcloud.storage.types import Provider, ObjectDoesNotExistError
from libcloud.storage.providers import get_driver

CONFIG_PATH = '/etc/airtime/airtime.conf'

class CloudStorageDownloader:
    def __init__(self):
        config = self.read_config_file(CONFIG_PATH)
        
        CLOUD_STORAGE_CONFIG_SECTION = "cloud_storage"
        self._provider = config.get(CLOUD_STORAGE_CONFIG_SECTION, 'provider')
        self._bucket = config.get(CLOUD_STORAGE_CONFIG_SECTION, 'bucket')
        self._api_key = config.get(CLOUD_STORAGE_CONFIG_SECTION, 'api_key')
        self._api_key_secret = config.get(CLOUD_STORAGE_CONFIG_SECTION, 'api_key_secret')
    
    def download_obj(self, dst, obj_name):
        cls = get_driver(getattr(Provider, self._provider))
        driver = cls(self._api_key, self._api_key_secret)

        try:
            cloud_obj = driver.get_object(container_name=self._bucket,
                                    object_name=obj_name)
        except ObjectDoesNotExistError:
            logging.info("%s does not exist on Amazon S3" % obj_name)

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
            print "Failed to open config file at " + config_path + ": " + e.strerror 
            sys.exit()
        except Exception:
            print e.strerror 
            sys.exit()

        return config
