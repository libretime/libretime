<?php
/**
 * @package Airtime
 * @subpackage StorageServer
 * @copyright 2010 Sourcefabric O.P.S.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

require_once(dirname(__FILE__).'/../../include/AirtimeIni.php');
require_once(dirname(__FILE__).'/../../include/AirtimeInstall.php');

AirtimeIni::CreateIniFile();
AirtimeIni::UpdateIniFiles();

echo PHP_EOL."*** Updating Database Tables ***".PHP_EOL;
$migrations = array('20110312121200', '20110331111708', '20110402164819');
foreach($migrations as $migration) {
    AirtimeInstall::ExecuteDoctrineMigration(__DIR__, $migration);
}

echo PHP_EOL."*** Updating Pypo ***".PHP_EOL;
system("python ".__DIR__."/../../../python_apps/pypo/install/pypo-install.py");

echo PHP_EOL."*** Recorder Installation ***".PHP_EOL;
system("python ".__DIR__."/../../../python_apps/show-recorder/install/recorder-install.py");

