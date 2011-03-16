<?php
/**
 * @package Airtime
 * @subpackage StorageServer
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

require_once(dirname(__FILE__).'/installInit.php');

AirtimeInstall::ExitIfNotRoot();

echo "******************************** Update Begin *********************************".PHP_EOL;
AirtimeInstall::UpdateIniValue('../build/build.properties', 'project.home', realpath(__dir__.'/../'));

echo PHP_EOL."*** Updating Database Tables ***".PHP_EOL;
AirtimeInstall::MigrateTables(__DIR__);

echo PHP_EOL."*** Updating Pypo ***".PHP_EOL;
system("python ".__DIR__."/../pypo/install/pypo-install.py");

echo "******************************* Update Complete *******************************".PHP_EOL;


