<?php

global $configRun;

function showConfigCheckPage() {
    if (!isset($configRun) || !$configRun) {
        // This will run any necessary setup we need if
        // configuration hasn't been initialized
        airtimeCheckConfiguration();
    }

    require_once(WEB_ROOT_PATH . 'config-check.php');
    die();
}

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

if (array_key_exists('config', $_GET)) {
    showConfigCheckPage();
}

// If a configuration file exists, forward to our boot script
if (file_exists(BUILD_PATH . AIRTIME_CONFIG)) {
    /*
     * Even if the user has been through the setup process and
     * created an airtime.conf file (or they may have simply
     * copied the example file) their settings aren't necessarily
     * correctly configured.
     *
     * If something is improperly configured, show the user a
     * configuration checklist page so they know what went wrong
     */
    if (!airtimeCheckConfiguration()) {
        $configRun = true;
        showConfigCheckPage();
    }

    require_once(WEB_ROOT_PATH . 'airtime-boot.php');
}
// Otherwise, we'll need to run our configuration setup
else {
    require_once(BUILD_PATH . SETUP_DIR . 'setup-config.php');
}

