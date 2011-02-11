<?php

require_once (__DIR__."/configs/navigation.php");
require_once (__DIR__."/configs/ACL.php");

require_once 'propel/runtime/lib/Propel.php';
Propel::init(__DIR__."/configs/airtime-conf.php");

//DateTime in PHP 5.3.0+ need a default timezone set.
$tz = ini_get('date.timezone') ? ini_get('date.timezone') : 'UTC';
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

//Zend_Session::start();
Zend_Validate::setDefaultNamespaces("Zend");

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

        $this->view->headLink()->appendStylesheet('/css/pro_dropdown_3.css');
		$this->view->headLink()->appendStylesheet('/css/styles.css');
	}

	protected function _initHeadScript()
	{
		$view = $this->getResource('view');
		$view->headScript()->appendFile('/js/libs/jquery-1.4.4.min.js','text/javascript');
		$view->headScript()->appendFile('/js/libs/jquery-ui-1.8.8.custom.min.js','text/javascript');
		$view->headScript()->appendFile('/js/libs/stuHover.js','text/javascript');
        $view->headScript()->appendFile('/js/libs/jquery.stickyPanel.js','text/javascript');
        $view->headScript()->appendFile('/js/qtip/jquery.qtip-1.0.0.min.js','text/javascript');

        //scripts for now playing bar
        $this->view->headScript()->appendFile('/js/playlist/helperfunctions.js','text/javascript');
		$this->view->headScript()->appendFile('/js/playlist/playlist.js','text/javascript');

        $view->headScript()->appendFile('/js/airtime/common/common.js','text/javascript');   
    }
    
    protected function _initViewHelpers(){
        $view = $this->getResource('view');
        $view->addHelperPath('../application/views/helpers', 'Airtime_View_Helper');
    }
    
    protected function _initTitle(){
        $view = $this->getResource('view');
        $view->headTitle(Application_Model_Preference::GetHeadTitle());
    }
}

