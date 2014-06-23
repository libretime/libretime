<?php

class Application_Form_Preferences extends Zend_Form
{
    public function init()
    {
        $baseUrl = Application_Common_OsPath::getBaseDir();

        $this->setMethod('post');

        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'form/preferences.phtml'))
        ));

        $general_pref = new Application_Form_GeneralPreferences();
        $this->addSubForm($general_pref, 'preferences_general');

        $soundcloud_pref = new Application_Form_SoundcloudPreferences();
        $this->addSubForm($soundcloud_pref, 'preferences_soundcloud');

    }
}
