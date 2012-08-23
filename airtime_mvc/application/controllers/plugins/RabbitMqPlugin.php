<?php

class RabbitMqPlugin extends Zend_Controller_Plugin_Abstract
{
    public function dispatchLoopShutdown()
    {
        if (Application_Model_RabbitMq::$doPush) {
            $md = array('schedule' => Application_Model_Schedule::GetScheduledPlaylists());
            Application_Model_RabbitMq::SendMessageToPypo("update_schedule", $md);
            if (!isset($_SERVER['AIRTIME_SRV'])) {
                Application_Model_RabbitMq::SendMessageToShowRecorder("update_recorder_schedule");
            }
        }

        if (memory_get_peak_usage() > 25*pow(2, 20)) {

            Logging::debug("Peak memory usage: "
                .(memory_get_peak_usage()/1000000)
                ." MB while accessing URI ".$_SERVER['REQUEST_URI']);
            Logging::debug("Should try to keep memory footprint under 25 MB");
        }
    }
}
