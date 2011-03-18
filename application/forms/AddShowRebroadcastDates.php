<?php

class Application_Form_AddShowRebroadcastDates extends Zend_Form_SubForm
{

    public function init()
    {
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'form/add-show-rebroadcast.phtml'))
        ));

        //Add date select
		$this->addElement('select', 'add_show_rebroadcast_date_1', array(
            'required' => false,
            'class' => ' input_select',
            'multiOptions' => array(
                "" => "",
                "0 days" => "+0 days",
				"1 day" => "+1 day",
                "2 days" => "+2 days",
                "3 days" => "+3 days"
            ),
            'decorators' => array(
                'ViewHelper'
            )  
        ));

        // Add start time element
        $this->addElement('text', 'add_show_rebroadcast_time_1', array(
            'class'      => 'input_text',
            'required' => false,
            'value'     => '',
            'filters'    => array('StringTrim'),
			'validators' => array(
				'NotEmpty',
        		array('date', false, array('HH:mm'))
    		),
            'decorators' => array(
                'ViewHelper'
            )  
        ));

        //Add date select
		$this->addElement('select', 'add_show_rebroadcast_date_2', array(
            'required' => false,
            'class' => ' input_select',
            'multiOptions' => array(
                "" => "",
                "0 days" => "+0 days",
				"1 day" => "+1 day",
                "2 days" => "+2 days",
                "3 days" => "+3 days"
            ),
            'decorators' => array(
                'ViewHelper'
            )  
        ));

        // Add start time element
        $this->addElement('text', 'add_show_rebroadcast_time_2', array(
            'class'      => 'input_text',
            'required' => false,
            'value'     => '',
            'filters'    => array('StringTrim'),
			'validators' => array(
				'NotEmpty',
        		array('date', false, array('HH:mm'))
    		),
            'decorators' => array(
                'ViewHelper'
            )  
        ));

        //Add date select
		$this->addElement('select', 'add_show_rebroadcast_date_3', array(
            'required' => false,
            'class' => ' input_select',
            'multiOptions' => array(
                "" => "",
                "0 days" => "+0 days",
				"1 day" => "+1 day",
                "2 days" => "+2 days",
                "3 days" => "+3 days"
            ),
            'decorators' => array(
                'ViewHelper'
            )  
        ));

        // Add start time element
        $this->addElement('text', 'add_show_rebroadcast_time_3', array(
            'class'      => 'input_text',
            'required' => false,
            'value'     => '',
            'filters'    => array('StringTrim'),
			'validators' => array(
				'NotEmpty',
        		array('date', false, array('HH:mm'))
    		),
            'decorators' => array(
                'ViewHelper'
            )  
        ));

        //Add date select
		$this->addElement('select', 'add_show_rebroadcast_date_4', array(
            'required' => false,
            'class' => ' input_select',
            'multiOptions' => array(
                "" => "",
                "0 days" => "+0 days",
				"1 day" => "+1 day",
                "2 days" => "+2 days",
                "3 days" => "+3 days"
            ),
            'decorators' => array(
                'ViewHelper'
            )  
        ));

        // Add start time element
        $this->addElement('text', 'add_show_rebroadcast_time_4', array(
            'class'      => 'input_text',
            'required' => false,
            'value'     => '',
            'filters'    => array('StringTrim'),
			'validators' => array(
				'NotEmpty',
        		array('date', false, array('HH:mm'))
    		),
            'decorators' => array(
                'ViewHelper'
            )  
        ));

        //Add date select
		$this->addElement('select', 'add_show_rebroadcast_date_5', array(
            'required' => false,
            'class' => ' input_select',
            'multiOptions' => array(
                "" => "",
                "0 days" => "+0 days",
				"1 day" => "+1 day",
                "2 days" => "+2 days",
                "3 days" => "+3 days"
            ),
            'decorators' => array(
                'ViewHelper'
            )  
        ));

        // Add start time element
        $this->addElement('text', 'add_show_rebroadcast_time_5', array(
            'class'      => 'input_text',
            'required' => false,
            'value'     => '',
            'filters'    => array('StringTrim'),
			'validators' => array(
				'NotEmpty',
        		array('date', false, array('HH:mm'))
    		),
            'decorators' => array(
                'ViewHelper'
            )  
        ));
    }
}

