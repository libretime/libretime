<?php
require_once CONFIG_PATH . "conf.php";
$CC_CONFIG = Config::getConfig();

require_once CONFIG_PATH . "ACL.php";

// Since we initialize the database during the configuration check,
// check the $configRun global to avoid reinitializing unnecessarily
if (!isset($configRun) || !$configRun) {
    Propel::init(CONFIG_PATH . 'airtime-conf-production.php');
}

//Composer's autoloader
require_once 'autoload.php';

require_once CONFIG_PATH . "constants.php";
require_once 'Preference.php';
require_once 'Locale.php';
require_once "DateHelper.php";
require_once "LocaleHelper.php";
require_once "FileDataHelper.php";
require_once "HTTPHelper.php";
require_once "FileIO.php";
require_once "OsPath.php";
require_once "Database.php";
require_once "ProvisioningHelper.php";
require_once "SecurityHelper.php";
require_once "GoogleAnalytics.php";
require_once "Timezone.php";
require_once "Auth.php";
require_once "interface/OAuth2.php";
require_once "TaskManager.php";
require_once "UsabilityHints.php";
require_once __DIR__.'/services/CeleryService.php';
require_once __DIR__.'/services/SoundcloudService.php';
require_once __DIR__.'/forms/helpers/ValidationTypes.php';
require_once __DIR__.'/forms/helpers/CustomDecorators.php';
require_once __DIR__.'/controllers/plugins/RabbitMqPlugin.php';
require_once __DIR__.'/controllers/plugins/Maintenance.php';
require_once __DIR__.'/controllers/plugins/ConversionTracking.php';
require_once __DIR__.'/modules/rest/controllers/ShowImageController.php';
require_once __DIR__.'/modules/rest/controllers/MediaController.php';
require_once __DIR__.'/upgrade/Upgrades.php';

require_once (APPLICATION_PATH . "/logging/Logging.php");
Logging::setLogPath('/var/log/airtime/zendphp.log');

// We need to manually route because we can't load Zend without the database being initialized first.
if (array_key_exists("REQUEST_URI", $_SERVER) && (stripos($_SERVER["REQUEST_URI"], "/provisioning/create") !== false)) {
    $provisioningHelper = new ProvisioningHelper($CC_CONFIG["apiKey"][0]);
    $provisioningHelper->createAction();
    die();
}

Config::setAirtimeVersion();
require_once (CONFIG_PATH . 'navigation.php');

Zend_Validate::setDefaultNamespaces("Zend");

Application_Model_Auth::pinSessionToClient(Zend_Auth::getInstance());

$front = Zend_Controller_Front::getInstance();
$front->registerPlugin(new RabbitMqPlugin());
$front->registerPlugin(new Zend_Controller_Plugin_ConversionTracking());
$front->throwExceptions(false);

