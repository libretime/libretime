<?php
/**
 * @package Airtime
 * @copyright 2011 Sourcefabric O.P.S.
 * @license http://www.gnu.org/licenses/gpl.txt
 *
 * Checks if a current version of Airtime is installed.
 * If so, the user is presented with the help menu and can
 * choose -r to reinstall.
 * 
 * Returns 0 if Airtime is not installed
 * Returns 1 if the same version of Airtime already installed
 * Returns 2 if a previous version of Airtime is installed we can upgrade from
 * Returns 3 if a version of Airtime is installed that we can't upgrade from.
 */
require_once(dirname(__FILE__).'/AirtimeInstall.php');
require_once(__DIR__.'/airtime-constants.php');

AirtimeInstall::ExitIfNotRoot();

$version = AirtimeInstall::GetVersionInstalled();
// The current version is already installed.
echo "* Checking for existing Airtime installation...".PHP_EOL;
if (isset($version)){
    if ($version === false){
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
