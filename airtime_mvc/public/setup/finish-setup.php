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
    
    const AIRTIME_CONF_PATH = "/etc/airtime/airtime.conf";

    function __construct($settings) {
    }

    function runSetup() {
        $message = null;
        $errors = array();

        if (file_exists("/etc/airtime/")) {
            if (!$this->moveAirtimeConfig()) {
                $message = "Error moving airtime.conf or deleting /tmp/airtime.conf.temp!";
                $errors[] = "ERR";
            }
        } else {
            $message = "Failed to move airtime.conf; /etc/airtime doesn't exist!";
            $errors[] = "ERR";
        }
        
        if (empty($errors)) {
            // Write service configurations for pypo and media-monitor
            $this->startServices();
        }
        
        return array(
            "message" => $message,
            "errors" => $errors,
        );
    }
    
    /**
     * Moves /tmp/airtime.conf.temp to /etc/airtime.conf and then removes it to complete setup
     * @return boolean false if either of the copy or removal operations fail
     */
    function moveAirtimeConfig() {
        return copy(AIRTIME_CONF_TEMP_PATH, self::AIRTIME_CONF_PATH)
            && unlink(AIRTIME_CONF_TEMP_PATH);
    }
    
    function startServices() {
        exec("service airtime-media-monitor start");
        exec("service airtime-playout start");
        exec("service airtime-liquidsoap start");
    }

}