<?php

class Application_Form_AddShowRebroadcastDates extends Zend_Form_SubForm
{

    public function init()
    {
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'form/add-show-rebroadcast.phtml'))
        ));


        $relativeDates = array();
        $relativeDates[""] = "";
        for($i=0; $i <=30; $i++) {
           $relativeDates["$i days"] = "+$i days";  
        }

        //Add date select
		$this->addElement('select', 'add_show_rebroadcast_date_1', array(
            'required' => false,
            'class' => ' input_select',
            'multiOptions' => $relativeDates,
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
            'multiOptions' => $relativeDates,
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
            'multiOptions' => $relativeDates,
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
            'multiOptions' => $relativeDates,
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
            'multiOptions' => $relativeDates,
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

