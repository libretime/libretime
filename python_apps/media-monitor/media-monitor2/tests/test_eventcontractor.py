import unittest
from media.monitor.eventcontractor import EventContractor
#from media.monitor.exceptions import BadSongFile
from media.monitor.events import FakePyinotify, NewFile, MoveFile, \
DeleteFile

class TestMMP(unittest.TestCase):
    def test_event_registered(self):
        ev = EventContractor()
        e1 = NewFile( FakePyinotify('bull.mp3') ).proxify()
        e2 = MoveFile( FakePyinotify('bull.mp3') ).proxify()
        ev.register(e1)
        self.assertTrue( ev.event_registered(e2) )

    def test_get_old_event(self):
        ev = EventContractor()
        e1 = NewFile( FakePyinotify('bull.mp3') ).proxify()
        e2 = MoveFile( FakePyinotify('bull.mp3') ).proxify()
        ev.register(e1)
        self.assertEqual( ev.get_old_event(e2), e1 )

    def test_register(self):
        ev = EventContractor()
        e1 = NewFile( FakePyinotify('bull.mp3') ).proxify()
        e2 = DeleteFile( FakePyinotify('bull.mp3') ).proxify()
        self.assertTrue( ev.register(e1) )

        self.assertFalse( ev.register(e2) )

        self.assertEqual( len(ev.store.keys()), 1 )

        delete_ev = e1.safe_pack()[0]
        self.assertEqual( delete_ev['mode'], u'delete')
        self.assertEqual( len(ev.store.keys()), 0 )

        e3 = DeleteFile( FakePyinotify('horse.mp3') ).proxify()
        self.assertTrue( ev.register(e3) )
        self.assertTrue( ev.register(e2) )


    def test_register2(self):
        ev = EventContractor()
        p = 'bull.mp3'
        events = [
                NewFile( FakePyinotify(p) ),
                NewFile( FakePyinotify(p) ),
                DeleteFile( FakePyinotify(p) ),
                NewFile( FakePyinotify(p) ),
                NewFile( FakePyinotify(p) ), ]
        events = map(lambda x: x.proxify(), events)
        actual_events = []
        for e in events:
            if ev.register(e):
                actual_events.append(e)
        self.assertEqual( len(ev.store.keys()), 1 )
        #packed = [ x.safe_pack() for x in actual_events ]

if __name__ == '__main__': unittest.main()
