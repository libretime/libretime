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
AirtimeInstall::MigrateTablesToVersion(__DIR__, '20110402164819');

echo PHP_EOL."*** Updating Pypo ***".PHP_EOL;
system("python ".__DIR__."/../../../python_apps/pypo/install/pypo-install.py");

echo PHP_EOL."*** Recorder Installation ***".PHP_EOL;
system("python ".__DIR__."/../../../python_apps/show-recorder/install/recorder-install.py");

