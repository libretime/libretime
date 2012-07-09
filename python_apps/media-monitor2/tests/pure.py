# -*- coding: utf-8 -*-
import unittest
import media.monitor.pure as mmp

class TestMMP(unittest.TestCase):
    def test_apply_rules(self):
        sample_dict = {
            'key' : 'val',
            'test' : 'IT',
        }
        rules = {
            'key' : lambda x : x.upper(),
            'test' : lambda y : y.lower()
        }
        mmp.apply_rules_dict(sample_dict, rules)
        self.assertEqual(sample_dict['key'], 'VAL')
        self.assertEqual(sample_dict['test'], 'it')

    def test_default_to(self):
        sd = { }
        def_keys = ['one','two','three']
        mmp.default_to(dictionary=sd, keys=def_keys, default='DEF')
        for k in def_keys: self.assertEqual( sd[k], 'DEF' )

    def test_normalized_metadata(self):
        pass

    def test_organized_path(self):
        pass


if __name__ == '__main__': unittest.main()
