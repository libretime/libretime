<?php

class Application_Form_AddShowWhat extends Zend_Form_SubForm
{
    public function init()
    {
    	// retrieves the length limit for each char field
    	// and store to assoc array
    	$maxLens = Application_Model_Show::GetMaxLengths();
    	
        // Hidden element to indicate whether the show is new or
        // whether we are updating an existing show.
        $this->addElement('hidden', 'add_show_id', array(
            'decorators' => array('ViewHelper')
        ));
        
        // Add name element
        $this->addElement('text', 'add_show_name', array(
            'label'      => 'Name:',
            'class'      => 'input_text',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array('NotEmpty'),
        	'value'		=> 'Untitled Show',
        	'validators' => array(array('StringLength', false, array(0, $maxLens['name'])))
        ));

         // Add URL element
        $this->addElement('text', 'add_show_url', array(
            'label'      => 'URL:',
            'class'      => 'input_text',
            'required'   => false,
            'filters'    => array('StringTrim'),
            'validators' => array('NotEmpty', array('StringLength', false, array(0, $maxLens['url'])))
        ));

         // Add genre element
        $this->addElement('text', 'add_show_genre', array(
            'label'      => 'Genre:',
            'class'      => 'input_text',
            'required'   => false,
            'filters'    => array('StringTrim'),
        	'validators' => array(array('StringLength', false, array(0, $maxLens['genre'])))
        ));

		 // Add the description element
        $this->addElement('textarea', 'add_show_description', array(
            'label'      => 'Description:',
            'required'   => false,
            'class'      => 'input_text_area',
        	'validators' => array(array('StringLength', false, array(0, $maxLens['description'])))
		));

        $descText = $this->getElement('add_show_description');

        $descText->setDecorators(array(array('ViewScript', array(
            'viewScript' => 'form/add-show-block.phtml',
            'class'      => 'block-display'
        ))));

    }
}

