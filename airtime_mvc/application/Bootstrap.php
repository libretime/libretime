<?php

require_once __DIR__."/configs/navigation.php";
require_once __DIR__."/configs/ACL.php";

require_once 'propel/runtime/lib/Propel.php';
Propel::init(__DIR__."/configs/airtime-conf.php");

require_once __DIR__."/logging/Logging.php";
require_once __DIR__."/configs/constants.php";
require_once __DIR__."/configs/conf.php";
require_once 'DB.php';

require_once 'Soundcloud.php';
require_once 'MusicDir.php';
require_once 'Playlist.php';
require_once 'StoredFile.php';
require_once 'Schedule.php';
require_once 'Preference.php';
require_once 'Shows.php';
require_once 'Users.php';
require_once 'RabbitMq.php';
require_once 'DateHelper.php';
require_once __DIR__.'/controllers/plugins/RabbitMqPlugin.php';

global $CC_CONFIG, $CC_DBC;
$dsn = $CC_CONFIG['dsn'];

$CC_DBC = DB::connect($dsn, FALSE);
if (PEAR::isError($CC_DBC)) {
    echo "ERROR: ".$CC_DBC->getMessage()." ".$CC_DBC->getUserInfo()."\n";
    exit(1);
}
$CC_DBC->setFetchMode(DB_FETCHMODE_ASSOC);

//DateTime in PHP 5.3.0+ need a default timezone set.
date_default_timezone_set(Application_Model_Preference::GetTimezone());

Logging::setLogPath('/var/log/airtime/zendphp.log');

Zend_Validate::setDefaultNamespaces("Zend");

$front = Zend_Controller_Front::getInstance();
$front->registerPlugin(new RabbitMqPlugin()); 


/* The bootstrap class should only be used to initialize actions that return a view. 
   Actions that return JSON will not use the bootstrap class! */
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
        $baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();

		$view->headLink()->appendStylesheet($baseUrl.'/css/redmond/jquery-ui-1.8.8.custom.css');
        $view->headLink()->appendStylesheet($baseUrl.'/css/pro_dropdown_3.css');
		$view->headLink()->appendStylesheet($baseUrl.'/css/styles.css');
	}

	protected function _initHeadScript()
	{
		$view = $this->getResource('view');
        $baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();

        $view->headScript()->appendFile($baseUrl.'/js/libs/jquery-1.5.2.min.js','text/javascript');
		$view->headScript()->appendFile($baseUrl.'/js/libs/jquery-ui-1.8.11.custom.min.js','text/javascript');
        $view->headScript()->appendFile($baseUrl.'/js/libs/jquery.stickyPanel.js','text/javascript');
        $view->headScript()->appendFile($baseUrl.'/js/qtip/jquery.qtip-1.0.0.min.js','text/javascript');

        //scripts for now playing bar
        $view->headScript()->appendFile($baseUrl.'/js/airtime/dashboard/helperfunctions.js','text/javascript');
		$view->headScript()->appendFile($baseUrl.'/js/airtime/dashboard/playlist.js','text/javascript');

        $view->headScript()->appendFile($baseUrl.'/js/airtime/common/common.js','text/javascript');
    }

    protected function _initViewHelpers()
    {
        $view = $this->getResource('view');
        $view->addHelperPath('../application/views/helpers', 'Airtime_View_Helper');
    }

    protected function _initTitle()
    {
        $view = $this->getResource('view');
        $view->headTitle(Application_Model_Preference::GetHeadTitle());
    }

    protected function _initZFDebug()
    {
        if (APPLICATION_ENV == "development"){
            global $CC_DBC;
            
            $autoloader = Zend_Loader_Autoloader::getInstance();
            $autoloader->registerNamespace('ZFDebug');

            $options = array(
                'plugins' => array('Variables', 
                                   'Exception',
                                   'Memory',
                                   'Time')
            );
            $debug = new ZFDebug_Controller_Plugin_Debug($options);

            $this->bootstrap('frontController');
            $frontController = $this->getResource('frontController');
            $frontController->registerPlugin($debug);
        }
    }
}

