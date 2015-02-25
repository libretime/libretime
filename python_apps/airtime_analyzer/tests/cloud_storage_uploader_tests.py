from nose.tools import *
from ConfigParser import SafeConfigParser
from airtime_analyzer.cloud_storage_uploader import CloudStorageUploader
from airtime_analyzer.airtime_analyzer import AirtimeAnalyzerServer
from airtime_analyzer import config_file

def setup():
    pass

def teardown():
    pass

def test_analyze():

    cloud_storage_config = SafeConfigParser()
    cloud_storage_config.add_section("current_backend")
    cloud_storage_config.set("current_backend", "storage_backend", "file")
    cl = CloudStorageUploader(cloud_storage_config)
