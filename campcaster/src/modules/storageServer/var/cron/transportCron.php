#!/usr/bin/php
<?php
require_once(dirname(__FILE__).'/../conf.php');
require_once('DB.php');
require_once(dirname(__FILE__).'/../LocStor.php');

PEAR::setErrorHandling(PEAR_ERROR_RETURN);
$CC_DBC = DB::connect($CC_CONFIG['dsn'], TRUE);
$CC_DBC->setErrorHandling(PEAR_ERROR_RETURN);
$CC_DBC->setFetchMode(DB_FETCHMODE_ASSOC);

$gb = new LocStor();
$tr = new Transport($gb);

$r = $tr->cronMain();
if (!$r) {
    exit(1);
}

exit(0);
?>