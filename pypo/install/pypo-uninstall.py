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
    os.system("killall -u " + username)
    
    #allow all process to be completely closed before we attempt to delete user
    print "Waiting for processes to close..."
    time.sleep(5)
    
    os.system("deluser --remove-home " + username)
    #os.system("delgroup " + username)

try:
    print "Removing log directories"
    remove_path("/var/log/pypo")
    
    print "Removing pypo files"
    remove_path(BASE_PATH)
    
    print "Removing daemontool script pypo-fetch"
    os.system("svc -dx /etc/service/pypo-fetch")
    remove_path("rm -rf /etc/service/pypo-fetch")
    
    print "Removing daemontool script pypo-push"
    os.system("svc -dx /etc/service/pypo-push")
    remove_path("rm -rf /etc/service/pypo-push")
    
    print "Removing daemontool script pypo-liquidsoap"
    os.system("svc -dx /etc/service/pypo-liquidsoap")
    remove_path("rm -rf /etc/service/pypo-liquidsoap")

    remove_user("pypo")  
except Exception, e:
    print "exception:" + str(e)