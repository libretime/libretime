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
#   Version  : $Revision: 1.9 $
#   Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/storageServer/var/xmlrpc/testRunner.sh,v $
#-------------------------------------------------------------------------------

#DEBUG=yes

COMM=$1
shift
GUNID=$1

METADATA="<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<metadata><title>ěščřžé</title></metadata>"

echo ""
XMLRPC=`cd var/install; php -q getXrUrl.php` || exit $?
echo "# storageServer XMLRPC URL: $XMLRPC"

TESTDIR=`dirname $0`
XR_CLI="$TESTDIR/xr_cli_test.py -s ${XMLRPC}"

login() {
    echo -n "# login: "
    SESSID=`$XR_CLI login root q` || exit $?
    echo "sessid: $SESSID"
}

test() {
    echo "# test: "
    $XR_CLI test $SESSID stringForUppercase || exit $?
}

existsAudioClip() {
    echo -n "# existsAudioClip (${GUNID}): "
    $XR_CLI existsAudioClip $SESSID $GUNID || exit $?
}

storeAudioClip() {
#    echo -n "# storeAudioClip: "
#    MEDIA=../tests/ex1.mp3
    MEDIA=var/tests/ex1.mp3
    MD5=`md5sum $MEDIA`; for i in $MD5; do MD5=$i; break; done
    echo "md5=$MD5"
    echo -n "# storeAudioClipOpen: "
    RES=`$XR_CLI storeAudioClipOpen "$SESSID" '' "$METADATA" "$MD5"` || \
    	{ ERN=$?; echo $RES; exit $ERN; }
    unset URL
    for i in $RES; do if [ -z $URL ] ;  then URL=$i; else TOKEN=$i; fi; done
    echo $TOKEN
    echo $URL
    if [ $DEBUG ]; then echo -n "Press enter ..."; read KEY; fi
    echo -n "# curl (PUT): "
    curl -C 0 -T $MEDIA $URL || { ERN=$?; echo $RGUNID; exit $ERN; }
    echo "status: $?"
    if [ $DEBUG ]; then echo -n "Press enter ..."; read KEY; fi
    echo -n "# storeAudioClipClose: "
    RGUNID=`$XR_CLI storeAudioClipClose "$SESSID" "$TOKEN"` || \
    	{ ERN=$?; echo $RGUNID; exit $ERN; }
    echo $RGUNID
}

accessRawAudioData() {
    echo "# accessRawAudioData: "
    RES=`$XR_CLI accessRawAudioData $SESSID $GUNID` || \
    	{ ERN=$?; echo $RES; exit $ERN; }
    unset URL
    for i in $RES; do if [ -z $URL ] ;  then URL=$i; else TOKEN=$i; fi; done
    echo $TOKEN
    echo $URL
    if [ $DEBUG ]; then echo -n "Press enter ..."; read KEY; fi
    echo -n "# releaseRawAudioData: "
    $XR_CLI releaseRawAudioData $SESSID $TOKEN || exit $?
}

downloadRAD() {
    echo "# downloadRawAudioDataOpen: "
    RES=`$XR_CLI downloadRawAudioDataOpen $SESSID $GUNID` || \
    	{ ERN=$?; echo $RES; exit $ERN; }
    unset URL
    for i in $RES; do if [ -z $URL ] ;  then URL=$i; else TOKEN=$i; fi; done
    echo $TOKEN
    echo $URL
    if [ $DEBUG ]; then echo -n "Press enter ..."; read KEY; fi
    echo -n "# curl: "
    curl -Ifs $URL > /dev/null || { ERN=$?; echo $RES; exit $ERN; }
    echo "status: $?"
    echo -n "# downloadRawAudioDataClose: "
    $XR_CLI downloadRawAudioDataClose $SESSID $TOKEN || exit $?
}

downloadMeta() {
    echo "# downloadMetadataOpen: "
    RES=`$XR_CLI downloadMetadataOpen $SESSID $GUNID` || \
    	{ ERN=$?; echo $RES; exit $ERN; }
    unset URL
    for i in $RES; do if [ -z $URL ] ;  then URL=$i; else TOKEN=$i; fi; done
    echo $TOKEN
    echo $URL
    if [ $DEBUG ]; then echo -n "Press enter ..."; read KEY; fi
    echo -n "# curl: "
#     curl -Ifs $URL > /dev/null || { ERN=$?; echo $RES; exit $ERN; }
    METAOUT=`curl -fs $URL;` || { ERN=$?; echo $RES; exit $ERN; }
    echo "OK"
    if [ $DEBUG ]; then echo $METAOUT; echo -n "Press enter ..."; read KEY; fi
    echo -n "#  metadata check:"
    if [ "x$METAOUT" != "x$METADATA" ] ; then
        echo " NOT MATCH"
        echo " Expected:"; echo $METADATA
        echo " Downloaded:"; echo $METAOUT
        exit 1
    else
        echo " OK"
    fi
    echo -n "# downloadMetadataClose: "
    $XR_CLI downloadMetadataClose $SESSID $TOKEN || exit $?
}

deleteAudioClip() {
    echo -n "# deleteAudioClip: "
    $XR_CLI deleteAudioClip $SESSID $GUNID || exit $?
}

updateAudioClipMetadata() {
    echo -n "#updateAudioClipMetadata: "
    $XR_CLI updateAudioClipMetadata $SESSID $GUNID '../tests/mdata3.xml' || exit $?
}

getAudioClip() {
    echo -n "#getAudioClip: "
    $XR_CLI getAudioClip $SESSID $GUNID | $TESTDIR/urldecode || exit $?
}

searchMetadata() {
    echo -n "# searchMetadata: "
#    $XR_CLI searchMetadata $SESSID '../tests/srch_cri1.xml' || exit $?
    $XR_CLI searchMetadata $SESSID 'John %' || exit $?
}

logout() {
    echo -n "# logout: "
    $XR_CLI logout $SESSID || exit $?
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
    existsAudioClip
    accessRawAudioData
    downloadRAD
    downloadMeta
    deleteAudioClip
    existsAudioClip
    logout
    echo "#XMLRPC tests: OK."
    echo ""
elif [ "$COMM" == "help" ]; then
    usage
else
    echo "Unknown command"
    usage
fi
