<?php

class Application_Form_LiveStreamingPreferences extends Zend_Form_SubForm
{

    public function init()
    {
        $CC_CONFIG = Config::getConfig();
        $isDemo = isset($CC_CONFIG['demo']) && $CC_CONFIG['demo'] == 1;
        $isStreamConfigable = Application_Model_Preference::GetEnableStreamConf() == "true";

        $defaultFade = Application_Model_Preference::GetDefaultTransitionFade();
        if ($defaultFade == "") {
            $defaultFade = '00.000000';
        }

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
                        ->addValidator('regex', false, array('/^[0-9]{1,2}(\.\d{1,6})?$/',
                        'messages' => _('enter a time in seconds 00{.000000}')))
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

        return $isValid;
    }

}
