#!/usr/bin/env python
# -*- coding: utf-8 -*-

import os
import sys

if os.geteuid() != 0:
    print "Please run this as root."
    sys.exit(1)
    
try:
    print "Stopping daemontool script pypo-fetch"
    os.system("svc -dx /etc/service/pypo-fetch")
    
    print "Stopping daemontool script pypo-push"
    os.system("svc -dx /etc/service/pypo-push")
    
    print "Stopping daemontool script pypo-liquidsoap"
    os.system("svc -dx /etc/service/pypo-liquidsoap")
    
except Exception, e:
    print "exception:" + str(e)