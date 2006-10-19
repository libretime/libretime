#!/bin/bash
#-------------------------------------------------------------------------------
#   Copyright (c) 2004 Media Development Loan Fund
#
#   This file is part of the Campcaster project.
#   http://campcaster.campware.org/
#   To report bugs, send an e-mail to bugs@campware.org
#
#   Campcaster is free software; you can redistribute it and/or modify
#   it under the terms of the GNU General Public License as published by
#   the Free Software Foundation; either version 2 of the License, or
#   (at your option) any later version.
#
#   Campcaster is distributed in the hope that it will be useful,
#   but WITHOUT ANY WARRANTY; without even the implied warranty of
#   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#   GNU General Public License for more details.
#
#   You should have received a copy of the GNU General Public License
#   along with Campcaster; if not, write to the Free Software
#   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
#
#   Author   : $Author$
#   Version  : $Revision$
#   Location : $URL$
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

#-------------------------------------------------------------------------------
# storage related functions
#-------------------------------------------------------------------------------
login() {
    echo -n "# login: "
    SESSID=`$XR_CLI login root q` || \
        { ERN=$?; echo $SESSID; exit $ERN; }
    echo "sessid: $SESSID"
}

storeOpenClose() {
    GUNID=$1 ; shift
    [[ "x$1" != "x" ]] && MEDIA="$1" || MEDIA="../tests/ex1.mp3"
    shift
    [[ "x$1" != "x" ]] && METADATA=`cat "$1"` || METADATA="<?xml version=\"1.0\"?>
    <audioClip><metadata xmlns=\"http://www.streamonthefly.org/\"
     xmlns:dc=\"http://purl.org/dc/elements/1.1/\"
     xmlns:dcterms=\"http://purl.org/dc/terms/\">
     <dcterms:extent>00:00:11.000000</dcterms:extent>
     <dc:title>Transport test file 1</dc:title>
    </metadata></audioClip>"
    #echo "# store: gunid=$GUNID mediafile=$MEDIA metadata=$METADATA"
    echo "# store: "
    MD5=`md5sum $MEDIA`; for i in $MD5; do MD5=$i; break; done
    RES=`$XR_CLI storeAudioClipOpen "$SESSID" "$GUNID" "$METADATA" "stored_file.mp3" "$MD5"` || \
        { ERN=$?; echo $RES; exit $ERN; }
    unset URL
    for i in $RES; do if [ -z $URL ] ;  then URL=$i; else TOKEN=$i; fi; done
    echo "  URL   = $URL"
    echo "  TOKEN = $TOKEN"
    echo -n "# curl (PUT): "
    curl -C 0 -T $MEDIA $URL || exit $?
    echo "status: $?"
    echo -n "# storeAudioClipClose: "
    GUNID=`$XR_CLI storeAudioClipClose "$SESSID" "$TOKEN"` || \
        { ERN=$?; echo $GUNID; exit $ERN; }
    echo $GUNID
}

deleteAudioClip() {
    GUNID=$1 ; shift
    echo -n "# deleteAudioClip: "
    $XR_CLI deleteAudioClip $SESSID $GUNID 1
}

#-------------------------------------------------------------------------------
# transport related functions
#-------------------------------------------------------------------------------
getTransportInfo() {
    TRTOK=$1 ; shift
    echo "# getTransportInfo:"
    $XR_CLI getTransportInfo $TRTOK
    echo "#  status: $?"
}

upload2Hub() {
    GUNID=$1 ; shift
    echo -n "# upload2Hub: ($GUNID)"
    TRTOK=`$XR_CLI upload2Hub $SESSID $GUNID` || \
        { ERN=$?; echo $TRTOK; exit $ERN; }
    echo $TRTOK
}

downloadFromHub() {
    GUNID=$1 ; shift
    echo -n "# downloadFromHub: ($GUNID)"
    TRTOK=`$XR_CLI downloadFromHub $SESSID $GUNID` || \
        { ERN=$?; echo $TRTOK; exit $ERN; }
    echo $TRTOK
}

uploadFile2Hub() {
    FILE=$1 ; shift
    echo -n "# uploadFile2Hub: "
    TRTOK=`$XR_CLI uploadFile2Hub $SESSID $FILE` || \
        { ERN=$?; echo $TRTOK; exit $ERN; }
    echo $TRTOK
}

getHubInitiatedTransfers() {
    echo -n "# getHubInitiatedTransfers: "
    TRTOK=`$XR_CLI getHubInitiatedTransfers $SESSID` || \
        { ERN=$?; echo $TRTOK; exit $ERN; }
    echo $TRTOK
}

startHubInitiatedTransfer() {
    TRTOK=$1 ; shift
    echo -n "# startHubInitiatedTransfer: "
    RES=`$XR_CLI startHubInitiatedTransfer $TRTOK` || \
        { ERN=$?; echo $TRTOK; exit $ERN; }
    echo $RES
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

#-------------------------------------------------------------------------------
# playlist related functions
#-------------------------------------------------------------------------------
PLID="123456789abcdef8"

createPlaylistAndEdit() {
    PLID=$1 ; shift
    echo -n "# createPlaylist: "
    $XR_CLI createPlaylist $SESSID $PLID "newPlaylist.xml" || exit $?
    DATE=`date '+%H:%M:%S'`
    [[ "x$1" != "x" ]] && PLAYLIST=`cat "$1"` || PLAYLIST="<?xml
 version=\"1.0\"  encoding=\"UTF-8\"?>
<playlist id=\"123456789abcdea1\"><metadata>
  <dc:title>XY $DATE</dc:title>
  <dcterms:extent>00:00:01.000000</dcterms:extent>
</metadata>
<playlistElement id=\"123456789abcdef1\" relativeOffset=\"0\">
   <audioClip id=\"123456789abcdefa\"/>
</playlistElement>
<playlistElement id=\"123456789abcdef2\" relativeOffset=\"12\">
   <audioClip id=\"123456789abcdefb\"/>
</playlistElement>
</playlist>"
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
    PLID=$1 ; shift
    echo -n "# deletePlaylist (${PLID}): "
    $XR_CLI deletePlaylist $SESSID $PLID 1
    # || exit $?
    echo "#  status: $?"
}


#-------------------------------------------------------------------------------
# executable part
#-------------------------------------------------------------------------------
GUNID_="a23456789abcdef3"
PLID_=$GUNID_
MEDIA_="../tests/ex1.mp3"

login
for i in 0000000000010001 0000000000010002; do echo $i
    storeOpenClose $i "../tests/$i" "../tests/$i.xml"
done

deletePlaylist $PLID_
deletePlaylist $PLID_
createPlaylistAndEdit $PLID_ "../tests/0000000000000001.xml"

upload2Hub $PLID_
for i in $(seq 5); do getTransportInfo $TRTOK; sleep 1; done

#sleep 10
for i in 0000000000010001 0000000000010002; do echo $i
    deleteAudioClip $i
done
deletePlaylist $PLID_

echo "STOP - press ENTER"; read key

downloadFromHub $PLID_
for i in $(seq 5); do getTransportInfo $TRTOK; sleep 1; done

logout

echo "#Transport test: OK"
exit 0
