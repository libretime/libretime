<?
header("Content-type: text/plain");
echo "\nTransport test\n\n";

require_once '../conf.php';
require_once 'DB.php';
require_once '../LocStor.php';

#PEAR::setErrorHandling(PEAR_ERROR_PRINT, "%s<hr>\n");
PEAR::setErrorHandling(PEAR_ERROR_RETURN);
$dbc = DB::connect($config['dsn'], TRUE);
$dbc->setFetchMode(DB_FETCHMODE_ASSOC);
$gb = &new LocStor(&$dbc, $config);

$gunid     = 'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA';
$mediaFile = '../tests/ex1.mp3';
$mdataFile = '../tests/mdata1.xml';

echo"login: ".($sessid = $gb->login('root', 'q'))."\n";

echo"Store:\n";
$r = $gb->storeAudioClip($sessid, $gunid, $mediaFile, $mdataFile);      var_dump($r);
                                     
echo"Upload:\n"; $r = $gb->uploadFile('', $gunid, $sessid);             var_dump($r);

echo"Cron:\n"; $r = $gb->cronJob();                                     var_dump($r);

echo"Delete:\n";    $r = $gb->deleteAudioClip($sessid, $gunid);         var_dump($r);

echo"Download:\n"; $r = $gb->downloadFile($gunid, $sessid);             var_dump($r);

echo"Cron:\n"; $r = $gb->cronJob();                                     var_dump($r);

echo"logout: ".($r = $gb->logout($sessid))."\n";

echo"\n";
?>