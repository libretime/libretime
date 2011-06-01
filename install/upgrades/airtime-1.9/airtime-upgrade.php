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

AirtimeInstall::CreateZendPhpLogFile();

const CONF_DIR_BINARIES = "/usr/lib/airtime";

function InstallPhpCode($phpDir)
{
    global $CC_CONFIG;
    
    $AIRTIME_SRC = realpath(__DIR__.'/../../../airtime_mvc');
    
    echo "* Installing PHP code to ".$phpDir.PHP_EOL;
    exec("mkdir -p ".$phpDir);
    exec("cp -R ".$AIRTIME_SRC."/* ".$phpDir);
}

public static function InstallBinaries()
{
    $utilsSrc = __DIR__."/../../../utils";
    
    echo "* Installing binaries to ".CONF_DIR_BINARIES.PHP_EOL;
    exec("mkdir -p ".CONF_DIR_BINARIES);
    exec("cp -R ".$utilsSrc." ".CONF_DIR_BINARIES);
}

public static function UninstallBinaries()
{
    echo "* Removing Airtime binaries from ".CONF_DIR_BINARIES.PHP_EOL;
    exec("rm -rf ".CONF_DIR_BINARIES);
}


/* In version 1.9.0 we have have switched from daemontools to more traditional
 * init.d daemon system. Let's remove all the daemontools files
 */

exec("airtime-pypo-stop");
exec("airtime-show-recorder-stop");
exec("airtime-media-monitor-stop");

exec("svc -dx /etc/service/pypo");
exec("svc -dx /etc/service/pypo/log");
exec("svc -dx /etc/service/pypo-liquidsoap");
exec("svc -dx /etc/service/pypo-liquidsoap/log");
exec("svc -dx /etc/service/recorder");
exec("svc -dx /etc/service/recorder/log");

$pathnames = array("/usr/bin/airtime-pypo-start",
                "/usr/bin/airtime-pypo-stop",
                "/usr/bin/airtime-show-recorder-start",
                "/usr/bin/airtime-show-recorder-stop",
                "/usr/bin/airtime-media-monitor-start",
                "/usr/bin/airtime-media-monitor-stop",
                "/etc/service/pypo",
                "/etc/service/pypo-liquidsoap",
                "/etc/service/media-monitor",
                "/etc/service/recorder",
                "/var/log/airtime/pypo/main",
                "/var/log/airtime/show-recorder/main"
                );

foreach ($pathnames as $pn){
    echo "Removing $pn\n";
    exec("rm -rf ".$pn);
}


$values = parse_ini_file(CONF_FILE_AIRTIME, true);
$phpDir = $values['general']['airtime_dir'];

InstallPhpCode($phpDir);

//update utils (/usr/lib/airtime) folder
UninstallBinaries();
InstallBinaries();
