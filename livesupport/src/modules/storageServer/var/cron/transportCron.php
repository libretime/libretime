#!/usr/bin/php
<?php
require_once dirname(__FILE__).'/../conf.php';
require_once 'DB.php';
require_once dirname(__FILE__).'/../LocStor.php';

PEAR::setErrorHandling(PEAR_ERROR_RETURN);
$dbc = DB::connect($config['dsn'], TRUE);
$dbc->setErrorHandling(PEAR_ERROR_RETURN);
$dbc->setFetchMode(DB_FETCHMODE_ASSOC);

$gb =& new LocStor($dbc, $config);
$tr =& new Transport($gb);

$r = $tr->cronMain();
if(!$r) exit(1);

exit(0);
?>