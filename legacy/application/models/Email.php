<?php

class Application_Model_Email
{
    /**
     * Send email.
     *
     * @param string $subject
     * @param string $message
     * @param mixed  $to
     *
     * @return string
     */
    public static function send($subject, $message, $to)
    {
        return Celery::sendTask('libretime_api.core.tasks.send_mail', [], [
            'subject' => $subject,
            'message' => $message,
            'recipients' => [$to],
        ]);
    }
}
