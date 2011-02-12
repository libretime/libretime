<?php

class Application_Form_EditAudioMD extends Zend_Form
{

    public function init()
    {
         // Set the method for the display form to POST
        $this->setMethod('post');

		// Add title field
        $this->addElement('text', 'dc:title', array(
            'label'      => 'Title:',
            'required'   => true,
            'class'      => 'input_text',
            'filters'    => array('StringTrim'),
            'validators' => array(
                'NotEmpty',
            )
        ));

		// Add artist field
        $this->addElement('text', 'dc:creator', array(
            'label'      => 'Artist:',
            'required'   => true,
            'class'      => 'input_text',
            'filters'    => array('StringTrim'),
            'validators' => array(
                'NotEmpty',
            )
        ));

		// Add album field
        $this->addElement('text', 'dc:source', array(
            'label'      => 'Album:',
            'class'      => 'input_text',
            'filters'    => array('StringTrim')
        ));

		// Add genre field
        $this->addElement('text', 'genre', array(
            'label'      => 'Genre:',
            'class'      => 'input_text',
            'filters'    => array('StringTrim')
        ));

		// Add year field
        $this->addElement('text', 'ls:year', array(
            'label'      => 'Year:',
            'class'      => 'input_text',
            'filters'    => array('StringTrim'),
            'validators' => array(
				array('date', false, array('YYYY-MM-DD')),
                array('date', false, array('YYYY-MM')),
        		array('date', false, array('YYYY'))
    		) 
        ));

		// Add label field
        $this->addElement('text', 'dc:publisher', array(
            'label'      => 'Label:',
            'class'      => 'input_text',
            'filters'    => array('StringTrim')
        ));

		// Add composer field
        $this->addElement('text', 'ls:composer', array(
            'label'      => 'Composer:',
            'class'      => 'input_text',
            'filters'    => array('StringTrim')
        ));

		// Add mood field
        $this->addElement('text', 'ls:mood', array(
            'label'      => 'Mood:',
            'class'      => 'input_text',
            'filters'    => array('StringTrim')
        ));

		// Add language field
        $this->addElement('text', 'dc:language', array(
            'label'      => 'Language:',
            'class'      => 'input_text',
            'filters'    => array('StringTrim')
        ));

		// Add the submit button
        $this->addElement('submit', 'submit', array(
            'ignore'   => true,
            'class'    => 'ui-button ui-state-default',
            'label'    => 'Submit',
        ));
    }


}

