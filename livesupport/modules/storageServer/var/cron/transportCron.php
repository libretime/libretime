#!/usr/bin/php -q
<?php
require_once "../conf.php";
require_once "DB.php";
require_once '../LocStor.php';

PEAR::setErrorHandling(PEAR_ERROR_RETURN);
$dbc = DB::connect($config['dsn'], TRUE);
$dbc->setFetchMode(DB_FETCHMODE_ASSOC);

$gb = &new LocStor(&$dbc, $config);

$res = $gb->cronJob();

var_dump($res);
?>