<?php
class Application_Service_ShowFormService
{
    private $ccShow;
    private $instanceId;

    public function __construct($showId = null, $instanceId = null)
    {
        if (!is_null($showId)) {
            $this->ccShow = CcShowQuery::create()->findPk($showId);
        }
        $this->instanceId = $instanceId;
    }

    /**
     * 
     * @return array of show forms
     */
    public function createShowForms()
    {
        $formWhat    = new Application_Form_AddShowWhat();
        $formWho     = new Application_Form_AddShowWho();
        $formWhen    = new Application_Form_AddShowWhen();
        $formRepeats = new Application_Form_AddShowRepeats();
        $formStyle   = new Application_Form_AddShowStyle();
        $formLive    = new Application_Form_AddShowLiveStream();
        $formRecord = new Application_Form_AddShowRR();
        $formAbsoluteRebroadcast = new Application_Form_AddShowAbsoluteRebroadcastDates();
        $formRebroadcast = new Application_Form_AddShowRebroadcastDates();

        $formWhat->removeDecorator('DtDdWrapper');
        $formWho->removeDecorator('DtDdWrapper');
        $formWhen->removeDecorator('DtDdWrapper');
        $formRepeats->removeDecorator('DtDdWrapper');
        $formStyle->removeDecorator('DtDdWrapper');
        $formLive->removeDecorator('DtDdWrapper');
        $formRecord->removeDecorator('DtDdWrapper');
        $formAbsoluteRebroadcast->removeDecorator('DtDdWrapper');
        $formRebroadcast->removeDecorator('DtDdWrapper');

        $forms = array();
        $forms["what"] = $formWhat;
        $forms["who"] = $formWho;
        $forms["when"] = $formWhen;
        $forms["repeats"] = $formRepeats;
        $forms["style"] = $formStyle;
        $forms["live"] = $formLive;
        $forms["record"] = $formRecord;
        $forms["abs_rebroadcast"] = $formAbsoluteRebroadcast;
        $forms["rebroadcast"] = $formRebroadcast;

        return $forms;
    }

    /**
     * 
     * Popluates the what, when, and repeat forms
     * with default values
     */
    public function populateNewShowForms($formWhat, $formWhen, $formRepeats)
    {
        $formWhat->populate(
            array('add_show_id' => '-1',
                  'add_show_instance_id' => '-1'));

        $formWhen->populate(
            array('add_show_start_date' => date("Y-m-d"),
                  'add_show_start_time' => '00:00',
                  'add_show_end_date_no_repeate' => date("Y-m-d"),
                  'add_show_end_time' => '01:00',
                  'add_show_duration' => '01h 00m'));

        $formRepeats->populate(array('add_show_end_date' => date("Y-m-d")));
    }

    /**
     * 
     * Delegates populating each show form with the appropriate
     * data of the current show being edited
     * 
     * @param $forms
     */
    public function delegateFormPopulation($forms)
    {
        $this->populateFormWhat($forms["what"]);
        $this->populateFormWhen($forms["when"]);
        $this->populateFormRepeats($forms["repeats"]);
        $this->populateFormWho($forms["who"]);
        $this->populateFormStyle($forms["style"]);
        $this->populateFormRecord($forms["record"]);
        $this->populateFormRebroadcastRelative($forms["rebroadcast"]);
    }

    private function populateFormWhat($form)
    {
        $form->populate(
            array(
                'add_show_instance_id' => $this->instanceId,
                'add_show_id' => $this->ccShow->getDbId(),
                'add_show_name' => $this->ccShow->getDbName(),
                'add_show_url' => $this->ccShow->getDbUrl(),
                'add_show_genre' => $this->ccShow->getDbGenre(),
                'add_show_description' => $this->ccShow->getDbDescription()));
    }

