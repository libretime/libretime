<?php

require_once 'DbUpgrade.php';
require_once 'common/UpgradeCommon.php';

$filename = "/etc/airtime/airtime.conf";
$values = parse_ini_file($filename, true);

AirtimeDatabaseUpgrade::start($values);
