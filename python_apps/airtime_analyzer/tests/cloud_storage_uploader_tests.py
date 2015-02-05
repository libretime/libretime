from nose.tools import *
from airtime_analyzer.cloud_storage_uploader import CloudStorageUploader
from airtime_analyzer.airtime_analyzer import AirtimeAnalyzerServer
from airtime_analyzer import config_file

def setup():
    pass

def teardown():
    pass

def test_analyze():
    cloud_storage_config_path = '/etc/airtime-saas/production/cloud_storage.conf'
    cloud_storage_config = config_file.read_config_file(cloud_storage_config_path)
    cl = CloudStorageUploader(cloud_storage_config)
    cl._storage_backend = "file"