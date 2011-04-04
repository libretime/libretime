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

         // Add URL element
        $this->addElement('text', 'add_show_url', array(
            'label'      => 'URL:',
            'class'      => 'input_text',
            'required'   => false,
            'filters'    => array('StringTrim'),
            'validators' => array('NotEmpty')
        ));

         // Add genre element
        $this->addElement('text', 'add_show_genre', array(
            'label'      => 'Genre:',
            'class'      => 'input_text',
            'required'   => false,
            'filters'    => array('StringTrim')
        ));

		 // Add the description element
        $this->addElement('textarea', 'add_show_description', array(
            'label'      => 'Description:',
            'required'   => false,
            'class'      => 'input_text_area'
		));

        $descText = $this->getElement('add_show_description');

        $descText->setDecorators(array(array('ViewScript', array(
            'viewScript' => 'form/add-show-block.phtml',
            'class'      => 'block-display'
        ))));

    }


}

