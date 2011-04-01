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
        		array('date', false, array('HH:mm')),
                array('regex', false, array('/^[0-9:]+$/', 'messages' => 'Invalid character entered'))
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
        		array('date', false, array('HH:mm')),
                array('regex', false, array('/^[0-9:]+$/', 'messages' => 'Invalid character entered'))
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
        		array('date', false, array('HH:mm')),
                array('regex', false, array('/^[0-9:]+$/', 'messages' => 'Invalid character entered'))
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
        		array('date', false, array('HH:mm')),
                array('regex', false, array('/^[0-9:]+$/', 'messages' => 'Invalid character entered'))
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
        		array('date', false, array('HH:mm')),
                array('regex', false, array('/^[0-9:]+$/', 'messages' => 'Invalid character entered'))
    		),
            'decorators' => array(
                'ViewHelper'
            )  
        ));
    }

    public function checkReliantFields($formData) {

        $valid = true;

        for($i=1; $i<=5; $i++) {
        
            $days = $formData['add_show_rebroadcast_date_'.$i];

            if($days == "") {
                continue;
            }

            $days = explode(" ", $days);
            $day = $days[0];

            $show_start_time = $formData['add_show_start_date']."".$formData['add_show_start_time'];
            $show_end = new DateTime($show_start_time);

            $duration = $formData['add_show_duration'];
            $duration = explode(":", $duration);

            $show_end->add(new DateInterval("PT$duration[0]H"));
            $show_end->add(new DateInterval("PT$duration[1]M"));
             $show_end->add(new DateInterval("PT1H"));//min time to wait until a rebroadcast
           
            $rebroad_start = $formData['add_show_start_date']."".$formData['add_show_rebroadcast_time_'.$i];
            $rebroad_start = new DateTime($rebroad_start);
            $rebroad_start->add(new DateInterval("P".$day."D"));

            if($rebroad_start < $show_end) {
                $this->getElement('add_show_rebroadcast_time_'.$i)->setErrors(array("Must wait at least 1 hour to rebroadcast"));
                $valid = false;
            }
        }           
 
        return $valid;
    }
}

