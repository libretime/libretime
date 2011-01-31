<?php

class Application_Form_AddShowWhen extends Zend_Form_SubForm
{

    public function init()
    {
		// Add start date element
        $this->addElement('text', 'add_show_start_date', array(
            'label'      => 'Date Start:',
            'class'      => 'input_text',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
				'NotEmpty',
        		array('date', false, array('YYYY-MM-DD'))
    		) 
        ));

		// Add start time element
        $this->addElement('text', 'add_show_start_time', array(
            'label'      => 'Start Time:',
            'class'      => 'input_text',
            'required'   => true,
            'filters'    => array('StringTrim'),
			'validators' => array(
				'NotEmpty',
        		array('date', false, array('HH:mm'))
    		) 
        ));

		// Add duration element
        $this->addElement('text', 'add_show_duration', array(
            'label'      => 'Duration:',
            'class'      => 'input_text',
            'required'   => true,
            'filters'    => array('StringTrim'),
			'validators' => array(
				'NotEmpty',
        		array('date', false, array('HH:mm'))
    		) 
        ));

		// Add repeats element
		$this->addElement('checkbox', 'add_show_repeats', array(
            'label'      => 'repeats',
            'required'   => false,
		));

    }

    public function checkReliantFields($formData) {
       
        $now_timestamp = date("Y-m-d H:i:s");
        $start_timestamp = $formData['add_show_start_date']."".$formData['add_show_start_time'];

        $now_epoch = strtotime($now_timestamp);
        $start_epoch = strtotime($start_timestamp);

        if($start_epoch < $now_epoch) {
            $this->getElement('add_show_start_time')->setErrors(array('Cannot create show in the past'));
            return false;
        }
 
        return true;
    }

}

