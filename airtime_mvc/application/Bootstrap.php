<?php
require_once CONFIG_PATH . "conf.php";
$CC_CONFIG = Config::getConfig();

require_once CONFIG_PATH . "ACL.php";

// Since we initialize the database during the configuration check,
// check the $configRun global to avoid reinitializing unnecessarily
if (!isset($configRun) || !$configRun) {
    Propel::init(CONFIG_PATH . 'airtime-conf-production.php');
}

require_once CONFIG_PATH . "constants.php";

Logging::setLogPath(LIBRETIME_LOG_DIR . '/zendphp.log');

// We need to manually route because we can't load Zend without the database being initialized first.
if (array_key_exists("REQUEST_URI", $_SERVER) && (stripos($_SERVER["REQUEST_URI"], "/provisioning/create") !== false)) {
    $provisioningHelper = new ProvisioningHelper($CC_CONFIG["apiKey"][0]);
    $provisioningHelper->createAction();
    die();
}

Zend_Session::setOptions(array('strict' => true));
Config::setAirtimeVersion();
require_once (CONFIG_PATH . 'navigation.php');

Zend_Validate::setDefaultNamespaces("Zend");

$front = Zend_Controller_Front::getInstance();
$front->registerPlugin(new RabbitMqPlugin());
$front->registerPlugin(new Zend_Controller_Plugin_ConversionTracking());
$front->throwExceptions(false);

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

    protected function _initZFDebug()
    {

        Zend_Controller_Front::getInstance()->throwExceptions(false);

        /*
        if (APPLICATION_ENV == "development") {
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
        */
    }

    protected function _initRouter()
    {
        $front = Zend_Controller_Front::getInstance();
        $router = $front->getRouter();
        $front->setBaseUrl(Application_Common_OsPath::getBaseDir());

        $router->addRoute(
            'password-change',
            new Zend_Controller_Router_Route('password-change/:user_id/:token', array(
                'module' => 'default',
                'controller' => 'login',
                'action' => 'password-change',
            )));
    }

    public function _initPlugins()
    {
        $front = Zend_Controller_Front::getInstance();
        $front->registerPlugin(new Zend_Controller_Plugin_Maintenance());
        $front->registerPlugin(new PageLayoutInitPlugin($this));
    }
}

