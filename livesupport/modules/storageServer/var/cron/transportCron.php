#!/usr/bin/php -q
<?php
chdir(dirname(__FILE__));
require_once "../conf.php";
require_once "DB.php";
require_once '../LocStor.php';

PEAR::setErrorHandling(PEAR_ERROR_RETURN);
$dbc = DB::connect($config['dsn'], TRUE);
$dbc->setFetchMode(DB_FETCHMODE_ASSOC);

$gb = &new LocStor(&$dbc, $config);
$tr =& new Transport(&$dbc, &$gb, $config);
$cnt = 1;

#$res = $gb->cronJob();
#var_dump($res);

for($i=0; $i<$cnt; $i++){
    $r = $tr->uploadCron();
    if(!$r) exit(1);
}

for($i=0; $i<$cnt; $i++){
    $r = $tr->downloadCron();
    if(!$r) exit(1);
}

exit(0);
?>