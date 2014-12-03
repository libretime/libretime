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

// Define application path constants
define('ROOT_PATH', dirname( __DIR__) . '/');
define('LIB_PATH', ROOT_PATH . 'library/');
define('BUILD_PATH', ROOT_PATH . 'build/');
define('SETUP_PATH', BUILD_PATH . 'airtime-setup/');
define('APPLICATION_PATH', ROOT_PATH . 'application/');
define('CONFIG_PATH', APPLICATION_PATH . 'configs/');

define('AIRTIME_CONFIG', 'airtime.conf');

require_once(LIB_PATH . "propel/runtime/lib/Propel.php");
require_once(CONFIG_PATH . 'conf.php');
require_once(SETUP_PATH . 'load.php');

// This allows us to pass ?config as a parameter to any page
// and get to the config checklist.
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
    if (!checkConfiguration()) {
        $configRun = true;
        showConfigCheckPage();
    }

    require_once(APPLICATION_PATH . 'airtime-boot.php');
}
// Otherwise, we'll need to run our configuration setup
else {
    $airtimeSetup = true;
    require_once(SETUP_PATH . 'setup-config.php');
}

