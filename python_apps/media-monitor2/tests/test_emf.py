# -*- coding: utf-8 -*-
import unittest

from media.metadata.process import global_reader
import media.metadata.definitions as defs
defs.load_definitions()

class TestMMP(unittest.TestCase):
    def test_sanity(self):
        m = global_reader.read_mutagen("/home/rudi/music/Nightingale.mp3")
        self.assertTrue( len(m) > 0 )
