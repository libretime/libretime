<?php
/*------------------------------------------------------------------------------

    Copyright (c) 2004 Media Development Loan Fund
 
    This file is part of the LiveSupport project.
    http://livesupport.campware.org/
    To report bugs, send an e-mail to bugs@campware.org
 
    LiveSupport is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.
  
    LiveSupport is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
 
    You should have received a copy of the GNU General Public License
    along with LiveSupport; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 
 
    Author   : $Author$
    Version  : $Revision$
    Location : $URL$

------------------------------------------------------------------------------*/
header("Content-type: text/plain");
echo "\n# Transport test:\n";

require_once '../conf.php';
require_once 'DB.php';
require_once '../GreenBox.php';
require_once '../LocStor.php';

#PEAR::setErrorHandling(PEAR_ERROR_PRINT, "%s<hr>\n");
PEAR::setErrorHandling(PEAR_ERROR_RETURN);
$dbc = DB::connect($config['dsn'], TRUE);
$dbc->setFetchMode(DB_FETCHMODE_ASSOC);
$gb = &new GreenBox($dbc, $config);
$tr = &new Transport($gb);
$ls = &new LocStor($dbc, $config);
@unlink("{$tr->transDir}/activity.log");
@unlink("{$tr->transDir}/debug.log");
$tr->_cleanUp();

$gunid     = 'a23456789abcdefb';
$mediaFile = '../tests/ex1.mp3';
$mdataFile = '../tests/mdata1.xml';

/* ========== PING ========== */
/*
echo"#  Login: ".($sessid = $gb->login('root', 'q'))."\n";

echo"#  Ping: ";
$r = $tr->pingToArchive();
if(PEAR::isError($r)){ echo "ERROR: ".$r->getMessage()."\n"; exit(1); }
var_export($r); echo"\n";

echo"#  logout: "; $r = $gb->logout($sessid);
if(PEAR::isError($r)){ echo "ERROR: ".$r->getMessage()."\n"; exit(1); }
echo "$r\n";
*/
/* ========== STORE ========== */
echo "#  UPLOAD test:\n";
echo"#  Login: ".($sessid = $gb->login('root', 'q'))."\n";

echo"#  Store: ";
$parid = $gb->_getHomeDirIdFromSess($sessid);
$oid = $r = $gb->bsPutFile($parid, "xx1.mp3", $mediaFile, $mdataFile, $gunid, 'audioclip');
if(PEAR::isError($r)){ if($r->getCode()!=GBERR_GUNID){ echo "ERROR: ".$r->getMessage()."\n"; exit(1); }}
$comm = "ls -l {$gb->storageDir}/a23"; echo `$comm`;
echo "$oid\n";

/* ========== DELETE FROM HUB ========== */
echo"#  loginToArchive: ";
$r = $tr->loginToArchive();
if(PEAR::isError($r)){ echo "ERROR: ".$r->getMessage()." / ".$r->getUserInfo()."\n"; exit(1); }
echo "{$r['sessid']}\n";
$asessid = $r['sessid'];
echo"#  deleteAudioClip on Hub: ";
$r = $tr->xmlrpcCall(
    'archive.deleteAudioClip',
    array(
        'sessid'    => $asessid,
        'gunid'     => $gunid,
        'forced'  => TRUE,
    )
);
if(PEAR::isError($r)){ echo "ERROR: ".$r->getMessage()." / ".$r->getUserInfo()."\n"; if($r->getCode()!=800+GBERR_FILENEX) exit(1); }
else{ echo " {$r['status']}\n"; }
echo"#  logoutFromArchive: ";
$r = $tr->logoutFromArchive($asessid);
if(PEAR::isError($r)){ echo "ERROR: ".$r->getMessage()." / ".$r->getUserInfo()."\n"; exit(1); }
var_export($r); echo"\n";


/* ========== UPLOAD ========== */
echo"#  uploadAudioClip2Hub: ";
$r = $gb->upload2Hub($gunid);
if(PEAR::isError($r)){ echo "ERROR: ".$r->getMessage()."/".$r->getUserInfo()."\n"; exit(1); }
var_export($r); echo"\n";
$trtok = $r;

echo"#  logout: "; $r = $gb->logout($sessid);
if(PEAR::isError($r)){ echo "ERROR: ".$r->getMessage()."/".$r->getUserInfo()."\n"; exit(1); }
echo "$r\n";
/*
*/
#$trtok='280a6f1c18389620';

