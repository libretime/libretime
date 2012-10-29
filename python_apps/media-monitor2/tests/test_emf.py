# -*- coding: utf-8 -*-
import unittest
#from pprint import pprint as pp

from media.metadata.process import global_reader
from media.monitor.metadata import Metadata

import media.metadata.definitions as defs
defs.load_definitions()

class TestMMP(unittest.TestCase):

    def setUp(self):
        self.maxDiff = None

    def metadatas(self,f):
        return global_reader.read_mutagen(f), Metadata(f).extract()

    def test_old_metadata(self):
        path = "/home/rudi/music/Nightingale.mp3"
        m = global_reader.read_mutagen(path)
        self.assertTrue( len(m) > 0 )
        n = Metadata(path)
        self.assertEqual(n.extract(), m)

    def test_recorded(self):
        recorded_file = "./15:15:00-Untitled Show-256kbps.ogg"
        emf, old = self.metadatas(recorded_file)
        self.assertEqual(emf, old)

if __name__ == '__main__': unittest.main()
