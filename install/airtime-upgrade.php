<?php
/**
 * @package Airtime
 * @subpackage StorageServer
 * @copyright 2010 Sourcefabric O.P.S.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

require_once(dirname(__FILE__).'/include/AirtimeIni.php');

AirtimeIni::ExitIfNotRoot();

if(file_exists(dirname(__FILE__).'/../VERSION')) {
    $version = file_get_contents(dirname(__FILE__).'/../VERSION');
    echo "Airtime Version: ".$version." ".PHP_EOL;
}
else if(AirtimeInstall::DbTableExists('cc_show_rebroadcast') === true) {
    $version = "1.7.0";
    echo "Airtime Version: ".$version." ".PHP_EOL;
}
else {
    $version = "1.6";
    echo "Airtime Version: ".$version." ".PHP_EOL;
}

echo "******************************** Update Begin *********************************".PHP_EOL;

if(strcmp($version, "1.7.0") < 0) {
    system("php ".__DIR__."/upgrades/airtime-1.7/airtime-upgrade.php");
}
if(strcmp($version, "1.8.0") < 0) {
    system("php ".__DIR__."/upgrades/airtime-1.8/airtime-upgrade.php");
}

echo "******************************* Update Complete *******************************".PHP_EOL;


