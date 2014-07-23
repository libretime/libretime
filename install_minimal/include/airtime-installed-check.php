<?php
/**
 * Checks if a current version of Airtime is installed.
 * If so, the user is presented with the help menu and can
 * choose -r to reinstall.
 *
 * Returns 0 if Airtime is not installed
 * Returns 1 if the same version of Airtime already installed
 * Returns 2 if a previous version of Airtime is installed we can upgrade from
 * Returns 3 if a version of Airtime is installed that we can't upgrade from.
 */
require_once(__DIR__.'/AirtimeInstall.php');
require_once(__DIR__.'/airtime-constants.php');

AirtimeInstall::ExitIfNotRoot();

if (!file_exists('/etc/airtime/airtime.conf')) {
    #airtime.conf doesn't exist, and we need it to connect to database
    #Assume Airtime is not installed.
    exit(0);
}

require_once(AirtimeInstall::GetAirtimeSrcDir()."/application/configs/db-conf.php");
$CC_CONFIG = Config::getConfig();
require_once('vendor/propel/propel1/runtime/lib/Propel.php');
Propel::init(AirtimeInstall::GetAirtimeSrcDir()."/application/configs/airtime-conf-production.php");

$version = AirtimeInstall::GetVersionInstalled();

// The current version is already installed.
echo "* Checking for existing Airtime installation...".PHP_EOL;
if (isset($version)){
    if (is_null($version)){
        //version of Airtime older than 1.7.0 detected
        exit(3);
    } else {
        if (($version == AIRTIME_VERSION)) {
            //same version of Airtime is already installed
            exit(1);
        } else if (strcmp($version, AIRTIME_VERSION) < 0){
            //previous version of Airtime is installed.
            exit(2);
        }
    }
} else {
    //no previous version of Airtime found
    exit(0);
}
