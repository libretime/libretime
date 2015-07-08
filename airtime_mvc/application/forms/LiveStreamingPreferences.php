<?php

class Application_Form_LiveStreamingPreferences extends Zend_Form_SubForm
{

    public function init()
    {
        $CC_CONFIG = Config::getConfig();
        $isDemo = isset($CC_CONFIG['demo']) && $CC_CONFIG['demo'] == 1;

        $defaultFade = Application_Model_Preference::GetDefaultTransitionFade();

        $this->setDecorators(array(
                                 array('ViewScript', array('viewScript' => 'form/preferences_livestream.phtml')),
                             ));

        // automatic trasition on source disconnection
        $auto_transition = new Zend_Form_Element_Checkbox("auto_transition");
        $auto_transition->setLabel(_("Auto Switch Off:"))
                        ->setValue(Application_Model_Preference::GetAutoTransition());
        $this->addElement($auto_transition);

        // automatic switch on upon source connection
        $auto_switch = new Zend_Form_Element_Checkbox("auto_switch");
        $auto_switch->setLabel(_("Auto Switch On:"))
                        ->setValue(Application_Model_Preference::GetAutoSwitch());
        $this->addElement($auto_switch);

        // Default transition fade
        $transition_fade = new Zend_Form_Element_Text("transition_fade");
        $transition_fade->setLabel(_("Switch Transition Fade (s):"))
                        ->setFilters(array('StringTrim'))
                        ->addValidator('regex', false, array('/^\d*(\.\d+)?$/',
                                'messages' => _('Please enter a time in seconds (eg. 0.5)')))
                        ->setValue($defaultFade);
        $this->addElement($transition_fade);

        //Master username
        $master_username = new Zend_Form_Element_Text('master_username');
        $master_username->setAttrib('autocomplete', 'off')
                        ->setAllowEmpty(true)
                        ->setLabel(_('Username:'))
                        ->setFilters(array('StringTrim'))
                        ->setValue(Application_Model_Preference::GetLiveStreamMasterUsername());
        $this->addElement($master_username);

        //Master password
        if ($isDemo) {
                $master_password = new Zend_Form_Element_Text('master_password');
        } else {
                $master_password = new Zend_Form_Element_Password('master_password');
                $master_password->setAttrib('renderPassword','true');
        }
        $master_password->setAttrib('autocomplete', 'off')
                        ->setAttrib('renderPassword','true')
                        ->setAllowEmpty(true)
                        ->setValue(Application_Model_Preference::GetLiveStreamMasterPassword())
                        ->setLabel(_('Password:'))
                        ->setFilters(array('StringTrim'));
        $this->addElement($master_password);

        $masterSourceParams = parse_url(Application_Model_Preference::GetMasterDJSourceConnectionURL());

        // Master source connection url parameters
        $masterSourceHost = new Zend_Form_Element_Text('master_source_host');
        $masterSourceHost->setAttrib('readonly', true)
            ->setLabel(_('Host:'))
            ->setValue(isset($masterSourceParams["host"])?$masterSourceParams["host"]:"");
        $this->addElement($masterSourceHost);

        $masterSourcePort = new Zend_Form_Element_Text('master_source_port');
        $masterSourcePort->setAttrib('readonly', true)
            ->setLabel(_('Port:'))
            ->setValue(isset($masterSourceParams["port"])?$masterSourceParams["port"]:"");
        $this->addElement($masterSourcePort);

        $masterSourceMount = new Zend_Form_Element_Text('master_source_mount');
        $masterSourceMount->setAttrib('readonly', true)
            ->setLabel(_('Mount:'))
            ->setValue(isset($masterSourceParams["path"])?$masterSourceParams["path"]:"");
        $this->addElement($masterSourceMount);

        $showSourceParams = parse_url(Application_Model_Preference::GetLiveDJSourceConnectionURL());

        // Show source connection url parameters
        $showSourceHost = new Zend_Form_Element_Text('show_source_host');
        $showSourceHost->setAttrib('readonly', true)
            ->setLabel(_('Host:'))
            ->setValue(isset($showSourceParams["host"])?$showSourceParams["host"]:"");
        $this->addElement($showSourceHost);

        $showSourcePort = new Zend_Form_Element_Text('show_source_port');
        $showSourcePort->setAttrib('readonly', true)
            ->setLabel(_('Port:'))
            ->setValue(isset($showSourceParams["port"])?$showSourceParams["port"]:"");
        $this->addElement($showSourcePort);

        $showSourceMount = new Zend_Form_Element_Text('show_source_mount');
        $showSourceMount->setAttrib('readonly', true)
            ->setLabel(_('Mount:'))
            ->setValue(isset($showSourceParams["path"])?$showSourceParams["path"]:"");
        $this->addElement($showSourceMount);

        // demo only code
        if ($isDemo) {
            $elements = $this->getElements();
            foreach ($elements as $element) {
                if ($element->getType() != 'Zend_Form_Element_Hidden') {
                    $element->setAttrib("disabled", "disabled");
                }
            }
        }
    }

    public function updateVariables()
    {
        $CC_CONFIG = Config::getConfig();

        $isDemo = isset($CC_CONFIG['demo']) && $CC_CONFIG['demo'] == 1;
        $masterSourceParams = parse_url(Application_Model_Preference::GetMasterDJSourceConnectionURL());
        $showSourceParams = parse_url(Application_Model_Preference::GetLiveDJSourceConnectionURL());

        $this->setDecorators(
            array (
                array ('ViewScript',
                    array (
                      'viewScript'                  => 'form/preferences_livestream.phtml',
                      'master_source_host'          => isset($masterSourceParams["host"])?$masterSourceParams["host"]:"",
                      'master_source_port'          => isset($masterSourceParams["port"])?$masterSourceParams["port"]:"",
                      'master_source_mount'         => isset($masterSourceParams["path"])?$masterSourceParams["path"]:"",
                      'show_source_host'            => isset($showSourceParams["host"])?$showSourceParams["host"]:"",
                      'show_source_port'            => isset($showSourceParams["port"])?$showSourceParams["port"]:"",
                      'show_source_mount'           => isset($showSourceParams["path"])?$showSourceParams["path"]:"",
                      'isDemo'                      => $isDemo,
                    )
                )
            )
        );
    }

    public function isValid($data)
    {
        return parent::isValid($data);
    }

}
