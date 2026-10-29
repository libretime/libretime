<?php

class Application_Model_Email
{
    /**
     * Send email.
     *
     * @param string $subject
     * @param string $message
     * @param mixed  $to
     */
    public static function send($subject, $message, $to): string
    {
        return Celery::sendTask('libretime_api.core.tasks.send_mail', [], [
            'subject' => $subject,
            'message' => $message,
            'recipients' => [$to],
        ]);
    }
}
