<?php
require_once 'customvalidators/ConditionalNotEmpty.php';
require_once 'customvalidators/PasswordNotEmpty.php';

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
        $master_username->setAttrib('class', 'input_text')
                        ->setLabel('Master Username')
                        ->setFilters(array('StringTrim'))
                        ->setValue(Application_Model_Preference::GetLiveSteamMasterUsername())
                        ->setDecorators(array('ViewHelper'));
        $this->addElement($master_username);
        
        //Master password
        $master_password = new Zend_Form_Element_Text('master_password');
        $master_password->setAttrib('class', 'input_text')
                        ->setAttrib('autocomplete', 'off')
                        ->setAttrib('renderPassword', true)
                        ->setLabel('Master Password')
                        ->setFilters(array('StringTrim'))
                        ->setValidators(array(new ConditionalNotEmpty(array('auto_enable_live_stream'=>'1'))))
                        ->setValue(Application_Model_Preference::GetLiveSteamMasterUsername())
                        ->setDecorators(array('ViewHelper'));
        $this->addElement($master_password);
    }
}