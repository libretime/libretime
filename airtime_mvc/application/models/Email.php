<?php

class Application_Model_Email
{

    /**
     * Send email
     *
     * @param  string $subject
     * @param  string $message
     * @param  mixed  $to
     * @return boolean
     */
    public static function send($subject, $message, $to) {

        $headers = 'From: Airtime <noreply@account.sourcefabric.com>';
        return mail($to, $subject, $message, $headers);

    }

}
