import json
import unittest

from api_clients.utils import ApcUrl, ApiRequest
from mock import MagicMock, patch


class ResponseInfo:
    @property
    def headers(self):
        return {"content-type": "application/json"}

    def json(self):
        return {"ok", "ok"}


class TestApiRequest(unittest.TestCase):
    def test_init(self):
        u = ApiRequest("request_name", ApcUrl("/test/ing"))
        self.assertEqual(u.name, "request_name")

    def test_call_json(self):
        ret = {"ok": "ok"}
        read = MagicMock()
        read.headers = {"content-type": "application/json"}
        read.json = MagicMock(return_value=ret)
        u = "http://localhost/testing"
        with patch("requests.get") as mock_method:
            mock_method.return_value = read
            request = ApiRequest("mm", ApcUrl(u))()
            self.assertEqual(request, ret)

    def test_call_html(self):
        ret = "<html><head></head><body></body></html>"
        read = MagicMock()
        read.headers = {"content-type": "application/html"}
        read.text = MagicMock(return_value=ret)
        u = "http://localhost/testing"
        with patch("requests.get") as mock_method:
            mock_method.return_value = read
            request = ApiRequest("mm", ApcUrl(u))()
            self.assertEqual(request.text(), ret)


if __name__ == "__main__":
    unittest.main()
