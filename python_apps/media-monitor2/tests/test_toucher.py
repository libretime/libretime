# -*- coding: utf-8 -*-
import unittest
import time
import media.monitor.pure as mmp
from media.monitor.toucher import Toucher, ToucherThread

class BaseTest(unittest.TestCase):
    def setUp(self):
        self.p = "api_client.cfg"

class TestToucher(BaseTest):
    def test_toucher(self):
        t1 = mmp.last_modified(self.p)
        t = Toucher(self.p)
        t()
        t2 = mmp.last_modified(self.p)
        print("(t1,t2) = (%d, %d) diff => %d" % (t1, t2, t2 - t1))
        self.assertTrue( t2 > t1 )

class TestToucherThread(BaseTest):
    def test_thread(self):
        t1 = mmp.last_modified(self.p)
        ToucherThread(self.p, interval=1)
        time.sleep(2)
        t2 = mmp.last_modified(self.p)
        print("(t1,t2) = (%d, %d) diff => %d" % (t1, t2, t2 - t1))
        self.assertTrue( t2 > t1 )

if __name__ == '__main__': unittest.main()

























