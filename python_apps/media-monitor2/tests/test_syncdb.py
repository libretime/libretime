# -*- coding: utf-8 -*-
import unittest
import os
from media.monitor.syncdb import SyncDB
from media.monitor.log import get_logger
from media.monitor.pure import partition
import api_clients.api_client as ac

class TestSyncDB(unittest.TestCase):
    def setUp(self):
        self.ac = ac.AirtimeApiClient(logger=get_logger())

    def test_syncdb_init(self):
        sdb = SyncDB(self.ac)
        self.assertTrue( len(sdb.directories.keys()) > 0 )

    def test_list(self):
        self.sdb = SyncDB(self.ac)
        for watch_dir in self.sdb.list_directories():
            self.assertTrue( os.path.exists(watch_dir) )

    def test_directory_get_files(self):
        sdb = SyncDB(self.ac)
        print(sdb.directories)
        for wdir in sdb.list_directories():
            files = sdb.directory_get_files(wdir)
            self.assertTrue( len(files) >= 0 )
            self.assertTrue( isinstance(files, list) )
            exist, deleted = partition(os.path.exists, files)
            print("(exist, deleted) = (%d, %d)" % ( len(exist), len(deleted) ) )

if __name__ == '__main__': unittest.main()
