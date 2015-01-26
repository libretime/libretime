<?php

class Application_Form_LiveStreamingPreferences extends Zend_Form_SubForm
{

    public function init()
    {
        $CC_CONFIG = Config::getConfig();
        $isDemo = isset($CC_CONFIG['demo']) && $CC_CONFIG['demo'] == 1;
        $isStreamConfigable = Application_Model_Preference::GetEnableStreamConf() == "true";

        $defaultFade = Application_Model_Preference::GetDefaultTransitionFade();

        // automatic trasition on source disconnection
        $auto_transition = new Zend_Form_Element_Checkbox("auto_transition");
        $auto_transition->setLabel(_("Auto Switch Off"))
                        ->setValue(Application_Model_Preference::GetAutoTransition())
                        ->setDecorators(array('ViewHelper'));
        $this->addElement($auto_transition);

        // automatic switch on upon source connection
        $auto_switch = new Zend_Form_Element_Checkbox("auto_switch");
        $auto_switch->setLabel(_("Auto Switch On"))
                        ->setValue(Application_Model_Preference::GetAutoSwitch())
                        ->setDecorators(array('ViewHelper'));
        $this->addElement($auto_switch);

        // Default transition fade
        $transition_fade = new Zend_Form_Element_Text("transition_fade");
        $transition_fade->setLabel(_("Switch Transition Fade (s)"))
                        ->setFilters(array('StringTrim'))
                        ->addValidator('regex', false, array('/^\d*(\.\d+)?$/',
                                'messages' => _('Please enter a time in seconds (eg. 0.5)')))
                        ->setValue($defaultFade)
                        ->setDecorators(array('ViewHelper'));
        $this->addElement($transition_fade);

        //Master username
        $master_username = new Zend_Form_Element_Text('master_username');
        $master_username->setAttrib('autocomplete', 'off')
                        ->setAllowEmpty(true)
                        ->setLabel(_('Master Username'))
                        ->setFilters(array('StringTrim'))
                        ->setValue(Application_Model_Preference::GetLiveStreamMasterUsername())
                        ->setDecorators(array('ViewHelper'));
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
                        ->setLabel(_('Master Password'))
                        ->setFilters(array('StringTrim'))
                        ->setDecorators(array('ViewHelper'));
        $this->addElement($master_password);

        //Master source connection url
        $master_dj_connection_url = new Zend_Form_Element_Text('master_dj_connection_url');
        $master_dj_connection_url->setAttrib('readonly', true)
                                 ->setLabel(_('Master Source Connection URL'))
                                 ->setValue(Application_Model_Preference::GetMasterDJSourceConnectionURL())
                                 ->setDecorators(array('ViewHelper'));
        $this->addElement($master_dj_connection_url);

        //Show source connection url
        $live_dj_connection_url = new Zend_Form_Element_Text('live_dj_connection_url');
        $live_dj_connection_url->setAttrib('readonly', true)
                                 ->setLabel(_('Show Source Connection URL'))
                                 ->setValue(Application_Model_Preference::GetLiveDJSourceConnectionURL())
                                 ->setDecorators(array('ViewHelper'));
        $this->addElement($live_dj_connection_url);

        //liquidsoap harbor.input port
            $betweenValidator = Application_Form_Helper_ValidationTypes::overrideBetweenValidator(1024, 49151);
            $m_port = Application_Model_StreamSetting::getMasterLiveStreamPort();
            $master_dj_port = new Zend_Form_Element_Text('master_harbor_input_port');
            $master_dj_port->setLabel(_("Master Source Port"))
                    ->setValue($m_port)
                    ->setValidators(array($betweenValidator))
                    ->addValidator('regex', false, array('pattern'=>'/^[0-9]+$/', 'messages'=>array('regexNotMatch'=>_('Only numbers are allowed.'))))
                    ->setDecorators(array('ViewHelper'));
            $this->addElement($master_dj_port);

            $m_mount = Application_Model_StreamSetting::getMasterLiveStreamMountPoint();
            $master_dj_mount = new Zend_Form_Element_Text('master_harbor_input_mount_point');
            $master_dj_mount->setLabel(_("Master Source Mount Point"))
                    ->setValue($m_mount)
                    ->setValidators(array(
                            array('regex', false, array('/^[^ &<>]+$/', 'messages' => _('Invalid character entered')))))
                    ->setDecorators(array('ViewHelper'));
            $this->addElement($master_dj_mount);

            //liquidsoap harbor.input port
            $l_port = Application_Model_StreamSetting::getDjLiveStreamPort();
            $live_dj_port = new Zend_Form_Element_Text('dj_harbor_input_port');
            $live_dj_port->setLabel(_("Show Source Port"))
                    ->setValue($l_port)
                    ->setValidators(array($betweenValidator))
                    ->addValidator('regex', false, array('pattern'=>'/^[0-9]+$/', 'messages'=>array('regexNotMatch'=>_('Only numbers are allowed.'))))
                    ->setDecorators(array('ViewHelper'));
            $this->addElement($live_dj_port);

            $l_mount = Application_Model_StreamSetting::getDjLiveStreamMountPoint();
            $live_dj_mount = new Zend_Form_Element_Text('dj_harbor_input_mount_point');
            $live_dj_mount->setLabel(_("Show Source Mount Point"))
                    ->setValue($l_mount)
                    ->setValidators(array(
                            array('regex', false, array('/^[^ &<>]+$/', 'messages' => _('Invalid character entered')))))
                    ->setDecorators(array('ViewHelper'));
            $this->addElement($live_dj_mount);
        // demo only code
        if (!$isStreamConfigable) {
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
        $master_dj_connection_url = Application_Model_Preference::GetMasterDJSourceConnectionURL();
        $live_dj_connection_url = Application_Model_Preference::GetLiveDJSourceConnectionURL();

        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'form/preferences_livestream.phtml', 'master_dj_connection_url'=>$master_dj_connection_url, 'live_dj_connection_url'=>$live_dj_connection_url, 'isDemo' => $isDemo))
        ));
    }

    public function isValid($data)
    {
        $isValid = parent::isValid($data);
            $master_harbor_input_port = $data['master_harbor_input_port'];
            $dj_harbor_input_port = $data['dj_harbor_input_port'];

            if ($master_harbor_input_port == $dj_harbor_input_port && $master_harbor_input_port != "") {
                $element = $this->getElement("dj_harbor_input_port");
                $element->addError(_("You cannot use same port as Master DJ port."));
                $isValid = false;
            }
            if ($master_harbor_input_port != "") {
                if (is_numeric($master_harbor_input_port)) {
                    if ($master_harbor_input_port != Application_Model_StreamSetting::getMasterLiveStreamPort()) {
                        $sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
                        try {
                            socket_bind($sock, 0, $master_harbor_input_port);
                        } catch (Exception $e) {
                            $element = $this->getElement("master_harbor_input_port");
                            $element->addError(sprintf(_("Port %s is not available"), $master_harbor_input_port));
                            $isValid = false;
                        }
                        
                        socket_close($sock);
                    }
                } else {
                    $isValid = false;
                }
            }
            if ($dj_harbor_input_port != "") {
                if (is_numeric($dj_harbor_input_port)) {
                    if ($dj_harbor_input_port != Application_Model_StreamSetting::getDjLiveStreamPort()) {
                        $sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
                        try {
                            socket_bind($sock, 0, $dj_harbor_input_port);
                        } catch (Exception $e) {
                            $element = $this->getElement("dj_harbor_input_port");
                            $element->addError(sprintf(_("Port %s is not available"), $dj_harbor_input_port));
                            $isValid = false;
                        }
                        socket_close($sock);
                    }
                } else {
                    $isValid = false;
                }
            }

        return $isValid;
    }

}
