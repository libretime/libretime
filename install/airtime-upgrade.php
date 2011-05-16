<?php
/**
 * @package Airtime
 * @subpackage StorageServer
 * @copyright 2010 Sourcefabric O.P.S.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

require_once(dirname(__FILE__).'/include/AirtimeIni.php');
set_include_path(__DIR__.'/../airtime_mvc/library' . PATH_SEPARATOR . get_include_path());
require_once __DIR__.'/../airtime_mvc/application/configs/conf.php';
require_once(dirname(__FILE__).'/include/AirtimeInstall.php');

AirtimeInstall::ExitIfNotRoot();
AirtimeInstall::DbConnect(true);

$version = AirtimeInstall::GetAirtimeVersion();
if (!$version){
    if(AirtimeInstall::DbTableExists('cc_show_rebroadcast') === true) {
        $version = "1.7.0";
        echo "Airtime Version: ".$version." ".PHP_EOL;
    }
    else {
        $version = "1.6";
        echo "Airtime Version: ".$version." ".PHP_EOL;
    }
}

echo "******************************** Update Begin *********************************".PHP_EOL;

if(strcmp($version, "1.7.0") < 0) {
    system("php ".__DIR__."/upgrades/airtime-1.7/airtime-upgrade.php");
}
if(strcmp($version, "1.8.0") < 0) {
    system("php ".__DIR__."/upgrades/airtime-1.8/airtime-upgrade.php");
}
if(strcmp($version, "1.8.1") < 0) {
    system("php ".__DIR__."/upgrades/airtime-1.8.1/airtime-upgrade.php");
}
if(strcmp($version, "1.8.2") < 0) {
    system("php ".__DIR__."/upgrades/airtime-1.8.2/airtime-upgrade.php");
}
//if (strcmp($version, "1.9.0") < 0){
    //system("php ".__DIR__."/upgrades/airtime-1.9/airtime-upgrade.php");
//}

AirtimeInstall::SetAirtimeVersion(AIRTIME_VERSION);

echo PHP_EOL."*** Updating Pypo ***".PHP_EOL;
system("python ".__DIR__."/../python_apps/pypo/install/pypo-install.py");

echo PHP_EOL."*** Updating Recorder ***".PHP_EOL;
system("python ".__DIR__."/../python_apps/show-recorder/install/recorder-install.py");

echo "******************************* Update Complete *******************************".PHP_EOL;


