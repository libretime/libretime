<?php

class Application_Form_Preferences extends Zend_Form
{

    public function init()
    {
        $this->setAction('/Preference/update')->setMethod('post');
        
        // Add login element
        $this->addElement('text', 'stationName', array(
            'class'      => 'input_text',
            'label'      => 'Station Name:',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array('NotEmpty'),
            'value' => Application_Model_Preference::GetValue("station_name")
        ));

        $defaultFade = Application_Model_Preference::GetValue("default_fade");
        if($defaultFade == ""){
            $defaultFade = '00:00:00.000000';
        }

         // Add login element
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
                                            "Show",
                                            "Station name - Show name"));
        $stream_format->setValue(Application_Model_Preference::GetStreamLabelFormat());
        $this->addElement($stream_format);

        $this->addElement('submit', 'submit', array(
            'class'    => 'ui-button ui-state-default',
            'ignore'   => true,
            'label'    => 'Submit',
        ));
        
    }
}
