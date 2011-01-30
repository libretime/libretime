<?php

require_once (__DIR__."/configs/navigation.php");
require_once (__DIR__."/configs/ACL.php");

require_once 'propel/runtime/lib/Propel.php';
Propel::init(__DIR__."/configs/propel-config.php");

//DateTime in PHP 5.3.0+ need a default timezone set.
$tz = ini_get('date.timezone') ? ini_get('date.timezone') : 'America/Toronto';
date_default_timezone_set($tz);

require_once (__DIR__."/configs/constants.php");
require_once (__DIR__."/configs/conf.php");
require_once 'DB.php';

require_once 'Playlist.php';
require_once 'StoredFile.php';
require_once 'Schedule.php';
require_once 'Shows.php';
require_once 'Users.php';

global $CC_CONFIG, $CC_DBC;	
$dsn = $CC_CONFIG['dsn'];

$CC_DBC = DB::connect($dsn, TRUE);
if (PEAR::isError($CC_DBC)) {
	echo "ERROR: ".$CC_DBC->getMessage()." ".$CC_DBC->getUserInfo()."\n";
	exit(1);
}
$CC_DBC->setFetchMode(DB_FETCHMODE_ASSOC);

Zend_Session::start();

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	protected function _initDoctype()
    {
        $this->bootstrap('view');
        $view = $this->getResource('view');
        $view->doctype('XHTML1_STRICT');
    }

	protected function _initHeadLink()
	{
		$view = $this->getResource('view');

		$view->headLink()->appendStylesheet('/css/redmond/jquery-ui-1.8.8.custom.css');
	}

	protected function _initHeadScript()
	{
		$view = $this->getResource('view');
		$view->headScript()->appendFile('/js/libs/jquery-1.4.4.min.js','text/javascript');
		$view->headScript()->appendFile('/js/libs/jquery-ui-1.8.8.custom.min.js','text/javascript');
		$view->headScript()->appendFile('/js/libs/stuHover.js','text/javascript');

        //TODO: Find better place to put this in.
        $view->addHelperPath('views/helpers', 'Airtime_View_Helper');
    }
}

