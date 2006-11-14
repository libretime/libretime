<?php
header("Content-type: text/plain");
echo "\n#StorageServer storeWebstream  test:\n";

require_once '../conf.php';
require_once 'DB.php';
require_once '../GreenBox.php';

#PEAR::setErrorHandling(PEAR_ERROR_PRINT, "%s<hr>\n");
PEAR::setErrorHandling(PEAR_ERROR_RETURN);
$dbc = DB::connect($config['dsn'], TRUE);
$dbc->setFetchMode(DB_FETCHMODE_ASSOC);
$gb = new GreenBox($dbc, $config);

#$gunid = "123456789abcdee0";
$gunid = "";
#$mdataFileLP = '../tests/mdata1.xml';
$mdataFileLP = NULL;

echo"#  Login: ".($sessid = $gb->login('root', 'q'))."\n";
$parid = $gb->_getHomeDirId($sessid);

echo"#  storeWebstream: "; $r = $gb->storeWebstream(
    $parid, 'test stream', $mdataFileLP, $sessid, $gunid, 'http://localhost/y');
if(PEAR::isError($r)){ echo "ERROR: ".$r->getMessage()." ".$r->getUserInfo()."\n"; exit(1); }
echo ""; var_dump($r);
#$id = $gb->_idFromGunid($gunid);
$id = $r;

echo"#  getMdata: "; $r = $gb->getMdata($id, $sessid);
if(PEAR::isError($r)){ echo "ERROR: ".$r->getMessage()." ".$r->getUserInfo()."\n"; exit(1); }
echo "\n$r\n";

echo"#  deleteFile: "; $r = $gb->deleteFile($id, $sessid);
if(PEAR::isError($r)){ echo "ERROR: ".$r->getMessage()." ".$r->getUserInfo()."\n"; exit(1); }
echo "\n$r\n";

echo"#  logout: "; $r = $gb->logout($sessid);
if(PEAR::isError($r)){ echo "ERROR: ".$r->getMessage()."\n"; exit(1); }
echo "$r\n";

echo "#storeWebstream test: OK.\n\n"
?>