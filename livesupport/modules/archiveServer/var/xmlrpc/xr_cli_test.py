#!/usr/bin/python

#------------------------------------------------------------------------------
#
#    Copyright (c) 2004 Media Development Loan Fund
# 
#    This file is part of the LiveSupport project.
#    http://livesupport.campware.org/
#    To report bugs, send an e-mail to bugs@campware.org
# 
#    LiveSupport is free software; you can redistribute it and/or modify
#    it under the terms of the GNU General Public License as published by
#    the Free Software Foundation; either version 2 of the License, or
#    (at your option) any later version.
#  
#    LiveSupport is distributed in the hope that it will be useful,
#    but WITHOUT ANY WARRANTY; without even the implied warranty of
#    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#    GNU General Public License for more details.
# 
#    You should have received a copy of the GNU General Public License
#    along with LiveSupport; if not, write to the Free Software
#    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
# 
# 
#    Author   : $Author: tomas $
#    Version  : $Revision: 1.1 $
#    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/archiveServer/var/xmlrpc/Attic/xr_cli_test.py,v $
#
#------------------------------------------------------------------------------

from xmlrpclib import *
import sys

if len(sys.argv)<3:
    print """
 Usage: xr_cli_pok.py [-v] [-s http://<server>/<path>/xmlrpc/xrGreenBox.php] <command> <args>
   commands and args:
     listMethods
     methodHelp <method>
     methodSignature <method>
     ping <string>
     login <username> <password>
     logout <session_id>
"""
    sys.exit(1)

pars = sys.argv
verbose=0
if pars[1]=="-v":
    pars.pop(1)
    verbose=1
if pars[1]=="-s":
    pars.pop(1)
    serverPath = pars.pop(1)
else:
    serverPath = 'http://localhost:80/storage/xmlrpc/xrLocStor.php'
server = Server(serverPath)
method = pars.pop(1)
pars.pop(0)
if verbose:
    print "method: "+method
    print "pars: "
    print pars
    print "result:"
#sys.exit(0)

try:
    if method=="listMethods":
        print server.system.listMethods()
    elif method=="methodHelp":
        print server.system.methodHelp(pars[0])
    elif method=="methodSignature":
        print server.system.methodSignature(pars[0])
    elif method=="ping":
        print server.archive.ping({'par':pars[0]})
    elif method=="login":
        print server.archive.login({'login':pars[0], 'pass':pars[1]})
    elif method=="logout":
        print server.archive.logout({'sessid':pars[0]})
    elif method=="downloadOpen":
        print server.archive.downloadOpen({'sessid':pars[0], 'gunid':pars[1]})
    elif method=="downloadClose":
        print server.archive.downloadClose({'sessid':pars[0], 'url':pars[1]})
    else:
        print "Unknown command: "+method
        sys.exit(1)
except Error, v:
    print "XML-RPC Error:",v
