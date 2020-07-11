import unittest
import json
from mock import MagicMock, patch
from api_clients.api_client import ApcUrl, ApiRequest

class ResponseInfo:
    def get_content_type(self):
        return 'application/json'

class TestApiRequest(unittest.TestCase):
    def test_init(self):
        u = ApiRequest('request_name', ApcUrl('/test/ing'))
        self.assertEqual(u.name, "request_name")

    def test_call(self):
        ret = json.dumps( {'ok':'ok'} )
        read = MagicMock()
        read.read = MagicMock(return_value=ret)
        read.info = MagicMock(return_value=ResponseInfo())
        u = 'http://localhost/testing'
        with patch('urllib.request.urlopen') as mock_method:
            mock_method.return_value = read
            request = ApiRequest('mm', ApcUrl(u))()
            self.assertEqual(request, json.loads(ret))

if __name__ == '__main__': unittest.main()
