import unittest
import json
from mock import patch, MagicMock
from configobj import ConfigObj
from api_clients.api_client import RequestProvider, api_config

class TestRequestProvider(unittest.TestCase):
    def setUp(self):
        self.cfg = api_config
        self.cfg['general'] = {}
        self.cfg['general']['base_dir'] = '/test'
        self.cfg['general']['base_port'] = 80
        self.cfg['general']['base_url'] = 'localhost'
        self.cfg['general']['api_key'] = 'TEST_KEY'
        self.cfg['api_base'] = 'api'
    def test_test(self):
        self.assertTrue('general' in self.cfg)
    def test_init(self):
        rp = RequestProvider(self.cfg)
        self.assertTrue( len( rp.available_requests() ) > 0 )
    def test_contains(self):
        rp = RequestProvider(self.cfg)
        methods = ['upload_recorded', 'update_media_url', 'list_all_db_files']
        for meth in methods:
            self.assertTrue( meth in rp.requests )

if __name__ == '__main__': unittest.main()
