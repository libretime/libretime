<?php

class Application_Form_AddShowWhen extends Zend_Form_SubForm
{

    public function init()
    {

        //$this->setDisableLoadDefaultDecorators(true);
        //$this->removeDecorator('DtDdWrapper');

		// Add start date element
        $this->addElement('text', 'add_show_start_date', array(
            'label'      => 'Date/Time Start:',
            'class'      => 'input_text',
            'required'   => true,
            'value'     => date("Y-m-d"),
            'filters'    => array('StringTrim'),
            'validators' => array(
				'NotEmpty',
        		array('date', false, array('YYYY-MM-DD'))
    		)
        ));
        
		// Add start time element
        $startTime = $this->addElement('text', 'add_show_start_time', array(
        	'decorators' => array('ViewHelper', array('HtmlTag', array('tag'=>'dd'))),
            'class'      => 'input_text',
            'required'   => true,
        	'value'		 => '00:00',
            'filters'    => array('StringTrim'),
			'validators' => array(
				'NotEmpty',
        		array('date', false, array('HH:mm')),
                array('regex', false, array('/^[0-9:]+$/', 'messages' => 'Invalid character entered'))
    		)
        ));

        // Add end date element
        $this->addElement('text', 'add_show_end_date_no_repeat', array(
            'label'      => 'Date/Time End:',
            'class'      => 'input_text',
            'required'   => true,
            'value'     => date("Y-m-d"),
            'filters'    => array('StringTrim'),
            'validators' => array(
				'NotEmpty',
        		array('date', false, array('YYYY-MM-DD'))
    		)
        ));
        
        // Add end time element
        $this->addElement('text', 'add_show_end_time', array(
        	'decorators' => array('ViewHelper', array('HtmlTag', array('tag'=>'dd'))),
            'class'      => 'input_text',
            'required'   => true,
        	'value'		 => '01:00',
            'filters'    => array('StringTrim'),
            'validators' => array(
                'NotEmpty',
                array('date', false, array('HH:mm')),
                array('regex', false, array('/^[0-9:]+$/', 'messages' => 'Invalid character entered'))
            )
                
        ));
        
        // Add duration element
        $this->addElement('text', 'add_show_duration', array(
        	'label'		 => 'Duration:',
            'class'      => 'input_text',
        	'value'		 => '01h00m',
        	'readonly'	 => true
        ));

		// Add repeats element
		$this->addElement('checkbox', 'add_show_repeats', array(
            'label'      => 'Repeats?',
            'required'   => false,
		));

    }

    public function checkReliantFields($formData, $startDateModified) {

        $valid = true;

        $now_timestamp = date("Y-m-d H:i:s");
        $start_timestamp = $formData['add_show_start_date']." ".$formData['add_show_start_time'];

        $now_epoch = strtotime($now_timestamp);
        $start_epoch = strtotime($start_timestamp);


		if ((($formData['add_show_id'] != -1) && $startDateModified) || ($formData['add_show_id'] == -1)){
	        if($start_epoch < $now_epoch) {
	            $this->getElement('add_show_start_date')->setErrors(array('Cannot create show in the past'));
	            $valid = false;
	        }
	    }
	    
        if( $formData["add_show_duration"] == "0m" ) {
            $this->getElement('add_show_duration')->setErrors(array('Cannot have duration 0m'));
            $valid = false;
        }elseif(strpos($formData["add_show_duration"], 'h') !== false && intval(substr($formData["add_show_duration"], 0, strpos($formData["add_show_duration"], 'h'))) > 23) {
            $this->getElement('add_show_duration')->setErrors(array('Cannot have duration > 24h'));
            $valid = false;
        }

        return $valid;
    }

}

