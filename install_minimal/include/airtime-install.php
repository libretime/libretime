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

$version = AirtimeInstall::GetVersionInstalled();

// -------------------------------------------------------------------------
// The only way we get here is if we are doing a new install or a reinstall.
// -------------------------------------------------------------------------

$newInstall = false;
if(is_null($version)) {
    $newInstall = true;
}

$db_install = true;
if (getenv("nodb")=="t") {
	$db_install = false;
}

$overwrite = false;
if (getenv("overwrite") == "t" || $newInstall == true) {
    $overwrite = true;
} else if (getenv("preserve") == "f" && getenv("overwrite") == "f" && getenv("reinstall") == "t") {
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

// Update the build.properties file to point to the correct directory.
AirtimeIni::UpdateIniValue(AirtimeInstall::CONF_DIR_WWW.'/build/build.properties', 'project.home', AirtimeInstall::CONF_DIR_WWW);

require_once(AirtimeInstall::GetAirtimeSrcDir().'/application/configs/conf.php');

echo "* Airtime Version: ".AIRTIME_VERSION.PHP_EOL;

AirtimeInstall::InstallStorageDirectory();

if ($db_install) {
    if($newInstall) {
        //call external script. "y" argument means force creation of database tables.
        passthru('php '.__DIR__.'/airtime-db-install.php y');
        //AirtimeInstall::DbConnect(true);
    } else {
        require_once('airtime-db-install.php');
    }
}

AirtimeInstall::CreateZendPhpLogFile();

/* FINISHED AIRTIME PHP INSTALLER */
