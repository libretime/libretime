<?php

class Application_Form_AddShowAbsoluteRebroadcastDates extends Zend_Form_SubForm
{

    public function init()
    {
        //$this->setDisableLoadDefaultDecorators(true);

        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'form/add-show-rebroadcast-absolute.phtml'))
        ));

		// Add start date element
        $this->addElement('text', 'add_show_rebroadcast_absolute_date_1', array(
            'label'      => 'Rebroadcast Date:',
            'class'      => 'input_text',
            'required' => false,
            'value'     => '',
            'filters'    => array('StringTrim'),
            'validators' => array(
				'NotEmpty',
        		array('date', false, array('YYYY-MM-DD'))
    		),
            'decorators' => array(
                'ViewHelper'
            ) 
        ));

        
        // Add start time element
        $this->addElement('text', 'add_show_rebroadcast_absolute_time_1', array(
            'label'      => 'Rebroadcast Time:',
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

        // Add start date element
        $this->addElement('text', 'add_show_rebroadcast_absolute_date_2', array(
            'label'      => 'Rebroadcast Date:',
            'class'      => 'input_text',
            'required' => false,
            'value'     => '',
            'filters'    => array('StringTrim'),
            'validators' => array(
				'NotEmpty',
        		array('date', false, array('YYYY-MM-DD'))
    		),
            'decorators' => array(
                'ViewHelper'
            ) 
        ));

        
        // Add start time element
        $this->addElement('text', 'add_show_rebroadcast_absolute_time_2', array(
            'label'      => 'Rebroadcast Time:',
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

         // Add start date element
        $this->addElement('text', 'add_show_rebroadcast_absolute_date_3', array(
            'label'      => 'Rebroadcast Date:',
            'class'      => 'input_text',
            'required' => false,
            'value'     => '',
            'filters'    => array('StringTrim'),
            'validators' => array(
				'NotEmpty',
        		array('date', false, array('YYYY-MM-DD'))
    		),
            'decorators' => array(
                'ViewHelper'
            ) 
        ));

        
        // Add start time element
        $this->addElement('text', 'add_show_rebroadcast_absolute_time_3', array(
            'label'      => 'Rebroadcast Time:',
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

         // Add start date element
        $this->addElement('text', 'add_show_rebroadcast_absolute_date_4', array(
            'label'      => 'Rebroadcast Date:',
            'class'      => 'input_text',
            'required' => false,
            'value'     => '',
            'filters'    => array('StringTrim'),
            'validators' => array(
				'NotEmpty',
        		array('date', false, array('YYYY-MM-DD'))
    		),
            'decorators' => array(
                'ViewHelper'
            ) 
        ));

        
        // Add start time element
        $this->addElement('text', 'add_show_rebroadcast_absolute_time_4', array(
            'label'      => 'Rebroadcast Time:',
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

         // Add start date element
        $this->addElement('text', 'add_show_rebroadcast_absolute_date_5', array(
            'label'      => 'Rebroadcast Date:',
            'class'      => 'input_text',
            'required' => false,
            'value'     => '',
            'filters'    => array('StringTrim'),
            'validators' => array(
				'NotEmpty',
        		array('date', false, array('YYYY-MM-DD'))
    		),
            'decorators' => array(
                'ViewHelper'
            ) 
        ));

        
        // Add start time element
        $this->addElement('text', 'add_show_rebroadcast_absolute_time_5', array(
            'label'      => 'Rebroadcast Time:',
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

