#!/usr/bin/env python
# -*- coding: utf-8 -*-

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
import string
from subprocess import Popen, PIPE, STDOUT

def create_path(path):
  if not (os.path.exists(path)):
    print "Creating directory " + path
    os.makedirs(path)
    
try:
  # Does pypo user exist?
  print "Checking for pypo user..."
  p = Popen('id pypo', shell=True, stdin=PIPE, stdout=PIPE, stderr=STDOUT, close_fds=True)
  output = p.stdout.read()
  if (output[0:3] != "uid"):
    # Make the pypo user
    print "Creating pypo user..."
    os.system("adduser --system --quiet --group --disabled-login --no-create-home pypo")

  print "Creating directories..."
  create_path("/var/log/pypo")
  os.system("chmod -R 755 /var/log/pypo")
  os.system("chown -R pypo:pypo /var/log/pypo")
  #os.mkdirs("/var/log/liquidsoap")
  #os.system("chown -R liquidsoap:liquidsoap /var/log/liquidsoap")
  create_path("/opt/pypo")
  create_path("/opt/pypo/cache")
  create_path("/opt/pypo/files")
  create_path("/opt/pypo/files/basic")
  create_path("/opt/pypo/files/fallback")
  create_path("/opt/pypo/files/jingles")
  create_path("/opt/pypo/archive")
  os.system("chmod -R 755 /opt/pypo/")
  os.system("chown -R pypo:pypo /opt/pypo")
except Exception, e:
  print "exception:" + str(e)
  
print "Done."

