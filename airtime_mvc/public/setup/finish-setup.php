<?php

/**
 * User: sourcefabric
 * Date: 09/12/14
 *
 * Class FinishSetup
 *
 * Wrapper class for finalizing and moving airtime.conf
 */
class FinishSetup extends Setup {

    function __construct($settings) {
    }

    function runSetup() {
        if ($this->createAirtimeConfigDirectory()) {
            $this->moveAirtimeConfig();
        }
    }

    function createAirtimeConfigDirectory() {
        return file_exists("/etc/airtime/") ? true
            : mkdir("/etc/airtime/", 0755, true);
    }

    function moveAirtimeConfig() {
        return copy(AIRTIME_CONF_TEMP_PATH, "/etc/airtime/airtime.conf");
    }

}