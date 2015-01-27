# -*- coding: utf-8 -*-
import unittest
import os
import sys
from api_clients import api_client as apc


import prepare_tests

class TestApiClient(unittest.TestCase):
    def setUp(self):
        test_path = prepare_tests.api_client_path
        print("Running from api_config: %s" % test_path)
        if not os.path.exists(test_path):
            print("path for config does not exist: '%s' % test_path")
            # TODO : is there a cleaner way to exit the unit testing?
            sys.exit(1)
        self.apc = apc.AirtimeApiClient(config_path=test_path)
        self.apc.register_component("api-client-tester")
        # All of the following requests should error out in some way
        self.bad_requests = [
                { 'mode' : 'foo', 'is_record' : 0 },
                { 'mode' : 'bar', 'is_record' : 1 },
                { 'no_mode' : 'at_all' }, ]

    def test_bad_requests(self):
        responses = self.apc.send_media_monitor_requests(self.bad_requests, dry=True)
        for response in responses:
            self.assertTrue( 'key' in response )
            self.assertTrue( 'error' in response )
            print( "Response: '%s'" % response )

    # We don't actually test any well formed requests because it is more
    # involved

if __name__ == '__main__': unittest.main()


