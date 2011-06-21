<?php
/**
 * @package Airtime
 * @copyright 2011 Sourcefabric O.P.S.
 * @license http://www.gnu.org/licenses/gpl.txt
 *
 * Checks if a previous version of Airtime is currently installed, upgrades Airtime if so.
 * Performs a new install (new configs, database install) if a version of Airtime is not found
 * If the current version is found to be installed the User is presented with the help menu and can choose -r to reinstall.
 */
set_include_path(__DIR__.'/../../airtime_mvc/library' . PATH_SEPARATOR . get_include_path());

require_once(dirname(__FILE__).'/AirtimeIni.php');
require_once(dirname(__FILE__).'/AirtimeInstall.php');
require_once(AirtimeInstall::GetAirtimeSrcDir().'/application/configs/constants.php');

AirtimeInstall::ExitIfNotRoot();

$newInstall = false;
$version = AirtimeInstall::GetVersionInstalled();

require_once('Zend/Loader/Autoloader.php');
$autoloader = Zend_Loader_Autoloader::getInstance();

try {
    $opts = new Zend_Console_Getopt(
        array(
            'help|h' => 'Displays usage information.',
            'overwrite|o' => 'Overwrite any existing config files.',
            'preserve|p' => 'Keep any existing config files.',
			'no-db|n' => 'Turn off database install.',
            'reinstall|r' => 'Force a fresh install of this Airtime Version'
        )
    );
    $opts->parse();
}
catch (Zend_Console_Getopt_Exception $e) {
    exit($e->getMessage() ."\n\n". $e->getUsageMessage());
}

if (isset($opts->h)) {
    echo $opts->getUsageMessage();
    exit(1);
}

// The current version is already installed.
if(isset($version) && ($version != false) && ($version == AIRTIME_VERSION) && !isset($opts->r)) {
    echo "Airtime $version is already installed.".PHP_EOL;
    echo $opts->getUsageMessage();
    exit(1);
}
// A previous version exists - if so, upgrade.
if(isset($version) && ($version != false) && ($version < AIRTIME_VERSION)) {
    echo "Airtime version $version found.".PHP_EOL;

    echo "It appears you already have a version of Airtime installed.\n";
    echo "Upgrading is currently disabled for Airtime 1.9.0-beta1.\n";
    exit(2);
    
    require_once("airtime-upgrade.php");

    //Make sure to exit with non-zero error code so that airtime-install
    //shell script does not continue with installing pypo, show-recorder,
    //media-monitor etc.
    exit(2);
}

// -------------------------------------------------------------------------
// The only way we get here is if we are doing a new install or a reinstall.
// -------------------------------------------------------------------------

if(is_null($version)) {
    $newInstall = true;
}

$db_install = true;
if (is_null($opts->r) && isset($opts->n)){
	$db_install = false;
}

$overwrite = false;
if (isset($opts->o) || $newInstall == true) {
    $overwrite = true;
}
else if (!isset($opts->p) && !isset($opts->o) && isset($opts->r)) {
    if (AirtimeIni::IniFilesExist()) {
        $userAnswer = "x";
        while (!in_array($userAnswer, array("o", "O", "p", "P", ""))) {
            echo PHP_EOL."You have existing config files. Do you want to (O)verwrite them, or (P)reserve them? (o/P) ";
            $userAnswer = trim(fgets(STDIN));
        }
        if (in_array($userAnswer, array("o", "O"))) {
            $overwrite = true;
        }
    }
    else {
        $overwrite = true;
    }
}

if ($overwrite) {
    echo "* Creating INI files".PHP_EOL;
    AirtimeIni::CreateIniFiles();
}
AirtimeIni::CreateMonitFile();


AirtimeInstall::InstallPhpCode();
AirtimeInstall::InstallBinaries();

if ($overwrite) {
    echo "* Initializing INI files".PHP_EOL;
    AirtimeIni::UpdateIniFiles();
}

// Update the build.properties file to point to the correct directory.
AirtimeIni::UpdateIniValue(AirtimeInstall::CONF_DIR_WWW.'/build/build.properties', 'project.home', AirtimeInstall::CONF_DIR_WWW);

require_once(AirtimeInstall::GetAirtimeSrcDir().'/application/configs/conf.php');

echo "* Airtime Version: ".AIRTIME_VERSION.PHP_EOL;

if ($db_install) {
    if($newInstall) {
        system('php '.__DIR__.'/airtime-db-install.php y');
        AirtimeInstall::DbConnect(true);
    } else {
        require_once('airtime-db-install.php');
    }
}

AirtimeInstall::InstallStorageDirectory();

AirtimeInstall::CreateSymlinksToUtils();

AirtimeInstall::CreateZendPhpLogFile();

AirtimeInstall::SetUniqueId();

$h = rand(0,23);
$m = rand(0,59);

$fp = fopen('/etc/cron.d/airtime-crons','a');
fwrite($fp, "$m $h * * * root /usr/lib/airtime/utils/phone_home_stat\n");
fclose($fp);

/* FINISHED AIRTIME PHP INSTALLER */
