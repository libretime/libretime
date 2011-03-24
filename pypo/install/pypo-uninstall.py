#!/usr/bin/env python
# -*- coding: utf-8 -*-

import os
import sys
import time

if os.geteuid() != 0:
    print "Please run this as root."
    sys.exit(1)
    
BASE_PATH = '/opt/pypo/'

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
    os.system("python %s/pypo-stop.py" % get_current_script_dir())
    
    print "Removing log directories"
    remove_path("/var/log/pypo")
    
    print "Removing pypo files"
    remove_path(BASE_PATH)
    
    print "Removing daemontool script pypo"
    remove_path("/etc/service/pypo")

    if os.path.exists("/etc/service/pypo-fetch"):
        remove_path("/etc/service/pypo-fetch")

    if os.path.exists("/etc/service/pypo-push"):
        remove_path("/etc/service/pypo-push")
        
    print "Removing daemontool script pypo-liquidsoap"
    remove_path("/etc/service/pypo-liquidsoap")

    remove_user("pypo")
    print "Uninstall complete."
except Exception, e:
    print "exception:" + str(e)
