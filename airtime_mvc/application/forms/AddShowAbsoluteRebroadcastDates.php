<?php

class Application_Form_AddShowAbsoluteRebroadcastDates extends Zend_Form_SubForm
{

    public function init()
    {
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'form/add-show-rebroadcast-absolute.phtml'))
        ));

        for($i=1; $i<=10; $i++) {

            $text = new Zend_Form_Element_Text("add_show_rebroadcast_date_absolute_$i");
            $text->setAttrib('class', 'input_text');
            $text->addFilter('StringTrim');
            $text->addValidator('date', false, array('YYYY-MM-DD'));
            $text->setRequired(false);
            $text->setDecorators(array('ViewHelper'));
            $this->addElement($text);

            $text = new Zend_Form_Element_Text("add_show_rebroadcast_time_absolute_$i");
            $text->setAttrib('class', 'input_text');
            $text->addFilter('StringTrim');
            $text->addValidator('date', false, array('HH:mm'));
            $text->addValidator('regex', false, array('/^[0-9:]+$/', 'messages' => 'Invalid character entered'));
            $text->setRequired(false);
            $text->setDecorators(array('ViewHelper'));
            $this->addElement($text);
        }
    }

    public function checkReliantFields($formData) {

        $valid = true;

        for($i=1; $i<=10; $i++) {

            $day = $formData['add_show_rebroadcast_date_absolute_'.$i];

            if(trim($day) == "" && trim($time) == "") {
                continue;
            }

            if (trim($day) == ""){
                $this->getElement('add_show_rebroadcast_date_absolute_'.$i)->setErrors(array("Day must be specified"));
                $valid = false;
            }
            
            $time = $formData['add_show_rebroadcast_time_absolute_'.$i];
            if (trim($time) == ""){
                $this->getElement('add_show_rebroadcast_time_absolute_'.$i)->setErrors(array("Time must be specified"));
                $valid = false;
            }

            if($valid === false) {
                return false;
            }

            $show_start_time = $formData['add_show_start_date']."".$formData['add_show_start_time'];
            $show_end = new DateTime($show_start_time);

            $duration = $formData['add_show_duration'];
            $duration = explode(":", $duration);

            $show_end->add(new DateInterval("PT$duration[0]H"));
            $show_end->add(new DateInterval("PT$duration[1]M"));
            $show_end->add(new DateInterval("PT1H"));//min time to wait until a rebroadcast

            $rebroad_start = $day."".$formData['add_show_rebroadcast_time_absolute_'.$i];
            $rebroad_start = new DateTime($rebroad_start);

            if($rebroad_start < $show_end) {
                $this->getElement('add_show_rebroadcast_time_absolute_'.$i)->setErrors(array("Must wait at least 1 hour to rebroadcast"));
                $valid = false;
            }
        }

        return $valid;
    }
}

