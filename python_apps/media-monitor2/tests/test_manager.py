import unittest
from media.monitor.manager import Manager

def add_paths(m,paths):
    for path in paths:
        m.add_watch_directory(path)

class TestManager(unittest.TestCase):
    def setUp(self):
        self.opath = "/home/rudi/Airtime/python_apps/media-monitor2/tests/"
        self.ppath = "/home/rudi/Airtime/python_apps/media-monitor2/media/"
        self.paths = [self.opath, self.ppath]

    def test_init(self):
        man = Manager()
        self.assertTrue( len(man.watched_directories) == 0 )
        self.assertTrue( man.watch_channel is not None )
        self.assertTrue( man.organize_channel is not None )

    def test_organize_path(self):
        man = Manager()
        man.set_organize_path( self.opath )
        self.assertEqual( man.get_organize_path(), self.opath )
        man.set_organize_path( self.ppath )
        self.assertEqual( man.get_organize_path(), self.ppath )

    def test_add_watch_directory(self):
        man = Manager()
        add_paths(man, self.paths)
        for path in self.paths:
            self.assertTrue( man.has_watch(path) )

    def test_remove_watch_directory(self):
        man = Manager()
        add_paths(man, self.paths)
        for path in self.paths:
            self.assertTrue( man.has_watch(path) )
            man.remove_watch_directory( path )
            self.assertTrue( not man.has_watch(path) )

if __name__ == '__main__': unittest.main()
