<?php

class Application_Form_LiveStreamingPreferences extends Zend_Form_SubForm
{

    public function init()
    {
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
        $m_port = Application_Model_StreamSetting::GetMasterLiveSteamPort();
        $master_dj_port = new Zend_Form_Element_Text('master_harbor_input_port');
        $master_dj_port->setLabel("Master DJ Port")
                ->setValue($m_port)
                ->setValidators(array(new Zend_Validate_Between(array('min'=>0, 'max'=>99999))))
                ->addValidator('regex', false, array('pattern'=>'/^[0-9]+$/', 'messages'=>array('regexNotMatch'=>'Only numbers are allowed.')))
                ->setDecorators(array('ViewHelper'));
        $this->addElement($master_dj_port);
        
        $m_mount = Application_Model_StreamSetting::GetMasterLiveSteamMountPoint();
        $master_dj_mount = new Zend_Form_Element_Text('master_harbor_input_mount_point');
        $master_dj_mount->setLabel("Master DJ Mount Point")
                ->setValue($m_mount)
                ->setValidators(array(
                        array('regex', false, array('/^[^ &<>]+$/', 'messages' => 'Invalid character entered'))))
                ->setDecorators(array('ViewHelper'));
        $this->addElement($master_dj_mount);
        
        //liquidsoap harbor.input port
        $l_port = Application_Model_StreamSetting::GetDJLiveSteamPort();
        $live_dj_port = new Zend_Form_Element_Text('dj_harbor_input_port');
        $live_dj_port->setLabel("DJ Port")
                ->setValue($l_port)
                ->setValidators(array(new Zend_Validate_Between(array('min'=>0, 'max'=>99999))))
                ->addValidator('regex', false, array('pattern'=>'/^[0-9]+$/', 'messages'=>array('regexNotMatch'=>'Only numbers are allowed.')))
                ->setDecorators(array('ViewHelper'));
        $this->addElement($live_dj_port);
        
        $l_mount = Application_Model_StreamSetting::GetDJLiveSteamMountPoint();
        $live_dj_mount = new Zend_Form_Element_Text('dj_harbor_input_mount_point');
        $live_dj_mount->setLabel("DJ Mount Point")
                ->setValue($l_mount)
                ->setValidators(array(
                        array('regex', false, array('/^[^ &<>]+$/', 'messages' => 'Invalid character entered'))))
                ->setDecorators(array('ViewHelper'));
        $this->addElement($live_dj_mount);
        
        $master_dj_connection_url = Application_Model_Preference::GetMasterDJSourceConnectionURL();
        $live_dj_connection_url = Application_Model_Preference::GetLiveDJSourceConnectionURL();
        
        $master_dj_connection_url = ($master_dj_connection_url == "")?("http://".$_SERVER['SERVER_NAME'].":".$m_port."/".$m_mount):$master_dj_connection_url;
        $live_dj_connection_url = ($live_dj_connection_url == "")?"http://".$_SERVER['SERVER_NAME'].":".$l_port."/".$l_mount:$live_dj_connection_url;
        
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'form/preferences_livestream.phtml', 'master_dj_connection_url'=>$master_dj_connection_url, 'live_dj_connection_url'=>$live_dj_connection_url,))
        ));
    }
}