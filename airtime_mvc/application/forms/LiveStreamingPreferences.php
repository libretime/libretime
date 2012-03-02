<?php

class Application_Form_LiveStreamingPreferences extends Zend_Form_SubForm
{

    public function init()
    {
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'form/preferences_livestream.phtml'))
        ));

        //enable Auto-enable for all shows
        $auto_enable = new Zend_Form_Element_Checkbox('auto_enable_live_stream');
        $auto_enable->setLabel('Auto-enable for all shows')
                    ->setRequired(false)
                    ->setValue(Application_Model_Preference::GetLiveSteamAutoEnable())
                    ->setDecorators(array('ViewHelper'));
        $this->addElement($auto_enable);

        //Master username
        $master_username = new Zend_Form_Element_Text('master_username');
        $master_username->setAttrib('autocomplete', 'off')
                        ->setAllowEmpty(true)
                        ->setLabel('Master Username')
                        ->setFilters(array('StringTrim'))
                        ->setValue(Application_Model_Preference::GetLiveSteamMasterUsername())
                        ->setDecorators(array('ViewHelper'));
        $this->addElement($master_username);
        
        //Master password
        $master_password = new Zend_Form_Element_Password('master_password');
        $master_password->setAttrib('autocomplete', 'off')
                        ->setAttrib('renderPassword','true')
                        ->setAllowEmpty(true)
                        ->setValue(Application_Model_Preference::GetLiveSteamMasterPassword())
                        ->setLabel('Master Password')
                        ->setFilters(array('StringTrim'))
                        ->setDecorators(array('ViewHelper'));
        $this->addElement($master_password);
        
        //liquidsoap harbor.input port
        $port = new Zend_Form_Element_Text('master_harbor_input_port');
        $port->setLabel("Master DJ Port")
                ->setValue(Application_Model_StreamSetting::GetMasterLiveSteamPort())
                ->setValidators(array(new Zend_Validate_Between(array('min'=>0, 'max'=>99999))))
                ->addValidator('regex', false, array('pattern'=>'/^[0-9]+$/', 'messages'=>array('regexNotMatch'=>'Only numbers are allowed.')))
                ->setDecorators(array('ViewHelper'));
        $this->addElement($port);
        
        $mount = new Zend_Form_Element_Text('master_harbor_input_mount_point');
        $mount->setLabel("Master DJ Mount Point")
                ->setValue(Application_Model_StreamSetting::GetMasterLiveSteamMountPoint())
                ->setValidators(array(
                        array('regex', false, array('/^[^ &<>]+$/', 'messages' => 'Invalid character entered'))))
                ->setDecorators(array('ViewHelper'));
        $this->addElement($mount);
        
        //liquidsoap harbor.input port
        $port = new Zend_Form_Element_Text('dj_harbor_input_port');
        $port->setLabel("DJ Port")
                ->setValue(Application_Model_StreamSetting::GetDJLiveSteamPort())
                ->setValidators(array(new Zend_Validate_Between(array('min'=>0, 'max'=>99999))))
                ->addValidator('regex', false, array('pattern'=>'/^[0-9]+$/', 'messages'=>array('regexNotMatch'=>'Only numbers are allowed.')))
                ->setDecorators(array('ViewHelper'));
        $this->addElement($port);
        
        $mount = new Zend_Form_Element_Text('dj_harbor_input_mount_point');
        $mount->setLabel("DJ Mount Point")
                ->setValue(Application_Model_StreamSetting::GetDJLiveSteamMountPoint())
                ->setValidators(array(
                        array('regex', false, array('/^[^ &<>]+$/', 'messages' => 'Invalid character entered'))))
                ->setDecorators(array('ViewHelper'));
        $this->addElement($mount);
    }
}