import unittest
import json
from mock import patch, MagicMock
from configobj import ConfigObj
from .. api_client import RequestProvider

class TestRequestProvider(unittest.TestCase):
    def setUp(self):
        self.cfg = ConfigObj('api_client.cfg')
    def test_test(self):
        self.assertTrue('api_key' in self.cfg)
    def test_init(self):
        rp = RequestProvider(self.cfg)
        self.assertTrue( len( rp.available_requests() ) > 0 )
    def test_contains(self):
        rp = RequestProvider(self.cfg)
        methods = ['upload_recorded', 'update_media_url', 'list_all_db_files']
        for meth in methods:
            self.assertTrue( meth in rp )

    def test_notify_webstream_data(self):
        ret = json.dumps( {'testing' : '123' } )
        rp = RequestProvider(self.cfg)
        read = MagicMock()
        read.read = MagicMock(return_value=ret)
        with patch('urllib2.urlopen') as mock_method:
            mock_method.return_value = read
            response = rp.notify_webstream_data(media_id=123)
            mock_method.called_once_with(media_id=123)
            self.assertEqual(json.loads(ret), response)

if __name__ == '__main__': unittest.main()
