<?php

require_once dirname(__DIR__) . '/application/preload.php';

$configRun = false;
$extensions = get_loaded_extensions();
$airtimeSetup = false;

function showConfigCheckPage()
{
    global $configRun;
    if (!$configRun) {
        // This will run any necessary setup we need if
        // configuration hasn't been initialized
        checkConfiguration();
    }

    require_once CONFIG_PATH . '/config-check.php';

    exit();
}

function isApiCall()
{
    $path = $_SERVER['PHP_SELF'];

    return strpos($path, 'api') !== false;
}

//Rest Module Controllers - for custom Rest_RouteController.php
set_include_path(APPLICATION_PATH . '/modules/rest/controllers/' . PATH_SEPARATOR . get_include_path());

// Vendors (Composer, zend-loader is explicitly specified due to https://github.com/zf1/zend-application/pull/2#issuecomment-102599655)
set_include_path(VENDOR_PATH . PATH_SEPARATOR . VENDOR_PATH . '/zf1s/zend-loader/library/' . PATH_SEPARATOR . get_include_path());

// Ensure library/ is on include_path
set_include_path(LIB_PATH . PATH_SEPARATOR . get_include_path());

if (!class_exists('Propel')) {
    exit('Error: Propel not found. Did you install Airtime\'s third-party dependencies with composer? (Check the README.)');
}

require_once SETUP_PATH . '/load.php';

// This allows us to pass ?config as a parameter to any page
// and get to the config checklist.
if (array_key_exists('config', $_GET)) {
    showConfigCheckPage();
}

// If a configuration file exists, forward to our boot script
if (file_exists(LIBRETIME_CONFIG_FILEPATH)) {
    require_once APPLICATION_PATH . '/airtime-boot.php';
}
// Otherwise, we'll need to run our configuration setup
else {
    $airtimeSetup = true;

    require_once SETUP_PATH . '/setup-config.php';
}
