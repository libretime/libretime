<?php

class Application_Model_Email {
	
    /**
     * Send email
     *
     * @param string $subject
     * @param string $message
     * @param mixed $tos
     * @return void
     */
    public static function send($subject, $message, $tos, $from = null)
    {
        $mail = new Zend_Mail('utf-8');
        $mail->setSubject($subject);
        $mail->setBodyText($message);
        $mail->setFrom(isset($from) ? $from : Application_Model_Preference::GetSystemEmail());

        foreach ((array) $tos as $to) {
            $mail->addTo($to);
        }

        $mail->send();
    }
}