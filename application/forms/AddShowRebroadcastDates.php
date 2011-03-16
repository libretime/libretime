<?php

class Application_Form_AddShowRebroadcastDates extends Zend_Form_SubForm
{

    public function init()
    {
        //Add type select
		$this->addElement('select', 'add_show_rebroadcast_date_1', array(
            'label'    => 'Rebroadcast Day:',
            'required' => true,
            'class' => ' input_select',
            'multiOptions' => array(
                "0 days" => "+0 days ",
				"1 day" => "+1 day ",
                "2 days" => "+2 days",
                "3 days" => "+3 days"
            ), 
        ));

        // Add start time element
        $this->addElement('text', 'add_show_start_time_1', array(
            'label'      => 'Rebroadcast Time:',
            'class'      => 'input_text',
            'required'   => true,
            'value'     => '0:00',
            'filters'    => array('StringTrim'),
			'validators' => array(
				'NotEmpty',
        		array('date', false, array('HH:mm'))
    		) 
        ));
    }


}

