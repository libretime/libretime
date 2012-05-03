<?php
/**
 * @package Airtime
 * @subpackage StorageServer
 * @copyright 2010 Sourcefabric O.P.S.
 * @license http://www.gnu.org/licenses/gpl.txt
 */
require_once(__DIR__.'/airtime-constants.php');
require_once(dirname(__FILE__).'/AirtimeIni.php');
require_once(dirname(__FILE__).'/AirtimeInstall.php');

if(posix_geteuid() != 0) {
    echo "Must be root user.\n";
    exit(1);
}

require_once(__DIR__.'/airtime-constants.php');
require_once(__DIR__.'/AirtimeIni.php');
require_once(__DIR__.'/AirtimeInstall.php');
require_once 'propel/runtime/lib/Propel.php';
Propel::init(AirtimeInstall::GetAirtimeSrcDir()."/application/configs/db-conf.php");
Propel::init(AirtimeInstall::GetAirtimeSrcDir()."/application/configs/airtime-conf-production.php");


function pause()
{
    /* Type "sudo -s" to change to root user then type "export AIRTIME_INSTALL_DEBUG=1" and then
     * start airtime-install to enable this feature. Is used to pause between upgrade scripts
     * to examine the state of the system and see if everything is as expected. */
    if (getenv("AIRTIME_INSTALL_DEBUG") === "1"){
        echo "Press Enter to Continue".PHP_EOL;
        fgets(STDIN);
    }
}

AirtimeInstall::DbConnect(true);
$con = Propel::getConnection();

$version = AirtimeInstall::GetVersionInstalled();

echo "******************************** Upgrade Begin *********************************".PHP_EOL;

//convert strings like 1.9.0-devel to 1.9.0
$version = substr($version, 0, 5);

$SCRIPTPATH = __DIR__;

if (strcmp($version, "1.9.0") < 0){
    echo "Unsupported Airtime version. You must upgrade from at least Airtime 1.9.0.".PHP_EOL;
    exit(1);
}
if (strcmp($version, "1.9.2") < 0){
    passthru("php --php-ini $SCRIPTPATH/../airtime-php.ini $SCRIPTPATH/../upgrades/airtime-1.9.2/airtime-upgrade.php");
    pause();
}
if (strcmp($version, "1.9.3") < 0){
    passthru("php --php-ini $SCRIPTPATH/../airtime-php.ini $SCRIPTPATH/../upgrades/airtime-1.9.3/airtime-upgrade.php");
    pause();
}
if (strcmp($version, "1.9.4") < 0){
    passthru("php --php-ini $SCRIPTPATH/../airtime-php.ini $SCRIPTPATH/../upgrades/airtime-1.9.4/airtime-upgrade.php");
    pause();
}
if (strcmp($version, "1.9.5") < 0){
    passthru("php --php-ini $SCRIPTPATH/../airtime-php.ini $SCRIPTPATH/../upgrades/airtime-1.9.5/airtime-upgrade.php");
    pause();
}
if (strcmp($version, "2.0.0") < 0){
    passthru("php --php-ini $SCRIPTPATH/../airtime-php.ini $SCRIPTPATH/../upgrades/airtime-2.0.0/airtime-upgrade.php");
    pause();
}
if (strcmp($version, "2.0.1") < 0){
    passthru("php --php-ini $SCRIPTPATH/../airtime-php.ini $SCRIPTPATH/../upgrades/airtime-2.0.1/airtime-upgrade.php");
    pause();
}
if (strcmp($version, "2.0.2") < 0){
    passthru("php --php-ini $SCRIPTPATH/../airtime-php.ini $SCRIPTPATH/../upgrades/airtime-2.0.2/airtime-upgrade.php");
    pause();
}
if (strcmp($version, "2.0.3") < 0){
    passthru("php --php-ini $SCRIPTPATH/../airtime-php.ini $SCRIPTPATH/../upgrades/airtime-2.0.3/airtime-upgrade.php");
    pause();
}
if (strcmp($version, "2.1.0") < 0){
    passthru("php --php-ini $SCRIPTPATH/../airtime-php.ini $SCRIPTPATH/../upgrades/airtime-2.1.0/airtime-upgrade.php");
} 

//set the new version in the database.
$sql = "DELETE FROM cc_pref WHERE keystr = 'system_version'";
$con->exec($sql);

$newVersion = AIRTIME_VERSION;
$sql = "INSERT INTO cc_pref (keystr, valstr) VALUES ('system_version', '$newVersion')";
$con->exec($sql);

echo "******************************* Upgrade Complete *******************************".PHP_EOL;
