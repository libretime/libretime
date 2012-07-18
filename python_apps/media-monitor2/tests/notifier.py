# -*- coding: utf-8 -*-
import unittest

from media.monitor.airtime import AirtimeNotifier, AirtimeMessageReceiver
from mock import patch

class TestReceiver(unittest.TestCase):
    def setUp(self):
        # TODO : properly mock this later
        cfg = {}
        self.amr = AirtimeMessageReceiver(cfg)

    def test_message(self):
        for event_type in self.amr.supported_messages():
            msg = { 'event_type' : event_type, 'extra_param' : 123 }
            filtered = { i : j for i,j in msg.iteritems() if i != 'event_type' }
            with patch.object(self.amr, 'execute_message') as mock_method:
                mock_method.side_effect = None
                ret = self.amr.message(msg)
                self.assertTrue(ret)
                mock_method.assert_called_with(event_type, filtered)

if __name__ == '__main__': unittest.main()
