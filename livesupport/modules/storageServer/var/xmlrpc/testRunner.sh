#!/bin/sh

#-------------------------------------------------------------------------------
#   Copyright (c) 2004 Media Development Loan Fund
#
#   This file is part of the LiveSupport project.
#   http://livesupport.campware.org/
#   To report bugs, send an e-mail to bugs@campware.org
#
#   LiveSupport is free software; you can redistribute it and/or modify
#   it under the terms of the GNU General Public License as published by
#   the Free Software Foundation; either version 2 of the License, or
#   (at your option) any later version.
#
#   LiveSupport is distributed in the hope that it will be useful,
#   but WITHOUT ANY WARRANTY; without even the implied warranty of
#   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#   GNU General Public License for more details.
#
#   You should have received a copy of the GNU General Public License
#   along with LiveSupport; if not, write to the Free Software
#   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
#
#   Author   : $Author: tomas $
#   Version  : $Revision: 1.2 $
#   Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/storageServer/var/xmlrpc/testRunner.sh,v $
#-------------------------------------------------------------------------------

COMM=$1
shift
GUNID=$1

XMLRPC=http://localhost:80/livesupport/modules/storageServer/var/xmlrpc/xrLocStor.php
echo "XMLRPC server URL (check it in troubles):"
echo $XMLRPC

TESTDIR=`dirname $0`
XR_CLI="$TESTDIR/xr_cli_test.py -s $XMLRPC"

login() {
    echo "login:"
    SESSID=`$XR_CLI login root q`
    echo "sessid: $SESSID"
}

test() {
    echo "test:"
    $XR_CLI test $SESSID stringForUppercase
}

existsAudioClip() {
    echo "existsAudioClip:"
    $XR_CLI existsAudioClip $SESSID $GUNID
}

accessRawAudioData() {
    echo "accessRawAudioData:"
    FPATH=`$XR_CLI accessRawAudioData $SESSID $GUNID`
    FPATH="<?echo urldecode(\"$FPATH\")?>"
    FPATH=`echo "$FPATH" | php -q`
    echo $FPATH
    ls -l $FPATH
    echo "releaseRawAudioData:"
    $XR_CLI releaseRawAudioData $SESSID $FPATH
#$XR_CLI getAudioClip $SESSID $GUNID
}

storeAudioClip() {
    echo "storeAudioClip:"
    MEDIA=../tests/ex1.mp3
    METADATA=../tests/testStorage.xml
    RGUNID=`$XR_CLI storeAudioClip "$SESSID" '' "$MEDIA" "$METADATA"`
    echo $RGUNID
}

deleteAudioClip() {
    echo "deleteAudioClip:"
    $XR_CLI deleteAudioClip $SESSID $GUNID
}

updateAudioClipMetadata() {
    echo "updateAudioClipMetadata:"
    $XR_CLI updateAudioClipMetadata $SESSID $GUNID '../tests/mdata3.xml'
}

getAudioClip() {
    echo "getAudioClip:"
    $XR_CLI getAudioClip $SESSID $GUNID | ./urldecode
}

searchMetadata() {
    echo "searchMetadata:"
#    $XR_CLI searchMetadata $SESSID '../tests/srch_cri1.xml'
    $XR_CLI searchMetadata $SESSID 'John %'
}

logout() {
    echo "logout:"
    $XR_CLI logout $SESSID
}

usage(){
    echo "Usage: $0 <command> [args]"
    echo -e "commands:\n test\n existsAudioClip\n accessRawAudioData"
    echo -e " storeAudioClip\n deleteAudioClip\n updateAudioClipMetadata"
    echo -e " getAudioClip\n searchMetadata\n"
}

if [ "$COMM" == "test" ]; then
    login
    test
    logout
elif [ "$COMM" == "existsAudioClip" ]; then
    login
    existsAudioClip
    logout
elif [ "$COMM" == "accessRawAudioData" ]; then
    login
    accessRawAudioData
    logout
elif [ "$COMM" == "storeAudioClip" ]; then
    login
    storeAudioClip
    logout
elif [ "$COMM" == "deleteAudioClip" ]; then
    login
    deleteAudioClip
    logout
elif [ "$COMM" == "updateAudioClipMetadata" ]; then
    login
    updateAudioClipMetadata
    logout
elif [ "$COMM" == "getAudioClip" ]; then
    login
    getAudioClip
    logout
elif [ "$COMM" == "searchMetadata" ]; then
    login
    searchMetadata
    logout
elif [ "x$COMM" == "x" ]; then
    login
    storeAudioClip
    GUNID=$RGUNID
    accessRawAudioData
    deleteAudioClip
    logout
elif [ "$COMM" == "help" ]; then
    usage
else
    echo "Unknown command"
    usage
fi
