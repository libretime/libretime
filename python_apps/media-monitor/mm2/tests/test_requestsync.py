import unittest
from mock import MagicMock

from media.monitor.request import RequestSync

class TestRequestSync(unittest.TestCase):

    def apc_mock(self):
        fake_apc = MagicMock()
        fake_apc.send_media_monitor_requests = MagicMock()
        return fake_apc

    def watcher_mock(self):
        fake_watcher = MagicMock()
        fake_watcher.flag_done = MagicMock()
        return fake_watcher

    def request_mock(self):
        fake_request = MagicMock()
        fake_request.safe_pack = MagicMock(return_value=[])
        return fake_request

    def test_send_media_monitor(self):
        fake_apc      = self.apc_mock()
        fake_requests = [ self.request_mock() for x in range(1,5) ]
        fake_watcher  = self.watcher_mock()
        rs = RequestSync(fake_watcher, fake_requests, fake_apc)
        rs.run_request()
        self.assertEquals(fake_apc.send_media_monitor_requests.call_count, 1)

    def test_flag_done(self):
        fake_apc      = self.apc_mock()
        fake_requests = [ self.request_mock() for x in range(1,5) ]
        fake_watcher  = self.watcher_mock()
        rs = RequestSync(fake_watcher, fake_requests, fake_apc)
        rs.run_request()
        self.assertEquals(fake_watcher.flag_done.call_count, 1)

    def test_safe_pack(self):
        fake_apc      = self.apc_mock()
        fake_requests = [ self.request_mock() for x in range(1,5) ]
        fake_watcher  = self.watcher_mock()
        rs = RequestSync(fake_watcher, fake_requests, fake_apc)
        rs.run_request()
        for req in fake_requests:
            self.assertEquals(req.safe_pack.call_count, 1)

if __name__ == '__main__': unittest.main()
