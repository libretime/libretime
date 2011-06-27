<?php

class Application_Form_AddShowWhen extends Zend_Form_SubForm
{

    public function init()
    {
        // Add start date element
        $startDate = new Zend_Form_Element_Text('add_show_start_date');
        $startDate->class = 'input_text';
        $startDate->setRequired(true)
                    ->setLabel('Date/Time Start:')
                    ->setValue(date("Y-m-d"))
                    ->setFilters(array('StringTrim'))
                    ->setValidators(array(
                        'NotEmpty',
                        array('date', false, array('YYYY-MM-DD'))))
                    ->setDecorators(array(
                        array(array('open'=>'HtmlTag'), array('tag' => 'dd', 'openOnly'=>true)),
                        'ViewHelper',
                        'Description',
                        array('Label', array('tag' =>'dt'))));
        $this->addElement($startDate);
        
        // Add start time element
        $startTime = new Zend_Form_Element_Text('add_show_start_time');
        $startTime->class = 'input_text';
        $startTime->setRequired(true)
                    ->setValue('00:00')
                    ->setFilters(array('StringTrim'))
                    ->setValidators(array(
                        'NotEmpty',
                        array('date', false, array('HH:mm')),
                        array('regex', false, array('/^[0-9:]+$/', 'messages' => 'Invalid character entered'))))
                    ->setDecorators(array(
                        'ViewHelper',
                        'Errors',
                        array(array('close'=>'HtmlTag'), array('tag' => 'dd', 'closeOnly'=>true))));
        $this->addElement($startTime);

        // Add end date element
        $endDate = new Zend_Form_Element_Text('add_show_end_date_no_repeat');
        $endDate->class = 'input_text';
        $endDate->setRequired(true)
                    ->setLabel('Date/Time End:')
                    ->setValue(date("Y-m-d"))
                    ->setFilters(array('StringTrim'))
                    ->setValidators(array(
                        'NotEmpty',
                        array('date', false, array('YYYY-MM-DD'))))
                    ->setDecorators(array(
                        array(array('open'=>'HtmlTag'), array('tag' => 'dd', 'openOnly'=>true)),
                        'ViewHelper',
                        'Description',
                        array('Label', array('tag' =>'dt'))));
        $this->addElement($endDate);
        
        // Add end time element
        $endTime = new Zend_Form_Element_Text('add_show_end_time');
        $endTime->class = 'input_text';
        $endTime->setRequired(true)
                    ->setValue('01:00')
                    ->setFilters(array('StringTrim'))
                    ->setValidators(array(
                        'NotEmpty',
                        array('date', false, array('HH:mm')),
                        array('regex', false, array('/^[0-9:]+$/', 'messages' => 'Invalid character entered'))))
                    ->setDecorators(array(
                        'ViewHelper',
                        'Errors',
                        array(array('close'=>'HtmlTag'), array('tag' => 'dd', 'closeOnly'=>true))));
        $this->addElement($endTime);
        
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
	            $this->getElement('add_show_start_time')->setErrors(array('Cannot create show in the past'));
	            $valid = false;
	        }
	    }
	    
        if( $formData["add_show_duration"] == "0m" ) {
            $this->getElement('add_show_duration')->setErrors(array('Cannot have duration 0m'));
            $valid = false;
        }elseif(strpos($formData["add_show_duration"], 'h') !== false && intval(substr($formData["add_show_duration"], 0, strpos($formData["add_show_duration"], 'h'))) > 24) {
            $this->getElement('add_show_duration')->setErrors(array('Cannot have duration greater than 24h'));
            $valid = false;
        }elseif( strstr($formData["add_show_duration"], '-') ){
        	$this->getElement('add_show_duration')->setErrors(array('Cannot have duration < 0m'));
            $valid = false;
        }

        return $valid;
    }

}

