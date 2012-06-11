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
        $mailServerConfigured = Application_Model_Preference::GetMailServerConfigured() == true ? true : false;
        if ($mailServerConfigured) {
            $username = Application_Model_Preference::GetMailServerEmailAddress();
            $password = Application_Model_Preference::GetMailServerPassword();
            $mailServer = Application_Model_Preference::GetMailServer();
            $mailServerPort = Application_Model_Preference::GetMailServerPort();
            if (!empty($mailServerPort)) {
                $port = Application_Model_Preference::GetMailServerPort();
            }
            
            $config = array(
                'auth' => 'login',
                'ssl' => 'ssl',
                'username' => $username,
                'password' => $password
            );
            
            if (isset($port)) {
                $config['port'] = $port;
            }
		    
            $transport = new Zend_Mail_Transport_Smtp($mailServer, $config); 	
        }
        
        $mail = new Zend_Mail('utf-8');
        $mail->setSubject($subject);
        $mail->setBodyText($message);
		
        foreach ((array) $tos as $to) {
            $mail->addTo($to);
        }

        if ($mailServerConfigured) {
            $mail->setFrom(isset($from) ? $from : Application_Model_Preference::GetMailServerEmailAddress());
            $mail->send($transport);
        } else {
            $mail->setFrom(isset($from) ? $from : Application_Model_Preference::GetSystemEmail());
            $mail->send();
        }    
    }
}
