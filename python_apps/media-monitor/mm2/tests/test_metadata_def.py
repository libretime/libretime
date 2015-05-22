# -*- coding: utf-8 -*-
import unittest

import media.metadata.process as md

class TestMetadataDef(unittest.TestCase):
    def test_simple(self):

        with md.metadata('MDATA_TESTING') as t:
            t.optional(True)
            t.depends('ONE','TWO')
            t.default('unknown')
            t.translate(lambda kw: kw['ONE'] + kw['TWO'])

        h = { 'ONE' : "testing", 'TWO' : "123" }
        result = md.global_reader.read('test_path',h)
        self.assertTrue( 'MDATA_TESTING' in result )
        self.assertEqual( result['MDATA_TESTING'], 'testing123' )
        h1 = { 'ONE' : 'big testing', 'two' : 'nothing' }
        result1 = md.global_reader.read('bs path', h1)
        self.assertEqual( result1['MDATA_TESTING'], 'unknown' )

    def test_topo(self):
        with md.metadata('MDATA_TESTING') as t:
            t.depends('shen','sheni')
            t.default('megitzda')
            t.translate(lambda kw: kw['shen'] + kw['sheni'])

        with md.metadata('shen') as t:
            t.default('vaxo')

        with md.metadata('sheni') as t:
            t.default('gio')

        with md.metadata('vaxo') as t:
            t.depends('shevetsi')

        v = md.global_reader.read('bs mang', {})
        self.assertEqual(v['MDATA_TESTING'], 'vaxogio')
        self.assertTrue( 'vaxo' not in v )

        md.global_reader.clear()

if __name__ == '__main__': unittest.main()
