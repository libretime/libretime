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
#   Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/archiveServer/var/xmlrpc/testRunner.sh,v $
#-------------------------------------------------------------------------------

COMM=$1
shift
GUNID=$1

XMLRPC=`cd var/install; php -q getXrUrl.php` || exit $?
echo " archiveServer XMLRPC URL:"
echo $XMLRPC

TESTDIR=`dirname $0`
XR_CLI="$TESTDIR/xr_cli_test.py -s $XMLRPC"

login() {
    echo "login:"
    SESSID=`$XR_CLI login root q`
    echo "sessid: $SESSID"
}

ping() {
    echo "ping:"
    $XR_CLI ping abcDef
}

# existsAudioClip() {
#     echo "existsAudioClip:"
#     $XR_CLI existsAudioClip $SESSID $GUNID
# }

downloadOpenClose() {
    echo "downloadOpen:"
    FURL=`$XR_CLI downloadOpen $SESSID $GUNID`
    FURL="<?echo urldecode(\"$FURL\")?>"
    FURL=`echo "$FURL" | php -q`
    echo $FURL
#    ls -l `dirname $FURL`
    echo "downloadClose:"
    $XR_CLI downloadClose $SESSID $FURL
#$XR_CLI getAudioClip $SESSID $GUNID
}

# storeAudioClip() {
#     echo "storeAudioClip:"
#     MEDIA=../tests/ex1.mp3
#     METADATA=../tests/testStorage.xml
#     RGUNID=`$XR_CLI storeAudioClip "$SESSID" 'X' "$MEDIA" "$METADATA"`
#     echo $RGUNID
# }

# searchMetadata() {
#     echo "searchMetadata:"
# #    $XR_CLI searchMetadata $SESSID '../tests/srch_cri1.xml'
#     $XR_CLI searchMetadata $SESSID 'John %'
# }

logout() {
    echo "logout:"
    $XR_CLI logout $SESSID
}

usage(){
    echo "Usage: $0 <command> [args]"
    echo -e "commands:\n test, ...\n"
}

if [ "$COMM" == "ping" ]; then
    ping
elif [ "x$COMM" == "x" ]; then
#    echo "No action"
    login
    downloadOpenClose
    logout
elif [ "$COMM" == "help" ]; then
    usage
else
    echo "Unknown command"
    usage
fi
