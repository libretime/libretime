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
     * @return array of show forms
     */
    public function createShowForms()
    {
        $formWhat = new Application_Form_AddShowWhat();
        $formAutoPlaylist = new Application_Form_AddShowAutoPlaylist();
        $formWho = new Application_Form_AddShowWho();
        $formWhen = new Application_Form_AddShowWhen();
        $formRepeats = new Application_Form_AddShowRepeats();
        $formStyle = new Application_Form_AddShowStyle();
        $formLive = new Application_Form_AddShowLiveStream();
        $formRecord = new Application_Form_AddShowRR();
        $formAbsoluteRebroadcast = new Application_Form_AddShowAbsoluteRebroadcastDates();
        $formRebroadcast = new Application_Form_AddShowRebroadcastDates();

        $formWhat->removeDecorator('DtDdWrapper');
        $formAutoPlaylist->removeDecorator('DtDdWrapper');
        $formWho->removeDecorator('DtDdWrapper');
        $formWhen->removeDecorator('DtDdWrapper');
        $formRepeats->removeDecorator('DtDdWrapper');
        $formStyle->removeDecorator('DtDdWrapper');
        $formLive->removeDecorator('DtDdWrapper');
        $formRecord->removeDecorator('DtDdWrapper');
        $formAbsoluteRebroadcast->removeDecorator('DtDdWrapper');
        $formRebroadcast->removeDecorator('DtDdWrapper');

        $forms = [];
        $forms['what'] = $formWhat;
        $forms['autoplaylist'] = $formAutoPlaylist;
        $forms['who'] = $formWho;
        $forms['when'] = $formWhen;
        $forms['repeats'] = $formRepeats;
        $forms['style'] = $formStyle;
        $forms['live'] = $formLive;
        $forms['record'] = $formRecord;
        $forms['abs_rebroadcast'] = $formAbsoluteRebroadcast;
        $forms['rebroadcast'] = $formRebroadcast;

        return $forms;
    }

    /**
     * Popluates the what, autoplaylist, when, and repeat forms
     * with default values.
     *
     * @param mixed $formWhat
     * @param mixed $formWhen
     * @param mixed $formRepeats
     */
    public function populateNewShowForms($formWhat, $formWhen, $formRepeats)
    {
        $formWhat->populate(
            [
                'add_show_id' => '-1',
                'add_show_instance_id' => '-1',
            ]
        );

        $formWhen->populate(
            [
                'add_show_start_date' => date('Y-m-d'),
                'add_show_start_time' => '00:00',
                'add_show_end_date_no_repeate' => date('Y-m-d'),
                'add_show_end_time' => '01:00',
                'add_show_duration' => '01h 00m',
            ]
        );

        $formRepeats->populate(['add_show_end_date' => date('Y-m-d')]);
    }

    public function delegateShowInstanceFormPopulation($forms)
    {
        $this->populateFormWhat($forms['what']);
        $this->populateInstanceFormWhen($forms['when']);
        $this->populateFormWho($forms['who']);
        $this->populateFormLive($forms['live']);
        $this->populateFormStyle($forms['style']);

        /* Only the field on the 'when' form will get updated so we should
         * make all other forms disabled or readonly
         *
         * 'what' needs to be readonly because zendform will not validate
         * if they are disabled
         *
         * All other forms can be disabled because we do not update those values
         * when the user edits a repeating instance
         */
        $forms['what']->makeReadonly();
        $forms['what']->enableInstanceDesc();
        $forms['autoplaylist']->disable();
        $forms['repeats']->disable();
        $forms['who']->disable();
        $forms['style']->disable();
        // Hide the show logo fields when users are editing a single instance
        $forms['style']->hideShowLogo();
        $forms['live']->disable();
        $forms['record']->disable();
        $forms['rebroadcast']->disable();
        $forms['abs_rebroadcast']->disable();
    }

    /**
     * Delegates populating each show form with the appropriate
     * data of the current show being edited.
     *
     * @param mixed $forms
     */
    public function delegateShowFormPopulation($forms)
    {
        $this->populateFormWhat($forms['what']);
        // local show start DT
        $this->populateFormAutoPlaylist($forms['autoplaylist']);
        $showStart = $this->populateFormWhen($forms['when']);
        $this->populateFormRepeats($forms['repeats'], $showStart);
        $this->populateFormWho($forms['who']);
        $this->populateFormStyle($forms['style']);
        $this->populateFormLive($forms['live']);
        $this->populateFormRecord($forms['record']);
        $this->populateFormRebroadcastRelative($forms['rebroadcast']);
        $this->populateFormRebroadcastAbsolute($forms['abs_rebroadcast']);
    }

    private function populateFormWhat($form)
    {
        $ccShowInstance = CcShowInstancesQuery::create()->findPk($this->instanceId);

        $form->populate(
            [
                'add_show_instance_id' => $this->instanceId,
                'add_show_id' => $this->ccShow->getDbId(),
                'add_show_name' => $this->ccShow->getDbName(),
                'add_show_url' => $this->ccShow->getDbUrl(),
                'add_show_genre' => $this->ccShow->getDbGenre(),
                'add_show_description' => $this->ccShow->getDbDescription(),
                'add_show_instance_description' => $ccShowInstance->getDbDescription(),
            ]
        );
    }

    private function populateFormAutoPlaylist($form)
    {
        $ccShowInstance = CcShowInstancesQuery::create()->findPk($this->instanceId);

        $form->populate(
            [
                'add_show_has_autoplaylist' => $this->ccShow->getDbHasAutoPlaylist() ? 1 : 0,
                'add_show_autoplaylist_id' => $this->ccShow->getDbAutoPlaylistId(),
                'add_show_autoplaylist_repeat' => $this->ccShow->getDbAutoPlaylistRepeat(),
            ]
        );
    }

    private function populateFormWhen($form)
    {
        if ($this->ccShow->isRepeating()) {
            $ccShowDay = $this->ccShow->getFirstRepeatingCcShowDay();
        } else {
            $ccShowDay = $this->ccShow->getFirstCcShowDay();
        }

        $showStart = $ccShowDay->getLocalStartDateAndTime();
        $showEnd = $ccShowDay->getLocalEndDateAndTime();

        // check if the first show is in the past
        if ($ccShowDay->isShowStartInPast()) {
            // for a non-repeating show, we should never allow user to change the start time.
            // for a repeating show, we should allow because the form works as repeating template form
            if (!$ccShowDay->isRepeating()) {
                $form->disableStartDateAndTime();
            } else {
                $showStartAndEnd = $this->getNextFutureRepeatShowTime();
                if (!is_null($showStartAndEnd)) {
                    $showStart = $showStartAndEnd['starts'];
                    $showEnd = $showStartAndEnd['ends'];
                }
                if ($this->hasShowStarted($showStart)) {
                    $form->disableStartDateAndTime();
                }
            }
        }

        // Disable starting a show 'now' when editing an existing show.
        $form->getElement('add_show_start_now')->setAttrib('disable', ['now']);

        $form->populate(
            [
                'add_show_start_date' => $showStart->format('Y-m-d'),
                'add_show_start_time' => $showStart->format('H:i'),
                'add_show_end_date_no_repeat' => $showEnd->format('Y-m-d'),
                'add_show_end_time' => $showEnd->format('H:i'),
                'add_show_duration' => $ccShowDay->formatDuration(true),
                'add_show_timezone' => $ccShowDay->getDbTimezone(),
                'add_show_repeats' => $ccShowDay->isRepeating() ? 1 : 0,
            ]
        );

        return $showStart;
    }

    public function getNextFutureRepeatShowTime()
    {
        $ccShowInstance = CcShowInstancesQuery::create()
            ->filterByDbShowId($this->ccShow->getDbId())
            ->filterByDbModifiedInstance(false)
            ->filterByDbStarts(gmdate('Y-m-d H:i:s'), Criteria::GREATER_THAN)
            ->orderByDbStarts()
            ->findOne();

        if (!$ccShowInstance) {
            return null;
        }

        $starts = new DateTime($ccShowInstance->getDbStarts(), new DateTimeZone('UTC'));
        $ends = new DateTime($ccShowInstance->getDbEnds(), new DateTimeZone('UTC'));
        $showTimezone = $this->ccShow->getFirstCcShowDay()->getDbTimezone();

        $starts->setTimezone(new DateTimeZone($showTimezone));
        $ends->setTimezone(new DateTimeZone($showTimezone));

        return ['starts' => $starts, 'ends' => $ends];
    }

    private function populateInstanceFormWhen($form)
    {
        $ccShowInstance = CcShowInstancesQuery::create()->findPk($this->instanceId);

        // get timezone the show is created in
        $timezone = $ccShowInstance->getCcShow()->getFirstCcShowDay()->getDbTimezone();
        // $timezone = new DateTimeZone(Application_Model_Preference::GetDefaultTimezone());

        // DateTime object in UTC
        $showStart = $ccShowInstance->getDbStarts(null);
        $showStart->setTimezone(new DateTimeZone($timezone));

        $showEnd = $ccShowInstance->getDbEnds(null);
        $showEnd->setTimezone(new DateTimeZone($timezone));

        // if the show has started, do not allow editing on the start time
        if ($showStart->getTimestamp() <= time()) {
            $form->disableStartDateAndTime();
        }

        // Disable starting a show 'now' when editing an existing show.
        $form->getElement('add_show_start_now')->setAttrib('disable', ['now']);

        $form->populate(
            [
                'add_show_start_now' => 'future',
                'add_show_start_date' => $showStart->format('Y-m-d'),
                'add_show_start_time' => $showStart->format('H:i'),
                'add_show_end_date_no_repeat' => $showEnd->format('Y-m-d'),
                'add_show_end_time' => $showEnd->format('H:i'),
                'add_show_duration' => $this->calculateDuration(
                    $showStart->format(DEFAULT_TIMESTAMP_FORMAT),
                    $showEnd->format(DEFAULT_TIMESTAMP_FORMAT),
                    $timezone
                ),
                'add_show_timezone' => $timezone,
                'add_show_repeats' => 0,
            ]
        );

        $form->getElement('add_show_repeats')->setOptions(['disabled' => true]);
    }

    /**
     * Enter description here ...
     *
     * @param DateTime $nextFutureShowStart user's local timezone
     * @param mixed    $form
     */
    private function populateFormRepeats($form, $nextFutureShowStart)
    {
        if ($this->ccShow->isRepeating()) {
            $ccShowDays = $this->ccShow->getRepeatingCcShowDays();
        } else {
            $ccShowDays = $this->ccShow->getCcShowDayss();
        }

        $days = [];
        foreach ($ccShowDays as $ccShowDay) {
            $showStart = $ccShowDay->getLocalStartDateAndTime();
            array_push($days, $showStart->format('w'));
        }

        $service_show = new Application_Service_ShowService($this->ccShow->getDbId());
        $repeatEndDate = $service_show->getRepeatingEndDate();
        // end dates are stored non-inclusively so we need to
        // subtract one day
        if (!is_null($repeatEndDate)) {
            $repeatEndDate->sub(new DateInterval('P1D'));
        }

        // default monthly repeat type
        $monthlyRepeatType = 2;
        $repeatType = $ccShowDays[0]->getDbRepeatType();
        if ($repeatType == REPEAT_MONTHLY_WEEKLY) {
            $monthlyRepeatType = $repeatType;
            // a repeat type of 2 means the show is repeating monthly
            $repeatType = 2;
        } elseif ($repeatType == REPEAT_MONTHLY_MONTHLY) {
            $monthlyRepeatType = $repeatType;
        }

        $form->populate(
            [
                'add_show_linked' => $this->ccShow->getDbLinked(),
                'add_show_repeat_type' => $repeatType,
                'add_show_day_check' => $days,
                'add_show_end_date' => (!is_null($repeatEndDate)) ? $repeatEndDate->format('Y-m-d') : null,
                'add_show_no_end' => is_null($repeatEndDate),
                'add_show_monthly_repeat_type' => $monthlyRepeatType,
            ]
        );

        if (!$this->ccShow->isLinkable() || $this->ccShow->isRecorded()) {
            $form->getElement('add_show_linked')->setOptions(['disabled' => true]);
        }

        /* Because live editing of a linked show is disabled, we will make
         * the linking option readonly if the current show is being edited. We
         * dont' want the user to suddenly not be able to edit the current show
         *
         * Readonly does not work with checkboxes but we can't disable it
         * because the value won't get posted. In add-show.js we stop the
         * onclick event from firing by returning false
         */
        if ($this->hasShowStarted($nextFutureShowStart)) {
            $form->getElement('add_show_linked')->setAttrib('readonly', 'readonly');
        }
    }

    private function populateFormWho($form)
    {
        $ccShowHosts = $this->ccShow->getCcShowHostss();

        $hosts = [];
        foreach ($ccShowHosts as $ccShowHost) {
            array_push($hosts, $ccShowHost->getDbHost());
        }

        $form->populate(['add_show_hosts' => $hosts]);
    }

    private function populateFormStyle($form)
    {
        $src = $this->ccShow->getDbImagePath() ?
            $this->imagePathToDataUri($this->ccShow->getDbImagePath()) : '';

        $form->populate(
            [
                'add_show_background_color' => $this->ccShow->getDbBackgroundColor(),
                'add_show_color' => $this->ccShow->getDbColor(),
                'add_show_logo_current' => $src,
            ]
        );
    }

    /**
     * Convert a static image from disk to a base64 data URI.
     *
     * @param unknown $path
     *                      - the path to the image on the disk
     *
     * @return string
     *                - the data URI representation of the image
     */
    private function imagePathToDataUri($path)
    {
        $imageData = null;
        $bytesRead = 0;

        try {
            ob_start();
            header('Content-type: image/*');
            $bytesRead = @readfile($path);
            $imageData = base64_encode(ob_get_contents());
            ob_end_clean();
            if ($bytesRead === false) {
                $imageData = null;
            }
        } catch (Exception $e) {
            Logging::error('Failed to read image: ' . $path);
            $imageData = null;
        }
        // return the data URI - data:{mime};base64,{data}
        return ($imageData === null || $imageData === '') ?
            '' : 'data: ' . mime_content_type($path) . ';base64,' . $imageData;
    }

    private function populateFormLive($form)
    {
        $form->populate(
            [
                'cb_airtime_auth' => $this->ccShow->getDbLiveStreamUsingAirtimeAuth(),
                'cb_custom_auth' => $this->ccShow->getDbLiveStreamUsingCustomAuth(),
                'custom_username' => $this->ccShow->getDbLiveStreamUser(),
                'custom_password' => $this->ccShow->getDbLiveStreamPass(),
            ]
        );
    }

    private function populateFormRecord($form)
    {
        $form->populate(
            [
                'add_show_record' => $this->ccShow->isRecorded(),
                'add_show_rebroadcast' => $this->ccShow->isRebroadcast(),
            ]
        );

        $form->getElement('add_show_record')->setOptions(['disabled' => true]);
    }

    private function populateFormRebroadcastRelative($form)
    {
        $relativeRebroadcasts = $this->ccShow->getRebroadcastsRelative();

        $formValues = [];
        $i = 1;
        foreach ($relativeRebroadcasts as $rr) {
            $formValues["add_show_rebroadcast_date_{$i}"] = $rr->getDbDayOffset();
            $formValues["add_show_rebroadcast_time_{$i}"] = Application_Common_DateHelper::removeSecondsFromTime(
                $rr->getDbStartTime()
            );
            ++$i;
        }

        $form->populate($formValues);
    }

    private function populateFormRebroadcastAbsolute($form)
    {
        $absolutRebroadcasts = $this->ccShow->getRebroadcastsAbsolute();
        $timezone = $this->ccShow->getFirstCcShowDay()->getDbTimezone();

        $formValues = [];
        $i = 1;
        foreach ($absolutRebroadcasts as $ar) {
            // convert dates to user's local time
            $start = new DateTime($ar->getDbStarts(), new DateTimeZone('UTC'));
            $start->setTimezone(new DateTimeZone($timezone));
            $formValues["add_show_rebroadcast_date_absolute_{$i}"] = $start->format('Y-m-d');
            $formValues["add_show_rebroadcast_time_absolute_{$i}"] = $start->format('H:i');
            ++$i;
        }

        $form->populate($formValues);
    }

    /**
     * Enter description here ...
     *
     * @param DateTime $showStart   user's local time
     * @param mixed    $p_showStart
     */
    private function hasShowStarted($p_showStart)
    {
        $showStart = clone $p_showStart;
        $showStart->setTimeZone(new DateTimeZone('UTC'));

        if ($showStart->format('Y-m-d H:i:s') < gmdate('Y-m-d H:i:s')) {
            return true;
        }

        return false;
    }

    /**
     * Before we send the form data in for validation, there
     * are a few fields we may need to adjust first.
     *
     * @param mixed $formData
     */
    public function preEditShowValidationCheck($formData)
    {
        // If the start date or time were disabled, don't validate them
        $validateStartDate = $formData['start_date_disabled'] === 'false';
        $validateStartTime = $formData['start_time_disabled'] === 'false';

        // CcShowDays object of the show currently being edited
        $currentShowDay = $this->ccShow->getFirstCcShowDay();

        // DateTime object
        $dt = $currentShowDay->getLocalStartDateAndTime();
        $formData['add_show_record'] = $currentShowDay->getDbRecord();

        // if the show is repeating, set the start date to the next
        // repeating instance in the future
        $originalShowStartDateTime = $this->getCurrentOrNextInstanceStartTime();
        if (!$originalShowStartDateTime) {
            $originalShowStartDateTime = $dt;
        }

        return [$formData, $validateStartDate, $validateStartTime, $originalShowStartDateTime];
    }

    /**
     * Returns a DateTime object, in the user's local time,
     * of the current or next show instance start time.
     *
     * Returns null if there is no next future repeating show instance
     */
    public function getCurrentOrNextInstanceStartTime()
    {
        $ccShowInstance = CcShowInstancesQuery::create()
            ->filterByDbShowId($this->ccShow->getDbId())
            ->filterByDbModifiedInstance(false)
            ->filterByDbStarts(gmdate('Y-m-d H:i:s'), Criteria::GREATER_EQUAL)
            ->orderByDbStarts()
            ->findOne();

        if (!$ccShowInstance) {
            return null;
        }

        $starts = new DateTime($ccShowInstance->getDbStarts(), new DateTimeZone('UTC'));
        $showTimezone = $this->ccShow->getFirstCcShowDay()->getDbTimezone();

        $starts->setTimezone(new DateTimeZone($showTimezone));

        return $starts;
    }

    /**
     * Validates show forms.
     *
     * @param mixed      $forms
     * @param mixed      $formData
     * @param mixed      $validateStartDate
     * @param null|mixed $originalStartDate
     * @param mixed      $editShow
     * @param null|mixed $instanceId
     *
     * @return bool
     */
    public function validateShowForms(
        $forms,
        $formData,
        $validateStartDate = true,
        $originalStartDate = null,
        $editShow = false,
        $instanceId = null
    ) {
        $what = $forms['what']->isValid($formData);
        $autoplaylist = $forms['autoplaylist']->isValid($formData);
        $live = $forms['live']->isValid($formData);
        $record = $forms['record']->isValid($formData);
        $who = $forms['who']->isValid($formData);

        /*
         * hack to prevent validating the file upload field since it
         * isn't passed into $data
         */
        $upload = $forms['style']->getElement('add_show_logo');
        $forms['style']->removeElement('add_show_logo');

        $style = $forms['style']->isValid($formData);

        // re-add the upload element
        $forms['style']->addElement($upload);

        $when = $forms['when']->isWhenFormValid(
            $formData,
            $validateStartDate,
            $originalStartDate,
            $editShow,
            $instanceId
        );

        $repeats = true;
        if ($formData['add_show_repeats']) {
            $repeats = $forms['repeats']->isValid($formData);

            /*
             * Make the absolute rebroadcast form valid since
             * it does not get used if the show is repeating
             */
            $forms['abs_rebroadcast']->reset();
            $absRebroadcast = true;

            $rebroadcast = true;
            if (isset($formData['add_show_rebroadcast']) && $formData['add_show_rebroadcast']) {
                $formData['add_show_duration'] = Application_Service_ShowService::formatShowDuration(
                    $formData['add_show_duration']
                );
                $rebroadcast = $forms['rebroadcast']->isValid($formData);
            }
        } else {
            /*
             * Make the rebroadcast form valid since it does
             * not get used if the show is not repeating.
             * Instead, we use the absolute rebroadcast form
             */
            $forms['rebroadcast']->reset();
            $rebroadcast = true;

            $absRebroadcast = true;
            if (isset($formData['add_show_rebroadcast']) && $formData['add_show_rebroadcast']) {
                $formData['add_show_duration'] = Application_Service_ShowService::formatShowDuration(
                    $formData['add_show_duration']
                );
                $absRebroadcast = $forms['abs_rebroadcast']->isValid($formData);
            }
        }

        return $what && $autoplaylist && $live && $record && $who && $style && $when
            && $repeats && $absRebroadcast && $rebroadcast;
    }

    public function calculateDuration($start, $end, $timezone)
    {
        try {
            $tz = new DateTimeZone($timezone);
            $startDateTime = new DateTime($start, $tz);
            $endDateTime = new DateTime($end, $tz);

            $duration = $startDateTime->diff($endDateTime);

            $day = intval($duration->format('%d'));
            if ($day > 0) {
                $hour = intval($duration->format('%h'));
                $min = intval($duration->format('%i'));
                $hour += $day * 24;
                $hour = min($hour, 99);
                $sign = $duration->format('%r');

                return sprintf('%s%02dh %02dm', $sign, $hour, $min);
            }

            return $duration->format('%r%Hh %Im');
        } catch (Exception $e) {
            Logging::info($e->getMessage());

            return 'Invalid Date';
        }
    }

    /**
     * When the timezone is changed in add-show form this function
     * applies the new timezone to the start and end time.
     *
     * @param $date String
     * @param $time String
     * @param $timezone String
     * @param mixed $newTimezone
     * @param mixed $oldTimezone
     */
    public static function localizeDateTime($date, $time, $newTimezone, $oldTimezone)
    {
        $dt = new DateTime($date . ' ' . $time, new DateTimeZone($oldTimezone));

        $dt->setTimeZone(new DateTimeZone($newTimezone));

        return [
            'date' => $dt->format('Y-m-d'),
            'time' => $dt->format('H:i'),
        ];
    }
}
