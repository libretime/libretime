<?php
require_once "../conf.php";
require_once "DB.php";
require_once "../Transport.php";

PEAR::setErrorHandling(PEAR_ERROR_RETURN);
$dbc = DB::connect($config['dsn'], TRUE);
$dbc->setFetchMode(DB_FETCHMODE_ASSOC);

$tr =& new Transport(&$dbc, $config);

$gunid = "AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA";

#$r = $tr->install();                       var_dump($r);
#$r = $tr->uninstall();                     var_dump($r);
#$r = $tr->getTransportStatus();            var_dump($r);

echo "uploadFile:\n";
$r = $tr->uploadFile('ex2.wav', $gunid);    var_dump($r);
echo "uploadCron:\n";
$r = $tr->uploadCron();                     var_dump($r);

echo "downloadFile:\n";
$r = $tr->downloadFile($gunid);            var_dump($r);
echo "downloadCron:\n";
$r = $tr->downloadCron();                  var_dump($r);

#$r = $tr->uploadAbort(1);                  var_dump($r);

?>