    private function populateFormWhen($form)
    {
        $ccShowDay = $this->ccShow->getFirstCcShowDay();

        $showStart = $ccShowDay->getLocalStartDateAndTime();
        $showEnd = $ccShowDay->getLocalEndDateAndTime($showStart);

        //check if the first show is in the past
        if ($ccShowDay->isShowStartInPast()) {
            //for a non-repeating show, we should never allow user to change the start time.
            //for a repeating show, we should allow because the form works as repeating template form
            if (!$ccShowDay->isRepeating()) {
                $form->disableStartDateAndTime();
            } else {
                list($showStart, $showEnd) = $this->getNextFutureRepeatShowTime();
            }
        }

        $form->populate(
            array(
                'add_show_start_date' => $showStart->format("Y-m-d"),
                'add_show_start_time' => $showStart->format("H:i"),
                'add_show_end_date_no_repeat' => $showEnd->format("Y-m-d"),
                'add_show_end_time'    => $showEnd->format("H:i"),
                'add_show_duration' => $ccShowDay->formatDuration(true),
                'add_show_repeats' => $ccShowDay->isRepeating() ? 1 : 0));
    }

    private function populateFormRepeats($form)
    {
        $ccShowDays = $this->ccShow->getCcShowDays();

        $days = array();
        foreach ($ccShowDays as $ccShowDay) {
            $showStart = $ccShowDay->getLocalStartDateAndTime();
            array_push($days, $showStart->format("w"));
        }

        $service_show = new Application_Service_ShowService($this->ccShow->getDbId());
        $repeatEndDate = new DateTime($service_show->getRepeatingEndDate(), new DateTimeZone(
            $ccShowDays[0]->getDbTimezone()));
        //end dates are stored non-inclusively so we need to
        //subtract one day
        $repeatEndDate->sub(new DateInterval("P1D"));

        $form->populate(
            array(
                'add_show_repeat_type' => $ccShowDays[0]->getDbRepeatType(),
                'add_show_day_check' => $days,
                'add_show_end_date' => $repeatEndDate->format("Y-m-d"),
                'add_show_no_end' => (!$service_show->getRepeatingEndDate())));
    }

    private function populateFormWho($form)
    {
        $ccShowHosts = $this->ccShow->getCcShowHostss();

        $hosts = array();
        foreach ($ccShowHosts as $ccShowHost) {
            array_push($hosts, $ccShowHost->getDbHost());
        }

        $form->populate(array('add_show_hosts' => $hosts));
    }

    private function populateFormStyle($form)
    {
        $form->populate(
            array(
                'add_show_background_color' => $this->ccShow->getDbBackgroundColor(),
                'add_show_color' => $this->ccShow->getDbColor()));
    }

    private function populateFormRecord($form)
    {
        $form->populate(
            array(
                'add_show_record' => $this->ccShow->isRecorded(),
                'add_show_rebroadcast' => $this->ccShow->isRebroadcast()));

        $form->getElement('add_show_record')->setOptions(array('disabled' => true));
    }

    public function populateFormRebroadcastRelative($form)
    {
        $relativeRebroadcasts = $this->ccShow->getRebroadcastsRelative();

        $formValues = array();
        $i = 1;
        foreach ($relativeRebroadcasts as $rr) {
            $formValues["add_show_rebroadcast_date_$i"] = $rr->getDbDayOffset();
            $formValues["add_show_rebroadcast_time_$i"] = Application_Common_DateHelper::removeSecondsFromTime(
                $rr->getDbStartTime());
            $i++;
        }

        $form->populate($formValues);
    }

