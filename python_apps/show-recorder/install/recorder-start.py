#!/usr/bin/env python
# -*- coding: utf-8 -*-

import os
import sys

if os.geteuid() != 0:
    print "Please run this as root."
    sys.exit(1)
    
try:
    print "Starting daemontool script recorder"
    os.system("svc -u /etc/service/recorder")
    
except Exception, e:
    print "exception:" + str(e)
