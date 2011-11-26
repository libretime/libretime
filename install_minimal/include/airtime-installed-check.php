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
 * Returns 1 if a previous version of Airtime installed
 * Returns 2 if the same version of Airtime is installed
 * Returns 3 if a version of Airtime that we can't upgrade from is installed.
 * Returns 4 if we need to print help message.
 * Returns 5 if we need should only install apache files (web-only).
 */
require_once(dirname(__FILE__).'/AirtimeInstall.php');
require_once(__DIR__.'/airtime-constants.php');

AirtimeInstall::ExitIfNotRoot();

$opts = AirtimeInstall::getOpts();

if (is_null($opts)) {
    exit(0);
}

if (isset($opts->h)) {
    AirtimeInstall::printUsage($opts);
    exit(4);
}

//install media-monitor
if (isset($opts->w)){
    exit(5);
}

$version = AirtimeInstall::GetVersionInstalled();
// The current version is already installed.
echo "* Checking for existing install of Airtime...".PHP_EOL;
if (isset($version) && ($version != false)){
    if (($version == AIRTIME_VERSION) && !isset($opts->r)) {
        echo "Airtime $version is already installed.".PHP_EOL;
        AirtimeInstall::printUsage($opts);
        exit(2);
    } else if (strcmp($version, AIRTIME_VERSION) < 0){
        echo " * Found previous version: $version".PHP_EOL;
        exit(1);
    }
} else {
    echo " * Not Found".PHP_EOL;
}

if($version === false){
    echo "A version of Airtime older than 1.7.0 detected, please upgrade to 1.7.0 first.\n";
    echo "You will then be able to upgrade to 1.9.0 using this installer.\n";
    exit(3);
}
