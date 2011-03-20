#!/usr/bin/env python
# -*- coding: utf-8 -*-

import os
import sys

if os.geteuid() != 0:
    print "Please run this as root."
    sys.exit(1)
    
try:
    print "Stopping daemontool script pypo"
    os.system("svc -dx /etc/service/pypo 2>/dev/null")
 
    print "Stopping daemontool script pypo-liquidsoap"
    os.system("svc -dx /etc/service/pypo-liquidsoap 2>/dev/null")
    os.system("killall liquidsoap")
    
except Exception, e:
    print "exception:" + str(e)
