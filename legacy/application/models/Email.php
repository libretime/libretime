<?php

declare(strict_types=1);

class Application_Model_Email
{
    /**
     * Send email.
     *
     * @param string $subject
     * @param string $message
     * @param mixed  $to
     *
     * @return bool
     */
    public static function send($subject, $message, $to)
    {
        $headers = sprintf('From: %s <%s>', SAAS_PRODUCT_BRANDING_NAME, LIBRETIME_EMAIL_FROM);

        return mail($to, $subject, $message, $headers);
    }
}
