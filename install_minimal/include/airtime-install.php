<?php
/**
 * @package Airtime
 * @copyright 2011 Sourcefabric O.P.S.
 * @license http://www.gnu.org/licenses/gpl.txt
 */
require_once(__DIR__.'/AirtimeIni.php');
require_once(__DIR__.'/AirtimeInstall.php');
require_once(__DIR__.'/airtime-constants.php');

// -------------------------------------------------------------------------
// The only way we get here is if we are doing a new install or a reinstall.
// -------------------------------------------------------------------------

if (PHP_VERSION_ID < 50300) //PHP 5.3
{
    echo("ERROR: Your PHP version is too old!\nAirtime requires PHP 5.3 or greater.\n");
    exit(1);
}

$iniExists = file_exists("/etc/airtime/airtime.conf");
if ($iniExists) {
    //reinstall, Will ask if we should rewrite config files.
    require_once(AirtimeInstall::GetAirtimeSrcDir().'/application/configs/conf.php');
    $CC_CONFIG = Config::getConfig();
    require_once 'vendor/propel/propel1/runtime/lib/Propel.php';
    Propel::init(AirtimeInstall::GetAirtimeSrcDir()."/application/configs/airtime-conf-production.php");
    $version = AirtimeInstall::GetVersionInstalled();
    $newInstall = is_null($version);
} else {
    //create config files without asking
    $newInstall = true;
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
if (!$iniExists){
    require_once(AirtimeInstall::GetAirtimeSrcDir().'/application/configs/conf.php');
    $CC_CONFIG = Config::getConfig();
    require_once 'vendor/propel/propel1/runtime/lib/Propel.php';
    Propel::init(AirtimeInstall::GetAirtimeSrcDir()."/application/configs/airtime-conf-production.php");
}

echo "* Airtime Version: ".AIRTIME_VERSION.PHP_EOL;

AirtimeInstall::InstallStorageDirectory();

$db_install = getenv("nodb")!="t";
if ($db_install) {
    
    echo "* Checking database for correct encoding".PHP_EOL;
    exec('su -c \'psql -t -c "SHOW SERVER_ENCODING"\' postgres | grep -i "UTF.*8"', $out, $return_code);
    if ($return_code != 0){
        echo " * Unfortunately your postgresql database has not been created using a UTF-8 encoding.".PHP_EOL;
        echo " * As of Airtime 2.1, installs will fail unless the encoding has been set to UTF-8. Please verify this is the case".PHP_EOL;
        echo " * and try the install again".PHP_EOL;
        exit(1);
    }
    
    if($newInstall) {
        //call external script. "y" argument means force creation of database tables.
        passthru('php '.__DIR__.'/airtime-db-install.php y');
    } else {
        require_once('airtime-db-install.php');
    }
}
