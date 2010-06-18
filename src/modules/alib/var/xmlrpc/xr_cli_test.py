#!/usr/bin/python

#------------------------------------------------------------------------------
#
#    Copyright (c) 2004 Media Development Loan Fund
# 
#    This file is part of the Campcaster project.
#    http://campcaster.campware.org/
#    To report bugs, send an e-mail to bugs@campware.org
# 
#    Campcaster is free software; you can redistribute it and/or modify
#    it under the terms of the GNU General Public License as published by
#    the Free Software Foundation; either version 2 of the License, or
#    (at your option) any later version.
#  
#    Campcaster is distributed in the hope that it will be useful,
#    but WITHOUT ANY WARRANTY; without even the implied warranty of
#    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#    GNU General Public License for more details.
# 
#    You should have received a copy of the GNU General Public License
#    along with Campcaster; if not, write to the Free Software
#    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
# 
# 
#    Author   : $Author$
#    Version  : $Revision$
#    Location : $URL$
#
#------------------------------------------------------------------------------

from xmlrpclib import *
import sys

if len(sys.argv)<3:
    print """
 Usage: xr_cli_pok.py http://<server>/<path>/xmlrpc/alib_xr.php <command> <args>
   commands and args:
     test <teststring> [<sessin_id>]
     login <username> <password>
     logout <session_id>
"""
    sys.exit(1)
elif sys.argv[2]=="test":
    if len(sys.argv)>3:
        tstr=sys.argv[3]
    if len(sys.argv)>4:
        sessid=sys.argv[4]
    else:
        sessid=''
    path=sys.argv[1]
    server = Server(path)
    try:
        r = server.alib.xrTest(tstr, sessid)
        print r
    except Error, v:
        print "XML-RPC Error:",v
elif sys.argv[2]=="login":
    login=sys.argv[3]
    passwd=sys.argv[4]
    path=sys.argv[1]
    server = Server(path)
    try:
        r = server.alib.login(login, passwd)
        print r
    except Error, v:
        print "XML-RPC Error:",v
elif sys.argv[2]=="logout":
    sessid=sys.argv[3]
    path=sys.argv[1]
    server = Server(path)
    try:
        r = server.alib.logout(sessid)
        print r
    except Error, v:
        print "XML-RPC Error:",v
else:
    print "Unknown command: "+sys.argv[2]
    sys.exit(1)



