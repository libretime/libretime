<?php

class Application_Form_Preferences extends Zend_Form
{

    public function init()
    {
        $this->setAction('/Preference/update')->setMethod('post');
        
        //Station name
        $this->addElement('text', 'stationName', array(
            'class'      => 'input_text',
            'label'      => 'Station Name:',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array('NotEmpty'),
            'value' => Application_Model_Preference::GetValue("station_name")
        ));

        $defaultFade = Application_Model_Preference::GetDefaultFade();
        if($defaultFade == ""){
            $defaultFade = '00:00:00.000000';
        }

        //Default station fade
        $this->addElement('text', 'stationDefaultFade', array(
            'class'      => 'input_text',
            'label'      => 'Default Fade:',
            'required'   => false,
            'filters'    => array('StringTrim'),
            'validators' => array(array('regex', false, 
                array('/^[0-2][0-3]:[0-5][0-9]:[0-5][0-9](\.\d{1,6})?$/', 
                'messages' => 'enter a time 00:00:00{.000000}'))),
            'value' => $defaultFade
        ));
            
        $stream_format = new Zend_Form_Element_Radio('streamFormat');
        $stream_format->setLabel('Stream Label:');
        $stream_format->setMultiOptions(array("Artist - Title",
                                            "Show - Artist - Title",
                                            "Station name - Show name"));
        $stream_format->setValue(Application_Model_Preference::GetStreamLabelFormat());
        $this->addElement($stream_format);

        $third_party_api = new Zend_Form_Element_Radio('thirdPartyApi');
        $third_party_api->setLabel('Allow Remote Websites To Access Show Schedule Info');
        $third_party_api->setMultiOptions(array("Disabled",
                                            "Enabled"));
        $third_party_api->setValue(Application_Model_Preference::GetAllow3rdPartyApi());
        $this->addElement($third_party_api);


		$this->addElement('checkbox', 'UseSoundCloud', array(
            'label'      => 'Automatically Upload Recorded Shows To SoundCloud',
            'required'   => false,
            'value' => Application_Model_Preference::GetDoSoundCloudUpload()
		));

        //SoundCloud Username
        $this->addElement('text', 'SoundCloudUser', array(
            'class'      => 'input_text',
            'label'      => 'SoundCloud Email:',
            'required'   => false,
            'filters'    => array('StringTrim'),
            'value' => Application_Model_Preference::GetSoundCloudUser()
        ));

        //SoundCloud Password
        $this->addElement('password', 'SoundCloudPassword', array(
            'class'      => 'input_text',
            'label'      => 'SoundCloud Password:',
            'required'   => false,
            'filters'    => array('StringTrim'),
            'value' => Application_Model_Preference::GetSoundCloudPassword()
        ));

         // Add the description element
        $this->addElement('textarea', 'SoundCloudTags', array(
            'label'      => 'space separated SoundCloud Tags',
            'required'   => false,
            'class'      => 'input_text_area',
            'value' => Application_Model_Preference::GetSoundCloudTags()
		));

        //SoundCloud default genre
        $this->addElement('text', 'SoundCloudGenre', array(
            'class'      => 'input_text',
            'label'      => 'Default Genre:',
            'required'   => false,
            'filters'    => array('StringTrim'),
            'value' => Application_Model_Preference::GetSoundCloudGenre()
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
        $this->addElement($select);

        $this->addElement('submit', 'submit', array(
            'class'    => 'ui-button ui-state-default',
            'ignore'   => true,
            'label'    => 'Submit',
        ));

        
        
    }
}
