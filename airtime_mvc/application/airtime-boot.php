<?php

//  Only enable cookie secure if we are supporting https.
//  Ideally, this would always be on and we would force https,
//  but the default installation configs are likely to be installed by
//  amature users on the setup that does not have https.  Forcing
//  cookie_secure on non https would result in confusing login problems.
if(!empty($_SERVER['HTTPS'])) {
    ini_set('session.cookie_secure', '1');
}
ini_set('session.cookie_httponly', '1');

error_reporting(E_ALL|E_STRICT);

function exception_error_handler($errno, $errstr, $errfile, $errline) {
    //Check if the statement that threw this error wanted its errors to be
    //suppressed. If so then return without with throwing exception.
    if (0 === error_reporting()) return;
    throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
    return false;
}

set_error_handler("exception_error_handler");

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

defined('VERBOSE_STACK_TRACE')
    || define('VERBOSE_STACK_TRACE', (getenv('VERBOSE_STACK_TRACE') ? getenv('VERBOSE_STACK_TRACE') : true));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    get_include_path(),
    realpath(LIB_PATH)
)));

set_include_path(APPLICATION_PATH . 'common' . PATH_SEPARATOR . get_include_path());
set_include_path(APPLICATION_PATH . 'common/enum' . PATH_SEPARATOR . get_include_path());
set_include_path(APPLICATION_PATH . 'common/interface' . PATH_SEPARATOR . get_include_path());

//Propel classes.
set_include_path(APPLICATION_PATH . 'models' . PATH_SEPARATOR . get_include_path());

//Controller plugins.
set_include_path(APPLICATION_PATH . 'controllers' . PATH_SEPARATOR . get_include_path());

//Controller plugins.
set_include_path(APPLICATION_PATH . 'controllers/plugins' . PATH_SEPARATOR . get_include_path());

//Services.
set_include_path(APPLICATION_PATH . '/services/' . PATH_SEPARATOR . get_include_path());

//cloud storage directory
set_include_path(APPLICATION_PATH . '/cloud_storage' . PATH_SEPARATOR . get_include_path());

//Upgrade directory
set_include_path(APPLICATION_PATH . '/upgrade/' . PATH_SEPARATOR . get_include_path());

//Common directory
set_include_path(APPLICATION_PATH . '/common/' . PATH_SEPARATOR . get_include_path());

//Composer's autoloader
require_once 'autoload.php';

/** Zend_Application */
$application = new Zend_Application(
        APPLICATION_ENV,
	CONFIG_PATH . 'application.ini',
	true
);

require_once(APPLICATION_PATH . "logging/Logging.php");
Logging::setLogPath(LIBRETIME_LOG_DIR . '/zendphp.log');
Logging::setupParseErrorLogging();

// Create application, bootstrap, and run
try {
    $sapi_type = php_sapi_name();
    if (substr($sapi_type, 0, 3) == 'cli') {
        set_include_path(APPLICATION_PATH . PATH_SEPARATOR . get_include_path());
        require_once("Bootstrap.php");
    } else {
        $application->bootstrap()->run();
    }
} catch (Exception $e) {
    if ($e->getCode() == 401)
    {
        header($_SERVER['SERVER_PROTOCOL'] . ' 401 Unauthorized', true, 401);
        return;
    }

    header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
    Logging::error($e->getMessage());

    if (VERBOSE_STACK_TRACE) {
        echo $e->getMessage();
        echo "<pre>";
        echo $e->getTraceAsString();
        echo "</pre>";
        Logging::error($e->getMessage());
        Logging::error($e->getTraceAsString());
    } else {
        Logging::error($e->getTrace());
    }
    throw $e;
}

