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

def is_natty():
    try:
        f = open('/etc/lsb-release')
    except IOError as e:
        #File doesn't exist, so we're not even dealing with Ubuntu
        return False

    for line in f:
        split = line.split("=")
        split[0] = split[0].strip(" \r\n")
        split[1] = split[1].strip(" \r\n")
        if split[0] == "DISTRIB_CODENAME" and split[1] == "natty":
            return True
    return False


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

  architecture = platform.architecture()[0]
  natty = is_natty()
    
  if architecture == '64bit' and natty:
      print "Installing 64-bit liquidsoap binary (Natty)"
      shutil.copy("%s/../liquidsoap/liquidsoap-amd64-natty"%current_script_dir, "%s/../liquidsoap/liquidsoap"%current_script_dir)
  elif architecture == '32bit' and natty:
      print "Installing 32-bit liquidsoap binary (Natty)"
      shutil.copy("%s/../liquidsoap/liquidsoap-i386-natty"%current_script_dir, "%s/../liquidsoap/liquidsoap"%current_script_dir)
  elif architecture == '64bit' and not natty:
      print "Installing 64-bit liquidsoap binary"
      shutil.copy("%s/../liquidsoap/liquidsoap-amd64"%current_script_dir, "%s/../liquidsoap/liquidsoap"%current_script_dir)
  elif architecture == '32bit' and not natty:
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
  os.system("rm -f /usr/bin/airtime-playout")
  os.system("ln -s "+config["bin_dir"]+"/bin/airtime-playout /usr/bin/")
  os.system("rm -f /usr/bin/airtime-liquidsoap")
  os.system("ln -s "+config["bin_dir"]+"/bin/airtime-liquidsoap /usr/bin/")

  
  print "Installing pypo daemon"
  shutil.copy(config["bin_dir"]+"/bin/airtime-playout-init-d", "/etc/init.d/airtime-playout")

  print "Waiting for processes to start..."
  p = Popen("/etc/init.d/airtime-playout start", shell=True)
  sts = os.waitpid(p.pid, 0)[1]
  
except Exception, e:
  print "exception:" + str(e)
  sys.exit(1)
  


