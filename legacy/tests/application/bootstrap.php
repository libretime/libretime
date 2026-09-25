<?php

require_once dirname(__DIR__, 2) . '/application/preload.php';

error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');
ini_set('error_log', LIBRETIME_LOG_FILEPATH);

// Make PHPUnit hand deprecations back to PHP instead of converting them to exceptions.
if (class_exists('PHPUnit_Framework_Error_Deprecated')) {
    PHPUnit_Framework_Error_Deprecated::$enabled = false;
}

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, [
    realpath('./library'),
    get_include_path(),
]));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, [
    get_include_path(),
    realpath(APPLICATION_PATH . '/../library'),
]));

// Ensure vendor/ is on the include path
set_include_path(implode(PATH_SEPARATOR, [
    get_include_path(),
    realpath(APPLICATION_PATH . '/../vendor'),
    realpath(APPLICATION_PATH . '/../vendor/zf1s/zend-loader/library'),
]));

set_include_path(implode(PATH_SEPARATOR, [
    get_include_path(),
    realpath(APPLICATION_PATH . '/../vendor/libretime/propel1/runtime/lib'),
]));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, [
    get_include_path(),
    realpath(APPLICATION_PATH . '/../../install_minimal/include'),
]));

Logging::setLogPath(LIBRETIME_LOG_FILEPATH);

set_include_path(APPLICATION_PATH . '/common' . PATH_SEPARATOR . get_include_path());

// Propel classes.
set_include_path(APPLICATION_PATH . '/models' . PATH_SEPARATOR . get_include_path());

// Services
set_include_path(APPLICATION_PATH . '/services' . PATH_SEPARATOR . get_include_path());

// models
set_include_path(APPLICATION_PATH . '/models' . PATH_SEPARATOR . get_include_path());

// Controllers.
set_include_path(APPLICATION_PATH . '/controllers' . PATH_SEPARATOR . get_include_path());

// Controller plugins.
set_include_path(APPLICATION_PATH . '/controllers/plugins' . PATH_SEPARATOR . get_include_path());

// test data
set_include_path(APPLICATION_PATH . '/../tests/application/testdata' . PATH_SEPARATOR . get_include_path());

// helper functions
set_include_path(APPLICATION_PATH . '/../tests/application/helpers' . PATH_SEPARATOR . get_include_path());

// Started before Propel::init(), which triggers config validation that
// autoloads League\Uri\Uri; PHP 8.4 emits a deprecation notice at compile
// time for that class, and any output at all before Zend_Session::start()
// makes it throw.
Zend_Session::start();

require_once 'libretime/propel1/runtime/lib/Propel.php';
Propel::init('../application/configs/airtime-conf-production.php');
