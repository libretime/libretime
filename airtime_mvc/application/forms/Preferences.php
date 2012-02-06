<?php

class Application_Form_Preferences extends Zend_Form
{

    public function init()
    {
        $this->setAction('/Preference');
        $this->setMethod('post');

        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'form/preferences.phtml'))
        ));
        
        $general_pref = new Application_Form_GeneralPreferences();
        $this->addSubForm($general_pref, 'preferences_general');
        
        $livestream_pref = new Application_Form_LiveStreamingPreferences();
        $this->addSubForm($livestream_pref, 'preferences_livestream');

        $soundcloud_pref = new Application_Form_SoundcloudPreferences();
        $this->addSubForm($soundcloud_pref, 'preferences_soundcloud');
        
        /*$support_pref = new Application_Form_SupportPreferences();
        $this->addSubForm($support_pref, 'preferences_support');*/

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
