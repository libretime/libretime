# -*- coding: utf-8 -*-
import unittest
import os
from media.monitor.syncdb import AirtimeDB
from media.monitor.log import get_logger
from media.monitor.pure import partition
import api_clients.api_client as ac
import prepare_tests

class TestAirtimeDB(unittest.TestCase):
    def setUp(self):
        self.ac = ac.AirtimeApiClient(logger=get_logger(),
                                      config_path=prepare_tests.real_config)

    def test_syncdb_init(self):
        sdb = AirtimeDB(self.ac)
        self.assertTrue( len(sdb.list_storable_paths()) > 0 )

    def test_list(self):
        self.sdb = AirtimeDB(self.ac)
        for watch_dir in self.sdb.list_storable_paths():
            self.assertTrue( os.path.exists(watch_dir) )

    def test_directory_get_files(self):
        sdb = AirtimeDB(self.ac)
        print(sdb.list_storable_paths())
        for wdir in sdb.list_storable_paths():
            files = sdb.directory_get_files(wdir)
            print( "total files: %d" % len(files) )
            self.assertTrue( len(files) >= 0 )
            self.assertTrue( isinstance(files, set) )
            exist, deleted = partition(os.path.exists, files)
            print("(exist, deleted) = (%d, %d)" % ( len(exist), len(deleted) ) )

if __name__ == '__main__': unittest.main()
