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
  else:
    print "User already exists."

def copy_dir(src_dir, dest_dir):
  if (os.path.exists(dest_dir)) and (dest_dir != "/"):
    print "Removing old directory "+dest_dir
    shutil.rmtree(dest_dir)
  if not (os.path.exists(dest_dir)):
    print "Copying directory "+src_dir+" to "+dest_dir
    shutil.copytree(src_dir, dest_dir)
    
try:
  # Create users
  create_user("pypo")
  #create_user("pypo-logger")

  print "Creating log directories"
  create_path("/var/log/pypo")
  os.system("chmod -R 755 /var/log/pypo")
  os.system("chown -R pypo:pypo /var/log/pypo")
  #os.mkdirs("/var/log/liquidsoap")
  #os.system("chown -R liquidsoap:liquidsoap /var/log/liquidsoap")

  create_path(BASE_PATH)
  create_path(BASE_PATH+"bin")
  create_path(BASE_PATH+"cache")
  create_path(BASE_PATH+"files")
  create_path(BASE_PATH+"files/basic")
  create_path(BASE_PATH+"files/fallback")
  create_path(BASE_PATH+"files/jingles")
  create_path(BASE_PATH+"archive")
  
  print "Copying pypo files"
  shutil.copy("../scripts/silence-playlist.lsp", BASE_PATH+"files/basic")
  shutil.copy("../scripts/silence.mp3", BASE_PATH+"files/basic")
  #shutil.copy("../pypo-cli.py", BASE_PATH+"bin")
  #shutil.copy("../pypo-notify.py", BASE_PATH+"bin")
  #shutil.copy("../logging.cfg", BASE_PATH+"bin")
  #shutil.copy("../config.cfg", BASE_PATH+"bin")
  #shutil.copy("../pypo-log.sh", BASE_PATH+"bin")
  copy_dir("..", BASE_PATH+"bin/")
  #copy_dir("../util", BASE_PATH+"bin/")
  #copy_dir("../api_clients", BASE_PATH+"bin/api_clients")
  #copy_dir("../scripts", BASE_PATH+"bin/scripts")
  #copy_dir("../dls", BASE_PATH+"bin/dls")
  #copy_dir("../dls", BASE_PATH+"bin/dls")
  
  print "Setting permissions"
  os.system("chmod -R 755 "+BASE_PATH)
  os.system("chown -R pypo:pypo "+BASE_PATH)

  print "Installing daemontool script pypo-fetch"
  create_path("/etc/service/pypo-fetch")
  create_path("/etc/service/pypo-fetch/log")
  shutil.copy("pypo-daemontools-fetch.sh", "/etc/service/pypo-fetch/run")
  shutil.copy("pypo-daemontools-logger.sh", "/etc/service/pypo-fetch/log/run")
  os.system("chmod -R 755 /etc/service/pypo-fetch")
  os.system("chown -R pypo:pypo /etc/service/pypo-fetch")
  time.sleep(1)
  os.system("svc -t /etc/service/pypo-fetch")

  print "Installing daemontool script pypo-push"
  create_path("/etc/service/pypo-push")
  create_path("/etc/service/pypo-push/log")
  shutil.copy("pypo-daemontools-push.sh", "/etc/service/pypo-push/run")
  shutil.copy("pypo-daemontools-logger.sh", "/etc/service/pypo-push/log/run")
  os.system("chmod -R 755 /etc/service/pypo-push")
  os.system("chown -R pypo:pypo /etc/service/pypo-push")
  time.sleep(1)
  os.system("svc -t /etc/service/pypo-push")

  print "Installing daemontool script pypo-liquidsoap"
  os.system("svc -dk /etc/service/pypo-liquidsoap")  
  os.system("killall liquidsoap")
  create_path("/etc/service/pypo-liquidsoap")  
  create_path("/etc/service/pypo-liquidsoap/log")  
  shutil.copy("pypo-daemontools-liquidsoap.sh", "/etc/service/pypo-liquidsoap/run")
  shutil.copy("pypo-daemontools-logger.sh", "/etc/service/pypo-liquidsoap/log/run")
  os.system("chmod -R 755 /etc/service/pypo-liquidsoap")
  os.system("chown -R pypo:pypo /etc/service/pypo-liquidsoap")
  time.sleep(1)
  os.system("svc -u /etc/service/pypo-liquidsoap")

  print "Waiting for processes to start..."
  time.sleep(2)

  p = Popen('svstat /etc/service/pypo-fetch', shell=True, stdin=PIPE, stdout=PIPE, stderr=STDOUT, close_fds=True)
  output = p.stdout.read()
  if (output.find("unable to open supervise/ok: file does not exist") >= 0):
    print "Install has completed, but daemontools is not running, please make sure you have it installed and then reboot."
    sys.exit()
  print output
  
  p = Popen('svstat /etc/service/pypo-push', shell=True, stdin=PIPE, stdout=PIPE, stderr=STDOUT, close_fds=True)
  output = p.stdout.read()
  print output
  
  p = Popen('svstat /etc/service/pypo-liquidsoap', shell=True, stdin=PIPE, stdout=PIPE, stderr=STDOUT, close_fds=True)
  output = p.stdout.read()
  print output

#os.symlink(BASE_PATH+"bin/pypo-log.sh", "/usr/local/bin/")
  
  
except Exception, e:
  print "exception:" + str(e)
  


