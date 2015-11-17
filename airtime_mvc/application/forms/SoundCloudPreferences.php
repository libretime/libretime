<?php
require_once 'customvalidators/ConditionalNotEmpty.php';

class Application_Form_SoundcloudPreferences extends Zend_Form_SubForm
{

    public function init()
    {
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'form/preferences_soundcloud.phtml'))
        ));

        $select = new Zend_Form_Element_Select('SoundCloudLicense');
        $select->setLabel(_('Default License:'));
        $select->setAttrib('class', 'input_select');
        $select->setMultiOptions(array(
                                     "all-rights-reserved" => _("All rights are reserved"),
                                     "no-rights-reserved" => _("The work is in the public domain"),
                                     "cc-by" => _("Creative Commons Attribution"),
                                     "cc-by-nc" => _("Creative Commons Attribution Noncommercial"),
                                     "cc-by-nd" => _("Creative Commons Attribution No Derivative Works"),
                                     "cc-by-sa" => _("Creative Commons Attribution Share Alike"),
                                     "cc-by-nc-nd" => _("Creative Commons Attribution Noncommercial Non Derivate Works"),
                                     "cc-by-nc-sa" => _("Creative Commons Attribution Noncommercial Share Alike")
                                 ));
        $select->setRequired(false);
        $select->setValue(Application_Model_Preference::getDefaultSoundCloudLicenseType());
        $this->addElement($select);

        $select = new Zend_Form_Element_Select('SoundCloudSharing');
        $select->setLabel(_('Default Sharing Type:'));
        $select->setAttrib('class', 'input_select');
        $select->setMultiOptions(array(
                                     "public" => _("Public"),
                                     "private" => _("Private"),
                                 ));
        $select->setRequired(false);
        $select->setValue(Application_Model_Preference::getDefaultSoundCloudSharingType());
        $this->addElement($select);

        $this->addElement('image', 'SoundCloudConnect', array(
            'src'        => 'http://connect.soundcloud.com/2/btn-connect-sc-l.png',
            'decorators' => array(
                'ViewHelper'
            )
        ));

        $this->addElement('image', 'SoundCloudDisconnect', array(
            'src'        => 'http://connect.soundcloud.com/2/btn-disconnect-l.png',
            'decorators' => array(
                'ViewHelper'
            )
        ));

    }

}
