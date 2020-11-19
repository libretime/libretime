<?php
error_reporting(E_ALL | E_STRICT);

// load composer autoloader
require_once __DIR__.'/../../../vendor/autoload.php';

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../../application/'));

// Define path to configs directory
define('CONFIG_PATH', APPLICATION_PATH . '/configs/');

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'testing'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath('./library'),
    get_include_path(),
)));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    get_include_path(),
    realpath(APPLICATION_PATH . '/../library')
)));

// Ensure vendor/ is on the include path
set_include_path(implode(PATH_SEPARATOR, array(
    get_include_path(),
    realpath(APPLICATION_PATH . '/../../vendor'),
    realpath(APPLICATION_PATH . '/../../vendor/zf1s/zend-loader/library')
)));

set_include_path(implode(PATH_SEPARATOR, array(
    get_include_path(),
    realpath(APPLICATION_PATH . '/../../vendor/propel/propel1/runtime/lib')
)));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    get_include_path(),
    realpath(APPLICATION_PATH . '/../../install_minimal/include')
)));

require_once CONFIG_PATH . '/constants.php';

Logging::setLogPath(LIBRETIME_LOG_DIR . '/zendphp.log');

set_include_path(APPLICATION_PATH . '/common' . PATH_SEPARATOR . get_include_path());

//Propel classes.
set_include_path(APPLICATION_PATH . '/models' . PATH_SEPARATOR . get_include_path());

//Services
set_include_path(APPLICATION_PATH . '/services' . PATH_SEPARATOR . get_include_path());

//models
set_include_path(APPLICATION_PATH . '/models' . PATH_SEPARATOR . get_include_path());

//Controllers.
set_include_path(APPLICATION_PATH . '/controllers' . PATH_SEPARATOR . get_include_path());

//Controller plugins.
set_include_path(APPLICATION_PATH . '/controllers/plugins' . PATH_SEPARATOR . get_include_path());

//test data
set_include_path(APPLICATION_PATH . '/../tests/application/testdata' . PATH_SEPARATOR . get_include_path());

//helper functions
set_include_path(APPLICATION_PATH . '/../tests/application/helpers' . PATH_SEPARATOR . get_include_path());

//cloud storage files
set_include_path(APPLICATION_PATH . '/cloud_storage' . PATH_SEPARATOR . get_include_path());

require_once APPLICATION_PATH.'/configs/conf.php';
require_once 'propel/propel1/runtime/lib/Propel.php';
Propel::init("../application/configs/airtime-conf-production.php");

Zend_Session::start();

