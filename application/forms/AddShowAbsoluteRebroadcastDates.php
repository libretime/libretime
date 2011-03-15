<?php

class Application_Form_AddShowAbsoluteRebroadcastDates extends Zend_Form_SubForm
{

    public function init()
    {
		// Add start date element
        $this->addElement('text', 'add_show_rebroadcast_absolute_date_1', array(
            'label'      => 'Rebroadcast Date:',
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
        $this->addElement('text', 'add_show_rebroadcast_absolute_time_1', array(
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

