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
    echo "Removing $pn";
    exec("rm -rf ".$pn);
}

/* Run install scripts for pypo, show-recorder and media-monitor.
 * This is to install the init.d scripts. */
exec("python ".__DIR__."/../../../python_apps/pypo/install/pypo-install.py");
exec("python ".__DIR__."/../../../python_apps/show-recorder/install/recorder-install.py");
exec("python ".__DIR__."/../../../python_apps/media-monitor/install/media-monitor-install.py");

