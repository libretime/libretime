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
#   Version  : $Revision: 1.17 $
#   Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/storageServer/var/xmlrpc/testRunner.sh,v $
#-------------------------------------------------------------------------------

#DEBUG=yes
#DEBUG_I=yes

COMM=$1
shift
GUNID=$1

#METADATA="<?xml version=\"1.0\" encoding=\"UTF-8\"?>
#<metadata><title>ěščřžé</title></metadata>"
METADATA="<?xml version=\"1.0\"?>
<audioClip><metadata xmlns=\"http://www.streamonthefly.org/\"
 xmlns:dc=\"http://purl.org/dc/elements/1.1/\"
 xmlns:dcterms=\"http://purl.org/dc/terms/\">
<dcterms:extent>00:00:11</dcterms:extent></metadata></audioClip>"

echo ""
XRDIR=`dirname $0`
XMLRPC=`cd var/install; php -q getXrUrl.php` || exit $?
echo "# storageServer XMLRPC URL: $XMLRPC"

cd $XRDIR
#XR_CLI="./xr_cli_test.py -s ${XMLRPC}"
XR_CLI="php -q xr_cli_test.php -s ${XMLRPC}"

login() {
    echo -n "# login: "
    SESSID=`$XR_CLI login root q` || \
    	{ ERN=$?; echo $SESSID; exit $ERN; }
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
    MEDIA=../tests/ex1.mp3
    MD5=`md5sum $MEDIA`; for i in $MD5; do MD5=$i; break; done
    if [ $DEBUG_I ]; then echo "md5=$MD5"; fi
    echo -n "# storeAudioClipOpen: "
    RES=`$XR_CLI storeAudioClipOpen "$SESSID" '' "$METADATA" "stored file.mp3" "$MD5"` || \
    	{ ERN=$?; echo $RES; exit $ERN; }
    unset URL
    for i in $RES; do if [ -z $URL ] ;  then URL=$i; else TOKEN=$i; fi; done
    echo $TOKEN
    if [ $DEBUG_I ]; then echo $URL; fi
    if [ $DEBUG_I ]; then echo -n "Press enter ..."; read KEY; fi
    echo -n "# curl (PUT): "
    curl -C 0 -T $MEDIA $URL || { ERN=$?; echo $RGUNID; exit $ERN; }
    echo "status: $?"
    if [ $DEBUG_I ]; then echo -n "Press enter ..."; read KEY; fi
    echo -n "# storeAudioClipClose: "
    RGUNID=`$XR_CLI storeAudioClipClose "$SESSID" "$TOKEN"` || \
    	{ ERN=$?; echo $RGUNID; exit $ERN; }
    echo $RGUNID
}

accessRawAudioData() {
    echo -n "# accessRawAudioData: "
    RES=`$XR_CLI accessRawAudioData $SESSID $GUNID` || \
    	{ ERN=$?; echo $RES; exit $ERN; }
    unset URL
    for i in $RES; do if [ -z $URL ] ;  then URL=$i; else TOKEN=$i; fi; done
    echo $TOKEN
    if [ $DEBUG_I ]; then echo $URL; fi
    if [ $DEBUG_I ]; then echo -n "Press enter ..."; read KEY; fi
    echo -n "# releaseRawAudioData: "
    $XR_CLI releaseRawAudioData $SESSID $TOKEN || exit $?
}

downloadRAD() {
    echo -n "# downloadRawAudioDataOpen: "
    RES=`$XR_CLI downloadRawAudioDataOpen $SESSID $GUNID` || \
    	{ ERN=$?; echo $RES; exit $ERN; }
    unset URL
    for i in $RES; do if [ -z $URL ] ;  then URL=$i; else TOKEN=$i; fi; done
    echo $TOKEN
    if [ $DEBUG_I ]; then echo $URL; fi
    if [ $DEBUG_I ]; then echo -n "Press enter ..."; read KEY; fi
    echo -n "# curl: "
    curl -Ifs $URL > /dev/null || { ERN=$?; echo $URL; exit $ERN; }
    echo "status: $?"
    echo -n "# downloadRawAudioDataClose: "
    $XR_CLI downloadRawAudioDataClose $SESSID $TOKEN || exit $?
}

downloadMeta() {
    echo -n "# downloadMetadataOpen: "
    RES=`$XR_CLI downloadMetadataOpen $SESSID $GUNID` || \
    	{ ERN=$?; echo $RES; exit $ERN; }
    unset URL
    for i in $RES; do if [ -z $URL ] ;  then URL=$i; else TOKEN=$i; fi; done
    echo $TOKEN
    if [ $DEBUG_I ]; then echo $URL; fi
    if [ $DEBUG_I ]; then echo -n "Press enter ..."; read KEY; fi
    echo -n "# curl: "
    METAOUT=`curl -fs $URL;` || { ERN=$?; echo $RES; exit $ERN; }
    echo "OK"
    if [ $DEBUG_I ]; then echo $METAOUT; echo -n "Press enter ..."; read KEY; fi
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
    $XR_CLI updateAudioClipMetadata $SESSID $GUNID "$METADATA" || exit $?
}

getAudioClip() {
    echo -n "#getAudioClip: "
    $XR_CLI getAudioClip $SESSID $GUNID || exit $?
}

searchMetadata() {
    echo -n "# searchMetadata: "
#    $XR_CLI searchMetadata $SESSID '../tests/srch_cri1.xml' || exit $?
    $XR_CLI searchMetadata $SESSID 'John %' || exit $?
}

