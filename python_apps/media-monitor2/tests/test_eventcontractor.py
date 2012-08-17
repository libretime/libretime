import unittest
from media.monitor.eventcontractor import EventContractor
#from media.monitor.exceptions import BadSongFile
from media.monitor.events import FakePyinotify, NewFile, MoveFile, \
DeleteFile
from mock import patch

class TestMMP(unittest.TestCase):
    def test_event_registered(self):
        ev = EventContractor()
        e1 = NewFile( FakePyinotify('bullshit.mp3') )
        e2 = MoveFile( FakePyinotify('bullshit.mp3') )
        ev.register(e1)
        self.assertTrue( ev.event_registered(e2) )

    def test_get_old_event(self):
        ev = EventContractor()
        e1 = NewFile( FakePyinotify('bullshit.mp3') )
        e2 = MoveFile( FakePyinotify('bullshit.mp3') )
        ev.register(e1)
        self.assertEqual( ev.get_old_event(e2), e1 )

    def test_register(self):
        ev = EventContractor()
        e1 = NewFile( FakePyinotify('bullshit.mp3') )
        e2 = DeleteFile( FakePyinotify('bullshit.mp3') )
        self.assertTrue( ev.register(e1) )

        # Check that morph_into is called when it should be
        with patch.object(NewFile, 'morph_into', return_value='kimchi') \
        as mock_method:
            ret = ev.register(e2)
            self.assertFalse(ret)
            mock_method.assert_called_once_with(e2)

        # This time we are not patching morph
        self.assertFalse( ev.register(e2) )
        # We did not an element
        self.assertTrue( len(ev.store.keys()) == 1 )
        morphed = ev.get_old_event(e2)
        self.assertTrue( isinstance(morphed, DeleteFile) )

        delete_ev = e1.safe_pack()[0]
        print( ev.store )
        self.assertEqual( delete_ev['mode'], u'delete')
        self.assertTrue( len(ev.store.keys()) == 0 )

        e3 = DeleteFile( FakePyinotify('horseshit.mp3') )
        self.assertTrue( ev.register(e3) )
        self.assertTrue( ev.register(e2) )


    def test_register2(self):
        ev = EventContractor()
        p = 'bullshit.mp3'
        events = [
                NewFile( FakePyinotify(p) ),
                NewFile( FakePyinotify(p) ),
                DeleteFile( FakePyinotify(p) ),
                NewFile( FakePyinotify(p) ),
                NewFile( FakePyinotify(p) ), ]
        actual_events = []
        for e in events:
            if ev.register(e):
                actual_events.append(e)
        self.assertEqual( len(ev.store.keys()), 1 )
        packed = [ x.safe_pack() for x in actual_events ]
        print(packed)

if __name__ == '__main__': unittest.main()
