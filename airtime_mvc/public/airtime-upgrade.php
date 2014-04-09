<?php

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));
//include index.php so we can use propel classes
require_once APPLICATION_PATH.'/../public/index.php';

require_once 'DbUpgrade.php';
require_once 'StorageQuotaUpgrade.php';

$filename = "/etc/airtime/airtime.conf";
$values = parse_ini_file($filename, true);

AirtimeDatabaseUpgrade::start($values);
StorageQuotaUpgrade::startUpgrade();
