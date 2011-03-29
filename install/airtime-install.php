<?php
/**
 * @package Airtime
 * @copyright 2010 Sourcefabric O.P.S.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

echo "******************************** Install Begin *********************************".PHP_EOL;

require_once(dirname(__FILE__).'/include/installInit.php');

ExitIfNotRoot();
CreateINIFile();

require_once(dirname(__FILE__).'/../application/configs/conf.php');
require_once(dirname(__FILE__).'/include/AirtimeInstall.php');

AirtimeInstall::CreateApiKey();
AirtimeInstall::UpdateIniValue('../build/build.properties', 'project.home', realpath(__dir__.'/../'));


echo PHP_EOL."*** Database Installation ***".PHP_EOL;

echo "* Creating Airtime Database User".PHP_EOL;
AirtimeInstall::CreateDatabaseUser();

echo "* Creating Airtime Database".PHP_EOL;
AirtimeInstall::CreateDatabase();

AirtimeInstall::DbConnect(true);

echo "* Install Postgresql Scripting Language".PHP_EOL;
AirtimeInstall::InstallPostgresScriptingLanguage();

echo "* Creating Database Tables".PHP_EOL;
AirtimeInstall::CreateDatabaseTables();

echo "* Storage Directory Setup".PHP_EOL;
AirtimeInstall::SetupStorageDirectory($CC_CONFIG);

echo "* Setting Dir Permissions".PHP_EOL;
AirtimeInstall::ChangeDirOwnerToWebserver($CC_CONFIG["storageDir"]);

echo "* Creating /usr/bin symlinks".PHP_EOL;
AirtimeInstall::CreateSymlinks($CC_CONFIG["storageDir"]);

echo "* Importing Sample Audio Clips".PHP_EOL;
system(__DIR__."/../utils/airtime-import --copy ../audio_samples/ > /dev/null");

echo PHP_EOL."*** Pypo Installation ***".PHP_EOL;
system("python ".__DIR__."/../python_apps/pypo/install/pypo-install.py");

echo PHP_EOL."*** Recorder Installation ***".PHP_EOL;
system("python ".__DIR__."/../python_apps/show-recorder/install/recorder-install.py");


echo "******************************* Install Complete *******************************".PHP_EOL;

