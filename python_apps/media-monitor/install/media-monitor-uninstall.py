#!/usr/bin/env python
# -*- coding: utf-8 -*-

import os
import sys
import time
from configobj import ConfigObj

if os.geteuid() != 0:
    print "Please run this as root."
    sys.exit(1)
    
PATH_INI_FILE = '/etc/airtime/MediaMonitor.cfg'

def remove_path(path):
    os.system("rm -rf " + path)

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

    os.system("/etc/init.d/airtime-media-monitor stop")
    os.system("rm -f /etc/init.d/airtime-media-monitor")
    
    print "Removing log directories"
    remove_path(config["log_dir"])
    
    print "Removing symlinks"
    os.system("rm -f /usr/bin/airtime-media-monitor")
    
    print "Removing application files"
    remove_path(config["bin_dir"])

    print "Uninstall complete."
except Exception, e:
    print "exception:" + str(e)