    /**
     * 
     * Before we send the form data in for validation, there
     * are a few fields we may need to adjust first
     * @param $formData
     */
    public function preEditShowValidationCheck($formData)
    {
        $validateStartDate = true;
        $validateStartTime = true;

        //CcShowDays object of the show currently being edited
        $currentShowDay = $this->ccShow->getFirstCcShowDay();

        //DateTime object
        $dt = $currentShowDay->getLocalStartDateAndTime();

        if (!array_key_exists('add_show_start_date', $formData)) {
            //Changing the start date was disabled, since the
            //array key does not exist. We need to repopulate this entry from the db.
            $formData['add_show_start_date'] = $dt->format("Y-m-d");

            if (!array_key_exists('add_show_start_time', $formData)) {
                $formData['add_show_start_time'] = $dt->format("H:i");
                $validateStartTime = false;
            }
            $validateStartDate = false;
        }
        $formData['add_show_record'] = $currentShowDay->getDbRecord();

        //if the show is repeating, set the start date to the next
        //repeating instance in the future
        if ($currentShowDay->isRepeating()) {
             list($originalShowStartDateTime,) = $this->getNextFutureRepeatShowTime();
        } else {
            $originalShowStartDateTime = $dt;
        }

        return array($formData, $validateStartDate, $validateStartTime, $originalShowStartDateTime);
    }

    /**
     * 
     * Returns 2 DateTime objects, in the user's local time,
     * of the next future repeat show instance start and end time
     */
    private function getNextFutureRepeatShowTime()
    {
        $sql = <<<SQL
SELECT starts, ends FROM cc_show_instances
WHERE ends > now() at time zone 'UTC'
AND show_id = :showId
ORDER BY starts
LIMIT 1
SQL;
        $result = Application_Common_Database::prepareAndExecute( $sql,
            array( 'showId' => $this->ccShow->getDbId() ), 'all' );
        
        foreach ($result as $r) {
            $starts = new DateTime($r["starts"], new DateTimeZone('UTC'));
            $ends = new DateTime($r["ends"], new DateTimeZone('UTC'));
        }

        $userTimezone = Application_Model_Preference::GetTimezone();

        $starts->setTimezone(new DateTimeZone($userTimezone));
        $ends->setTimezone(new DateTimeZone($userTimezone));

        return array($starts, $ends);
    }

    /**
     * 
     * Validates show forms
     * 
     * @return boolean
     */
    public function validateShowForms($forms, $formData, $validateStartDate = true,
        $originalStartDate=null, $editShow=false, $instanceId=null)
    {
        $what = $forms["what"]->isValid($formData);
        $live = $forms["live"]->isValid($formData);
        $record = $forms["record"]->isValid($formData);
        $who = $forms["who"]->isValid($formData);
        $style = $forms["style"]->isValid($formData);
        $when = $forms["when"]->isWhenFormValid($formData, $validateStartDate,
            $originalStartDate, $editShow, $instanceId);

        $repeats = true;
        if ($formData["add_show_repeats"]) {
            $repeats = $forms["repeats"]->isValid($formData);

            /*
             * Make the absolute rebroadcast form valid since
             * it does not get used if the show is repeating
             */
            $forms["abs_rebroadcast"]->reset();
            $absRebroadcast = true;

            $rebroadcast = true;
            if ($formData["add_show_rebroadcast"]) {
                $formData["add_show_duration"] = Application_Service_ShowService::formatShowDuration(
                    $formData["add_show_duration"]);
                $rebroadcast = $forms["rebroadcast"]->isValid($formData);
            }
        } else {
            /*
             * Make the rebroadcast form valid since it does
             * not get used if the show is not repeating.
             * Instead, we use the absolute rebroadcast form
             */
            $forms["rebroadcast"]->reset();
            $rebroadcast = true;

            $absRebroadcast = true;
            if ($formData["add_show_rebroadcast"]) {
                $formData["add_show_duration"] = Application_Service_ShowService::formatShowDuration(
                    $formData["add_show_duration"]);
                $absRebroadcast = $forms["abs_rebroadcast"]->isValid($formData);
            }
        }

        if ($what && $live && $record && $who && $style && $when &&
            $repeats && $absRebroadcast && $rebroadcast) {
            return true;
        } else {
            return false;
        }
    }
}