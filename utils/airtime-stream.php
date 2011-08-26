<?php

$airtimeIni = GetAirtimeConf();
$airtime_base_dir = $airtimeIni['general']['airtime_dir'];
$airtime_base_dir = "/home/james/src/airtime/airtime_mvc";

set_include_path("$airtime_base_dir/application/models" . PATH_SEPARATOR . get_include_path());
require_once("$airtime_base_dir/library/propel/runtime/lib/Propel.php");
Propel::init("$airtime_base_dir/application/configs/airtime-conf.php");

require_once("$airtime_base_dir/application/configs/conf.php");
/*require_once("$airtime_base_dir/application/models/Users.php");*/
require_once("$airtime_base_dir/application/models/Preference.php");
require_once('DB.php');
require_once('Console/Getopt.php');

// Do not allow remote execution
$arr = array_diff_assoc($_SERVER, $_ENV);
if (isset($arr["DOCUMENT_ROOT"]) && ($arr["DOCUMENT_ROOT"] != "") ) {
    header("HTTP/1.1 400");
    header("Content-type: text/plain; charset=UTF-8");
    echo "400 Not executable\r\n";
    exit(1);
}

function printUsage()
{
    echo "\n";
    echo "airtime-stream\n";
    echo "===============\n";
    echo "    This program allows you to manage Airtime stream.\n";
    echo "\n";
    echo "OPTIONS:\n";
    echo "    --maxbitrate <bitrate>\n";
    echo "        Set the max bitrate allowed by Airtime.\n";
    echo "    --numofstream <numofstream>\n";
    echo "        Set the number of stream allowed by Airtime.\n";
    echo "\n";
}


if (count($argv) != 3) {
    printUsage();
    exit;
}


$action = null;
switch ($argv[1]) {
    case '--maxbitrate':
        $action = "maxbitrate";
        break;
    case '--numofstream':
        $action = "numofstream";
        break;
}

$optionArg = $argv[2];
if (is_null($action)) {
    printUsage();
    exit;
}

PEAR::setErrorHandling(PEAR_ERROR_RETURN);
$CC_DBC = DB::connect($CC_CONFIG['dsn'], TRUE);
if (PEAR::isError($CC_DBC)) {
    die($CC_DBC->getMessage());
}
$CC_DBC->setFetchMode(DB_FETCHMODE_ASSOC);

if ($action == "maxbitrate") {
    Application_Model_Preference::SetMaxBitrate($optionArg);
} elseif ($action == "numofstream") {
    Application_Model_Preference::SetNumOfStream($optionArg);
}

function GetAirtimeConf()
{
    $ini = parse_ini_file("/etc/airtime/airtime.conf", true);

    if ($ini === false){
        echo "Error reading /etc/airtime/airtime.conf.".PHP_EOL;
        exit;
    }

    return $ini;
}
