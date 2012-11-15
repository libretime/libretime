import unittest
from copy import deepcopy
from media.saas.airtimeinstance import AirtimeInstance, NoConfigFile

class TestAirtimeInstance(unittest.TestCase):
    def setUp(self):
        self.cfg = {
            'api_client'    : 'tests/test_instance.py',
            'media_monitor' : 'tests/test_instance.py',
            'logging'       : 'tests/test_instance.py',
        }

    def test_init_good(self):
        AirtimeInstance("/root", self.cfg)
        self.assertTrue(True)

    def test_init_bad(self):
        cfg = deepcopy(self.cfg)
        cfg['api_client'] = 'bs'
        with self.assertRaises(NoConfigFile):
            AirtimeInstance("/root", cfg)
