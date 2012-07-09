<?php

class Application_Form_AddShowWhen extends Zend_Form_SubForm
{

    public function init()
    {
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'form/add-show-when.phtml'))
        ));
        
        // Add start date element
        $startDate = new Zend_Form_Element_Text('add_show_start_date');
        $startDate->class = 'input_text';
        $startDate->setRequired(true)
                    ->setLabel('Date/Time Start:')
                    ->setValue(date("Y-m-d"))
                    ->setFilters(array('StringTrim'))
                    ->setValidators(array(
                        'NotEmpty',
                        array('date', false, array('YYYY-MM-DD'))))
                    ->setDecorators(array('ViewHelper'));
        $startDate->setAttrib('alt', 'date');
        $this->addElement($startDate);
        
        // Add start time element
        $startTime = new Zend_Form_Element_Text('add_show_start_time');
        $startTime->class = 'input_text';
        $startTime->setRequired(true)
                    ->setValue('00:00')
                    ->setFilters(array('StringTrim'))
                    ->setValidators(array(
                        'NotEmpty',
                        array('date', false, array('HH:mm')),
                        array('regex', false, array('/^[0-2]?[0-9]:[0-5][0-9]$/', 'messages' => 'Time format should be HH:mm'))
                        ))->setDecorators(array('ViewHelper'));
        $startTime->setAttrib('alt', 'time');
        $this->addElement($startTime);

        // Add end date element
        $endDate = new Zend_Form_Element_Text('add_show_end_date_no_repeat');
        $endDate->class = 'input_text';
        $endDate->setRequired(true)
                    ->setLabel('Date/Time End:')
                    ->setValue(date("Y-m-d"))
                    ->setFilters(array('StringTrim'))
                    ->setValidators(array(
                        'NotEmpty',
                        array('date', false, array('YYYY-MM-DD'))))
                    ->setDecorators(array('ViewHelper'));
        $endDate->setAttrib('alt', 'date');
        $this->addElement($endDate);
        
        // Add end time element
        $endTime = new Zend_Form_Element_Text('add_show_end_time');
        $endTime->class = 'input_text';
        $endTime->setRequired(true)
                    ->setValue('01:00')
                    ->setFilters(array('StringTrim'))
                    ->setValidators(array(
                        'NotEmpty',
                        array('date', false, array('HH:mm')),
                        array('regex', false, array('/^[0-2]?[0-9]:[0-5][0-9]$/', 'messages' => 'Time format should be HH:mm'))))
                    ->setDecorators(array('ViewHelper'));
        $endTime->setAttrib('alt', 'time');
        $this->addElement($endTime);
        
        // Add duration element
        $this->addElement('text', 'add_show_duration', array(
            'label'      => 'Duration:',
            'class'      => 'input_text',
            'value'      => '01h 00m',
            'readonly'   => true,
            'decorators'  => array('ViewHelper')
        ));

        // Add repeats element
        $this->addElement('checkbox', 'add_show_repeats', array(
            'label'      => 'Repeats?',
            'required'   => false,
            'decorators'  => array('ViewHelper')
        ));

    }

    public function checkReliantFields($formData, $validateStartDate, $originalStartDate=null, $update=false, $instanceId=null) {
        $valid = true;
        
        $hours;
        $minutes;
        
        $start_time = $formData['add_show_start_date']." ".$formData['add_show_start_time'];
        $end_time = $formData['add_show_end_date_no_repeat']." ".$formData['add_show_end_time'];
        
        //DateTime stores $start_time in the current timezone
        $nowDateTime = new DateTime();
        $showStartDateTime = new DateTime($start_time);
        $showEndDateTime = new DateTime($end_time);
        if ($validateStartDate){
            if($showStartDateTime->getTimestamp() < $nowDateTime->getTimestamp()) {
                $this->getElement('add_show_start_time')->setErrors(array('Cannot create show in the past'));
                $valid = false;
            }
            // if edit action, check if original show start time is in the past. CC-3864
            if($originalStartDate){
                if($originalStartDate->getTimestamp() < $nowDateTime->getTimestamp()) {
                    $this->getElement('add_show_start_time')->setValue($originalStartDate->format("H:i"));
                    $this->getElement('add_show_start_date')->setValue($originalStartDate->format("Y-m-d"));
                    $this->getElement('add_show_start_time')->setErrors(array('Cannot modify start date/time of the show that is already started'));
                    $this->disableStartDateAndTime();
                    $valid = false;
                }
            }
        }
        
        // if end time is in the past, return error
        if($showEndDateTime->getTimestamp() < $nowDateTime->getTimestamp()) {
            $this->getElement('add_show_end_time')->setErrors(array('End date/time cannot be in the past'));
            $valid = false;
        }
        
        $pattern =  '/([0-9][0-9])h ([0-9][0-9])m/';
        
        if (preg_match($pattern, $formData['add_show_duration'], $matches) && count($matches) == 3) {
            $hours = $matches[1];
            $minutes = $matches[2];
            if( $formData["add_show_duration"] == "00h 00m" ) {
                $this->getElement('add_show_duration')->setErrors(array('Cannot have duration 00h 00m'));
                $valid = false;
            }elseif(strpos($formData["add_show_duration"], 'h') !== false && $hours >= 24) {
                if ($hours > 24 || ($hours == 24 && $minutes > 0)) {
                    $this->getElement('add_show_duration')->setErrors(array('Cannot have duration greater than 24h'));
                    $valid = false;
                }
            }elseif( strstr($formData["add_show_duration"], '-') ){
                $this->getElement('add_show_duration')->setErrors(array('Cannot have duration < 0m'));
                $valid = false;
            }
        }
        else {
            $valid = false;
        }

        /* Check if show is overlapping
         * We will only do this check if the show is valid
         * upto this point
         */
        if ($valid) {
            $show_start = new DateTime($start_time);
            $show_start->setTimezone(new DateTimeZone('UTC'));
            $show_end = new DateTime($end_time);
            $show_end->setTimezone(new DateTimeZone('UTC'));

            if ($formData["add_show_repeats"]) {
                
                //get repeating show end date
                if ($formData["add_show_no_end"]) {
                    $date = Application_Model_Preference::GetShowsPopulatedUntil();

                    if (is_null($date)) {
                        $populateUntilDateTime = new DateTime("now", new DateTimeZone('UTC'));
                        Application_Model_Preference::SetShowsPopulatedUntil($populateUntilDateTime);
                    } else {
                        $populateUntilDateTime = clone $date;
                    }
                
                } elseif (!$formData["add_show_no_end"]) {
                    $popUntil = $formData["add_show_end_date"]." ".$formData["add_show_end_time"];
                    $populateUntilDateTime = new DateTime($popUntil, new DateTimeZone('UTC'));
                }
                
                //get repeat interval
                if ($formData["add_show_repeat_type"] == 0) {
                    $interval = 'P7D';
                } elseif ($formData["add_show_repeat_type"] == 1) {
                    $interval = 'P14D';
                } elseif ($formData["add_show_repeat_type"] == 2) {
                    $interval = 'P1M';
                }
                
                /* Check first show
                 * Continue if the first show does not overlap
                 */
                $overlapping = Application_Model_Schedule::checkOverlappingShows($show_start, $show_end, $update, $instanceId);
                
                /* Check if repeats overlap with previously scheduled shows
                 * Do this for each show day
                 */
                if (!$overlapping) {
                    $startDow = date("w", $show_start->getTimestamp());
                    foreach($formData["add_show_day_check"] as $day) {
                        $repeatShowStart = clone $show_start;
                        $repeatShowEnd = clone $show_end;
                        $daysAdd=0;
                        if ($startDow !== $day){
                            if ($startDow > $day)
                                $daysAdd = 6 - $startDow + 1 + $day;
                            else
                                $daysAdd = $day - $startDow;

                            $repeatShowStart->add(new DateInterval("P".$daysAdd."D"));
                            $repeatShowEnd->add(new DateInterval("P".$daysAdd."D"));
                        }
                        while ($repeatShowStart->getTimestamp() < $populateUntilDateTime->getTimestamp()) {
                            $overlapping = Application_Model_Schedule::checkOverlappingShows($repeatShowStart, $repeatShowEnd, $update, $instanceId);
                            if ($overlapping) {
                                $valid = false;
                                $this->getElement('add_show_duration')->setErrors(array('Cannot schedule overlapping shows'));
                                break 1;
                            } else {
                                $repeatShowStart->add(new DateInterval($interval));
                                $repeatShowEnd->add(new DateInterval($interval));
                            }
                        }
                    }
                } else {
                    $valid = false;
                    $this->getElement('add_show_duration')->setErrors(array('Cannot schedule overlapping shows'));
                }
            } elseif ($formData["add_show_rebroadcast"]) {
                /* Check first show
                 * Continue if the first show does not overlap
                 */
                $overlapping = Application_Model_Schedule::checkOverlappingShows($show_start, $show_end, $update, $instanceId);
                
                if (!$overlapping) {
                    for ($i = 1; $i <= 10; $i++) {
                        $hours = ltrim($hours, '0');
                        if ($minutes != "00") {
                            $minutes = ltrim($minutes, '0');
                            $durationToAdd = "PT".$hours."H".$minutes."I";
                        } else {
                            $minutes = "0";
                            $durationToAdd = "PT".$hours."H";
                        }
                        
                        $abs_rebroadcast_start = $formData["add_show_rebroadcast_date_absolute_".$i]." ".
                                                 $formData["add_show_rebroadcast_time_absolute_".$i];
                        $rebroadcastShowStart = new DateTime($abs_rebroadcast_start);
                        $rebroadcastShowStart->setTimezone(new DateTimeZone('UTC'));
                        $rebroadcastShowEnd = clone $rebroadcastShowStart;
                        $rebroadcastShowEnd->add(new DateInterval($durationToAdd));
                        $overlapping = Application_Model_Schedule::checkOverlappingShows($rebroadcastShowStart, $rebroadcastShowEnd, $update, $instanceId);
                        if ($overlapping) {
                            $valid = false;
                            $this->getElement('add_show_duration')->setErrors(array('Cannot schedule overlapping shows'));
                            break;
                        }
                    }
                } else {
                    $valid = false;
                    $this->getElement('add_show_duration')->setErrors(array('Cannot schedule overlapping shows'));
                }
            } else {
              $overlapping = Application_Model_Schedule::checkOverlappingShows($show_start, $show_end, $update, $instanceId);
                if ($overlapping) {
                    $this->getElement('add_show_duration')->setErrors(array('Cannot schedule overlapping shows'));
                    $valid = false;
                }
            }
        }
	      
        return $valid;
    }
    
    public function disable(){
        $elements = $this->getElements();
        foreach ($elements as $element)
        {
            if ($element->getType() != 'Zend_Form_Element_Hidden')
            {
                $element->setAttrib('disabled','disabled');
            }
        }
    }
    
    public function disableRepeatCheckbox(){
        $element = $this->getElement('add_show_repeats');
        if ($element->getType() != 'Zend_Form_Element_Hidden')
        {
            $element->setAttrib('disabled','disabled');
        }
    }
    
    public function disableStartDateAndTime(){
        $elements = array($this->getElement('add_show_start_date'), $this->getElement('add_show_start_time'));
        foreach ($elements as $element)
        {
            if ($element->getType() != 'Zend_Form_Element_Hidden')
            {
                $element->setAttrib('disabled','disabled');
            }
        }
    }
}

