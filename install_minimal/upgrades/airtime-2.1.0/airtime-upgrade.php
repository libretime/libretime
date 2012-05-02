<?php
/**
 * @package Airtime
 * @subpackage StorageServer
 * @copyright 2010 Sourcefabric O.P.S.
 * @license http://www.gnu.org/licenses/gpl.txt
 */
 
/*
 * In the future, most Airtime upgrades will involve just mutating the
 * data that is stored on the system. For example, The only data
 * we need to convert between versions is the database, /etc/airtime, and
 * /srv/airtime. Everything else is just executable files that can be removed/replaced
 * with new versions.
 */

function get_conf_location(){
    $conf = parse_ini_file("/etc/airtime/airtime.conf", TRUE);
    $airtime_dir = $conf['general']['airtime_dir'];
    return $airtime_dir."/"."application/configs/conf.php";
}
 
$conf_path = get_conf_location();
require_once $conf_path;

set_include_path(__DIR__.'/../../../airtime_mvc/library' . PATH_SEPARATOR . get_include_path());

require_once 'propel/runtime/lib/Propel.php';
require_once 'common/UpgradeCommon.php';
require_once 'ConfFileUpgrade.php';
require_once 'DbUpgrade.php';
require_once 'MiscUpgrade.php';

UpgradeCommon::connectToDatabase();
UpgradeCommon::SetDefaultTimezone();

AirtimeConfigFileUpgrade::start();
AirtimeDatabaseUpgrade::start();
AirtimeMiscUpgrade::start();
