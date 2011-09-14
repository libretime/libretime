<?php

class Application_Form_Login extends Zend_Form
{

    public function init()
    { 
		// Set the method for the display form to POST
        $this->setMethod('post');

		// Add username element
        $this->addElement('text', 'username', array(
            'label'      => 'Username:',
            'class'      => 'input_text',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                'NotEmpty',
            )
        ));

		// Add password element
        $this->addElement('password', 'password', array(
            'label'      => 'Password:',
            'class'      => 'input_text',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                'NotEmpty',
            )
        ));
        
        $recaptchaNeeded = false;
        if(Application_Model_LoginAttempts::getAttempts($_SERVER['REMOTE_ADDR']) >= 3){
            $recaptchaNeeded = true;
        }
        if($recaptchaNeeded){
            // recaptcha
            $this->addRecaptcha();
        }
        
		// Add the submit button
        $this->addElement('submit', 'submit', array(
            'ignore'   => true,
            'label'    => 'Login',
            'class'      => 'ui-button ui-widget ui-state-default ui-button-text-only center'
        ));

    }
    
    public function addRecaptcha(){
        $pubKey = '6Ld4JsISAAAAAIxUKT4IjjOGi3DHqdoH2zk6WkYG';
        $privKey = '6Ld4JsISAAAAAJynYlXdrE4hfTReTSxYFe5szdyv';
        
        $recaptcha = new Zend_Service_ReCaptcha($pubKey, $privKey);
        
        $captcha = new Zend_Form_Element_Captcha('captcha',
            array(
                'label' => 'Type the characters you see in the picture below.',
                'captcha' =>  'ReCaptcha',
                'captchaOptions'        => array(
                    'captcha'   => 'ReCaptcha',
                    'service' => $recaptcha
                )
            )
        );
        $this->addElement($captcha);
    }


}

