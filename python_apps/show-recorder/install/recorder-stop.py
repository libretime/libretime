#!/usr/bin/env python
# -*- coding: utf-8 -*-

import os
import sys

if os.geteuid() != 0:
    print "Please run this as root."
    sys.exit(1)
    
try:
    print "Stopping daemontool script recorder"
    os.system("svc -dx /etc/service/recorder 1>/dev/null 2>&1")
    
except Exception, e:
    print "exception:" + str(e)
