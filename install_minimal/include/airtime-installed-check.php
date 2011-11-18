<?php
/**
 * @package Airtime
 * @copyright 2011 Sourcefabric O.P.S.
 * @license http://www.gnu.org/licenses/gpl.txt
 *
 * Checks if a current version of Airtime is installed.
 * If so, the user is presented with the help menu and can
 * choose -r to reinstall.
 */
require_once(dirname(__FILE__).'/AirtimeInstall.php');
require_once(__DIR__.'/airtime-constants.php');

AirtimeInstall::ExitIfNotRoot();

$opts = AirtimeInstall::getOpts();
if ($opts == NULL) {
    exit(1);
}

if (isset($opts->h)) {
    AirtimeInstall::printUsage($opts);
    exit(1);
}

$version = AirtimeInstall::GetVersionInstalled();
// The current version is already installed.
if (isset($version) && ($version != false) && ($version == AIRTIME_VERSION) && !isset($opts->r)) {
    echo "Airtime $version is already installed.".PHP_EOL;
    AirtimeInstall::printUsage($opts);
    exit(1);
}

if($version === false){
    echo "A version of Airtime older than 1.7.0 detected, please upgrade to 1.7.0 first.\n";
    echo "You will then be able to upgrade to 1.9.0 using this installer.\n";
    exit(3);
}