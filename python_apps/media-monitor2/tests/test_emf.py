# -*- coding: utf-8 -*-
import unittest
from pprint import pprint as pp

from media.metadata.process import global_reader
from media.monitor.metadata import Metadata

import media.metadata.definitions as defs
defs.load_definitions()

class TestMMP(unittest.TestCase):
    def test_sanity(self):
        path = "/home/rudi/music/Nightingale.mp3"
        m = global_reader.read_mutagen(path)
        self.assertTrue( len(m) > 0 )
        n = Metadata(path)
        self.assertEqual(n.extract(), m)


if __name__ == '__main__': unittest.main()
