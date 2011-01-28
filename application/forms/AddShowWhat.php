<?php

class Application_Form_AddShowWhat extends Zend_Form_SubForm
{

    public function init()
    {
        // Add name element
        $this->addElement('text', 'add_show_name', array(
            'label'      => 'Name:',
            'class'      => 'input_text',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array('NotEmpty')
        ));

        $nameLabel = $this->getElement('add_show_name');

        $nameLabel->setDecorators(array(array('ViewScript', array(
            'viewScript' => 'testform.phtml',
            'class'      => 'test template'
        ))));
        

		 // Add the description element
        $this->addElement('textarea', 'add_show_description', array(
            'label'      => 'Description:',
            'required'   => false,
            'class'      => 'input_text_area'
		));

    }


}

