<?php

class Application_Form_PlaylistMetadata extends Zend_Form_SubForm
{

    public function init()
    {
		// Add username element
        $this->addElement('text', 'title', array(
            'label'      => 'Title:',
            'class'      => 'input_text',
            'required'   => false,
            'filters'    => array('StringTrim'),
            'validators' => array(
                'NotEmpty',
            )
        ));

		 // Add the comment element
        $this->addElement('textarea', 'description', array(
            'label'      => 'Description:',
            'class'      => 'input_text_area',
            'required'   => false,
		));

         // Add the comment element
        $this->addElement('button', 'new_playlist_submit', array(
            'label'      => 'Submit',
            'ignore'   => true
		));
    }
}

