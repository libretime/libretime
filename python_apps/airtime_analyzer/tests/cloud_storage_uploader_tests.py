from nose.tools import *
from airtime_analyzer.cloud_storage_uploader import CloudStorageUploader
from airtime_analyzer.airtime_analyzer import AirtimeAnalyzerServer

def setup():
    pass

def teardown():
    pass

def test_analyze():
    cl = CloudStorageUploader()
    cl._storage_backend = "file"