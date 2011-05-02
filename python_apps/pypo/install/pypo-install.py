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

PATH_INI_FILE = '/etc/airtime/pypo.cfg'

def create_path(path):
  if not (os.path.exists(path)):
    print "Creating directory " + path
    os.makedirs(path)
  else:
    print "Directory already exists " + path

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
    #print "Removing old directory "+dest_dir
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
  print "Checking and removing any existing pypo processes"
  os.system("python %s/pypo-uninstall.py 1>/dev/null 2>&1"% current_script_dir)
  time.sleep(5)

  # Create users
  create_user("pypo")

  create_path(config["pypo_log_dir"])
  os.system("chmod -R 755 " + config["pypo_log_dir"])
  os.system("chown -R pypo:pypo "+config["pypo_log_dir"])

  create_path(config["liquidsoap_log_dir"])
  os.system("chmod -R 755 " + config["liquidsoap_log_dir"])
  os.system("chown -R pypo:pypo "+config["liquidsoap_log_dir"])

  create_path(config["bin_dir"]+"/bin")
  create_path(config["cache_dir"])
  create_path(config["file_dir"])
  create_path(config["tmp_dir"])
    
  if platform.architecture()[0] == '64bit':
      print "Installing 64-bit liquidsoap binary"
      shutil.copy("%s/../liquidsoap/liquidsoap-amd64"%current_script_dir, "%s/../liquidsoap/liquidsoap"%current_script_dir)
  elif platform.architecture()[0] == '32bit':
      print "Installing 32-bit liquidsoap binary"
      shutil.copy("%s/../liquidsoap/liquidsoap-i386"%current_script_dir, "%s/../liquidsoap/liquidsoap"%current_script_dir)
  else:
      print "Unknown system architecture."
      sys.exit(1)
  
  copy_dir("%s/.."%current_script_dir, config["bin_dir"]+"/bin/")
  copy_dir("%s/../../api_clients"%current_script_dir, config["bin_dir"]+"/api_clients/")
  
  print "Setting permissions"
  os.system("chmod -R 755 "+config["bin_dir"])
  os.system("chown -R pypo:pypo "+config["bin_dir"])
  os.system("chown -R pypo:pypo "+config["cache_base_dir"])
  
  print "Creating symbolic links"
  os.system("rm -f /usr/bin/airtime-pypo-start")
  os.system("ln -s "+config["bin_dir"]+"/bin/airtime-pypo-start /usr/bin/")
  os.system("rm -f /usr/bin/airtime-pypo-stop")
  os.system("ln -s "+config["bin_dir"]+"/bin/airtime-pypo-stop /usr/bin/")
  
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
  shutil.copy("%s/pypo-liquidsoap-daemontools-logger.sh"%current_script_dir, "/etc/service/pypo-liquidsoap/log/run")
  os.system("chmod -R 755 /etc/service/pypo-liquidsoap")
  os.system("chown -R pypo:pypo /etc/service/pypo-liquidsoap")

  print "Waiting for processes to start..."
  time.sleep(5)
  os.system("python /usr/bin/airtime-pypo-start")
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
  


