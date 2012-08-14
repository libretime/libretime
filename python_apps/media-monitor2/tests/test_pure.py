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

    def test_normalized_metadata(self):
        normal = mmp.normalized_metadata(self.md1,"")
        self.assertTrue(hasattr(normal['MDATA_KEY_CREATOR'],'startswith'))
        self.assertTrue('MDATA_KEY_CREATOR' in normal)
        self.assertTrue('MDATA_KEY_SOURCE' in normal)

    def test_organized_path(self):
        o_path = '/home/rudi/throwaway/ACDC_-_Back_In_Black-sample-64kbps.ogg'
        normal = mmp.normalized_metadata(self.md1,o_path)
        og = mmp.organized_path(o_path,
                                '/home/rudi/throwaway/fucking_around/watch/',
                                normal)
        real_path1 = \
            u'/home/rudi/throwaway/fucking_around/watch/unknown/unknown/ACDC_-_Back_In_Black-sample-64kbps-64kbps.ogg'
        self.assertTrue( 'unknown' in og, True )
        self.assertEqual( og, real_path1 )
        # for recorded it should be something like this
        # ./recorded/2012/07/2012-07-09-17-55-00-Untitled Show-256kbps.ogg

    def test_file_md5(self):
        p = os.path.realpath(__file__)
        m1 = mmp.file_md5(p)
        m2 = mmp.file_md5(p,10)
        self.assertTrue( m1 != m2 )
        self.assertRaises( ValueError, lambda : mmp.file_md5('/bull/shit/path') )
        self.assertTrue( m1 == mmp.file_md5(p) )

if __name__ == '__main__': unittest.main()
