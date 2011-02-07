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
            
        $this->addElement('submit', 'submit', array(
            'class'    => 'ui-button ui-state-default',
            'ignore'   => true,
            'label'    => 'Submit',
        ));
    }
}

