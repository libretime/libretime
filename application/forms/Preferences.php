<?php

class Application_Form_Preferences extends Zend_Form
{

    public function init()
    {
        $this->setAction('/Preference/update')->setMethod('post');
        
        // Add login element
        $this->addElement('text', 'stationName', array(
            'label'      => 'Station Name:',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array('NotEmpty')
        ));
        
        /*
        $this->addElement('select', 'test', array(
            'label'      => 'Live Stream Button: ',
            'multiOptions' => array(
				"e" => "enabled",
                "d" => "disabled"
            ))
        );
        */
        
        $this->addElement('submit', 'submit', array(
            'ignore'   => true,
            'label'    => 'Submit',
        ));
    }
}

