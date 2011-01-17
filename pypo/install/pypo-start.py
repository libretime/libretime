#!/usr/bin/env python
# -*- coding: utf-8 -*-

import os
import sys

if os.geteuid() != 0:
    print "Please run this as root."
    sys.exit(1)
    
try:
    print "Starting daemontool script pypo-fetch"
    os.system("svc -t /etc/service/pypo-fetch")
    
    print "Starting daemontool script pypo-push"
    os.system("svc -t /etc/service/pypo-push")
    
    print "Starting daemontool script pypo-liquidsoap"
    os.system("svc -t /etc/service/pypo-liquidsoap")
    
except Exception, e:
    print "exception:" + str(e)
