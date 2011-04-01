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

    public function checkReliantFields($formData) {

        $valid = true;

        for($i=1; $i<=5; $i++) {
        
            $day = $formData['add_show_rebroadcast_absolute_date_'.$i];

            if($day == "") {
                continue;
            }

            $show_start_time = $formData['add_show_start_date']."".$formData['add_show_start_time'];
            $show_end = new DateTime($show_start_time);

            $duration = $formData['add_show_duration'];
            $duration = explode(":", $duration);

            $show_end->add(new DateInterval("PT$duration[0]H"));
            $show_end->add(new DateInterval("PT$duration[1]M"));
            $show_end->add(new DateInterval("PT1H"));//min time to wait until a rebroadcast
           
            $rebroad_start = $day."".$formData['add_show_rebroadcast_absolute_time_'.$i];
            $rebroad_start = new DateTime($rebroad_start);
            
            if($rebroad_start < $show_end) {
                $this->getElement('add_show_rebroadcast_absolute_time_'.$i)->setErrors(array("Must wait at least 1 hour to rebroadcast"));
                $valid = false;
            }
        }           
 
        return $valid;
    }
}

