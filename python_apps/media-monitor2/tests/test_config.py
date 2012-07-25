# -*- coding: utf-8 -*-
import unittest
import pprint

from media.monitor.config import MMConfig
from media.monitor.exceptions import NoConfigFile, ConfigAccessViolation

pp = pprint.PrettyPrinter(indent=4)

class TestMMConfig(unittest.TestCase):
    def setUp(self):
        self.real_config = MMConfig("./test_config.cfg")
        #pp.pprint(self.real_config.cfg.dict)

    def test_bad_config(self):
        self.assertRaises( NoConfigFile, lambda : MMConfig("/fake/stuff/here") )

    def test_no_set(self):
        def myf(): self.real_config['bad'] = 'change'
        self.assertRaises( ConfigAccessViolation, myf )

    def test_copying(self):
        k = 'list_value_testing'
        mycopy = self.real_config[k]
        mycopy.append("another element")
        self.assertTrue( len(mycopy) , len(self.real_config[k]) + 1 )

if __name__ == '__main__': unittest.main()
