<?php
/**
 * @package Airtime
 * @copyright 2011 Sourcefabric O.P.S.
 * @license http://www.gnu.org/licenses/gpl.txt
 */
set_include_path(__DIR__.'/../airtime_mvc/library' . PATH_SEPARATOR . get_include_path());

echo PHP_EOL;
echo "******************************** Install Begin *********************************".PHP_EOL;

require_once(dirname(__FILE__).'/include/AirtimeIni.php');
require_once(dirname(__FILE__).'/include/AirtimeInstall.php');
require_once(AirtimeInstall::GetAirtimeSrcDir().'/application/configs/constants.php');

AirtimeInstall::ExitIfNotRoot();

$newInstall = false;
$version = AirtimeInstall::CheckForVersionBeforeInstall();

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
    exit;
}

//the current version exists.
if(isset($version) && $version != false && $version == AIRTIME_VERSION && !isset($opts->r)) {

    echo "Airtime $version is already installed.".PHP_EOL;
    exit();
}
//a previous version exists.
if(isset($version) && $version != false && $version < AIRTIME_VERSION) {

    echo "Airtime version $version found.".PHP_EOL;

    $command = "php airtime-upgrade.php";
    system($command);
    exit();
}
if(is_null($version)) {
    $newInstall = true;
}

$db_install = true;
if (is_null($opts->r) && isset($opts->n) && !$newInstall){
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

AirtimeInstall::InstallPhpCode();
AirtimeInstall::InstallBinaries();

if ($overwrite) {
    echo "* Initializing INI files".PHP_EOL;
    AirtimeIni::UpdateIniFiles();
}

require_once(AirtimeInstall::GetAirtimeSrcDir().'/application/configs/conf.php');

echo "* Airtime Version: ".AIRTIME_VERSION.PHP_EOL;

if ($db_install) {
    if($newInstall) {
        system('php airtime-db-install.php y');
    }
    else {
        require_once('airtime-db-install.php');
    }
}

AirtimeInstall::InstallStorageDirectory();

AirtimeInstall::ChangeDirOwnerToWebserver($CC_CONFIG["storageDir"]);

AirtimeInstall::CreateSymlinksToUtils();

AirtimeInstall::CreateZendPhpLogFile();

echo PHP_EOL."*** Pypo Installation ***".PHP_EOL;
system("python ".__DIR__."/../python_apps/pypo/install/pypo-install.py");

echo PHP_EOL."*** Recorder Installation ***".PHP_EOL;
system("python ".__DIR__."/../python_apps/show-recorder/install/recorder-install.py");

//wait for 1.9.0 release
//echo PHP_EOL."*** Media Monitor Installation ***".PHP_EOL;
//system("python ".__DIR__."/../python_apps/pytag-fs/install/media-monitor-install.py");

echo "******************************* Install Complete *******************************".PHP_EOL;

