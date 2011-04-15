#!/usr/bin/env python
# -*- coding: utf-8 -*-

import os
import sys
import time
from configobj import ConfigObj

if os.geteuid() != 0:
    print "Please run this as root."
    sys.exit(1)
    
PATH_INI_FILE = '/etc/airtime/recorder.cfg'

def remove_path(path):
    os.system("rm -rf " + path)

def remove_user(username):
    os.system("killall -u %s 1>/dev/null 2>&1" % username)
    
    #allow all process to be completely closed before we attempt to delete user
    print "Waiting for processes to close..."
    time.sleep(5)
    
    os.system("deluser --remove-home " + username + " 1>/dev/null 2>&1")

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
        sys.exit()

    os.system("python %s/recorder-stop.py" % get_current_script_dir())
    
    print "Removing log directories"
    remove_path(config["log_dir"])
    
    print "Removing application files"
    remove_path(config["bin_dir"])
    
    print "Removing media files"
    remove_path(config["base_recorded_files"])
    
    print "Removing daemontool script recorder"
    remove_path("rm -rf /etc/service/recorder")

    remove_user("pypo")
    print "Uninstall complete."
except Exception, e:
    print "exception:" + str(e)
