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
 
 
    Author   : $Author: tomas $
    Version  : $Revision: 1.5 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/storageServer/var/tests/transTest.php,v $

------------------------------------------------------------------------------*/
header("Content-type: text/plain");
echo "\n# archiveServer transport test:\n";

require_once '../conf.php';
require_once 'DB.php';
require_once '../LocStor.php';

#PEAR::setErrorHandling(PEAR_ERROR_PRINT, "%s<hr>\n");
PEAR::setErrorHandling(PEAR_ERROR_RETURN);
$dbc = DB::connect($config['dsn'], TRUE);
$dbc->setFetchMode(DB_FETCHMODE_ASSOC);
$gb = &new LocStor($dbc, $config);
$tr = &new Transport($gb->dbc, $gb, $gb->config);
@unlink("{$tr->transDir}/log");

$gunid     = 'a23456789abcdefa';
$mediaFile = '../tests/ex1.mp3';
$mdataFile = '../tests/mdata1.xml';

/* ========== UPLOAD ========== */
/*
*/
echo"#  Login: ".($sessid = $gb->login('root', 'q'))."\n";

echo"#  Store: ";
$parid = $gb->_getHomeDirId($sessid);
$oid = $gb->bsPutFile($parid, "xx1.mp3", $mediaFile, $mdataFile, $gunid, 'audioclip');
if(PEAR::isError($oid)){ echo "ERROR: ".$oid->getMessage()."\n"; exit(1); }
$comm = "ls -l {$gb->storageDir}/a23"; echo `$comm`;
echo "$oid\n";

echo"#  Transport uploadToArchive: ";
$r = $tr->uploadToArchive($gunid, $sessid);
if(PEAR::isError($r)){ echo "ERROR: ".$r->getUserInfo()."\n"; exit(1); }
var_dump($r);

echo"#  logout: "; $r = $gb->logout($sessid);
if(PEAR::isError($r)){ echo "ERROR: ".$r->getMessage()."\n"; exit(1); }
echo "$r\n";

foreach(array(1,2,3) as $nu){
    echo"#  Transport: uploadCron: "; $r = $tr->uploadCron();
    if(PEAR::isError($r)){
        echo "ERROR: ".$r->getMessage()."/".$r->getUserInfo()."\n"; exit(1);
    }
    var_dump($r);
}

echo"#  Login: ".($sessid = $gb->login('root', 'q'))."\n";
echo"#  Delete: ";    $r = $gb->deleteAudioClip($sessid, $gunid);
if(PEAR::isError($r)){ echo "ERROR: ".$r->getMessage()."\n"; exit(1); }
echo "$r\n";
echo"#  logout: "; $r = $gb->logout($sessid);
if(PEAR::isError($r)){ echo "ERROR: ".$r->getMessage()."\n"; exit(1); }
echo "$r\n";
$comm = "ls -l {$gb->storageDir}/a23"; echo `$comm`;

#echo `tail -n 20 ../trans/log`; exit;

/* === DOWNLOAD === */
/*
*/
echo"#  Login: ".($sessid = $gb->login('root', 'q'))."\n";

echo"#  Transport downloadFromArchive: ";
$r = $tr->downloadFromArchive($gunid, $sessid);
if(PEAR::isError($r)){ echo "ERROR: ".$r->getUserInfo()."\n"; exit(1); }
var_dump($r);

echo"#  logout: "; $r = $gb->logout($sessid);
if(PEAR::isError($r)){ echo "ERROR: ".$r->getMessage()."\n"; exit(1); }
echo "$r\n";

foreach(array(1,2,3) as $nu){
    echo"#  Transport: downloadCron: "; $r = $tr->downloadCron();
    if(PEAR::isError($r)){
        echo "ERROR: ".$r->getMessage()."/".$r->getUserInfo()."\n"; exit(1);
    }
    var_dump($r);
}
$comm = "ls -l {$gb->storageDir}/a23"; echo `$comm`;

echo `tail -n 20 ../trans/log`; exit;


/*
*/

/*
echo"#  Transport loginToArchive: "; $r = $tr->loginToArchive();
if(PEAR::isError($r)){ echo "ERROR: ".$r->getMessage()."\n"; exit(1); }
var_dump($r['sessid']);

echo"#  Transport logoutFromArchive: "; $r = $tr->logoutFromArchive($r['sessid']);
if(PEAR::isError($r)){ echo "ERROR: ".$r->getMessage()."\n"; exit(1); }
var_dump($r['status']);

echo"#  Ping: ";
$r = $tr->pingToArchive();
if(PEAR::isError($r)){ echo "ERROR: ".$r->getMessage()."\n"; exit(1); }
var_dump($r);

echo"#  Delete: ";    $r = $gb->deleteAudioClip($sessid, $gunid);
if(PEAR::isError($r)){ echo "ERROR: ".$r->getMessage()."\n"; exit(1); }
echo "$r\n";
*/

echo "#Transport test: OK.\n\n"
?>