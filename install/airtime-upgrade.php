<?php
/**
 * @package Airtime
 * @subpackage StorageServer
 * @copyright 2010 Sourcefabric O.P.S.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

require_once(dirname(__FILE__).'/include/AirtimeIni.php');

AirtimeIni::ExitIfNotRoot();

echo "******************************** Update Begin *********************************".PHP_EOL;

//system("php ".__DIR__."/upgrades/airtime-1.7/airtime-upgrade.php");
system("php ".__DIR__."/upgrades/airtime-1.8/airtime-upgrade.php");

echo "******************************* Update Complete *******************************".PHP_EOL;


