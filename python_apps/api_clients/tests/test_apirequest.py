import unittest
from .. api_client import ApcUrl, ApiRequest

class TestApiRequest(unittest.TestCase):
    def test_init(self):
        u = ApiRequest("request_name", ApcUrl("/test/ing"))
        self.assertEquals(u.name, "request_name")
