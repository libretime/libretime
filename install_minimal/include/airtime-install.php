<?php
/**
 * @package Airtime
 * @copyright 2011 Sourcefabric O.P.S.
 * @license http://www.gnu.org/licenses/gpl.txt
 *
 * Checks if a previous version of Airtime is currently installed and upgrades Airtime if so.
 * Performs a new install (new configs, database install) otherwise.
 */
require_once(dirname(__FILE__).'/AirtimeIni.php');
require_once(dirname(__FILE__).'/AirtimeInstall.php');
require_once(__DIR__.'/airtime-constants.php');

$opts = AirtimeInstall::getOpts();
if ($opts == NULL) {
    exit(1);
}

$version = AirtimeInstall::GetVersionInstalled();

// A previous version exists - if so, upgrade.
/*
if (isset($version) && ($version != false) && ($version < AIRTIME_VERSION) && !isset($opts->r)) {
    echo "Airtime version $version found.".PHP_EOL;
    
    require_once("airtime-upgrade.php");
    exit(0);
}
* */

// -------------------------------------------------------------------------
// The only way we get here is if we are doing a new install or a reinstall.
// -------------------------------------------------------------------------

$newInstall = false;
if(is_null($version)) {
    $newInstall = true;
}

$db_install = true;
if (is_null($opts->r) && isset($opts->n)) {
	$db_install = false;
}

$overwrite = false;
if (isset($opts->o) || $newInstall == true) {
    $overwrite = true;
} else if (!isset($opts->p) && !isset($opts->o) && isset($opts->r)) {
    if (AirtimeIni::IniFilesExist()) {
        $userAnswer = "x";
        while (!in_array($userAnswer, array("o", "O", "p", "P", ""))) {
            echo PHP_EOL."You have existing config files. Do you want to (O)verwrite them, or (P)reserve them? (o/P) ";
            $userAnswer = trim(fgets(STDIN));
        }
        if (in_array($userAnswer, array("o", "O"))) {
            $overwrite = true;
        }
    } else {
        $overwrite = true;
    }
}

if ($overwrite) {
    echo "* Creating INI files".PHP_EOL;
    AirtimeIni::CreateIniFiles();
    echo "* Initializing INI files".PHP_EOL;
    AirtimeIni::UpdateIniFiles();
}

//AirtimeInstall::InstallPhpCode(); //copies contents of airtime_mvc to /usr/share
//AirtimeInstall::InstallBinaries(); //copies utils to /usr/lib/airtime

// Update the build.properties file to point to the correct directory.
AirtimeIni::UpdateIniValue(AirtimeInstall::CONF_DIR_WWW.'/build/build.properties', 'project.home', AirtimeInstall::CONF_DIR_WWW);

require_once(AirtimeInstall::GetAirtimeSrcDir().'/application/configs/conf.php');

echo "* Airtime Version: ".AIRTIME_VERSION.PHP_EOL;

AirtimeInstall::InstallStorageDirectory();

if ($db_install) {
    if($newInstall) {
        //call external script. "y" argument means force creation of database tables.
        passthru('php '.__DIR__.'/airtime-db-install.php y');
        AirtimeInstall::DbConnect(true);
    } else {
        require_once('airtime-db-install.php');
    }
}

//AirtimeInstall::CreateSymlinksToUtils();

AirtimeInstall::CreateZendPhpLogFile();

//AirtimeInstall::CreateCronFile();

/* FINISHED AIRTIME PHP INSTALLER */
