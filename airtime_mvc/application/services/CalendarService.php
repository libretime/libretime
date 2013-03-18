<?php

class Application_Service_CalendarService
{
    private $service_show;
    private $service_showDays;
    private $service_showInstances;
    private $service_user;

    public function __construct()
    {
        $this->service_show = new Application_Service_ShowService();
        $this->service_showInstances = new Application_Service_ShowInstanceService();
        $this->service_user = new Application_Service_UserService();
    }
/*
 * Form stuff begins here
 * Typically I would keep form creation and validation
 * in the controller but since shows require 9 forms,
 * the controller will become too fat.
 * Maybe we should create a special form show service?
 */
    /**
     * 
     * @return array of schedule forms
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

    public function populateForm($form, $values)
    {
        $form->populate($values);
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
                $formData["add_show_duration"] = $this->formatShowDuration(
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
                $formData["add_show_duration"] = $this->formatShowDuration(
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
/*
 * Form stuff ends
 */

    public function formatShowDuration($duration) {
        $hPos = strpos($duration, 'h');
        $mPos = strpos($duration, 'm');

        $hValue = 0;
        $mValue = 0;

        if ($hPos !== false) {
            $hValue = trim(substr($duration, 0, $hPos));
        }
        if ($mPos !== false) {
            $hPos = $hPos === false ? 0 : $hPos+1;
            $mValue = trim(substr($duration, $hPos, -1 ));
        }

        return $hValue.":".$mValue;
    }

    public function addUpdateShow($showData, $isUpdate=false)
    {
        //CcSubj object
        $currentUser = $this->service_user->getCurrentUser();

        $repeatType = ($showData['add_show_repeats']) ? $showData['add_show_repeat_type'] : -1;
        $isRecorded = (isset($showData['add_show_record']) && $showData['add_show_record']) ? 1 : 0;
        $isRebroadcast = (isset($showData['add_show_rebroadcast']) && $showData['add_show_rebroadcast']) ? 1 : 0;

        $showData["add_show_duration"] = $this->formatShowDuration(
            $showData["add_show_duration"]);

        $con = Propel::getConnection();
        $con->beginTransaction();
        try {
            if (!$currentUser->isAdminOrPM()) {
                throw new Exception("Permission denied");
            }
            //update ccShow
            $ccShow = $this->service_show->setShow($showData, $isUpdate);
            $showId = $ccShow->getDbId();

            if ($isUpdate) {
                $this->service_showInstances->deleteInvalidInstances($showData, $isRecorded, $repeatType);
                // updates cc_show_instances start/end times, and updates
                // schedule start/end times
                $this->service_showInstances->applyShowStartEndDifference($showData, $showId);
                $this->service_showInstances->deleteRebroadcastInstances($showId);
                $this->service_showDays->deleteShowDays();
                $this->service_show->deleteShowHosts($showId);
                if ($isRebroadcast) {
                    //delete entry in cc_show_rebroadcast
                    $this->service_show->deleteShowRebroadcasts($showId);
                }
            }

            //update ccShowDays
            $this->service_showDays = new Application_Service_ShowDaysService($showId);
            $this->service_showDays->setShowDays($showData, $repeatType, $isRecorded);

            //update ccShowRebroadcasts
            $this->service_show->setShowRebroadcasts($showData, $showId, $repeatType, $isRecorded);

            //update ccShowHosts
            $this->service_show->setShowHosts($showData, $showId);

            //create new ccShowInstances
            $this->service_showInstances->delegateShowInstanceCreation($showId, $isRebroadcast, $isUpdate);

            //after all instances have been deleted/updated, we need to update
            //the schedule playout status
            if ($isUpdate) {
                $this->service_showInstances->updateScheduleStatus($showId);
            }

            $con->commit();
            Application_Model_RabbitMq::PushSchedule();
        } catch (Exception $e) {
            $con->rollback();
            $isUpdate ? $action = "update" : $action = "creation";
            Logging::info("EXCEPTION: Show ".$action." failed.");
            Logging::info($e->getMessage());
        }
    }

    /**
     * 
     * Before we send the form data in for validation, there
     * are a few fields we may need to adjust first
     * @param $formData
     */
    public function preEditShowValidationCheck($formData) {
        $validateStartDate = true;
        $validateStartTime = true;
        $this->service_showDays = new Application_Service_ShowDaysService(
            $formData["add_show_id"]);

        //CcShowDays object of the show currently being edited
        $currentShowDay = $this->service_showDays->getCurrentShowDay();

        if (!array_key_exists('add_show_start_date', $formData)) {
            //Changing the start date was disabled, since the
            //array key does not exist. We need to repopulate this entry from the db.
            //The start date will be returned in UTC time, so lets convert it to local time.
            $dt = Application_Common_DateHelper::ConvertToLocalDateTime(
                $this->service_showDays->getStartDateAndTime());
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
             $nextFutureRepeatShow = $this->service_showInstances
                 ->getNextFutureRepeatShowTime($formData["add_show_id"]);
             $originalShowStartDateTime = $nextFutureRepeatShow["starts"];
        } else {
            $originalShowStartDateTime = Application_Common_DateHelper::ConvertToLocalDateTime(
                $this->service_showDays->getStartDateAndTime());
        }

        return array($formData, $validateStartDate, $validateStartTime, $originalShowStartDateTime);
    }

}