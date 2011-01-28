#!/usr/bin/env python
# -*- coding: utf-8 -*-

import os
import sys

if os.geteuid() != 0:
    print "Please run this as root."
    sys.exit(1)
    
try:
    print "Stopping daemontool script pypo-fetch"
    os.system("svc -dx /etc/service/pypo-fetch 2>/dev/null")
 
    print "Stopping daemontool script pypo-push"
    os.system("svc -dx /etc/service/pypo-push 2>/dev/null")
    
    print "Stopping daemontool script pypo-liquidsoap"
    os.system("svc -dx /etc/service/pypo-liquidsoap 2>/dev/null")
    os.system("killall liquidsoap")
    
except Exception, e:
    print "exception:" + str(e)
