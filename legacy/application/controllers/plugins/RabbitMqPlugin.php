<?php

class RabbitMqPlugin extends Zend_Controller_Plugin_Abstract
{
    public function dispatchLoopShutdown()
    {
        if (Application_Model_RabbitMq::$doPush) {
            $md = ['schedule' => Application_Model_Schedule::getSchedule()];
            Application_Model_RabbitMq::SendMessageToPypo('update_schedule', $md);
            Application_Model_RabbitMq::SendMessageToShowRecorder('update_recorder_schedule');
        }

        if (memory_get_peak_usage() > 30 * 2 ** 20) {
            Logging::debug('Peak memory usage: '
                . (memory_get_peak_usage() / 1000000)
                . ' MB while accessing URI ' . $_SERVER['REQUEST_URI']);
            Logging::debug('Should try to keep memory footprint under 25 MB');
        }
    }
}
