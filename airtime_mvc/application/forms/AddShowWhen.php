<?php

class Application_Form_AddShowWhen extends Zend_Form_SubForm
{

    public function init()
    {

        //$this->setDisableLoadDefaultDecorators(true);
        //$this->removeDecorator('DtDdWrapper');

		// Add start date element
        $this->addElement('text', 'add_show_start_date', array(
            'label'      => 'Date Start:',
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
        $this->addElement('text', 'add_show_start_time', array(
            'label'      => 'Start Time:',
            'class'      => 'input_text',
            'required'   => true,
            'value'     => '0:00',
            'filters'    => array('StringTrim'),
			'validators' => array(
				'NotEmpty',
        		array('date', false, array('HH:mm')),
                array('regex', false, array('/^[0-9:]+$/', 'messages' => 'Invalid character entered'))
    		)
        ));

		// Add duration element
        $this->addElement('text', 'add_show_duration', array(
            'label'      => 'Duration:',
            'class'      => 'input_text',
            'value'      => '1:00',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                'NotEmpty',
                array('regex', false,
                    array('/^\d+:[0-5][0-9]$/',
                    'messages' => 'enter a duration: HH:mm'))
                )
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

        if(strtotime("00:00") == strtotime($formData["add_show_duration"])) {
            $this->getElement('add_show_duration')->setErrors(array('Cannot have duration 00:00'));
            $valid = false;
        }

        return $valid;
    }

}

