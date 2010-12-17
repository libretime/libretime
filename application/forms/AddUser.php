<?php

class Application_Form_AddUser extends Zend_Form
{

    public function init()
    {
        // Add login element
        $this->addElement('text', 'login', array(
            'label'      => 'Username:',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array('NotEmpty')
        ));

		// Add password element
        $this->addElement('text', 'password', array(
            'label'      => 'Password:',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array('NotEmpty')
        ));

		// Add first name element
        $this->addElement('text', 'first_name', array(
            'label'      => 'Firstname:',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array('NotEmpty')
        ));

		// Add last name element
        $this->addElement('text', 'last_name', array(
            'label'      => 'Lastname:',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array('NotEmpty')
        ));

		//Add type select
		$this->addElement('select', 'type', array(
            'required' => true,
            'multiOptions' => array(
				"A" => "admin",
                "H" => "host",
				"G" => "guest",
            ), 
        ));

		// Add the submit button
        $this->addElement('submit', 'submit', array(
            'ignore'   => true,
            'label'    => 'Submit',
        ));

    }


}

