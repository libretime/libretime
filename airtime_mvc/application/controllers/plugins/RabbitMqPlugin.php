<?php

class RabbitMqPlugin extends Zend_Controller_Plugin_Abstract
{
    public function dispatchLoopShutdown()
    {
        if (RabbitMq::$doPush) {
            $md = array('schedule' => Schedule::GetScheduledPlaylists());
            RabbitMq::SendMessageToPypo("update_schedule", $md);
        }
    }
}