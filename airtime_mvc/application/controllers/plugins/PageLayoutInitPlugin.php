<?php

/** Our standard page layout initialization has to be done via a plugin
 * because some of it requires session variables, and some of the routes
 * run without a session (like API calls). This is an optimization because
 * starting the session adds a fair amount of overhead.
 */
class PageLayoutInitPlugin extends Zend_Controller_Plugin_Abstract
{
    protected $_bootstrap = null;

    public function __construct($boostrap) {
        $this->_bootstrap = $boostrap;
    }

    /**
     * Start the session depending on which controller your request is going to.
     * We start the session explicitly here so that we can avoid starting sessions
     * needlessly for (stateless) requests to the API.
     * @param Zend_Controller_Request_Abstract $request
     * @throws Zend_Session_Exception
     */
    public function routeShutdown(Zend_Controller_Request_Abstract $request)
    {
        $controller = strtolower($request->getControllerName());
        $action = strtolower($request->getActionName());

        //List of controllers where we don't need a session, and we don't need
        //all the standard HTML / JS boilerplate.
        if (!in_array($controller, array(
            "index", //Radio Page
            "api",
            "auth",
            "error",
            "upgrade",
            "embed",
            "feeds"
        ))
        ) {
            //Start the session
            Zend_Session::start();
            Application_Model_Auth::pinSessionToClient(Zend_Auth::getInstance());

            //localization configuration
            Application_Model_Locale::configureLocalization();

            $this->_initGlobals();
            $this->_initCsrfNamespace();
            $this->_initHeadLink();
            $this->_initHeadScript();
            $this->_initTitle();
            $this->_initTranslationGlobals();
            $this->_initViewHelpers();
        }

        // Skip upgrades and task management when running unit tests
        if (getenv("AIRTIME_UNIT_TEST") != 1) {
            $taskManager = TaskManager::getInstance();

            // Run the upgrade on each request (if it needs to be run)
            // We can't afford to wait 7 minutes to run an upgrade: users could
            // have several minutes of database errors while waiting for a
            // schema change upgrade to happen after a deployment
            $taskManager->runTask('UpgradeTask');

            // Piggyback the TaskManager onto API calls. This provides guaranteed consistency
            // (there is at least one API call made from pypo to Airtime every 7 minutes) and
            // greatly reduces the chances of lock contention on cc_pref while the TaskManager runs
            if ($controller == "api") {
                $taskManager->runTasks();
            }
        }
    }

    protected function _initGlobals()
    {
        if (!Zend_Session::isStarted()) {
            return;
        }

        $view = $this->_bootstrap->getResource('view');
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

        // Dropzone also accept file extensions and doesn't correctly extract certain mimetypes (eg. FLAC - try it),
        // so we append the file extensions to the list of mimetypes and that makes it work.
        $mimeTypes = FileDataHelper::getAudioMimeTypeArray();
        $fileExtensions = array_values($mimeTypes);
        foreach($fileExtensions as &$extension) {
            $extension = '.' . $extension;
        }
        $view->headScript()->appendScript("var acceptedMimeTypes = " . json_encode(array_merge(array_keys($mimeTypes), $fileExtensions)) . ";");
    }

    /**
     * Create a global namespace to hold a session token for CSRF prevention
     */
    protected function _initCsrfNamespace()
    {
        /*
        if (!Zend_Session::isStarted()) {
            return;
        }*/

        $csrf_namespace = new Zend_Session_Namespace('csrf_namespace');
        // Check if the token exists
        if (!$csrf_namespace->authtoken) {
            // If we don't have a token, regenerate it and set a 1 week timeout
            // Should we log the user out here if the token is expired?
            $csrf_namespace->authtoken = sha1(uniqid(rand(), 1));
            $csrf_namespace->setExpirationSeconds(168 * 60 * 60);
        }

        //Here we are closing the session for writing because otherwise no requests
        //in this session will be handled in parallel. This gives a major boost to the perceived performance
        //of the application (page load times are more consistent, no lock contention).
        session_write_close();
        //Zend_Session::writeClose(true);
    }

    /**
     * Ideally, globals should be written to a single js file once
     * from a php init function. This will save us from having to
     * reinitialize them every request
     */
    private function _initTranslationGlobals()
    {
        $view = $this->_bootstrap->getResource('view');
        $view->headScript()->appendScript("var PRODUCT_NAME = '" . PRODUCT_NAME . "';");
        $view->headScript()->appendScript("var USER_MANUAL_URL = '" . USER_MANUAL_URL . "';");
        $view->headScript()->appendScript("var COMPANY_NAME = '" . COMPANY_NAME . "';");
        //Each page refresh or tab open has uniqID, not to be used for security
        $view->headScript()->appendScript("var UNIQID = '" . uniqid() . "';");

        $track_type_options = array();
        $track_types = Application_Model_Tracktype::getTracktypes();
        
        array_multisort(array_map(function($element) {
            return $element['type_name'];
        }, $track_types), SORT_ASC, $track_types);
        
        foreach ($track_types as $key => $tt) {
            $track_type_options[$tt['code']] = $tt['type_name'];
        }
        $ttarr = json_encode($track_type_options, JSON_FORCE_OBJECT);
        $view->headScript()->appendScript("var TRACKTYPES = " . $ttarr . ";");
    }

    protected function _initHeadLink()
    {
        $CC_CONFIG = Config::getConfig();

        $view = $this->_bootstrap->getResource('view');

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
        if (!Zend_Session::isStarted()) {
            return;
        }

        $CC_CONFIG = Config::getConfig();

        $view = $this->_bootstrap->getResource('view');

        $baseUrl = Application_Common_OsPath::getBaseDir();

        $view->headScript()->appendFile($baseUrl . 'js/libs/jquery-1.8.3.min.js?' . $CC_CONFIG['airtime_version'], 'text/javascript')
            ->appendFile($baseUrl . 'js/libs/jquery-ui-1.8.24.min.js?' . $CC_CONFIG['airtime_version'], 'text/javascript')
            ->appendFile($baseUrl . 'js/libs/angular.min.js?' . $CC_CONFIG['airtime_version'], 'text/javascript')
            ->appendFile($baseUrl . 'js/bootstrap/bootstrap.js?' . $CC_CONFIG['airtime_version'], 'text/javascript')
            ->appendFile($baseUrl . 'js/libs/underscore-min.js?' . $CC_CONFIG['airtime_version'], 'text/javascript')

            // ->appendFile($baseUrl . 'js/libs/jquery.stickyPanel.js?' . $CC_CONFIG['airtime_version'], 'text/javascript')
            ->appendFile($baseUrl . 'js/qtip/jquery.qtip.js?' . $CC_CONFIG['airtime_version'], 'text/javascript')
            ->appendFile($baseUrl . 'js/jplayer/jquery.jplayer.min.js?' . $CC_CONFIG['airtime_version'], 'text/javascript')
            ->appendFile($baseUrl . 'js/sprintf/sprintf-0.7-beta1.js?' . $CC_CONFIG['airtime_version'], 'text/javascript')
            ->appendFile($baseUrl . 'js/cookie/js.cookie.js?' . $CC_CONFIG['airtime_version'], 'text/javascript')
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
    }

    protected function _initViewHelpers()
    {
        $view = $this->_bootstrap->getResource('view');
        $view->addHelperPath(APPLICATION_PATH . 'views/helpers', 'Airtime_View_Helper');
    }

    protected function _initTitle()
    {
        $view = $this->_bootstrap->getResource('view');
        $view->headTitle(Application_Model_Preference::GetHeadTitle());
    }
}
