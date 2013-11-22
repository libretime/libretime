<?php

class Application_Form_AddShowWhen extends Zend_Form_SubForm
{

    public function init()
    {
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'form/add-show-when.phtml'))
        ));

        $notEmptyValidator = Application_Form_Helper_ValidationTypes::overrideNotEmptyValidator();
        $dateValidator = Application_Form_Helper_ValidationTypes::overrrideDateValidator("YYYY-MM-DD");
        $regexValidator = Application_Form_Helper_ValidationTypes::overrideRegexValidator(
            "/^[0-2]?[0-9]:[0-5][0-9]$/",
            _("'%value%' does not fit the time format 'HH:mm'"));

        // Add start date element
        $startDate = new Zend_Form_Element_Text('add_show_start_date');
        $startDate->class = 'input_text';
        $startDate->setRequired(true)
                    ->setLabel(_('Date/Time Start:'))
                    ->setValue(date("Y-m-d"))
                    ->setFilters(array('StringTrim'))
                    ->setValidators(array(
                        $notEmptyValidator,
                        $dateValidator))
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
                        $notEmptyValidator,
                        $regexValidator
                        ))->setDecorators(array('ViewHelper'));
        $startTime->setAttrib('alt', 'time');
        $this->addElement($startTime);

        // Add end date element
        $endDate = new Zend_Form_Element_Text('add_show_end_date_no_repeat');
        $endDate->class = 'input_text';
        $endDate->setRequired(true)
                    ->setLabel(_('Date/Time End:'))
                    ->setValue(date("Y-m-d"))
                    ->setFilters(array('StringTrim'))
                    ->setValidators(array(
                        $notEmptyValidator,
                        $dateValidator))
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
                        $notEmptyValidator,
                        $regexValidator))
                    ->setDecorators(array('ViewHelper'));
        $endTime->setAttrib('alt', 'time');
        $this->addElement($endTime);

        // Add duration element
        $this->addElement('text', 'add_show_duration', array(
            'label'      => _('Duration:'),
            'class'      => 'input_text',
            'value'      => '01h 00m',
            'readonly'   => true,
            'decorators'  => array('ViewHelper')
        ));

        $timezone = new Zend_Form_Element_Select('add_show_timezone');
        $timezone->setRequired(true)
                 ->setLabel(_("Timezone:"))
                 ->setMultiOptions(Application_Common_Timezone::getTimezones())
                 ->setValue(Application_Model_Preference::GetDefaultTimezone())
                 ->setAttrib('class', 'input_select add_show_input_select')
                 ->setDecorators(array('ViewHelper'));
        $this->addElement($timezone);

        // Add repeats element
        $this->addElement('checkbox', 'add_show_repeats', array(
            'label'      => _('Repeats?'),
            'required'   => false,
            'decorators'  => array('ViewHelper')
        ));

    }

    public function isWhenFormValid($formData, $validateStartDate, $originalStartDate,
        $update, $instanceId) {
        if (parent::isValid($formData)) {
            return self::checkReliantFields($formData, $validateStartDate,
                $originalStartDate, $update, $instanceId);
        } else {
            return false;
        }
    }

    public function checkReliantFields($formData, $validateStartDate, $originalStartDate=null, $update=false, $instanceId=null)
    {
        $valid = true;

        $start_time = $formData['add_show_start_date']." ".$formData['add_show_start_time'];
        $end_time = $formData['add_show_end_date_no_repeat']." ".$formData['add_show_end_time'];

        //DateTime stores $start_time in the current timezone
        $nowDateTime = new DateTime();
        $showStartDateTime = new DateTime($start_time);
        $showEndDateTime = new DateTime($end_time);
        if ($validateStartDate) {
            if ($showStartDateTime->getTimestamp() < $nowDateTime->getTimestamp()) {
                $this->getElement('add_show_start_time')->setErrors(array(_('Cannot create show in the past')));
                $valid = false;
            }
            // if edit action, check if original show start time is in the past. CC-3864
            if ($originalStartDate) {
                if ($originalStartDate->getTimestamp() < $nowDateTime->getTimestamp()) {
                    $this->getElement('add_show_start_time')->setValue($originalStartDate->format("H:i"));
                    $this->getElement('add_show_start_date')->setValue($originalStartDate->format("Y-m-d"));
                    $this->getElement('add_show_start_time')->setErrors(array(_('Cannot modify start date/time of the show that is already started')));
                    $this->disableStartDateAndTime();
                    $valid = false;
                }
            }
        }

        // if end time is in the past, return error
        if ($showEndDateTime->getTimestamp() < $nowDateTime->getTimestamp()) {
            $this->getElement('add_show_end_time')->setErrors(array(_('End date/time cannot be in the past')));
            $valid = false;
        }

        $pattern =  '/([0-9][0-9])h ([0-9][0-9])m/';

        if (preg_match($pattern, $formData['add_show_duration'], $matches) && count($matches) == 3) {
            $hours = $matches[1];
            $minutes = $matches[2];
            if ($formData["add_show_duration"] == "00h 00m") {
                $this->getElement('add_show_duration')->setErrors(array(_('Cannot have duration 00h 00m')));
                $valid = false;
            } elseif (strpos($formData["add_show_duration"], 'h') !== false && $hours >= 24) {
                if ($hours > 24 || ($hours == 24 && $minutes > 0)) {
                    $this->getElement('add_show_duration')->setErrors(array(_('Cannot have duration greater than 24h')));
                    $valid = false;
                }
            } elseif ( strstr($formData["add_show_duration"], '-') ) {
                $this->getElement('add_show_duration')->setErrors(array(_('Cannot have duration < 0m')));
                $valid = false;
            }
        } else {
            $valid = false;
        }

        /* Check if show is overlapping
         * We will only do this check if the show is valid
         * upto this point
         */
        if ($valid) {
            $utc = new DateTimeZone('UTC');
            $showTimezone = new DateTimeZone($formData["add_show_timezone"]);
            $show_start = new DateTime($start_time, $showTimezone);
            //we need to know the start day of the week in show's local timezome
            $startDow = $show_start->format("w");
            $show_start->setTimezone($utc);
            $show_end = new DateTime($end_time, $showTimezone);
            $show_end->setTimezone($utc);

            if ($formData["add_show_repeats"]) {

                //get repeating show end date
                if ($formData["add_show_no_end"]) {
                    $date = Application_Model_Preference::GetShowsPopulatedUntil();

                    if (is_null($date)) {
                        $populateUntilDateTime = new DateTime("now", $utc);
                        Application_Model_Preference::SetShowsPopulatedUntil($populateUntilDateTime);
                    } else {
                        $populateUntilDateTime = clone $date;
                    }

                } elseif (!$formData["add_show_no_end"]) {
                    $popUntil = $formData["add_show_end_date"]." ".$formData["add_show_end_time"];
                    $populateUntilDateTime = new DateTime($popUntil, $showTimezone);
                    $populateUntilDateTime->setTimezone($utc);
                }

                //get repeat interval
                if ($formData["add_show_repeat_type"] == 0) {
                    $interval = 'P7D';
                } elseif ($formData["add_show_repeat_type"] == 1) {
                    $interval = 'P14D';
                } elseif ($formData["add_show_repeat_type"] == 4) {
                    $interval = 'P21D';
                } elseif ($formData["add_show_repeat_type"] == 5) {
                    $interval = 'P28D';
                } elseif ($formData["add_show_repeat_type"] == 2) {
                    $interval = 'P1M';
                }

                /* Check first show
                 * Continue if the first show does not overlap
                 */
                if ($update) {
                    $overlapping = Application_Model_Schedule::checkOverlappingShows(
                                    $show_start, $show_end, $update, null, $formData["add_show_id"]);
                } else {
                    $overlapping = Application_Model_Schedule::checkOverlappingShows(
                                    $show_start, $show_end);
                }

                /* Check if repeats overlap with previously scheduled shows
                 * Do this for each show day
                 */
                if (!$overlapping) {

                    if (!isset($formData['add_show_day_check'])) {
                        return false;
                    }

                    foreach ($formData["add_show_day_check"] as $day) {
                        $repeatShowStart = clone $show_start;
                        $repeatShowEnd = clone $show_end;
                        $daysAdd=0;
                        if ($startDow !== $day) {
                            if ($startDow > $day)
                                $daysAdd = 6 - $startDow + 1 + $day;
                            else
                                $daysAdd = $day - $startDow;

                            /* In case we are crossing daylights saving time we need
                             * to convert show start and show end to local time before
                             * adding the interval for the next repeating show
                             */
                            $repeatShowStart->setTimezone($showTimezone);
                            $repeatShowEnd->setTimezone($showTimezone);
                            $repeatShowStart->add(new DateInterval("P".$daysAdd."D"));
                            $repeatShowEnd->add(new DateInterval("P".$daysAdd."D"));
                            //set back to UTC
                            $repeatShowStart->setTimezone($utc);
                            $repeatShowEnd->setTimezone($utc);
                        }
                        /* Here we are checking each repeating show by
                         * the show day.
                         * (i.e: every wednesday, then every thursday, etc.)
                         */
                        while ($repeatShowStart->getTimestamp() < $populateUntilDateTime->getTimestamp()) {
                            if ($formData['add_show_id'] == -1) {
                                //this is a new show
                                $overlapping = Application_Model_Schedule::checkOverlappingShows(
                                    $repeatShowStart, $repeatShowEnd);
                                
                                /* If the repeating show is rebroadcasted we need to check
                                 * the rebroadcast dates relative to the repeating show
                                 */
                                if (!$overlapping && $formData['add_show_rebroadcast']) {
                                    $overlapping = self::checkRebroadcastDates(
                                        $repeatShowStart, $formData, $hours, $minutes);
                                }
                            } else {
                                $overlapping = Application_Model_Schedule::checkOverlappingShows(
                                    $repeatShowStart, $repeatShowEnd, $update, null, $formData["add_show_id"]);
                                    
                                if (!$overlapping && $formData['add_show_rebroadcast']) {
                                    $overlapping = self::checkRebroadcastDates(
                                        $repeatShowStart, $formData, $hours, $minutes, true);
                                }
                            }
                            
                            if ($overlapping) {
                                $valid = false;
                                $this->getElement('add_show_duration')->setErrors(array(_('Cannot schedule overlapping shows')));
                                break 1;
                            } else {
                                $repeatShowStart->setTimezone($showTimezone);
                                $repeatShowEnd->setTimezone($showTimezone);
                                $repeatShowStart->add(new DateInterval($interval));
                                $repeatShowEnd->add(new DateInterval($interval));
                                $repeatShowStart->setTimezone($utc);
                                $repeatShowEnd->setTimezone($utc);
                            }
                        }
                    }
                } else {
                    $valid = false;
                    $this->getElement('add_show_duration')->setErrors(array(_('Cannot schedule overlapping shows')));
                }
            } elseif ($formData["add_show_rebroadcast"]) {
                /* Check first show
                 * Continue if the first show does not overlap
                 */
                $overlapping = Application_Model_Schedule::checkOverlappingShows($show_start, $show_end, $update, $instanceId);

                if (!$overlapping) {
                    $durationToAdd = "PT".$hours."H".$minutes."M";
                    for ($i = 1; $i <= 10; $i++) {
                        
                        if (empty($formData["add_show_rebroadcast_date_absolute_".$i])) break;
                        
                        $abs_rebroadcast_start = $formData["add_show_rebroadcast_date_absolute_".$i]." ".
                                                 $formData["add_show_rebroadcast_time_absolute_".$i];
                        $rebroadcastShowStart = new DateTime($abs_rebroadcast_start);
                        $rebroadcastShowStart->setTimezone(new DateTimeZone('UTC'));
                        $rebroadcastShowEnd = clone $rebroadcastShowStart;
                        $rebroadcastShowEnd->add(new DateInterval($durationToAdd));
                        $overlapping = Application_Model_Schedule::checkOverlappingShows($rebroadcastShowStart,
                            $rebroadcastShowEnd, $update, null, $formData["add_show_id"]);
                        if ($overlapping) {
                            $valid = false;
                            $this->getElement('add_show_duration')->setErrors(array(_('Cannot schedule overlapping shows')));
                            break;
                        }
                    }
                } else {
                    $valid = false;
                    $this->getElement('add_show_duration')->setErrors(array(_('Cannot schedule overlapping shows')));
                }
            } else {
              $overlapping = Application_Model_Schedule::checkOverlappingShows($show_start, $show_end, $update, $instanceId);
                if ($overlapping) {
                    $this->getElement('add_show_duration')->setErrors(array(_('Cannot schedule overlapping shows')));
                    $valid = false;
                }
            }
        }

        return $valid;
    }

    public function checkRebroadcastDates($repeatShowStart, $formData, $hours, $minutes, $showEdit=false) {
        $overlapping = false;
        for ($i = 1; $i <= 10; $i++) {
            if (empty($formData["add_show_rebroadcast_date_".$i])) break;
            $rebroadcastShowStart = clone $repeatShowStart;
            /* formData is in local time so we need to set the
             * show start back to local time
             */
            $rebroadcastShowStart->setTimezone(new DateTimeZone(
                $formData["add_show_timezone"]));
            $rebroadcastWhenDays = explode(" ", $formData["add_show_rebroadcast_date_".$i]);
            $rebroadcastWhenTime = explode(":", $formData["add_show_rebroadcast_time_".$i]);
            $rebroadcastShowStart->add(new DateInterval("P".$rebroadcastWhenDays[0]."D"));
            $rebroadcastShowStart->setTime($rebroadcastWhenTime[0], $rebroadcastWhenTime[1]);
            $rebroadcastShowStart->setTimezone(new DateTimeZone('UTC'));
            
            $rebroadcastShowEnd = clone $rebroadcastShowStart;
            $rebroadcastShowEnd->add(new DateInterval("PT".$hours."H".$minutes."M"));
            
            if ($showEdit) {
                $overlapping = Application_Model_Schedule::checkOverlappingShows(
                    $rebroadcastShowStart, $rebroadcastShowEnd, true, null, $formData['add_show_id']);
            } else {
                $overlapping = Application_Model_Schedule::checkOverlappingShows(
                    $rebroadcastShowStart, $rebroadcastShowEnd);
            }
            
            if ($overlapping) break;
        }
        
        return $overlapping;
    }
    
    public function disable()
    {
        $elements = $this->getElements();
        foreach ($elements as $element) {
            if ($element->getType() != 'Zend_Form_Element_Hidden') {
                $element->setAttrib('disabled','disabled');
            }
        }
    }

    public function disableRepeatCheckbox()
    {
        $element = $this->getElement('add_show_repeats');
        if ($element->getType() != 'Zend_Form_Element_Hidden') {
            $element->setAttrib('disabled','disabled');
        }
    }

    public function disableStartDateAndTime()
    {
        $elements = array($this->getElement('add_show_start_date'), $this->getElement('add_show_start_time'));
        foreach ($elements as $element) {
            if ($element->getType() != 'Zend_Form_Element_Hidden') {
                $element->setAttrib('disabled','disabled');
            }
        }
    }
}
