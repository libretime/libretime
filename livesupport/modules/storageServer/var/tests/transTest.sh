#!/bin/bash
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
#   Version  : $Revision: 1.1 $
#   Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/storageServer/var/tests/transTest.sh,v $
#-------------------------------------------------------------------------------

#-------------------------------------------------------------------------------
# This script call locstor.resetStorage XMLRPC method
#-------------------------------------------------------------------------------

reldir=`dirname $0`/../..
WWW_ROOT=`cd $reldir/var/install; php -q getWwwRoot.php` || exit $?
echo "#Transport test: URL: $WWW_ROOT"

#$reldir/var/xmlrpc/xr_cli_test.py -s $WWW_ROOT/xmlrpc/xrLocStor.php \
# resetStorage || exit $?

cd $reldir/var/xmlrpc
XR_CLI="php -q xr_cli_test.php -s $WWW_ROOT/xmlrpc/xrLocStor.php"

login() {
    echo -n "# login: "
    SESSID=`$XR_CLI login root q` || \
        { ERN=$?; echo $SESSID; exit $ERN; }
    echo "sessid: $SESSID"
}

storeOpen() {
    echo "# store: "
    METADATA="<?xml version=\"1.0\"?>
    <audioClip><metadata xmlns=\"http://www.streamonthefly.org/\"
     xmlns:dc=\"http://purl.org/dc/elements/1.1/\"
     xmlns:dcterms=\"http://purl.org/dc/terms/\">
    <dcterms:extent>00:00:11</dcterms:extent></metadata></audioClip>"
    MEDIA=../tests/ex1.mp3
    MD5=`md5sum $MEDIA`; for i in $MD5; do MD5=$i; break; done
    RES=`$XR_CLI storeAudioClipOpen "$SESSID" "$GUNID" "$METADATA" "stored_file.mp3" "$MD5"` || \
        { ERN=$?; echo $RES; exit $ERN; }
    unset URL
    for i in $RES; do if [ -z $URL ] ;  then URL=$i; else TOKEN=$i; fi; done
    echo "  URL   = $URL"
    echo "  TOKEN = $TOKEN"
}

storeClose() {
    echo -n "# curl (PUT): "
    curl -C 0 -T $MEDIA $URL || exit $?
    echo "status: $?"
    echo -n "# storeAudioClipClose: "
    GUNID=`$XR_CLI storeAudioClipClose "$SESSID" "$TOKEN"` || \
        { ERN=$?; echo $GUNID; exit $ERN; }
    echo $GUNID
}

deleteAudioClip() {
    echo -n "# deleteAudioClip: "
    $XR_CLI deleteAudioClip $SESSID $GUNID || exit $?
}

uploadToArchive() {
    echo -n "# uploadToArchive: "
    TRTOK=`$XR_CLI uploadToArchive $SESSID $GUNID` || \
        { ERN=$?; echo $TRTOK; exit $ERN; }
    echo $TRTOK
}

downloadFromArchive() {
    echo -n "# downloadFromArchive: "
    TRTOK=`$XR_CLI downloadFromArchive $SESSID $GUNID` || \
        { ERN=$?; echo $TRTOK; exit $ERN; }
    echo $TRTOK
}

getTransportInfo() {
    echo "# getTransportInfo:"
    $XR_CLI getTransportInfo $SESSID $TRTOK
    echo "#  status: $?"
}

transportCron() {
    echo -n "# transportCron: "
    ../cron/transportCron.php
    echo $?
}

logout() {
    echo -n "# logout: "
    $XR_CLI logout $SESSID || exit $?
}

PLID="123456789abcdef8"

createPlaylist() {
    echo -n "# createPlaylist: "
    $XR_CLI createPlaylist $SESSID $PLID "newPlaylist.xml" || exit $?
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

deletePlaylist() {
    echo -n "# deletePlaylist (${PLID}): "
    $XR_CLI deletePlaylist $SESSID $PLID
    # || exit $?
    echo "#  status: $?"
}

testPrint(){
    ls -l ../stor/a23
    md5sum ../stor/a23/a23456789abcdef2
}

#PLID="a23456789abcdef3"
#GUNID=$PLID


#GUNID="a23456789abcdef2"
login
storeOpen
storeClose

#createPlaylist
#editPlaylist

#testPrint

uploadToArchive
#TRTOK="99ce8d099fc10ac5"
getTransportInfo
transportCron
getTransportInfo
transportCron
getTransportInfo

deleteAudioClip
#deletePlaylist

#testPrint

downloadFromArchive
#TRTOK="72bbe5eaa3ce7165"
getTransportInfo
transportCron
getTransportInfo
transportCron
getTransportInfo

#testPrint

logout

echo "#Transport test: OK"
exit 0
