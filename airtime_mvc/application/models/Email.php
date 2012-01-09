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
    	/*
        $configMail = array( 'auth' => 'login',
                             'username' => 'user@gmail.com',
                             'password' => 'password',
                             'ssl' => 'ssl',
                             'port' => 465
        );
        $mailTransport = new Zend_Mail_Transport_Smtp('smtp.gmail.com',$configMail);
        */
    	
        $mail = new Zend_Mail('utf-8');
        $mail->setSubject($subject);
        $mail->setBodyText($message);
        $mail->setFrom(isset($from) ? $from : 'naomi.aro@sourcefabric.org');

        foreach ((array) $tos as $to) {
            $mail->addTo($to);
        }

        $mail->send();
    }
}