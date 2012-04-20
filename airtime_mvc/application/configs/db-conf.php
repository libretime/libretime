<?php

/* This file is only needed during upgrades when we need the database parameters from /etc/airtime/airtime.conf.
 * The reason we don't just use conf.php is because conf.php may try to load configuration parameters that aren't
 * yet available because airtime.conf hasn't been updated yet. This situation ends up throwing a lot of errors to stdout.
 * airtime*/

global $CC_CONFIG;

$filename = "/etc/airtime/airtime.conf";
$values = parse_ini_file($filename, true);

// Database config
$CC_CONFIG['dsn']['username'] = $values['database']['dbuser'];
$CC_CONFIG['dsn']['password'] = $values['database']['dbpass'];
$CC_CONFIG['dsn']['hostspec'] = $values['database']['host'];
$CC_CONFIG['dsn']['phptype'] = 'pgsql';
$CC_CONFIG['dsn']['database'] = $values['database']['dbname'];
