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

        $headers = sprintf('From: %s <noreply@account.sourcefabric.com>', SAAS_PRODUCT_BRANDING_NAME);
        return mail($to, $subject, $message, $headers);

    }

}
