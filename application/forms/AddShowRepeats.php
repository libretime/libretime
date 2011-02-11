<?php

class Application_Form_AddShowRepeats extends Zend_Form_SubForm
{

    public function init()
    {
        //Add type select
		$this->addElement('select', 'add_show_repeat_type', array(
            'required' => true,
            'label' => 'Repeat Type:',
            'class' => ' input_select',
            'multiOptions' => array(
				"0" => "weekly",
                "1" => "bi-weekly",
                "2" => "monthly"
            ), 
        ));

        // Add days checkboxes
		$this->addElement(
            'multiCheckbox',
            'add_show_day_check',
            array(
				'label' => 'Select Days:',
                'required' => false,
                'multiOptions' => array(
					"0" => "Sun",
					"1" => "Mon",
					"2" => "Tue",
					"3" => "Wed",
					"4" => "Thu",
					"5" => "Fri",
					"6" => "Sat",
                ),
         ));

		// Add end date element
        $this->addElement('text', 'add_show_end_date', array(
            'label'      => 'Date End:',
            'class'      => 'input_text',
            'value'     => date("Y-m-d"),
            'required'   => false,
            'filters'    => array('StringTrim'),
			'validators' => array(
				'NotEmpty',
        		array('date', false, array('YYYY-MM-DD'))
    		) 
        ));

		// Add no end element
		$this->addElement('checkbox', 'add_show_no_end', array(
            'label'      => 'no end',
            'required'   => false,
		));
    }

    public function checkReliantFields($formData) {
       
        $start_timestamp = $formData['add_show_start_date'];
        $end_timestamp = $formData['add_show_end_date'];

        $start_epoch = strtotime($start_timestamp);
        $end_epoch = strtotime($end_timestamp);

        if($end_epoch < $start_epoch) {
            $this->getElement('add_show_end_date')->setErrors(array('End date must be after start date'));
            return false;
        }
 
        return true;
    }

}

