import unittest
from media.monitor.manager import Manager

class TestManager(unittest.TestCase):
    def setUp(self):
        self.opath = "/home/rudi/Airtime/python_apps/media-monitor2/tests/"
        self.ppath = "/home/rudi/Airtime/python_apps/media-monitor2/media/"

    def test_init(self):
        man = Manager()
        self.assertTrue( len(man.watched_directories) == 0 )
        self.assertTrue( man.watch_channel is not None )
        self.assertTrue( man.organize_channel is not None )

    def test_organize_path(self):
        man = Manager()
        man.set_organize_path( self.opath )
        self.assertEqual( man.organize_path, self.opath )
        man.set_organize_path( self.ppath )
        self.assertEqual( man.organize_path, self.ppath )

    def test_store_path(self):
        man = Manager()
        man.set_store_path( self.opath )
        self.assertEqual( man.store_path, self.opath )


if __name__ == '__main__': unittest.main()