PLID="123456789abcdef8"

createPlaylist() {
    echo -n "# createPlaylist: "
    $XR_CLI createPlaylist $SESSID $PLID "newPlaylist.xml" || exit $?
}

accessPlaylist() {
    echo -n "# accessPlaylist: "
    RES=`$XR_CLI accessPlaylist $SESSID $PLID` || \
    	{ ERN=$?; echo $RES; exit $ERN; }
    unset URL
    for i in $RES; do if [ -z $URL ] ;  then URL=$i; else TOKEN=$i; fi; done
    echo $TOKEN
    if [ $DEBUG_I ]; then echo $URL; fi
    echo "# curl: "
    CURLOUT=`curl -fs $URL;` || { ERN=$?; echo $RES; exit $ERN; }
    if [ $DEBUG ]; then echo $CURLOUT; fi
    if [ $DEBUG_I ]; then echo -n "Press enter ..."; read KEY; fi
    echo "#  status: $?"
    if [ $DEBUG_I ]; then echo -n "Press enter ..."; read KEY; fi
    echo -n "# releasePlaylist: "
    $XR_CLI releasePlaylist $SESSID $TOKEN || exit $?
}

editPlaylist() {
    DATE=`date '+%H:%M:%S'`
    PLAYLIST="<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<smil><head><metadata>
 <rdf:RDF xmlns:rdf=\"http://www.w3.org/1999/02/22-rdf-syntax-ns#\" xmlns:dc=\"http://purl.org/metadata/dublin_core#\">
  <dc:title>XY $DATE</dc:title>
 </rdf:RDF>
</metadata></head><body><seq>
   <audio src=\"123456789abcdefa\"/>
   <audio src=\"123456789abcdefb\"/>
</seq></body></smil>"
    echo -n "# editPlaylist: "
    RES=`$XR_CLI editPlaylist $SESSID $PLID` || \
    	{ ERN=$?; echo $RES; exit $ERN; }
    unset URL
    for i in $RES; do if [ -z $URL ] ;  then URL=$i; else TOKEN=$i; fi; done
    echo $TOKEN
#    deletePlaylist
    if [ $DEBUG_I ]; then echo $URL; fi
    if [ $DEBUG_I ]; then echo -n "Press enter ..."; read KEY; fi
    if [ $DEBUG_I ]; then echo " Playlist:"; echo $PLAYLIST; fi
    echo -n "# savePlaylist: "
    $XR_CLI savePlaylist $SESSID $TOKEN "$PLAYLIST" || exit $?
}

existsPlaylist() {
    echo -n "# existsPlaylist (${PLID}): "
    $XR_CLI existsPlaylist $SESSID $PLID || exit $?
}

deletePlaylist() {
    echo -n "# deletePlaylist (${PLID}): "
    $XR_CLI deletePlaylist $SESSID $PLID
    # || exit $?
    echo "#  status: $?"
}

prefTest() {
    PREFKEY="testKey"
    PREFVAL="test preference value"
    echo -n "# savePref: "
    $XR_CLI savePref $SESSID "$PREFKEY" "$PREFVAL"|| exit $?
    echo -n "# loadPref: "
    VAL=`$XR_CLI loadPref $SESSID "$PREFKEY"` || \
    	{ ERN=$?; echo $VAL; exit $ERN; }
    echo "$VAL  "
    if [ "x$VAL" != "x$PREFVAL" ] ; then
        echo " NOT MATCH"
        echo " Expected:"; echo $PREFVAL
        echo " Returned:"; echo $VAL
        exit 1
    else
        echo "# pref value check: OK"
    fi
    echo -n "# delPref: "
    $XR_CLI delPref $SESSID "$PREFKEY"|| exit $?
    if [ $DEBUG ]; then
        echo -n "# loadPref: "
        VAL=`$XR_CLI loadPref $SESSID "$PREFKEY"` || echo $?
    else
        echo $VAL
    fi
}

logout() {
    echo -n "# logout: "
    $XR_CLI logout $SESSID || exit $?
}

preferenceTest(){
    echo "#XMLRPC preference test"
    login
    prefTest
    logout
    echo "#XMLRPC: preference: OK."
    echo ""
}

playlistTest(){
    echo "#XMLRPC playlists test"
    login
    existsPlaylist
    deletePlaylist
    createPlaylist
    existsPlaylist
    accessPlaylist
    editPlaylist
    accessPlaylist
    deletePlaylist
    existsPlaylist
    logout
    echo "#XMLRPC: playlists: OK."
    echo ""
}

storageTest(){
    echo "#XMLRPC: storage test"
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
    echo "#XMLRPC: storage: OK."
    echo ""
}

usage(){
    echo "Usage: $0 [<command>] [args]"
    echo -e "commands:\n test\n existsAudioClip\n accessRawAudioData"
    echo -e " storeAudioClip\n deleteAudioClip\n updateAudioClipMetadata"
    echo -e " getAudioClip\n searchMetadata\n"
    echo -e " preferences\n playlists\n storage\n"
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
elif [ "$COMM" == "preferences" ]; then
    preferenceTest
elif [ "$COMM" == "playlists" ]; then
    playlistTest
elif [ "$COMM" == "storage" ]; then
    storageTest
elif [ "x$COMM" == "x" ]; then
    storageTest
    playlistTest
    preferenceTest
elif [ "$COMM" == "help" ]; then
    usage
else
    echo "Unknown command"
    usage
fi
