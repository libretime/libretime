import json
import unittest

from api_clients.utils import RequestProvider
from api_clients.version1 import api_config
from configobj import ConfigObj
from mock import MagicMock, patch


class TestRequestProvider(unittest.TestCase):
    def setUp(self):
        self.cfg = api_config
        self.cfg["general"] = {}
        self.cfg["general"]["base_dir"] = "/test"
        self.cfg["general"]["base_port"] = 80
        self.cfg["general"]["base_url"] = "localhost"
        self.cfg["general"]["api_key"] = "TEST_KEY"
        self.cfg["api_base"] = "api"

    def test_test(self):
        self.assertTrue("general" in self.cfg)

    def test_init(self):
        rp = RequestProvider(self.cfg, {})
        self.assertEqual(len(rp.available_requests()), 0)

    def test_contains(self):
        methods = {
            "upload_recorded": "/1/",
            "update_media_url": "/2/",
            "list_all_db_files": "/3/",
        }
        rp = RequestProvider(self.cfg, methods)
        for meth in methods:
            self.assertTrue(meth in rp.requests)


if __name__ == "__main__":
    unittest.main()
