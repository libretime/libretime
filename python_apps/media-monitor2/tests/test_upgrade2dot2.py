from configobj import ConfigObj
import unittest
import os
import upgrade2dot2


def create_cfg(cfg):
    config = ConfigObj()
    config.filename = cfg['path']
    for k,v in cfg['data'].iteritems(): config[k] = v
    return config

class TestUpgrade(unittest.TestCase):

    def setUp(self):
        self.source = 'ttt1.cfg'
        self.dest = 'ttt2.cfg'

    def test_upgrade(self):
        cf = {
                'source' : {
                    'path' : self.source,
                    'data' : {
                        'key1' : 'val1',
                        'key2' : 'val2',
                        'key3' : 5,
                        'key4' : 10,},
                },
                'dest' : {
                    'path' : self.dest,
                    'data' : {
                        'key1' : 'NEW_VAL',
                        'key3' : 25, }
                }
        }
        config1, config2 = create_cfg(cf['source']), create_cfg(cf['dest'])
        for c in [config1,config2]: c.write()
        self.assertTrue( os.path.exists(cf['source']['path']) )
        self.assertTrue( os.path.exists(cf['dest']['path']) )
        # Finished preparing
        upgrade2dot2.upgrade({ cf['source']['path'] : cf['dest']['path'] })
        c1, c2 = ConfigObj(cf['source']['path']), ConfigObj(cf['dest']['path'])
        self.assertEqual( c2['key2'], 'val2')
        self.assertEqual( c2['key4'], '10')
        self.assertEqual( c2['key3'], '25')

    def tearDown(self):
        for clean in [ self.source, self.dest ]:
            os.unlink(clean)

if __name__ == '__main__': unittest.main()
