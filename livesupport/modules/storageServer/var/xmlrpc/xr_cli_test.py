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
#    Version  : $Revision: 1.8 $
#    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/storageServer/var/xmlrpc/Attic/xr_cli_test.py,v $
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
     test <session_id> <teststring>
     login <username> <password>
     authenticate <username> <password>
     logout <session_id>
     ...
"""
#     existsAudioClip <session_id> <global_unique_id>
#     storeAudioClip <session_id> <global_unique_id> <media_file_path> <metadata_file_path>
#     deleteAudioClip <session_id> <global_unique_id>
#     updateAudioClipMetadata <session_id> <global_unique_id> <metadata_file_path>
#     searchMetadata <session_id> <criteria>
#     accessRawAudioData <session_id> <global_unique_id>
#     releaseRawAudioData <session_id> <tmp_link_path>
#     getAudioClip <session_id> <global_unique_id>
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
    serverPath = 'http://localhost:80/livesupportStorageServer/xmlrpc/xrLocStor.php'
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
    elif method=="test":
        print server.locstor.test({'sessid':pars[0], 'teststring':pars[1]})
    elif method=="authenticate":
        print server.locstor.authenticate({'login':pars[0], 'pass':pars[1]})['authenticate']
    elif method=="login":
        print server.locstor.login({'login':pars[0], 'pass':pars[1]})['sessid']
    elif method=="logout":
        print server.locstor.logout({'sessid':pars[0]})
    elif method=="storeAudioClipOpen":
        r = server.locstor.storeAudioClipOpen({'sessid':pars[0], 'gunid':pars[1], 'metadata':pars[2], 'chsum':pars[3]})
        print r['url']+'\n'+r['token']
    elif method=="storeAudioClipClose":
        print server.locstor.storeAudioClipClose({'sessid':pars[0], 'token':pars[1]})['gunid']
    elif method=="accessRawAudioData":
        r = server.locstor.accessRawAudioData({'sessid':pars[0], 'gunid':pars[1]})
        print r['url']+'\n'+r['token']
    elif method=="releaseRawAudioData":
        print server.locstor.releaseRawAudioData({'sessid':pars[0], 'token':pars[1]})
    elif method=="downloadRawAudioDataOpen":
        r = server.locstor.downloadRawAudioDataOpen({'sessid':pars[0], 'gunid':pars[1]})
        print r['url']+'\n'+r['token']
    elif method=="downloadRawAudioDataClose":
        print server.locstor.downloadRawAudioDataClose({'sessid':pars[0], 'token':pars[1]})
    elif method=="downloadMetadataOpen":
        r = server.locstor.downloadMetadataOpen({'sessid':pars[0], 'gunid':pars[1]})
        print r['url']+'\n'+r['token']
    elif method=="downloadMetadataClose":
        print server.locstor.downloadMetadataClose({'sessid':pars[0], 'token':pars[1]})
    elif method=="deleteAudioClip":
        print server.locstor.deleteAudioClip({'sessid':pars[0], 'gunid':pars[1]})
    elif method=="existsAudioClip":
        print server.locstor.existsAudioClip({'sessid':pars[0], 'gunid':pars[1]})
    elif method=="updateAudioClipMetadata":
        print server.locstor.updateAudioClipMetadata({'sessid':pars[0], 'gunid':pars[1], 'mdataFileLP':pars[2]})
    elif method=="searchMetadata":
#        print server.locstor.searchMetadata({'sessid':pars[0], 'criteria':pars[1]})
        print server.locstor.searchMetadata({'sessid':pars[0], 'criteria':{'type':'and', 'conds':['a', 'b']}})
    elif method=="getAudioClip":
        r = server.locstor.getAudioClip({'sessid':pars[0], 'gunid':pars[1]})
        print r['metadata']
    elif method=="resetStorage":
        print server.locstor.resetStorage({})
    elif method=="openPut":
        r = server.locstor.openPut({}); print r['url']+'\n'+r['token']
    elif method=="closePut":
        print server.locstor.closePut({'token':pars[0], 'chsum':pars[1]})
    else:
        print "Unknown command: "+method
        sys.exit(1)
except Error, v:
#    print "XML-RPC Error:",v
    sys.exit(v)
