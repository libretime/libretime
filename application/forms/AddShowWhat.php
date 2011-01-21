<?php

class Application_Form_AddShowWhat extends Zend_Form_SubForm
{

    public function init()
    {
        // Add name element
        $this->addElement('text', 'add_show_name', array(
            'label'      => 'Name:',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array('NotEmpty')
        ));

		 // Add the description element
        $this->addElement('textarea', 'add_show_description', array(
            'label'      => 'Description:',
            'required'   => false,
		));

    }


}

