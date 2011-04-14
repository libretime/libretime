<?php
/**
 * @package Airtime
 * @copyright 2010 Sourcefabric O.P.S.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

echo PHP_EOL;
echo "******************************** Install Begin *********************************".PHP_EOL;

require_once(dirname(__FILE__).'/include/AirtimeIni.php');
require_once(dirname(__FILE__).'/include/AirtimeInstall.php');

AirtimeInstall::ExitIfNotRoot();
AirtimeIni::ExitIfIniFilesExist();
echo "* Creating INI files".PHP_EOL;
AirtimeIni::CreateIniFiles();
AirtimeInstall::InstallPhpCode();
AirtimeInstall::InstallBinaries();

AirtimeIni::UpdateIniFiles();

require_once(AirtimeInstall::GetAirtimeSrcDir().'/application/configs/conf.php');

echo "* Airtime Version: ".AIRTIME_VERSION.PHP_EOL;

//echo PHP_EOL."*** Database Installation ***".PHP_EOL;

AirtimeInstall::CreateDatabaseUser();

AirtimeInstall::CreateDatabase();

AirtimeInstall::DbConnect(true);

AirtimeInstall::InstallPostgresScriptingLanguage();

AirtimeInstall::CreateDatabaseTables();

AirtimeInstall::InstallStorageDirectory($CC_CONFIG);

AirtimeInstall::ChangeDirOwnerToWebserver($CC_CONFIG["storageDir"]);

AirtimeInstall::CreateSymlinksToUtils($CC_CONFIG["storageDir"]);

echo PHP_EOL."*** Pypo Installation ***".PHP_EOL;
system("python ".__DIR__."/../python_apps/pypo/install/pypo-install.py");

echo PHP_EOL."*** Recorder Installation ***".PHP_EOL;
system("python ".__DIR__."/../python_apps/show-recorder/install/recorder-install.py");

AirtimeInstall::SetAirtimeVersion(AIRTIME_VERSION);

echo "******************************* Install Complete *******************************".PHP_EOL;

