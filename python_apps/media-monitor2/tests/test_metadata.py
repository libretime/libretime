# -*- coding: utf-8 -*-
import os
import unittest
import sys
import media.monitor.metadata as mmm

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
                md_full = mmm.Metadata(full_path)
                md = md_full.extract()
                if i < 3:
                    i += 1
                    print("Sample metadata: '%s'" % md)
                self.assertTrue( len( md.keys() ) > 0 )
                utf8 = md_full.utf8()
                for k,v in md.iteritems():
                    if hasattr(utf8[k], 'decode'):
                        self.assertEqual( utf8[k].decode('utf-8'), md[k] )
            else: print("Skipping '%s' because it's a directory" % full_path)

    def test_airtime_mutagen_dict(self):
        for muta,airtime in mmm.mutagen2airtime.iteritems():
            self.assertEqual( mmm.airtime2mutagen[airtime], muta )

    def test_format_length(self):
        # TODO : add some real tests for this function
        x1 = 123456
        print("Formatting '%s' to '%s'" % (x1, mmm.format_length(x1)))

if __name__ == '__main__': unittest.main()
