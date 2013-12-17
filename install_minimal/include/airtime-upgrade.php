<?php
/**
 * @package Airtime
 * @subpackage StorageServer
 * @copyright 2010 Sourcefabric O.P.S.
 * @license http://www.gnu.org/licenses/gpl.txt
 */
if (posix_geteuid() != 0) {
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
    if (getenv("AIRTIME_INSTALL_DEBUG") === "1") {
        echo "Press Enter to Continue".PHP_EOL;
        fgets(STDIN);
    }
}

AirtimeInstall::DbConnect(true);
$con = Propel::getConnection();

$version = AirtimeInstall::GetVersionInstalled();

//Enforce a minimum PHP version
if (!AirtimeInstall::checkPHPVersion())
{
    exit(1);
}

echo "******************************** Upgrade Begin *********************************".PHP_EOL;

$CC_CONFIG = Config::getConfig();
$user = $CC_CONFIG['dsn']['username'];
$password = $CC_CONFIG['dsn']['password'];
$host = $CC_CONFIG['dsn']['hostspec'];
$database = $CC_CONFIG['dsn']['database'];
$airtime_version = AIRTIME_VERSION;

$target_dir = trim(getenv("HOME"));
if (strlen($target_dir) == 0) {
    $target_dir = "/tmp";
}

$target_file = "/airtime_$airtime_version.sql";
$target_path = $target_dir.$target_file;
echo "* Backing up current database to $target_path".PHP_EOL;
exec("export PGPASSWORD=$password && pg_dump -h $host -U $user -f $target_path $database", $arr, $return_code);
if ($return_code == 0) {
    echo " * Success".PHP_EOL;
} else {
    echo " * Failed".PHP_EOL;
    exit(1);
}

//convert strings like 1.9.0-devel to 1.9.0
$version = substr($version, 0, 5);

$SCRIPTPATH = __DIR__;

if (strcmp($version, "2.2.0") < 0) {
    echo "Unsupported Airtime version. You must upgrade from at least Airtime 2.2.0.".PHP_EOL;
    exit(1);
}
if (strcmp($version, "2.2.1") < 0) {
    passthru("php --php-ini $SCRIPTPATH/../airtime-php.ini $SCRIPTPATH/../upgrades/airtime-2.2.1/airtime-upgrade.php");
    pause();
}
if (strcmp($version, "2.3.0") < 0) {
    passthru("php --php-ini $SCRIPTPATH/../airtime-php.ini $SCRIPTPATH/../upgrades/airtime-2.3.0/airtime-upgrade.php");
    pause();
}
if (strcmp($version, "2.3.1") < 0) {
    passthru("php --php-ini $SCRIPTPATH/../airtime-php.ini $SCRIPTPATH/../upgrades/airtime-2.3.1/airtime-upgrade.php");
    pause();
}
if (strcmp($version, "2.4.0") < 0) {
    passthru("php --php-ini $SCRIPTPATH/../airtime-php.ini $SCRIPTPATH/../upgrades/airtime-2.4.0/airtime-upgrade.php");
    pause();
}
if (strcmp($version, "2.4.1") < 0) {
	passthru("php --php-ini $SCRIPTPATH/../airtime-php.ini $SCRIPTPATH/../upgrades/airtime-2.4.1/airtime-upgrade.php");
	pause();
}
if (strcmp($version, "2.5.0") < 0) {
    passthru("php --php-ini $SCRIPTPATH/../airtime-php.ini $SCRIPTPATH/../upgrades/airtime-2.5.0/airtime-upgrade.php");
    pause();
}
if (strcmp($version, "2.5.1") < 0) {
    passthru("php --php-ini $SCRIPTPATH/../airtime-php.ini $SCRIPTPATH/../upgrades/airtime-2.5.1/airtime-upgrade.php");
    pause();
}
echo "******************************* Upgrade Complete *******************************".PHP_EOL;
