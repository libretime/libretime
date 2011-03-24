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
from subprocess import Popen, PIPE, STDOUT

if os.geteuid() != 0:
    print "Please run this as root."
    sys.exit(1)

BASE_PATH = '/opt/pypo/'

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
  #add pypo to pulse-access group
  #os.system("adduser " + username + " pulse-access 1>/dev/null 2>&1")

def copy_dir(src_dir, dest_dir):
  if (os.path.exists(dest_dir)) and (dest_dir != "/"):
    print "Removing old directory "+dest_dir
    shutil.rmtree(dest_dir)
  if not (os.path.exists(dest_dir)):
    print "Copying directory "+src_dir+" to "+dest_dir
    shutil.copytree(src_dir, dest_dir)
                    
def get_current_script_dir():
  current_script_dir = os.path.realpath(__file__)
  index = current_script_dir.rindex('/')
  print current_script_dir[0:index]
  return current_script_dir[0:index]


try:
  current_script_dir = get_current_script_dir()
  print "Checking and removing any existing pypo processes"
  os.system("python %s/pypo-uninstall.py 1>/dev/null 2>&1"% current_script_dir)
  time.sleep(5)

  # Create users
  create_user("pypo")

  print "Creating log directories"
  create_path("/var/log/pypo")
  os.system("chmod -R 755 /var/log/pypo")
  os.system("chown -R pypo:pypo /var/log/pypo")

  create_path(BASE_PATH)
  create_path(BASE_PATH+"bin")
  create_path(BASE_PATH+"cache")
  create_path(BASE_PATH+"files")
  create_path(BASE_PATH+"tmp")
  create_path(BASE_PATH+"archive")
    
  if platform.architecture()[0] == '64bit':
      print "Installing 64-bit liquidsoap binary"
      shutil.copy("%s/../liquidsoap/liquidsoap64"%current_script_dir, "%s/../liquidsoap/liquidsoap"%current_script_dir)
  elif platform.architecture()[0] == '32bit':
      print "Installing 32-bit liquidsoap binary"
      shutil.copy("%s/../liquidsoap/liquidsoap32"%current_script_dir, "%s/../liquidsoap/liquidsoap"%current_script_dir)
  else:
      print "Unknown system architecture."
      sys.exit(1)
  
  copy_dir("%s/.."%current_script_dir, BASE_PATH+"bin/")
  copy_dir("%s/../../api_clients"%current_script_dir, BASE_PATH+"api_clients/")
  
  print "Setting permissions"
  os.system("chmod -R 755 "+BASE_PATH)
  os.system("chown -R pypo:pypo "+BASE_PATH)
  
  print "Installing pypo daemon"
  create_path("/etc/service/pypo")
  create_path("/etc/service/pypo/log")
  shutil.copy("%s/pypo-daemontools.sh"%current_script_dir, "/etc/service/pypo/run")
  shutil.copy("%s/pypo-daemontools-logger.sh"%current_script_dir, "/etc/service/pypo/log/run")
  os.system("chmod -R 755 /etc/service/pypo")
  os.system("chown -R pypo:pypo /etc/service/pypo")
  
  print "Installing liquidsoap daemon"
  create_path("/etc/service/pypo-liquidsoap")  
  create_path("/etc/service/pypo-liquidsoap/log")  
  shutil.copy("%s/pypo-daemontools-liquidsoap.sh"%current_script_dir, "/etc/service/pypo-liquidsoap/run")
  shutil.copy("%s/pypo-daemontools-logger.sh"%current_script_dir, "/etc/service/pypo-liquidsoap/log/run")
  os.system("chmod -R 755 /etc/service/pypo-liquidsoap")
  os.system("chown -R pypo:pypo /etc/service/pypo-liquidsoap")

  print "Waiting for processes to start..."
  time.sleep(5)
  os.system("python %s/pypo-start.py" % (get_current_script_dir()))
  time.sleep(2)

  found = True

  p = Popen('svstat /etc/service/pypo', shell=True, stdin=PIPE, stdout=PIPE, stderr=STDOUT, close_fds=True)
  output = p.stdout.read()
  if (output.find("unable to open supervise/ok: file does not exist") >= 0):
    found = False
  print output
    
  p = Popen('svstat /etc/service/pypo-liquidsoap', shell=True, stdin=PIPE, stdout=PIPE, stderr=STDOUT, close_fds=True)
  output = p.stdout.read()
  print output

  if not found:
    print "Pypo install has completed, but daemontools is not running, please make sure you have it installed and then reboot."
except Exception, e:
  print "exception:" + str(e)
  sys.exit(1)
  


