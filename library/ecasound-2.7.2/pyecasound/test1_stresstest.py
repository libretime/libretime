#!/usr/bin/env python

# -----------------------------------------------------------------------
# Runs a stress test using the pyeca interface
#
# Copyright (C) 2003 Kai Vehmanen (kai.vehmanen@wakkanet.fi)
# Licensed under GPL. See the file 'COPYING' for more information.
# -----------------------------------------------------------------------

import time
import sys
import os

# ---
# select pyeca implementation to use

# test the default implementation
from pyeca import *

# test the native Python implementation
#from ecacontrol import *

# test the C implementation
#from pyecasound import *

# ---
# configuration variables

# run for how many seconds
runlen = 5
# debug level (0, 1, 2, ...)
debuglevel = 1

if os.path.isfile('../ecasound/ecasound_debug'):
    os.environ['ECASOUND'] = '../ecasound/ecasound_debug'

if os.path.isfile('../ecasound/ecasound'):
    os.environ['ECASOUND'] = '../ecasound/ecasound'

# if above tests fail, the default ecasound binary
# will be used

# main program
e = ECA_CONTROL_INTERFACE(debuglevel)
result = 0

e.command("cs-add play_chainsetup")
e.command("c-add 1st_chain")
e.command("ai-add rtnull")
e.command("ao-add null")
e.command("cop-add -ezx:1,0.0")
e.command("ctrl-add -kos:2,-1,1,300,0")
e.command("cop-add -efl:300")
e.command("cop-add -evp")
e.command("cop-select 3")
e.command("copp-select 1")
e.command("cs-connect")
e.command("start")

total_cmds = 0

while 1 and e.last_type() != 'e':
    e.command("get-position")
    curpos = e.last_float()
    if curpos > runlen or e.last_type() == 'e': break
    e.command("copp-get")
    if debuglevel == 2:
        #print curpos, e.last_float()
        #if curpos == None:
        #    curpos = 0.0
        sys.stderr.write('%6.2f %6.4f\r' % (curpos,e.last_float()))
    else:
        if debuglevel == 1:
            sys.stderr.write('.')
            
    total_cmds = total_cmds + 2

if e.last_type() == 'e':
    print 'Ended to error:', e.last_error()
    result = -1
else:
    e.command("stop")
    e.command("cs-disconnect")

if debuglevel == 2:
    sys.stderr.write('\nprocessing speed: ' + str(total_cmds / runlen) + ' cmds/second.\n')

if debuglevel > 0:
    sys.stderr.write('\n')

sys.exit(result)
