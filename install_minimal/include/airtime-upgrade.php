<?php
/**
 * @package Airtime
 * @subpackage StorageServer
 * @copyright 2010 Sourcefabric O.P.S.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

require_once('DB.php');
require_once(__DIR__.'/airtime-constants.php');
require_once(dirname(__FILE__).'/AirtimeIni.php');
require_once(dirname(__FILE__).'/AirtimeInstall.php');

if(exec("whoami") != "root"){
    echo "Must be root user.\n";
    exit(1);
}

function pause(){
    /* Type "sudo -s" to change to root user then type "export AIRTIME_INSTALL_DEBUG=1" and then
     * start airtime-install to enable this feature. Is used to pause between upgrade scripts
     * to examine the state of the system and see if everything is as expected. */
    if (getenv("AIRTIME_INSTALL_DEBUG") === "1"){
        echo "Press Enter to Continue".PHP_EOL;
        fgets(STDIN);
    }
}

const CONF_FILE_AIRTIME = "/etc/airtime/airtime.conf";


global $CC_DBC, $CC_CONFIG;

$values = parse_ini_file('/etc/airtime/airtime.conf', true);

// Database config
$CC_CONFIG['dsn']['username'] = $values['database']['dbuser'];
$CC_CONFIG['dsn']['password'] = $values['database']['dbpass'];
$CC_CONFIG['dsn']['hostspec'] = $values['database']['host'];
$CC_CONFIG['dsn']['phptype'] = 'pgsql';
$CC_CONFIG['dsn']['database'] = $values['database']['dbname'];

$CC_DBC = DB::connect($CC_CONFIG['dsn'], FALSE);

if (PEAR::isError($CC_DBC)) {
    echo $CC_DBC->getMessage().PHP_EOL;
    echo $CC_DBC->getUserInfo().PHP_EOL;
    echo "Database connection problem.".PHP_EOL;
    echo "Check if database '{$CC_CONFIG['dsn']['database']}' exists".
        " with corresponding permissions.".PHP_EOL;
    exit(1);
} else {
    echo "* Connected to database".PHP_EOL;
    $CC_DBC->setFetchMode(DB_FETCHMODE_ASSOC);
}

$version = AirtimeInstall::GetVersionInstalled();

echo "******************************** Upgrade Begin *********************************".PHP_EOL;

//convert strings like 1.9.0-devel to 1.9.0
$version = substr($version, 0, 5);

$SCRIPTPATH = __DIR__;

if (strcmp($version, "1.7.0") < 0){
    passthru("php --php-ini $SCRIPTPATH/../airtime-php.ini $SCRIPTPATH/../upgrades/airtime-1.7.0/airtime-upgrade.php");
    pause();
}
if (strcmp($version, "1.8.0") < 0){
    passthru("php --php-ini $SCRIPTPATH/../airtime-php.ini $SCRIPTPATH/../upgrades/airtime-1.8.0/airtime-upgrade.php");
    pause();
}
if (strcmp($version, "1.8.1") < 0){
    passthru("php --php-ini $SCRIPTPATH/../airtime-php.ini $SCRIPTPATH/../upgrades/airtime-1.8.1/airtime-upgrade.php");
    pause();
}
if (strcmp($version, "1.8.2") < 0){
    passthru("php --php-ini $SCRIPTPATH/../airtime-php.ini $SCRIPTPATH/../upgrades/airtime-1.8.2/airtime-upgrade.php");
    pause();
}
if (strcmp($version, "1.9.0") < 0){
    passthru("php --php-ini $SCRIPTPATH/../airtime-php.ini $SCRIPTPATH/../upgrades/airtime-1.9.0/airtime-upgrade.php");
    pause();
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

//set the new version in the database.
$sql = "DELETE FROM cc_pref WHERE keystr = 'system_version'";
$CC_DBC->query($sql);

$values = parse_ini_file(CONF_FILE_AIRTIME, true);
$phpDir = $values['general']['airtime_dir'];

$newVersion = AIRTIME_VERSION;
$sql = "INSERT INTO cc_pref (keystr, valstr) VALUES ('system_version', '$newVersion')";
$CC_DBC->query($sql);

echo "******************************* Upgrade Complete *******************************".PHP_EOL;
