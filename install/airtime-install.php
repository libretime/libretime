<?php
/**
 * @package Airtime
 * @copyright 2010 Sourcefabric O.P.S.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

// Do not allow remote execution
$arr = array_diff_assoc($_SERVER, $_ENV);
if (isset($arr["DOCUMENT_ROOT"]) && ($arr["DOCUMENT_ROOT"] != "") ) {
    header("HTTP/1.1 400");
    header("Content-type: text/plain; charset=UTF-8");
    echo "400 Not executable\r\n";
    exit(1);
}

require_once(dirname(__FILE__).'/../application/configs/conf.php');
require_once(dirname(__FILE__).'/installInit.php');

echo "******************************** Install Begin *********************************".PHP_EOL;

AirtimeInstall::ExitIfNotRoot();
AirtimeInstall::CreateApiKey();
AirtimeInstall::UpdateIniValue('../build/build.properties', 'project.home', realpath(__dir__.'/../'));


echo PHP_EOL."*** Database Installation ***".PHP_EOL;

echo "* Creating Airtime database user".PHP_EOL;
AirtimeInstall::CreateDatabaseUser();

echo "* Creating Airtime database".PHP_EOL;
AirtimeInstall::CreateDatabase();

AirtimeInstall::DbConnect(true);

echo "* Installing Postgresql scripting language".PHP_EOL;
AirtimeInstall::InstallPostgresScriptingLanguage();

echo "* Creating database tables".PHP_EOL;
AirtimeInstall::CreateDatabaseTables();

echo "* Storage directory setup".PHP_EOL;
AirtimeInstall::SetupStorageDirectory($CC_CONFIG);

echo "* Giving Apache permission to access the storage directory".PHP_EOL;
AirtimeInstall::ChangeDirOwnerToWebserver($CC_CONFIG["storageDir"]);

echo "* Creating /usr/bin symlinks".PHP_EOL;
AirtimeInstall::CreateSymlinks($CC_CONFIG["storageDir"]);

echo "* Importing sample audio clips".PHP_EOL;
system(__DIR__."/../utils/airtime-import --copy ../audio_samples/ > /dev/null");

echo PHP_EOL."*** Pypo Installation ***".PHP_EOL;
system("python ".__DIR__."/../python_apps/pypo/install/pypo-install.py");

echo PHP_EOL."*** Recorder Installation ***".PHP_EOL;
system("python ".__DIR__."/../python_apps/show-recorder/install/recorder-install.py");


echo "******************************* Install Complete *******************************".PHP_EOL;

