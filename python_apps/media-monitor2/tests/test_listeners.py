import os, shutil
import time
import pyinotify
import unittest
from pydispatch import dispatcher

from media.monitor.listeners import OrganizeListener
from media.monitor.events import OrganizeFile

from os.path import join, normpath, abspath

def create_file(p):
    with open(p, 'w') as f: f.write(" ")

class TestOrganizeListener(unittest.TestCase):
    def setUp(self):
        self.organize_path = 'test_o'
        self.sig = 'org'
        def my_abs_path(x):
            return normpath(join(os.getcwd(), x))
        self.sample_files = [ my_abs_path(join(self.organize_path, f))
                for f in [ "gogi.mp3",
                           "gio.mp3",
                           "mimino.ogg" ] ]
        os.mkdir(self.organize_path)

    def test_flush_events(self):
        org = self.create_org()
        self.create_sample_files()
        received = [0]
        def pass_event(sender, event):
            if isinstance(event, OrganizeFile):
                received[0] += 1
                self.assertTrue( abspath(event.path) in self.sample_files )
        dispatcher.connect(pass_event, signal=self.sig, sender=dispatcher.Any,
                weak=True)
        org.flush_events( self.organize_path )
        self.assertEqual( received[0], len(self.sample_files) )
        self.delete_sample_files()

    def test_process(self):
        org = self.create_org()
        received = [0]
        def pass_event(sender, event):
            if isinstance(event, OrganizeFile):
                self.assertTrue( event.path in self.sample_files )
                received[0] += 1
        dispatcher.connect(pass_event, signal=self.sig, sender=dispatcher.Any,
                weak=True)
        wm = pyinotify.WatchManager()
        def stopper(notifier):
            return received[0] == len(self.sample_files)
        tn = pyinotify.ThreadedNotifier(wm, default_proc_fun=org)
        tn.daemon = True
        tn.start()
        wm.add_watch(self.organize_path, pyinotify.ALL_EVENTS, rec=True,
                auto_add=True)
        time.sleep(0.5)
        self.create_sample_files()
        time.sleep(1)
        self.assertEqual( len(self.sample_files), received[0] )
        self.delete_sample_files()

    def tearDown(self):
        shutil.rmtree(self.organize_path)

    def create_sample_files(self):
        for f in self.sample_files: create_file(f)

    def delete_sample_files(self):
        for f in self.sample_files: os.remove(f)

    def create_org(self):
        return OrganizeListener( signal=self.sig )

if __name__ == '__main__': unittest.main()

