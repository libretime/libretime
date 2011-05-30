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

// clean up old files
@unlink('/usr/bin/airtime-pypo-start');
@unlink('/usr/bin/airtime-pypo-stop');
@unlink(dirname(__FILE__).'/../../../python_apps/pypo/airtime-pypo-start');
@unlink(dirname(__FILE__).'/../../../python_apps/pypo/airtime-pypo-stop');

AirtimeInstall::CreateZendPhpLogFile();
