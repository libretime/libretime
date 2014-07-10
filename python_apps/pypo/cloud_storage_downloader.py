import os
import logging
import ConfigParser
import urllib2

from libcloud.storage.types import Provider, ContainerDoesNotExistError, ObjectDoesNotExistError
from libcloud.storage.providers import get_driver

CONFIG_PATH = '/etc/airtime/airtime.conf'

class CloudStorageDownloader:
    def __init__(self):
        config = self.read_config_file(CONFIG_PATH)
        
        S3_CONFIG_SECTION = "s3"
        self._s3_bucket = config.get(S3_CONFIG_SECTION, 'bucket')
        self._s3_api_key = config.get(S3_CONFIG_SECTION, 'api_key')
        self._s3_api_key_secret = config.get(S3_CONFIG_SECTION, 'api_key_secret')
    
    def download_obj(self, dst, obj_name):
        cls = get_driver(Provider.S3)
        driver = cls(self._s3_api_key, self._s3_api_key_secret)
        #object_name = os.path.basename(urllib2.unquote(obj_url).decode('utf8'))
        try:
            cloud_obj = driver.get_object(container_name=self._s3_bucket,
                                    object_name=obj_name)
        except ObjectDoesNotExistError:
            logging.info("Could not find object: %s" % obj_name)
            exit(-1)
        logging.info('Downloading: %s to %s' % (cloud_obj.name, dst))
        cloud_obj.download(destination_path=dst)

    def read_config_file(self, config_path):
        """Parse the application's config file located at config_path."""
        config = ConfigParser.SafeConfigParser()
        try:
            config.readfp(open(config_path))
        except IOError as e:
            print "Failed to open config file at " + config_path + ": " + e.strerror 
            exit(-1)
        except Exception:
            print e.strerror 
            exit(-1)

        return config