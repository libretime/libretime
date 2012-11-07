# -*- coding: utf-8 -*-
import unittest
import time
from media.saas.thread import InstanceThread, InstanceInheritingThread

# ugly but necessary for 2.7
signal = False
signal2 = False

class TestInstanceThread(unittest.TestCase):
    def test_user_inject(self):
        global signal
        signal = False
        u = "rudi"
        class T(InstanceThread):
            def run(me):
                global signal
                super(T, me).run()
                signal = True
                self.assertEquals(u, me.user())
        t = T(u, name="test_user_inject")
        t.daemon = True 
        t.start()
        time.sleep(0.2)
        self.assertTrue(signal)

    def test_inheriting_thread(utest):
        global signal2
        u = "testing..."

        class TT(InstanceInheritingThread):
            def run(self):
                global signal2
                utest.assertEquals(self.user(), u)
                signal2 = True

        class T(InstanceThread):
            def run(self):
                super(T, self).run()
                child_thread = TT(name="child thread")
                child_thread.daemon = True
                child_thread.start()

        parent_thread = T(u, name="Parent instance thread")
        parent_thread.daemon = True
        parent_thread.start()

        time.sleep(0.2)
        utest.assertTrue(signal2)

    def test_different_user(utest):
        u1, u2 = "ru", "di"
        class T(InstanceThread):
            def run(self):
                super(T, self).run()

        for u in [u1, u2]:
            t = T(u)
            t.daemon = True
            t.start()
            utest.assertEquals(t.user(), u)


if __name__ == '__main__': unittest.main()
