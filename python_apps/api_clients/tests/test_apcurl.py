import unittest
from api_clients.api_client import ApcUrl, UrlBadParam, IncompleteUrl

class TestApcUrl(unittest.TestCase):
    def test_init(self):
        url = "/testing"
        u = ApcUrl(url)
        self.assertEqual( u.base_url, url)

    def test_params_1(self):
        u = ApcUrl("/testing/%%key%%")
        self.assertEqual(u.params(key='val').url(), '/testing/val')

    def test_params_2(self):
        u = ApcUrl('/testing/%%key%%/%%api%%/more_testing')
        full_url = u.params(key="AAA",api="BBB").url()
        self.assertEqual(full_url, '/testing/AAA/BBB/more_testing')

    def test_params_ex(self):
        u = ApcUrl("/testing/%%key%%")
        with self.assertRaises(UrlBadParam):
            u.params(bad_key='testing')

    def test_url(self):
        u = "one/two/three"
        self.assertEqual( ApcUrl(u).url(), u )

    def test_url_ex(self):
        u = ApcUrl('/%%one%%/%%two%%/three').params(two='testing')
        with self.assertRaises(IncompleteUrl): u.url()
