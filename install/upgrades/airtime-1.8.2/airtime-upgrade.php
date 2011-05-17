<?php
/**
 * @package Airtime
 * @subpackage StorageServer
 * @copyright 2010 Sourcefabric O.P.S.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

set_include_path(__DIR__.'/../../../airtime_mvc/library' . PATH_SEPARATOR . get_include_path());
require_once __DIR__.'/../../../airtime_mvc/application/configs/conf.php';
require_once(dirname(__FILE__).'/../../include/AirtimeInstall.php');
require_once(dirname(__FILE__).'/../../include/AirtimeIni.php');

const CONF_FILE_AIRTIME = "/etc/airtime/airtime.conf";
const CONF_FILE_PYPO = "/etc/airtime/pypo.cfg";
const CONF_FILE_RECORDER = "/etc/airtime/recorder.cfg";
const CONF_FILE_LIQUIDSOAP = "/etc/airtime/liquidsoap.cfg";

$configFiles = array(CONF_FILE_AIRTIME,
                     CONF_FILE_PYPO,
                     CONF_FILE_RECORDER,
                     CONF_FILE_LIQUIDSOAP);

$suffix = date("Ymdhis");
foreach ($configFiles as $conf) {
    if (file_exists($conf)) {
        echo "Backing up $conf to $conf$suffix.bak".PHP_EOL;
        exec("cp $conf $conf$suffix.bak");
    }
}

AirtimeIni::CreateIniFiles();
echo "* Initializing INI files".PHP_EOL;
AirtimeIni::MergeConfigFiles($configFiles, $suffix);

global $CC_CONFIG;
$CC_CONFIG = Config::loadConfig($CC_CONFIG);

AirtimeInstall::InstallPhpCode();
AirtimeInstall::InstallBinaries();
