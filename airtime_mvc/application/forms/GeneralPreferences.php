<?php

class Application_Form_GeneralPreferences extends Zend_Form_SubForm
{

    public function init()
    {
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'form/preferences_general.phtml'))
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
            'value' => $defaultFade,
            'decorators' => array(
                'ViewHelper'
            )
        ));

        $stream_format = new Zend_Form_Element_Radio('streamFormat');
        $stream_format->setLabel('Stream Label:');
        $stream_format->setMultiOptions(array("Artist - Title",
                                            "Show - Artist - Title",
                                            "Station name - Show name"));
        $stream_format->setValue(Application_Model_Preference::GetStreamLabelFormat());
        $stream_format->setDecorators(array('ViewHelper'));
        $this->addElement($stream_format);

        $third_party_api = new Zend_Form_Element_Radio('thirdPartyApi');
        $third_party_api->setLabel('Allow Remote Websites To Access "Schedule" Info?<br> (Enable this to make front-end widgets work.)');
        $third_party_api->setMultiOptions(array("Disabled",
                                            "Enabled"));
        $third_party_api->setValue(Application_Model_Preference::GetAllow3rdPartyApi());
        $third_party_api->setDecorators(array('ViewHelper'));
        $this->addElement($third_party_api);

    }


}