//localization configuration
Application_Model_Locale::configureLocalization();

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

    protected function _initGlobals()
    {
        $view = $this->getResource('view');
        $baseUrl = Application_Common_OsPath::getBaseDir();

        $view->headScript()->appendScript("var baseUrl = '$baseUrl';");
        $this->_initTranslationGlobals($view);

        $user = Application_Model_User::GetCurrentUser();
        if (!is_null($user)) {
            $userType = $user->getType();
        } else {
            $userType = "";
        }
        $view->headScript()->appendScript("var userType = '$userType';");
    }

    /**
     * Create a global namespace to hold a session token for CSRF prevention
     */
    protected function _initCsrfNamespace()
    {
        $csrf_namespace = new Zend_Session_Namespace('csrf_namespace');
        // Check if the token exists
        if (!$csrf_namespace->authtoken) {
            // If we don't have a token, regenerate it and set a 2 hour timeout
            // Should we log the user out here if the token is expired?
            $csrf_namespace->authtoken = sha1(uniqid(rand(), 1));
            $csrf_namespace->setExpirationSeconds(2 * 60 * 60);
        }

        //Here we are closing the session for writing because otherwise no requests
        //in this session will be handled in parallel. This gives a major boost to the perceived performance
        //of the application (page load times are more consistent, no lock contention).
        session_write_close();
    }

    /**
     * Ideally, globals should be written to a single js file once
     * from a php init function. This will save us from having to
     * reinitialize them every request
     */
    private function _initTranslationGlobals()
    {
        $view = $this->getResource('view');
        $view->headScript()->appendScript("var PRODUCT_NAME = '" . PRODUCT_NAME . "';");
        $view->headScript()->appendScript("var USER_MANUAL_URL = '" . USER_MANUAL_URL . "';");
        $view->headScript()->appendScript("var COMPANY_NAME = '" . COMPANY_NAME . "';");
    }
    
    protected function _initTasks() {
        /* We need to wrap this here so that we aren't checking when we're running the unit test suite
         */
        if (getenv("AIRTIME_UNIT_TEST") != 1) {
            $taskManager = TaskManager::getInstance();
            $taskManager->runTask(AirtimeTask::UPGRADE);  // Run the upgrade on each request (if it needs to be run)
            //This will do the upgrade too if it's needed...
            $taskManager->runTasks();
        }
    }

    protected function _initHeadLink()
    {
        $CC_CONFIG = Config::getConfig();

        $view = $this->getResource('view');

        $baseUrl = Application_Common_OsPath::getBaseDir();

        $view->headLink(array('rel' => 'icon', 'href' => $baseUrl . 'favicon.ico?' . $CC_CONFIG['airtime_version'], 'type' => 'image/x-icon'), 'PREPEND')
            ->appendStylesheet($baseUrl . 'css/bootstrap.css?' . $CC_CONFIG['airtime_version'])
            ->appendStylesheet($baseUrl . 'css/redmond/jquery-ui-1.8.8.custom.css?' . $CC_CONFIG['airtime_version'])
            ->appendStylesheet($baseUrl . 'css/pro_dropdown_3.css?' . $CC_CONFIG['airtime_version'])
            ->appendStylesheet($baseUrl . 'css/qtip/jquery.qtip.min.css?' . $CC_CONFIG['airtime_version'])
            ->appendStylesheet($baseUrl . 'css/styles.css?' . $CC_CONFIG['airtime_version'])
            ->appendStylesheet($baseUrl . 'css/masterpanel.css?' . $CC_CONFIG['airtime_version'])
            ->appendStylesheet($baseUrl . 'css/tipsy/jquery.tipsy.css?' . $CC_CONFIG['airtime_version']);
    }

    protected function _initHeadScript()
    {
        $CC_CONFIG = Config::getConfig();

        $view = $this->getResource('view');

        $baseUrl = Application_Common_OsPath::getBaseDir();

        $view->headScript()->appendFile($baseUrl . 'js/libs/jquery-1.8.3.min.js?' . $CC_CONFIG['airtime_version'], 'text/javascript')
            ->appendFile($baseUrl . 'js/libs/jquery-ui-1.8.24.min.js?' . $CC_CONFIG['airtime_version'], 'text/javascript')
            ->appendFile($baseUrl . 'js/bootstrap/bootstrap.js?' . $CC_CONFIG['airtime_version'], 'text/javascript')
            ->appendFile($baseUrl . 'js/libs/underscore-min.js?' . $CC_CONFIG['airtime_version'], 'text/javascript')

            // ->appendFile($baseUrl . 'js/libs/jquery.stickyPanel.js?' . $CC_CONFIG['airtime_version'], 'text/javascript')
            ->appendFile($baseUrl . 'js/qtip/jquery.qtip.js?' . $CC_CONFIG['airtime_version'], 'text/javascript')
            ->appendFile($baseUrl . 'js/jplayer/jquery.jplayer.min.js?' . $CC_CONFIG['airtime_version'], 'text/javascript')
            ->appendFile($baseUrl . 'js/sprintf/sprintf-0.7-beta1.js?' . $CC_CONFIG['airtime_version'], 'text/javascript')
            ->appendFile($baseUrl . 'js/cookie/jquery.cookie.js?' . $CC_CONFIG['airtime_version'], 'text/javascript')
            ->appendFile($baseUrl . 'js/i18n/jquery.i18n.js?' . $CC_CONFIG['airtime_version'], 'text/javascript')
            ->appendFile($baseUrl . 'locale/general-translation-table?' . $CC_CONFIG['airtime_version'], 'text/javascript')
            ->appendFile($baseUrl . 'locale/datatables-translation-table?' . $CC_CONFIG['airtime_version'], 'text/javascript')

            ->appendScript("$.i18n.setDictionary(general_dict)")
            ->appendScript("var baseUrl='$baseUrl'");

        //These timezones are needed to adjust javascript Date objects on the client to make sense to the user's set timezone
        //or the server's set timezone.
        $serverTimeZone = new DateTimeZone(Application_Model_Preference::GetDefaultTimezone());
        $now = new DateTime("now", $serverTimeZone);
        $offset = $now->format("Z") * -1;
        $view->headScript()->appendScript("var serverTimezoneOffset = {$offset}; //in seconds");

        if (class_exists("Zend_Auth", false) && Zend_Auth::getInstance()->hasIdentity()) {
            $userTimeZone = new DateTimeZone(Application_Model_Preference::GetUserTimezone());
            $now = new DateTime("now", $userTimeZone);
            $offset = $now->format("Z") * -1;
            $view->headScript()->appendScript("var userTimezoneOffset = {$offset}; //in seconds");
        }

        //scripts for now playing bar
        $view->headScript()->appendFile($baseUrl . 'js/airtime/airtime_bootstrap.js?' . $CC_CONFIG['airtime_version'], 'text/javascript')
            ->appendFile($baseUrl . 'js/airtime/dashboard/helperfunctions.js?' . $CC_CONFIG['airtime_version'], 'text/javascript')
            ->appendFile($baseUrl . 'js/airtime/dashboard/dashboard.js?' . $CC_CONFIG['airtime_version'], 'text/javascript')
            ->appendFile($baseUrl . 'js/airtime/dashboard/versiontooltip.js?' . $CC_CONFIG['airtime_version'], 'text/javascript')
            ->appendFile($baseUrl . 'js/tipsy/jquery.tipsy.js?' . $CC_CONFIG['airtime_version'], 'text/javascript')

            ->appendFile($baseUrl . 'js/airtime/common/common.js?' . $CC_CONFIG['airtime_version'], 'text/javascript')
            ->appendFile($baseUrl . 'js/airtime/common/audioplaytest.js?' . $CC_CONFIG['airtime_version'], 'text/javascript');

        $user = Application_Model_User::getCurrentUser();
        if (!is_null($user)) {
            $userType = $user->getType();
        } else {
            $userType = "";
        }

        $view->headScript()->appendScript("var userType = '$userType';");
        if (array_key_exists('REQUEST_URI', $_SERVER) //Doesn't exist for unit tests
            && strpos($_SERVER['REQUEST_URI'], 'Dashboard/stream-player') === false
            && strpos($_SERVER['REQUEST_URI'], 'audiopreview') === false
            && $_SERVER['REQUEST_URI'] != "/") {
            $plan_level = strval(Application_Model_Preference::GetPlanLevel());
            // Since the Hobbyist plan doesn't come with Live Chat support, don't enable it
            if (Application_Model_Preference::GetLiveChatEnabled() && $plan_level !== 'hobbyist') {
                $client_id = strval(Application_Model_Preference::GetClientId());
                $station_url = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
                $view->headScript()->appendScript("var livechat_client_id = '$client_id';\n" .
                    "var livechat_plan_type = '$plan_level';\n" .
                    "var livechat_station_url = 'http://$station_url';");
                $view->headScript()->appendFile($baseUrl . 'js/airtime/common/livechat.js?' . $CC_CONFIG['airtime_version'], 'text/javascript');
            }
        }

        /*
        if (isset($CC_CONFIG['demo']) && $CC_CONFIG['demo'] == 1) {
            $view->headScript()->appendFile($baseUrl.'js/libs/google-analytics.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        }*/
    }

    protected function _initViewHelpers()
    {
        $view = $this->getResource('view');
        $view->addHelperPath(APPLICATION_PATH . 'views/helpers', 'Airtime_View_Helper');
        $view->assign('suspended', (Application_Model_Preference::getProvisioningStatus() == PROVISIONING_STATUS_SUSPENDED));
    }

    protected function _initTitle()
    {
        $view = $this->getResource('view');
        $view->headTitle(Application_Model_Preference::GetHeadTitle());
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
    }
}

