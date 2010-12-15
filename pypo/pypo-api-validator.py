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
    config = ConfigObj('config.cfg')
except Exception, e:
    print 'Error loading config file: ', e
    sys.exit()
    
    
class TestApiFunctions(unittest.TestCase):

    def setUp(self):
        self.api_client = api_client.api_client_factory(config)
        
    def test_is_server_compatible(self):
        self.assertTrue(self.api_client.is_server_compatible(False))

 
if __name__ == '__main__':
    unittest.main()
