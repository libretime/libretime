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
from subprocess import Popen, PIPE, STDOUT

if os.geteuid() != 0:
    print "Please run this as root."
    sys.exit(1)

PATH_INI_FILE = '/etc/airtime/MediaMonitor.cfg'

def create_path(path):
  if not (os.path.exists(path)):
    print "Creating directory " + path
    os.makedirs(path)

def create_user(username):
  print "Checking for user "+username
  p = Popen('id '+username, shell=True, stdin=PIPE, stdout=PIPE, stderr=STDOUT, close_fds=True)
  output = p.stdout.read()
  if (output[0:3] != "uid"):
    # Make the pypo user
    print "Creating user "+username
    os.system("adduser --system --quiet --group --shell /bin/bash "+username)
    
    #set pypo password
    p = os.popen('/usr/bin/passwd pypo 1>/dev/null 2>&1', 'w')
    p.write('pypo\n')
    p.write('pypo\n')
    p.close()
  else:
    print "User already exists."
  #add pypo to audio group
  os.system("adduser " + username + " audio 1>/dev/null 2>&1")

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
  #print current_script_dir[0:index]
  return current_script_dir[0:index]


try:
  # load config file
  try:
    config = ConfigObj(PATH_INI_FILE)
  except Exception, e:
    print 'Error loading config file: ', e
    sys.exit()

  current_script_dir = get_current_script_dir()
  print "Checking and removing any existing media monitor processes"
  os.system("python %s/media-monitor-uninstall.py 1>/dev/null 2>&1"% current_script_dir)
  time.sleep(5)

  # Create users
  create_user("pypo")

  print "Creating log directories"
  create_path(config["log_dir"])
  os.system("chmod -R 755 " + config["log_dir"])
  os.system("chown -R pypo:pypo "+config["log_dir"])

  copy_dir("%s/.."%current_script_dir, config["bin_dir"])
  
  print "Setting permissions"
  os.system("chmod -R 755 "+config["bin_dir"])
  os.system("chown -R pypo:pypo "+config["bin_dir"])

  print "Creating symbolic links"
  os.system("rm -f /usr/bin/airtime-media-monitor-start")
  os.system("ln -s "+config["bin_dir"]+"/airtime-media-monitor-start /usr/bin/")
  os.system("rm -f /usr/bin/airtime-media-monitor-stop")
  os.system("ln -s "+config["bin_dir"]+"/airtime-media-monitor-stop /usr/bin/")
  
  print "Installing recorder daemon"
  create_path("/etc/service/media-monitor")
  create_path("/etc/service/media-monitor/log")
  shutil.copy("%s/media-monitor-daemontools.sh"%current_script_dir, "/etc/service/media-monitor/run")
  shutil.copy("%s/media-monitor-daemontools-logger.sh"%current_script_dir, "/etc/service/media-monitor/log/run")
  os.system("chmod -R 755 /etc/service/media-monitor")
  os.system("chown -R pypo:pypo /etc/service/media-monitor")
  
  print "Waiting for processes to start..."
  time.sleep(5)
  os.system("python /usr/bin/airtime-media-monitor-start")
  time.sleep(2)

  found = True

  p = Popen('svstat /etc/service/media-monitor', shell=True, stdin=PIPE, stdout=PIPE, stderr=STDOUT, close_fds=True)
  output = p.stdout.read()
  if (output.find("unable to open supervise/ok: file does not exist") >= 0):
    found = False
  print output

  if not found:
    print "Media monitor install has completed, but daemontools is not running, please make sure you have it installed and then reboot."
except Exception, e:
  print "exception:" + str(e)
  sys.exit(1)
  


