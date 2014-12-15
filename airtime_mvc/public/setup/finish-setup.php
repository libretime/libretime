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
        $message = null;
        $errors = array();

        if ($this->checkAirtimeConfigDirectory()) {
            if (!$this->moveAirtimeConfig()) {
                $message = "Error moving airtime.conf or deleting /tmp/airtime.conf.temp!";
                $errors[] = "ERR";
            }
        } else {
            $message = "Failed to move airtime.conf; /etc/airtime doesn't exist!";
            $errors[] = "ERR";
        }

        return array(
            "message" => $message,
            "errors" => $errors,
        );
    }

    function checkAirtimeConfigDirectory() {
        return file_exists("/etc/airtime/");
    }

    function moveAirtimeConfig() {
        return copy(AIRTIME_CONF_TEMP_PATH, "/etc/airtime/airtime.conf")
            && unlink(AIRTIME_CONF_TEMP_PATH);
    }

}