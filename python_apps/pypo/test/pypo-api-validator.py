import time
import os
import traceback
from optparse import *
import sys
import time
import datetime
import logging
import logging.config
import shutil
import urllib
import urllib2
import pickle
import telnetlib
import random
import string
import operator
import inspect

# additional modules (should be checked)
from configobj import ConfigObj

# custom imports
from util import *
from api_clients import *

import random
import unittest

# configure logging
logging.config.fileConfig("logging-api-validator.cfg")

try:
    config = ConfigObj('/etc/airtime/pypo.cfg')
except Exception, e:
    print 'Error loading config file: ', e
    sys.exit()
    
    
class TestApiFunctions(unittest.TestCase):

    def setUp(self):
        self.api_client = api_client.api_client_factory(config)
        
    def test_is_server_compatible(self):
        self.assertTrue(self.api_client.is_server_compatible(False))

    def test_get_schedule(self):
        status, response = self.api_client.get_schedule()
        self.assertTrue(response.has_key("status"))
        self.assertTrue(response.has_key("playlists"))
        self.assertTrue(response.has_key("check"))
        self.assertTrue(status == 1)

    def test_get_media(self):
        self.assertTrue(True)

    def test_notify_scheduled_item_start_playing(self):
        arr = dict()
        arr["x"] = dict()
        arr["x"]["schedule_id"]=1
        
        response = self.api_client.notify_scheduled_item_start_playing("x", arr)
        self.assertTrue(response.has_key("status"))
        self.assertTrue(response.has_key("message"))

    def test_notify_media_item_start_playing(self):     
        response = self.api_client.notify_media_item_start_playing('{"schedule_id":1}', 5)
        self.assertTrue(response.has_key("status"))
        self.assertTrue(response.has_key("message"))
		
 
if __name__ == '__main__':
    unittest.main()
