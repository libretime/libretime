<?php

define('ROOT_PATH', dirname( __DIR__) . '/');
define('WEB_ROOT_PATH', __DIR__ . '/');
define('LIB_PATH', ROOT_PATH . 'library/');
define('BUILD_PATH', ROOT_PATH . 'build/');
define('SETUP_DIR', 'airtime-setup/');

// Define path to application directory
define('APPLICATION_PATH', ROOT_PATH . 'application');

define('AIRTIME_CONFIG', 'airtime.conf');

require_once(APPLICATION_PATH . "/configs/conf.php");
require_once(BUILD_PATH . SETUP_DIR . 'load.php');

function showConfigCheckPage() {
    airtimeConfigureDatabase();

    require_once(WEB_ROOT_PATH . 'config-check.php');
    die();
}

if (array_key_exists('config', $_GET)) {
    showConfigCheckPage();
}

// If a configuration file exists, forward to our boot script
if (file_exists(BUILD_PATH . AIRTIME_CONFIG)) {
    airtimeConfigureDatabase();

    // If the database doesn't exist, or is improperly configured,
    // show the user a configuration error page so they know what went wrong
    if (!airtimeCheckDatabase()) {
        showConfigCheckPage();
    }

    require_once(WEB_ROOT_PATH . 'airtime-boot.php');
}
// Otherwise, we'll need to run our configuration setup
else {
    require_once(BUILD_PATH . SETUP_DIR . 'setup-config.php');
}

