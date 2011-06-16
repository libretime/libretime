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
import platform
from configobj import ConfigObj
from subprocess import Popen

if os.geteuid() != 0:
    print "Please run this as root."
    sys.exit(1)

PATH_INI_FILE = '/etc/airtime/media-monitor.cfg'

def create_path(path):
  if not (os.path.exists(path)):
    print "Creating directory " + path
    os.makedirs(path)

def copy_dir(src_dir, dest_dir):
  if (os.path.exists(dest_dir)) and (dest_dir != "/"):
    print "Removing old directory "+dest_dir
    shutil.rmtree(dest_dir)
  if not (os.path.exists(dest_dir)):
    print "Copying directory "+os.path.realpath(src_dir)+" to "+os.path.realpath(dest_dir)
    shutil.copytree(src_dir, dest_dir)
                    
def get_current_script_dir():
  current_script_dir = os.path.realpath(__file__)
  index = current_script_dir.rindex('/')
  return current_script_dir[0:index]


try:
  # load config file
  try:
    config = ConfigObj(PATH_INI_FILE)
  except Exception, e:
    print 'Error loading config file: ', e
    sys.exit(1)

  current_script_dir = get_current_script_dir()

  p = Popen("/etc/init.d/airtime-media-monitor stop >/dev/null 2>&1", shell=True)
  sts = os.waitpid(p.pid, 0)[1]

  print "Creating log directories"
  create_path(config["log_dir"])
  os.system("chmod -R 755 " + config["log_dir"])
  os.system("chown -R pypo:pypo "+config["log_dir"])

  copy_dir("%s/.."%current_script_dir, config["bin_dir"])
  
  print "Setting permissions"
  os.system("chmod -R 755 "+config["bin_dir"])
  os.system("chown -R pypo:pypo "+config["bin_dir"])

  print "Installing media-monitor daemon"
  shutil.copy(config["bin_dir"]+"/airtime-media-monitor-init-d", "/etc/init.d/airtime-media-monitor")

  p = Popen("update-rc.d airtime-media-monitor defaults", shell=True)
  sts = os.waitpid(p.pid, 0)[1]
  
  print "Waiting for processes to start..."
  p = Popen("/etc/init.d/airtime-media-monitor start", shell=True)
  sts = os.waitpid(p.pid, 0)[1]

except Exception, e:
  print "exception:" + str(e)
  sys.exit(1)

