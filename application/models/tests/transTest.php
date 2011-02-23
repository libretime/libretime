<?php
/*
header("Content-type: text/plain");

require_once('../../conf.php');
require_once('DB.php');
require_once('../GreenBox.php');
require_once('../LocStor.php');

#PEAR::setErrorHandling(PEAR_ERROR_PRINT, "%s<hr>\n");
PEAR::setErrorHandling(PEAR_ERROR_RETURN);
$CC_DBC = DB::connect($CC_CONFIG['dsn'], TRUE);
$CC_DBC->setFetchMode(DB_FETCHMODE_ASSOC);
$gb = new GreenBox();
$tr = new Transport($gb);
$ls = new LocStor();
@unlink($CC_CONFIG['transDir']."/activity.log");
@unlink($CC_CONFIG['transDir']."/debug.log");
$tr->_cleanUp();

$gunid     = 'a23456789abcdefb';
$mediaFile = '../tests/ex1.mp3';
$mdataFile = '../tests/mdata1.xml';

// Test remote search
$result = $tr->remoteSearch("");
if (PEAR::isError($result)) {
    echo $result->message."\n";
} else {
    var_dump($result);
}

// ========== STORE ==========

echo"#  Store: ";
//$parid = $gb->_getHomeDirIdFromSess($sessid);
$values = array(
    "filename" => "xx1.mp3",
    "filepath" => $mediaFile,
    "metadata" => $mdataFile,
    "gunid" => $gunid,
    "filetype" => "audioclip"
);
$storedFile = StoredFile::Insert($values);
if (PEAR::isError($storedFile)) {
    if ($storedFile->getCode()!=GBERR_GUNID) {
        echo "ERROR: ".$storedFile->getMessage()."\n";
        exit(1);
    }
}
$oid = $storedFile->getId();
$comm = "ls -l ".$CC_CONFIG['storageDir']."/a23"; echo `$comm`;
echo "$oid\n";

// ========== DELETE FROM HUB ==========
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


// ========== UPLOAD ==========
echo "#  UPLOAD test:\n";
echo"#  uploadAudioClip2Hub: ";
$r = $gb->upload2Hub($gunid);
if(PEAR::isError($r)){ echo "ERROR: ".$r->getMessage()."/".$r->getUserInfo()."\n"; exit(1); }
var_export($r); echo"\n";
$trtok = $r;

echo"#  logout: "; $r = Alib::Logout($sessid);
if(PEAR::isError($r)){ echo "ERROR: ".$r->getMessage()."/".$r->getUserInfo()."\n"; exit(1); }
echo "$r\n";
#$trtok='280a6f1c18389620';

for($state='', $nu=1; ($state!='closed' && $state!='failed' && $nu<=12); $nu++, sleep(2)){
    echo"#  getTransportInfo: "; $r = $gb->getTransportInfo($trtok);
    if(PEAR::isError($r)){ echo "ERROR: ".$r->getMessage()."/".$r->getUserInfo()."\n"; exit(1); }
    $state = $r['state'];
    echo "#  state=$state, title={$r['title']}\n";
}
if($state=='failed') exit(1);

// === DELETE LOCAL ===
echo "#  Login: ".($sessid = Alib::Login('root', 'q'))."\n";
echo "#  Delete: ";    $r = $ls->deleteAudioClip($sessid, $gunid, TRUE);
if (PEAR::isError($r)) {
    echo "ERROR: ".$r->getMessage()."\n";
    exit(1);
}
echo "$r\n";
echo "#  logout: "; $r = Alib::Logout($sessid);
if (PEAR::isError($r)) {
    echo "ERROR: ".$r->getMessage()."\n";
    exit(1);
}
echo "$r\n";
$comm = "ls -l ".$CC_CONFIG['storageDir']."/a23";
echo `$comm`;

// === DOWNLOAD ===
echo "#  DOWNLOAD test:\n";
echo"#  Login: ".($sessid = Alib::Login('root', 'q'))."\n";

echo"#  downloadAudioClipFromHub: ";
$r = $gb->downloadFromHub($sessid, $gunid);
if(PEAR::isError($r)){ echo "ERROR: ".$r->getMessage()."/".$r->getUserInfo()."\n"; exit(1); }
var_export($r); echo"\n";
$trtok = $r;

echo"#  logout: "; $r = Alib::Logout($sessid);
if(PEAR::isError($r)){ echo "ERROR: ".$r->getMessage()."\n"; exit(1); }
echo "$r\n";

for($state='', $nu=1; ($state!='closed' && $state!='failed' && $nu<=12); $nu++, sleep(2)){
    echo"#  getTransportInfo: "; $r = $gb->getTransportInfo($trtok);
    if(PEAR::isError($r)){ echo "ERROR: ".$r->getMessage()."/".$r->getUserInfo()."\n"; exit(1); }
    $state = $r['state'];
    echo "#  state=$state, title={$r['title']}\n";
}
if($state=='failed') exit(1);

$comm = "ls -l ".$CC_CONFIG['storageDir']."/a23"; echo `$comm`;

if(file_exists("../trans/log")) echo `tail -n 25 ../trans/log`;
echo "#Transport test: OK.\n\n";
*/
