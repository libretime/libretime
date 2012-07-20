# -*- coding: utf-8 -*-
import os
import unittest
import sys
from media.monitor.metadata import Metadata

class TestMetadata(unittest.TestCase):
    def setUp(self):
        self.music_folder = u'/home/rudi/music'

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
                md_full = Metadata(full_path)
                md = md_full.extract()
                if i < 3:
                    i += 1
                    print("Sample metadata: '%s'" % md)
                self.assertTrue( len( md.keys() ) > 0 )
                self.assertTrue( 'MDATA_KEY_MD5' in md )
                utf8 = md_full.utf8()
                for k,v in md.iteritems():
                    if hasattr(utf8[k], 'decode'):
                        self.assertEqual( utf8[k].decode('utf-8'), md[k] )
            else: print("Skipping '%s' because it's a directory" % full_path)

if __name__ == '__main__': unittest.main()
