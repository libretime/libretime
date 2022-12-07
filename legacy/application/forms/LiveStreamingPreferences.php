<?php

declare(strict_types=1);

class Application_Form_LiveStreamingPreferences extends Zend_Form_SubForm
{
    public function init()
    {
        $defaultFade = Application_Model_Preference::GetDefaultTransitionFade();

        $this->setDecorators([
            ['ViewScript', ['viewScript' => 'form/preferences_livestream.phtml']],
        ]);

        // automatic trasition on source disconnection
        $auto_transition = new Zend_Form_Element_Checkbox('auto_transition');
        $auto_transition->setLabel(_('Auto Switch Off:'))
            ->setValue(Application_Model_Preference::GetAutoTransition());
        $this->addElement($auto_transition);

        // automatic switch on upon source connection
        $auto_switch = new Zend_Form_Element_Checkbox('auto_switch');
        $auto_switch->setLabel(_('Auto Switch On:'))
            ->setValue(Application_Model_Preference::GetAutoSwitch());
        $this->addElement($auto_switch);

        // Default transition fade
        $transition_fade = new Zend_Form_Element_Text('transition_fade');
        $transition_fade->setLabel(_('Switch Transition Fade (s):'))
            ->setFilters(['StringTrim'])
            ->addValidator('regex', false, [
                '/^\d*(\.\d+)?$/',
                'messages' => _('Please enter a time in seconds (eg. 0.5)'),
            ])
            ->setValue($defaultFade);
        $this->addElement($transition_fade);

        // Master username
        $master_username = new Zend_Form_Element_Text('master_username');
        $master_username->setAttrib('autocomplete', 'off')
            ->setAllowEmpty(true)
            ->setLabel(_('Username:'))
            ->setFilters(['StringTrim'])
            ->setValue(Application_Model_Preference::GetLiveStreamMasterUsername());
        $this->addElement($master_username);

        // Master password
        $master_password = new Zend_Form_Element_Password('master_password');
        $master_password
            ->setAttrib('autocomplete', 'off')
            ->setAttrib('renderPassword', 'true')
            ->setAllowEmpty(true)
            ->setValue(Application_Model_Preference::GetLiveStreamMasterPassword())
            ->setLabel(_('Password:'))
            ->setFilters(['StringTrim']);
        $this->addElement($master_password);

        // Master source connection url parameters
        $masterSourceHost = new Zend_Form_Element_Text('master_source_host');
        $masterSourceHost
            ->setLabel(_('Master Source Host:'))
            ->setAttrib('readonly', true)
            ->setValue(Application_Model_Preference::GetMasterDJSourceConnectionURL());
        $this->addElement($masterSourceHost);

        $masterSourcePort = new Zend_Form_Element_Text('master_source_port');
        $masterSourcePort
            ->setLabel(_('Master Source Port:'))
            ->setAttrib('readonly', true)
            ->setValue(Application_Model_StreamSetting::getMasterLiveStreamPort());
        $this->addElement($masterSourcePort);

        $masterSourceMount = new Zend_Form_Element_Text('master_source_mount');
        $masterSourceMount
            ->setLabel(_('Master Source Mount:'))
            ->setAttrib('readonly', true)
            ->setValue(Application_Model_StreamSetting::getMasterLiveStreamMountPoint());
        $this->addElement($masterSourceMount);

        // Show source connection url parameters
        $showSourceHost = new Zend_Form_Element_Text('show_source_host');
        $showSourceHost
            ->setLabel(_('Show Source Host:'))
            ->setAttrib('readonly', true)
            ->setValue(Application_Model_Preference::GetLiveDJSourceConnectionURL());
        $this->addElement($showSourceHost);

        $showSourcePort = new Zend_Form_Element_Text('show_source_port');
        $showSourcePort
            ->setLabel(_('Show Source Port:'))
            ->setAttrib('readonly', true)
            ->setValue(Application_Model_StreamSetting::getDjLiveStreamPort());
        $this->addElement($showSourcePort);

        $showSourceMount = new Zend_Form_Element_Text('show_source_mount');
        $showSourceMount
            ->setLabel(_('Show Source Mount:'))
            ->setAttrib('readonly', true)
            ->setValue(Application_Model_StreamSetting::getDjLiveStreamMountPoint());
        $this->addElement($showSourceMount);
    }

    public function updateVariables()
    {
        $this->setDecorators(
            [
                [
                    'ViewScript',
                    [
                        'viewScript' => 'form/preferences_livestream.phtml',
                        'master_source_host' => isset($masterSourceHost) ? Application_Model_Preference::GetMasterDJSourceConnectionURL() : '',
                        'master_source_port' => isset($masterSourcePort) ? Application_Model_StreamSetting::getMasterLiveStreamPort() : '',
                        'master_source_mount' => isset($masterSourceMount) ? Application_Model_StreamSetting::getMasterLiveStreamMountPoint() : '',
                        'show_source_host' => isset($showSourceHost) ? Application_Model_Preference::GetLiveDJSourceConnectionURL() : '',
                        'show_source_port' => isset($showSourcePort) ? Application_Model_StreamSetting::getDjLiveStreamPort() : '',
                        'show_source_mount' => isset($showSourceMount) ? Application_Model_StreamSetting::getDjLiveStreamMountPoint() : '',
                    ],
                ],
            ]
        );
    }
}
