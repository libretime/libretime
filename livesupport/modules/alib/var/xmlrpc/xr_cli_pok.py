#!/usr/bin/python

# $Id: xr_cli_pok.py,v 1.1 2004/07/23 00:22:13 tomas Exp $

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



