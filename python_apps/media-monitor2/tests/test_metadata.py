# -*- coding: utf-8 -*-
import os
import unittest
import sys
from media.monitor.metadata import Metadata

class TestMetadata(unittest.TestCase):
    def setUp(self):
        self.music_folder = '/home/rudi/music'

    def test_got_music_folder(self):
        t = os.path.exists(self.music_folder)
        self.assertTrue(t)
        if not t:
            print("'%s' must exist for this test to run." % self.music_folder )
            sys.exit(1)

    def test_metadata(self):
        full_paths = (os.path.join(self.music_folder,filename) for filename in os.listdir(self.music_folder))
        i = 0
        for full_path in full_paths:
            if os.path.isfile(full_path):
                md = Metadata(full_path).extract()
                if i < 3:
                    i += 1
                    print("Sample metadata: '%s'" % md)
                self.assertTrue( len( md.keys() ) > 0 )
                self.assertTrue( 'MDATA_KEY_MD5' in md )
            else:
                print("Skipping '%s'" % full_path)

if __name__ == '__main__': unittest.main()
