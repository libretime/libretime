<?php
/**
 * @package Airtime
 * @subpackage StorageServer
 * @copyright 2010 Sourcefabric O.P.S.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

//Pear classes.
set_include_path(__DIR__.'/../../airtime_mvc/library/pear' . PATH_SEPARATOR . get_include_path());

require_once('DB.php');
require_once(__DIR__.'/../../airtime_mvc/application/configs/constants.php');
require_once(dirname(__FILE__).'/AirtimeIni.php');
require_once(dirname(__FILE__).'/AirtimeInstall.php');

if(exec("whoami") != "root"){
    echo "Must be root user.\n";
    exit(1);
}

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

echo "******************************** Update Begin *********************************".PHP_EOL;

//convert strings like 1.9.0-devel to 1.9.0
$version = substr($version, 0, 5);

$SCRIPTPATH = __DIR__;

if (strcmp($version, "1.7.0") < 0){
    passthru("php --php-ini $SCRIPTPATH/../airtime-php.ini $SCRIPTPATH/../upgrades/airtime-1.7.0/airtime-upgrade.php");
}
if (strcmp($version, "1.8.0") < 0){
    passthru("php --php-ini $SCRIPTPATH/../airtime-php.ini $SCRIPTPATH/../upgrades/airtime-1.8.0/airtime-upgrade.php");
}
if (strcmp($version, "1.8.1") < 0){
    passthru("php --php-ini $SCRIPTPATH/../airtime-php.ini $SCRIPTPATH/../upgrades/airtime-1.8.1/airtime-upgrade.php");
}
if (strcmp($version, "1.8.2") < 0){
    passthru("php --php-ini $SCRIPTPATH/../airtime-php.ini $SCRIPTPATH/../upgrades/airtime-1.8.2/airtime-upgrade.php");
}
if (strcmp($version, "1.9.0") < 0){
    passthru("php --php-ini $SCRIPTPATH/../airtime-php.ini $SCRIPTPATH/../upgrades/airtime-1.9.0/airtime-upgrade.php");
}
if (strcmp($version, "1.9.2") < 0){
    passthru("php --php-ini $SCRIPTPATH/../airtime-php.ini $SCRIPTPATH/../upgrades/airtime-1.9.2/airtime-upgrade.php");
}
if (strcmp($version, "1.9.3") < 0){
    passthru("php --php-ini $SCRIPTPATH/../airtime-php.ini $SCRIPTPATH/../upgrades/airtime-1.9.3/airtime-upgrade.php");
}
if (strcmp($version, "1.9.4") < 0){
    passthru("php --php-ini $SCRIPTPATH/../airtime-php.ini $SCRIPTPATH/../upgrades/airtime-1.9.4/airtime-upgrade.php");
}

//set the new version in the database.
$sql = "DELETE FROM cc_pref WHERE keystr = 'system_version'";
$CC_DBC->query($sql);

$newVersion = AIRTIME_VERSION;
$sql = "INSERT INTO cc_pref (keystr, valstr) VALUES ('system_version', '$newVersion')";
$CC_DBC->query($sql);

echo PHP_EOL."*** Updating Api Client ***".PHP_EOL;
passthru("python $SCRIPTPATH/../../python_apps/api_clients/install/api_client_install.py");

echo PHP_EOL."*** Updating Pypo ***".PHP_EOL;
passthru("python $SCRIPTPATH/../../python_apps/pypo/install/pypo-install.py");

echo PHP_EOL."*** Updating Recorder ***".PHP_EOL;
passthru("python $SCRIPTPATH/../../python_apps/show-recorder/install/recorder-install.py");

echo PHP_EOL."*** Updating Media Monitor ***".PHP_EOL;
passthru("python $SCRIPTPATH/../../python_apps/media-monitor/install/media-monitor-install.py");

sleep(4);
passthru("airtime-check-system");

echo "******************************* Update Complete *******************************".PHP_EOL;
