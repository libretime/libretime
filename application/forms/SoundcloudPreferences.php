<?php

class Application_Form_SoundcloudPreferences extends Zend_Form_SubForm
{

    public function init()
    {
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'form/preferences_soundcloud.phtml'))
        ));

        //enable soundcloud uploads
        $this->addElement('checkbox', 'UseSoundCloud', array(
            'label'      => 'Upload Recorded Shows To SoundCloud',
            'required'   => false,
            'value' => Application_Model_Preference::GetDoSoundCloudUpload(),
            'decorators' => array(
                'ViewHelper'
            )
		));

        //SoundCloud Username
        $this->addElement('text', 'SoundCloudUser', array(
            'class'      => 'input_text',
            'label'      => 'SoundCloud Email:',
            'required'   => false,
            'filters'    => array('StringTrim'),
            'value' => Application_Model_Preference::GetSoundCloudUser(),
            'decorators' => array(
                'ViewHelper'
            )
        ));

        //SoundCloud Password
        $this->addElement('password', 'SoundCloudPassword', array(
            'class'      => 'input_text',
            'label'      => 'SoundCloud Password:',
            'required'   => false,
            'filters'    => array('StringTrim'),
            'value' => Application_Model_Preference::GetSoundCloudPassword(),
            'decorators' => array(
                'ViewHelper'
            )
        ));

         // Add the description element
        $this->addElement('textarea', 'SoundCloudTags', array(
            'label'      => 'space separated SoundCloud Tags',
            'required'   => false,
            'class'      => 'input_text_area',
            'value' => Application_Model_Preference::GetSoundCloudTags(),
            'decorators' => array(
                'ViewHelper'
            )
		));

        //SoundCloud default genre
        $this->addElement('text', 'SoundCloudGenre', array(
            'class'      => 'input_text',
            'label'      => 'Default Genre:',
            'required'   => false,
            'filters'    => array('StringTrim'),
            'value' => Application_Model_Preference::GetSoundCloudGenre(),
            'decorators' => array(
                'ViewHelper'
            )
        ));

        $select = new Zend_Form_Element_Select('SoundCloudTrackType');
        $select->setLabel('Default Track Type:');
        $select->setAttrib('class', 'input_select');
        $select->setMultiOptions(array(
                "" => "",
                "original" => "Original",
                "remix" => "Remix",
                "live" => "Live",
                "recording" => "Recording",
                "spoken" => "Spoken",
                "podcast" => "Podcast",
                "demo" => "Demo",
                "in progress" => "Work in progress",
                "stem" => "Stem",
                "loop" => "Loop",
                "sound effect" => "Sound Effect",
                "sample" => "One Shot Sample",
                "other" => "Other"
            ));
        $select->setRequired(false);
        $select->setValue(Application_Model_Preference::GetSoundCloudTrackType());
        $select->setDecorators(array('ViewHelper'));
        $this->addElement($select);

        $select = new Zend_Form_Element_Select('SoundCloudLicense');
        $select->setLabel('Default License:');
        $select->setAttrib('class', 'input_select');
        $select->setMultiOptions(array(
                "" => "",
                "no-rights-reserved" => "The work is in the public domain",
                "all-rights-reserved" => "All rights are reserved",
                "cc-by" => "Creative Commons Attribution",
                "cc-by-nc" => "Creative Commons Attribution Noncommercial",
                "cc-by-nd" => "Creative Commons Attribution No Derivative Works",
                "cc-by-sa" => "Creative Commons Attribution Share Alike",
                "cc-by-nc-nd" => "Creative Commons Attribution Noncommercial Non Derivate Works",
                "cc-by-nc-sa" => "Creative Commons Attribution Noncommercial Share Alike"
            ));
        $select->setRequired(false);
        $select->setValue(Application_Model_Preference::GetSoundCloudLicense());
        $select->setDecorators(array('ViewHelper'));
        $this->addElement($select);
    }


}

