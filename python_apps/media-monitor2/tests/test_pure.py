# -*- coding: utf-8 -*-
import unittest
import os
import media.monitor.pure as mmp

class TestMMP(unittest.TestCase):
    def setUp(self):
        self.md1 = {'MDATA_KEY_MD5': '71185323c2ab0179460546a9d0690107',
                    'MDATA_KEY_FTYPE': 'audioclip',
                    'MDATA_KEY_MIME': 'audio/vorbis',
                    'MDATA_KEY_DURATION': '0:0:25.000687',
                    'MDATA_KEY_SAMPLERATE': 48000,
                    'MDATA_KEY_BITRATE': 64000,
                    'MDATA_KEY_REPLAYGAIN': 0,
                    'MDATA_KEY_TITLE': u'ACDC_-_Back_In_Black-sample-64kbps'}

    def test_apply_rules(self):
        sample_dict = {
            'key' : 'val',
            'test' : 'IT',
        }
        rules = {
            'key' : lambda x : x.upper(),
            'test' : lambda y : y.lower()
        }
        sample_dict = mmp.apply_rules_dict(sample_dict, rules)
        self.assertEqual(sample_dict['key'], 'VAL')
        self.assertEqual(sample_dict['test'], 'it')

    def test_default_to(self):
        sd = { }
        def_keys = ['one','two','three']
        sd = mmp.default_to(dictionary=sd, keys=def_keys, default='DEF')
        for k in def_keys: self.assertEqual( sd[k], 'DEF' )

    def test_file_md5(self):
        p = os.path.realpath(__file__)
        m1 = mmp.file_md5(p)
        m2 = mmp.file_md5(p,10)
        self.assertTrue( m1 != m2 )
        self.assertRaises( ValueError, lambda : mmp.file_md5('/file/path') )
        self.assertTrue( m1 == mmp.file_md5(p) )

    def test_sub_path(self):
        f1 = "/home/testing/123.mp3"
        d1 = "/home/testing"
        d2 = "/home/testing/"
        self.assertTrue( mmp.sub_path(d1, f1) )
        self.assertTrue( mmp.sub_path(d2, f1) )

    def test_parse_int(self):
        self.assertEqual( mmp.parse_int("123"), "123" )
        self.assertEqual( mmp.parse_int("123asf"), "123" )
        self.assertEqual( mmp.parse_int("asdf"), None )

    def test_truncate_to_length(self):
        s1 = "testing with non string literal"
        s2 = u"testing with unicode literal"
        self.assertEqual( len(mmp.truncate_to_length(s1, 5)), 5)
        self.assertEqual( len(mmp.truncate_to_length(s2, 8)), 8)


    def test_owner_id(self):
        start_path = "testing.mp3"
        id_path    = "testing.mp3.identifier"
        o_id   = 123
        f = open(id_path, 'w')
        f.write("123")
        f.close()
        possible_id = mmp.owner_id(start_path)
        self.assertFalse( os.path.exists(id_path) )
        self.assertEqual( possible_id, o_id )
        self.assertEqual( -1, mmp.owner_id("something.random") )

if __name__ == '__main__': unittest.main()
