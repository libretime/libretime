<?php

class Application_Form_AddShowRepeats extends Zend_Form_SubForm
{

    public function init()
    {
        // Add days checkboxes
		$this->addElement(
            'multiCheckbox',
            'day_check',
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
        $this->addElement('text', 'end_date', array(
            'label'      => 'Date End:',
            'required'   => false,
            'filters'    => array('StringTrim'),
			'validators' => array(
				'NotEmpty',
        		array('date', false, array('YYYY-MM-DD'))
    		) 
        ));

		// Add no end element
		$this->addElement('checkbox', 'no_end', array(
            'label'      => 'no end',
            'required'   => false,
		));
    }


}

