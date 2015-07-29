<?php

class Application_Model_Email
{
    /**
     * Send email
     *
     * @param  string $subject
     * @param  string $message
     * @param  mixed  $tos
     * @return void
     */
    public static function send($subject, $message, $tos, $from = null)
    {

        return mail($tos, $subject, $message);

    }
}
