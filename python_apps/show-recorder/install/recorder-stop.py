#!/usr/bin/env python
# -*- coding: utf-8 -*-

import os
import sys

if os.geteuid() != 0:
    print "Please run this as root."
    sys.exit(1)
    
try:
    print "Stopping daemontool script pypo"
    os.system("svc -dx /etc/service/pypo 1>/dev/null 2>&1")

    if os.path.exists("/etc/service/pypo-fetch"):
        os.system("svc -dx /etc/service/pypo-fetch 1>/dev/null 2>&1")
    if os.path.exists("/etc/service/pypo-push"):
        os.system("svc -dx /etc/service/pypo-push 1>/dev/null 2>&1")
 
    print "Stopping daemontool script pypo-liquidsoap"
    os.system("svc -dx /etc/service/pypo-liquidsoap 1>/dev/null 2>&1")
    os.system("killall liquidsoap")
    
except Exception, e:
    print "exception:" + str(e)
