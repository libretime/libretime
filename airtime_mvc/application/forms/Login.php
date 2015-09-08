<?php

include('../library/phing/util/StringHelper.php');

class Application_Form_Login extends Zend_Form
{

    public function init()
    {
        $CC_CONFIG = Config::getConfig();

        // Set the method for the display form to POST
        $this->setMethod('post');

        //If the request comes from an origin we consider safe, we disable the CSRF
        //token checking ONLY for the login page. We do this to allow logins from WHMCS to work.
        $request = Zend_Controller_Front::getInstance()->getRequest();
        if ($request) {
            $refererUrl = $request->getHeader('referer');
            $originIsSafe = false;
            foreach (CORSHelper::getAllowedOrigins() as $safeOrigin) {
                if (StringHelper::startsWith($safeOrigin, $refererUrl)) {
                    $originIsSafe = true;
                    break;
                }
            }
        }

        if (!$originIsSafe) {
            $this->addElement('hash', 'csrf', array(
               'salt' => 'unique'
            ));
        }

        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'form/login.phtml'))
        ));

        // Add username element
        $username = new Zend_Form_Element_Text("username");
        $username->setLabel(_('Username:'))
            ->setAttribs(array(
                'autofocus' => 'true',
                'class' => 'input_text',
                'required' => 'true'))
            ->setValue((isset($CC_CONFIG['demo']) && $CC_CONFIG['demo'] == 1)?'admin':'')
            ->addFilter('StringTrim')
            ->setDecorators(array('ViewHelper'))
            ->setValidators(array('NotEmpty'));
        $this->addElement($username);

        // Add password element
        $this->addElement('password', 'password', array(
            'label'      => _('Password:'),
            'class'      => 'input_text',
            'required'   => true,
            'value'      => (isset($CC_CONFIG['demo']) && $CC_CONFIG['demo'] == 1)?'admin':'',
            'filters'    => array('StringTrim'),
            'validators' => array(
                'NotEmpty',
            ),
            'decorators' => array(
                'ViewHelper'
            )
        ));
        
        $locale = new Zend_Form_Element_Select("locale");
        $locale->setLabel(_("Language:"));
        $locale->setMultiOptions(Application_Model_Locale::getLocales());
        $locale->setDecorators(array('ViewHelper'));
        $this->addElement($locale);
        $this->setDefaults(array(
            "locale" => Application_Model_Locale::getUserLocale()
        ));

        if (Application_Model_LoginAttempts::getAttempts($_SERVER['REMOTE_ADDR']) >= 3) {
            $this->addRecaptcha();
        }

        // Add the submit button
        $this->addElement('submit', 'submit', array(
            'ignore'   => true,
            'label'    => _('Login'),
            'class'      => 'ui-button ui-widget ui-state-default ui-button-text-only center',
            'decorators' => array(
                'ViewHelper'
            )
        ));

    }

    public function addRecaptcha()
    {
        $pubKey = '6Ld4JsISAAAAAIxUKT4IjjOGi3DHqdoH2zk6WkYG';
        $privKey = '6Ld4JsISAAAAAJynYlXdrE4hfTReTSxYFe5szdyv';

        $params= array('ssl' => true);
        $recaptcha = new Zend_Service_ReCaptcha($pubKey, $privKey, $params);

        $captcha = new Zend_Form_Element_Captcha('captcha',
            array(
                'label' => _('Type the characters you see in the picture below.'),
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
