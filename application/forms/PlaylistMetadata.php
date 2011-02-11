<?php

class Application_Form_PlaylistMetadata extends Zend_Form
{

    public function init()
    {
		// Add username element
        $this->addElement('text', 'title', array(
            'label'      => 'Title:',
            'required'   => false,
            'filters'    => array('StringTrim'),
            'validators' => array(
                'NotEmpty',
            )
        ));

		 // Add the comment element
        $this->addElement('textarea', 'description', array(
            'label'      => 'Description:',
            'required'   => false,
		));
    }


}

