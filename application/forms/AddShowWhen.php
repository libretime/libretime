<?php

class Application_Form_AddShowWhen extends Zend_Form_SubForm
{

    public function init()
    {
		// Add start date element
        $this->addElement('text', 'start_date', array(
            'label'      => 'Date Start:',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
				'NotEmpty',
        		array('date', false, array('YYYY-MM-DD'))
    		) 
        ));

		// Add start time element
        $this->addElement('text', 'start_time', array(
            'label'      => 'Start Time:',
            'required'   => true,
            'filters'    => array('StringTrim'),
			'validators' => array(
				'NotEmpty',
        		array('date', false, array('HH:mm'))
    		) 
        ));

		// Add duration element
        $this->addElement('text', 'duration', array(
            'label'      => 'Duration:',
            'required'   => true,
            'filters'    => array('StringTrim'),
			'validators' => array(
				'NotEmpty',
        		array('date', false, array('HH:mm'))
    		) 
        ));

		// Add repeats element
		$this->addElement('checkbox', 'repeats', array(
            'label'      => 'repeats',
            'required'   => false,
		));

    }


}

