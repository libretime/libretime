# -*- coding: utf-8 -*-
import unittest
import json

from media.monitor.airtime import AirtimeNotifier, AirtimeMessageReceiver
from mock import patch, Mock
from media.monitor.config import MMConfig

from media.monitor.manager import Manager

def filter_ev(d): return { i : j for i,j in d.iteritems() if i != 'event_type' }

class TestReceiver(unittest.TestCase):
    def setUp(self):
        # TODO : properly mock this later
        cfg = {}
        self.amr = AirtimeMessageReceiver(cfg, Manager())

    def test_supported(self):
        # Every supported message should fire something
        for event_type in self.amr.dispatch_table.keys():
            msg = { 'event_type' : event_type, 'extra_param' : 123 }
            filtered = filter_ev(msg)
            # There should be a better way to test the following without
            # patching private methods
            with patch.object(self.amr, '_execute_message') as mock_method:
                mock_method.side_effect = None
                ret = self.amr.message(msg)
                self.assertTrue(ret)
                mock_method.assert_called_with(event_type, filtered)

    def test_no_mod_message(self):
        ev = { 'event_type' : 'new_watch', 'directory' : 'something here' }
        filtered = filter_ev(ev)
        with patch.object(self.amr, '_execute_message') as mock_method:
            mock_method.return_value = "tested"
            ret = self.amr.message(ev)
            self.assertTrue( ret ) # message passing worked
            mock_method.assert_called_with(ev['event_type'], filtered)
            # test that our copy of the message does not get modified
            self.assertTrue( 'event_type' in ev )

class TestAirtimeNotifier(unittest.TestCase):
    def test_handle_message(self):
        #from configobj import ConfigObj
        test_cfg = MMConfig('./test_config.cfg')
        ran = [False]
        class MockReceiver(object):
            def message(me,m):
                self.assertTrue( 'event_type' in m )
                self.assertEqual( m['path'], '/bs/path' )
                ran[0] = True
        airtime = AirtimeNotifier(cfg=test_cfg, message_receiver=MockReceiver())
        m1 = Mock()
        m1.ack = "ack'd message"
        m2 = Mock()
        m2.body = json.dumps({ 'event_type' : 'file_delete', 'path' : '/bs/path' })
        airtime.handle_message(body=m1,message=m2)
        self.assertTrue( ran[0] )




if __name__ == '__main__': unittest.main()
