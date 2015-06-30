<?php

$configRun = false;
$extensions = get_loaded_extensions();
$airtimeSetup = false;

function showConfigCheckPage() {
    global $configRun;
    if (!$configRun) {
        // This will run any necessary setup we need if
        // configuration hasn't been initialized
        checkConfiguration();
    }

    require_once(CONFIG_PATH . 'config-check.php');
    die();
}

function isApiCall() {
    $path = $_SERVER['PHP_SELF'];
    return strpos($path, "api") !== false;
}

// Define application path constants
define('ROOT_PATH', dirname( __DIR__) . '/');
define('LIB_PATH', ROOT_PATH . 'library/');
define('BUILD_PATH', ROOT_PATH . 'build/');
define('SETUP_PATH', BUILD_PATH . 'airtime-setup/');
define('APPLICATION_PATH', ROOT_PATH . 'application/');
define('CONFIG_PATH', APPLICATION_PATH . 'configs/');
define('VENDOR_PATH', ROOT_PATH . '../vendor/');

define("AIRTIME_CONFIG_STOR", "/etc/airtime/");

define('AIRTIME_CONFIG', 'airtime.conf');

//Vendors (Composer)
set_include_path(VENDOR_PATH . PATH_SEPARATOR . get_include_path());

// Ensure library/ is on include_path
set_include_path(LIB_PATH . PATH_SEPARATOR . get_include_path());

if (!@include_once(VENDOR_PATH . 'propel/propel1/runtime/lib/Propel.php'))
{
    die('Error: Propel not found. Did you install Airtime\'s third-party dependencies with composer? (Check the README.)');
}

require_once(CONFIG_PATH . 'conf.php');
require_once(SETUP_PATH . 'load.php');

// This allows us to pass ?config as a parameter to any page
// and get to the config checklist.
if (array_key_exists('config', $_GET)) {
    showConfigCheckPage();
}

$filename = isset($_SERVER['AIRTIME_CONF']) ?
    $_SERVER['AIRTIME_CONF'] : AIRTIME_CONFIG_STOR . AIRTIME_CONFIG;

// If a configuration file exists, forward to our boot script
if (file_exists($filename)) {
    require_once(APPLICATION_PATH . 'airtime-boot.php');
}
// Otherwise, we'll need to run our configuration setup
else {
    // Sometimes we can get into a weird NFS state where a station's airtime.conf has
    // been neg-cached - redirect to a 404 instead until the NFS cache is updated
    if (strpos($_SERVER['SERVER_NAME'], "airtime.pro") !== false) {
        header($_SERVER['SERVER_PROTOCOL'] . ' 404 Page Not Found', true, 404);
        exit;
    }
    $airtimeSetup = true;
    require_once(SETUP_PATH . 'setup-config.php');
}
