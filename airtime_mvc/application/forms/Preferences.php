<?php

class Application_Form_Preferences extends Zend_Form
{
    private $isSaas;

    public function init()
    {
        $this->setAction('/Preference');
        $this->setMethod('post');

        $isSaas = Application_Model_Preference::GetPlanLevel() == 'disabled'?false:true;
        $this->isSaas = $isSaas;

        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'form/preferences.phtml', "isSaas" => $this->isSaas))
        ));

        $general_pref = new Application_Form_GeneralPreferences();
        $this->addSubForm($general_pref, 'preferences_general');

        if (!$isSaas) {
            $email_pref = new Application_Form_EmailServerPreferences();
            $this->addSubForm($email_pref, 'preferences_email_server');
        }

        $soundcloud_pref = new Application_Form_SoundcloudPreferences();
        $this->addSubForm($soundcloud_pref, 'preferences_soundcloud');

        $this->addElement('submit', 'submit', array(
            'class'    => 'ui-button ui-state-default right-floated',
            'ignore'   => true,
            'label'    => 'Save',
            'decorators' => array(
                'ViewHelper'
            )
        ));
    }
}
