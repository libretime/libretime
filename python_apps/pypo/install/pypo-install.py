#!/usr/bin/env python
# -*- coding: utf-8 -*-

import time
import os
from optparse import *
import sys
import shutil
import platform
from configobj import ConfigObj
from subprocess import Popen

if os.geteuid() != 0:
    print "Please run this as root."
    sys.exit(1)

PATH_INI_FILE = '/etc/airtime/pypo.cfg'

def create_path(path):
  if not (os.path.exists(path)):
    print "Creating directory " + path
    os.makedirs(path)

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
    sys.exit(1)

  current_script_dir = get_current_script_dir()

  p = Popen("/etc/init.d/airtime-playout stop >/dev/null 2>&1", shell=True)
  sts = os.waitpid(p.pid, 0)[1]

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
      shutil.copy("%s/../liquidsoap_bin/liquidsoap-natty-amd64"%current_script_dir, "%s/../liquidsoap_bin/liquidsoap"%current_script_dir)
  elif architecture == '32bit' and natty:
      print "Installing 32-bit liquidsoap binary (Natty)"
      shutil.copy("%s/../liquidsoap_bin/liquidsoap-natty-i386"%current_script_dir, "%s/../liquidsoap_bin/liquidsoap"%current_script_dir)
  elif architecture == '64bit' and not natty:
      print "Installing 64-bit liquidsoap binary"
      shutil.copy("%s/../liquidsoap_bin/liquidsoap-amd64"%current_script_dir, "%s/../liquidsoap_bin/liquidsoap"%current_script_dir)
  elif architecture == '32bit' and not natty:
      print "Installing 32-bit liquidsoap binary"
      shutil.copy("%s/../liquidsoap_bin/liquidsoap-i386"%current_script_dir, "%s/../liquidsoap_bin/liquidsoap"%current_script_dir)
  else:
      print "Unknown system architecture."
      sys.exit(1)
  
  copy_dir("%s/.."%current_script_dir, config["bin_dir"]+"/bin/")
  
  print "Setting permissions"
  os.system("chmod -R 755 "+config["bin_dir"])
  os.system("chown -R pypo:pypo "+config["bin_dir"])
  os.system("chown -R pypo:pypo "+config["cache_base_dir"])

  print "Installing pypo daemon"
  shutil.copy(config["bin_dir"]+"/bin/airtime-playout-init-d", "/etc/init.d/airtime-playout")

  p = Popen("update-rc.d airtime-playout defaults >/dev/null 2>&1", shell=True)
  sts = os.waitpid(p.pid, 0)[1]

  print "Waiting for processes to start..."
  p = Popen("/etc/init.d/airtime-playout start", shell=True)
  sts = os.waitpid(p.pid, 0)[1]
 
  
except Exception, e:
  print "exception:" + str(e)
  sys.exit(1)