for($state='', $nu=1; ($state!='closed' && $state!='failed' && $nu<=12); $nu++, sleep(2)){
/*
    echo"# $nu: Transport: cronMain: "; $r = $tr->cronMain();
    if(PEAR::isError($r)){ echo "ERROR: ".$r->getMessage()."/".$r->getUserInfo()."\n"; exit(1); }
    var_export($r); echo"\n";
*/
    echo"#  getTransportInfo: "; $r = $gb->getTransportInfo($trtok);
    if(PEAR::isError($r)){ echo "ERROR: ".$r->getMessage()."/".$r->getUserInfo()."\n"; exit(1); }
    $state = $r['state'];
    echo "#  state=$state, title={$r['title']}\n";
}
if($state=='failed') exit(1);

/* === DELETE LOCAL === */
echo"#  Login: ".($sessid = $gb->login('root', 'q'))."\n";
echo"#  Delete: ";    $r = $ls->deleteAudioClip($sessid, $gunid, TRUE);
if(PEAR::isError($r)){ echo "ERROR: ".$r->getMessage()."\n"; exit(1); }
echo "$r\n";
echo"#  logout: "; $r = $gb->logout($sessid);
if(PEAR::isError($r)){ echo "ERROR: ".$r->getMessage()."\n"; exit(1); }
echo "$r\n";
$comm = "ls -l {$gb->storageDir}/a23"; echo `$comm`;
/*
*/

#echo"TMPEND\n"; exit;
#echo `tail -n 25 ../trans/log`; exit;
#$sleeptime=10; echo "sleep $sleeptime\n"; sleep($sleeptime);

/* === DOWNLOAD === */
echo "#  DOWNLOAD test:\n";
echo"#  Login: ".($sessid = $gb->login('root', 'q'))."\n";

echo"#  downloadAudioClipFromHub: ";
$r = $gb->downloadFromHub($sessid, $gunid);
if(PEAR::isError($r)){ echo "ERROR: ".$r->getMessage()."/".$r->getUserInfo()."\n"; exit(1); }
var_export($r); echo"\n";
$trtok = $r;

echo"#  logout: "; $r = $gb->logout($sessid);
if(PEAR::isError($r)){ echo "ERROR: ".$r->getMessage()."\n"; exit(1); }
echo "$r\n";

for($state='', $nu=1; ($state!='closed' && $state!='failed' && $nu<=12); $nu++, sleep(2)){
/*
    echo"# $nu: Transport: cronMain: "; $r = $tr->cronMain();
    if(PEAR::isError($r)){ echo "ERROR: ".$r->getMessage()."/".$r->getUserInfo()."\n"; exit(1); }
    var_export($r); echo"\n";
*/
    echo"#  getTransportInfo: "; $r = $gb->getTransportInfo($trtok);
    if(PEAR::isError($r)){ echo "ERROR: ".$r->getMessage()."/".$r->getUserInfo()."\n"; exit(1); }
    $state = $r['state'];
    echo "#  state=$state, title={$r['title']}\n";
}
if($state=='failed') exit(1);

$comm = "ls -l {$gb->storageDir}/a23"; echo `$comm`;
/*
*/

/*
echo"#  Login: ".($sessid = $gb->login('root', 'q'))."\n";
echo"#  Delete: ";    $r = $ls->deleteAudioClip($sessid, $gunid, TRUE);
if(PEAR::isError($r)){ echo "ERROR: ".$r->getMessage()."\n"; exit(1); }
echo "$r\n";
echo"#  logout: "; $r = $gb->logout($sessid);
if(PEAR::isError($r)){ echo "ERROR: ".$r->getMessage()."\n"; exit(1); }
echo "$r\n";
*/

#echo `tail -n 20 ../trans/log`; exit;

/*
echo"#  Transport loginToArchive: "; $r = $tr->loginToArchive();
if(PEAR::isError($r)){ echo "ERROR: ".$r->getMessage()."\n"; exit(1); }
var_export($r['sessid']); echo"\n";

echo"#  Transport logoutFromArchive: "; $r = $tr->logoutFromArchive($r['sessid']);
if(PEAR::isError($r)){ echo "ERROR: ".$r->getMessage()."\n"; exit(1); }
var_export($r['status']); echo"\n";

echo"#  Ping: ";
$r = $tr->pingToArchive();
if(PEAR::isError($r)){ echo "ERROR: ".$r->getMessage()."\n"; exit(1); }
var_export($r); echo"\n";

echo"#  Delete: ";    $r = $gb->deleteAudioClip($sessid, $gunid);
if(PEAR::isError($r)){ echo "ERROR: ".$r->getMessage()."\n"; exit(1); }
echo "$r\n";
*/

if(file_exists("../trans/log")) echo `tail -n 25 ../trans/log`;
echo "#Transport test: OK.\n\n"
?>