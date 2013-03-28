<?php
define("MAX_REBROADCAST_DATES", 10);
define("NO_REPEAT", -1);
define("REPEAT_WEEKLY", 0);
define("REPEAT_BI_WEEKLY", 1);
define("REPEAT_MONTHLY_MONTHLY", 2);
define("REPEAT_MONTHLY_WEEKLY", 3);

class Application_Service_ShowService
{
    private $ccShow;

    public function __construct($showId=null)
    {
        if (!is_null($showId)) {
            $this->ccShow = CcShowQuery::create()->findPk($showId);
        }
    }

    /**
     * 
     * When the user is editing a single instance of a repeating show
     * we want to treat it as a completely new show so we need to delete
     * the instance first
     * 
     * @param $instanceId
     */
    public function deleteRepeatingInstance($instanceId)
    {
        CcShowInstancesQuery::create()->findPk($instanceId)->delete();
    }

    public function addUpdateShow($showData, $isUpdate=false)
    {
        $service_user = new Application_Service_UserService();
        $currentUser = $service_user->getCurrentUser();

        if ($showData["add_show_repeats"]) {
            $repeatType = $showData["add_show_repeat_type"];
            if ($showData["add_show_repeat_type"] == 2) {
                $repeatType = $showData["add_show_monthly_repeat_type"];
            }
        } else {
            $repeatType = -1;
        }
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
            $this->setCcShow($showData, $isUpdate);

            if ($isUpdate) {
                $this->delegateInstanceCleanup($showData, $isRecorded, $repeatType);
                // updates cc_show_instances start/end times, and updates
                // schedule start/end times
                $this->applyShowStartEndDifference($showData);
                $this->deleteRebroadcastInstances();
                $this->deleteCcShowDays();
                $this->deleteCcShowHosts();
                if ($isRebroadcast) {
                    //delete entry in cc_show_rebroadcast
                    $this->deleteCcShowRebroadcasts();
                }
            }

            //update ccShowDays
            $this->setCcShowDays($showData, $repeatType, $isRecorded);

            //update ccShowRebroadcasts
            $this->setCcShowRebroadcasts($showData, $repeatType, $isRecorded);

            //update ccShowHosts
            $this->setCcShowHosts($showData);

            //create new ccShowInstances
            $this->delegateInstanceCreation($isRebroadcast, $isUpdate);

            if ($isUpdate) {
                $service_scheduler = new Application_Service_SchedulerService();
                $service_scheduler->removeGaps($this->ccShow->getDbId());
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
     * Receives a cc_show id and determines whether to create a 
     * single show instance or repeating show instances
     */
    public function delegateInstanceCreation($isRebroadcast=null, $isUpdate=false, $end=null)
    {
        $populateUntil = $this->getPopulateShowUntilDateTIme();

        if (is_null($this->ccShow)) {
            $ccShowDays = $this->getShowDaysInRange($populateUntil, $end);
        } else {
            $ccShowDays = $this->ccShow->getCcShowDays();
        }

        if (!is_null($end)) {
            $populateUntil = $end;
        }

        foreach ($ccShowDays as $day) {
            switch ($day->getDbRepeatType()) {
                case NO_REPEAT:
                    $this->createNonRepeatingInstance($day, $populateUntil, $isRebroadcast, $isUpdate);
                    break;
                case REPEAT_WEEKLY:
                    $this->createRepeatingInstances($day, $populateUntil, REPEAT_WEEKLY,
                        new DateInterval("P7D"), $isRebroadcast, $isUpdate);
                    break;
                case REPEAT_BI_WEEKLY:
                    $this->createRepeatingInstances($day, $populateUntil, REPEAT_BI_WEEKLY,
                        new DateInterval("P14D"), $isRebroadcast, $isUpdate);
                    break;
                case REPEAT_MONTHLY_MONTHLY:
                    $this->createMonthlyMonthlyRepeatInstances($day, $populateUntil, $isRebroadcast, $isUpdate);
                    break;
                case REPEAT_MONTHLY_WEEKLY:
                    $this->createRepeatingInstances($day, $populateUntil, REPEAT_MONTHLY_WEEKLY,
                        null, $isRebroadcast, $isUpdate);
                    break;
            }
        }
    }

    private function getShowDaysInRange($start, $end)
    {
        $endTimeString = $end->format("Y-m-d H:i:s");
        if (!is_null($start)) {
            $startTimeString = $start->format("Y-m-d H:i:s");
        } else {
            $today_timestamp = new DateTime("now", new DateTimeZone("UTC"));
            $startTimeString = $today_timestamp->format("Y-m-d H:i:s");
        }

        $c = new Criteria();
        $c->add(CcShowDaysPeer::FIRST_SHOW, $endTimeString, Criteria::LESS_THAN);
        $c->addAnd(CcShowDaysPeer::LAST_SHOW, $startTimeString, Criteria::GREATER_THAN);
        $c->addOr(CcShowDaysPeer::LAST_SHOW, null, Criteria::ISNULL);

        return CcShowDaysPeer::doSelect($c);
    }

    public static function formatShowDuration($duration)
    {
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

    /**
     * 
     * Deletes all the cc_show_days entries for a specific show
     * that is currently being edited. They will get recreated with
     * the new show day specs
     */
    private function deleteCcShowDays()
    {
        CcShowDaysQuery::create()->filterByDbShowId($this->ccShow->getDbId())->delete();
    }

    private function deleteRebroadcastInstances()
    {
        $sql = <<<SQL
DELETE FROM cc_show_instances
WHERE starts > :timestamp::TIMESTAMP
AND show_id = :showId
AND rebroadcast = 1;
SQL;
        Application_Common_Database::prepareAndExecute( $sql, array(
            ':showId' => $this->ccShow->getDbId(),
            ':timestamp'  => gmdate("Y-m-d H:i:s")), 'execute');
    }

    /**
     * TODO: This function is messy. Needs refactoring
     * 
     * When editing a show we may need to perform some actions to reflect the new specs:
     * - Delete some show instances
     * - Update duration
     * - Update start and end time
     * 
     * @param $showData edit show form values in raw form
     * @param $isRecorded value computed from the edit show form
     * @param $repeatType value computed from the edit show form
     */
    private function delegateInstanceCleanup($showData, $isRecorded, $repeatType)
    {
        $showId = $this->ccShow->getDbId();

        //CcShowDay object
        $currentShowDay = $this->ccShow->getFirstCcShowDay();

        //new end date in users' local time
        $endDateTime = $this->calculateEndDate($showData);
        if (!is_null($endDateTime)) {
            $endDate = $endDateTime->format("Y-m-d");
        } else {
            $endDate = $endDateTime;
        }

        //repeat option was toggled
        if ($showData['add_show_repeats'] != $currentShowDay->isRepeating()) {
            $this->deleteAllRepeatInstances($currentShowDay, $showId);
        }

        if ($showData['add_show_repeats']) {

            $localShowStart = $currentShowDay->getLocalStartDateAndTime();

            //if the start date changes, these are the repeat types
            //that require show instance deletion
            $deleteRepeatTypes = array(REPEAT_BI_WEEKLY, REPEAT_MONTHLY_MONTHLY, 
                REPEAT_MONTHLY_WEEKLY);

            if (in_array($repeatType, $deleteRepeatTypes) &&
                $showData["add_show_start_date"] != $localShowStart->format("Y-m-d")) {

                //Start date has changed when repeat type is bi-weekly or monthly.
                //This screws up the repeating positions of show instances, so
                //we need to delete them (CC-2351)
                $this->deleteAllInstances($showId);
            }

            $currentRepeatType = $currentShowDay->getDbRepeatType();
            //only delete instances if the show being edited was already repeating
            //and the repeat type changed
            if ($currentRepeatType != -1 && $repeatType != $currentRepeatType) {
                $this->deleteAllInstances($showId);
            } else {
                //repeat type is the same, check if the days of the week are the same
                $repeatingDaysChanged = false;

                $ccShowDays = $this->ccShow->getCcShowDays();
                $showDays = array();
                foreach ($ccShowDays as $day) {
                    $showDays[] = $day->getDbDay();
                }

                if (count($showData['add_show_day_check']) == count($showDays)) {
                    //same number of days checked, lets see if they are the same numbers
                    $intersect = array_intersect($showData['add_show_day_check'], $showDays);
                    if (count($intersect) != count($showData['add_show_day_check'])) {
                        $repeatingDaysChanged = true;
                    }
                } else {
                    $repeatingDaysChanged = true;
                }

                if ($repeatingDaysChanged) {
                    $daysRemoved = array_diff($showDays, $showData['add_show_day_check']);

                    if (count($daysRemoved) > 0) {
                        //delete repeating show instances for the repeating
                        //days that were removed
                        $this->deleteRemovedShowDayInstances($daysRemoved,
                            $ccShowDays, $showId);
                    }
                }

                if ($showData['add_show_start_date'] != $localShowStart->format("Y-m-d")
                    || $showData['add_show_start_time'] != $localShowStart->format("H:i")) {

                    //start date has been pushed forward so we need to delete
                    //any instances of this show scheduled before the new start date
                    if ($showData['add_show_start_date'] > $localShowStart->format("Y-m-d")) {
                        $this->deleteInstancesBeforeDate($showData['add_show_start_date'], $showId);
                    }

                    
                }
            }

            $currentShowEndDate = $this->getRepeatingEndDate();
            //check if "no end" option has changed
            if ($currentShowEndDate != $showData['add_show_no_end']) {
                //show "No End" option was toggled
                if (!$showData['add_show_no_end']) {
                    //"No End" option was unchecked so we need to delete the
                    //repeat instances that are scheduled after the new end date
                    $this->deleteInstancesFromDate($endDate, $showId);
                }
            }

            if ($currentShowEndDate != $showData['add_show_end_date']) {
                //end date was changed
                $newEndDate = strtotime($showData['add_show_end_date']);
                $oldEndDate = strtotime($currentShowEndDate);
                if ($newEndDate < $oldEndDate) {
                    //end date was pushed back so we have to delete any
                    //instances of this show scheduled after the new end date
                    $this->deleteInstancesFromDate($endDate, $showId);
                }
            }
        }//if repeats
    }

    public function getRepeatingEndDate()
    {
        $sql = <<<SQL
SELECT last_show
FROM cc_show_days
WHERE show_id = :showId
ORDER BY last_show DESC
SQL;

        $query = Application_Common_Database::prepareAndExecute( $sql,
            array( 'showId' => $this->ccShow->getDbId() ), 'column' );

        return ($query !== false) ? $query : false;
    }

    private function deleteInstancesFromDate($endDate, $showId)
    {
        $sql = <<<SQL
DELETE FROM cc_show_instances
WHERE date(starts) >= :endDate::DATE
  AND starts > :timestamp::TIMESTAMP
  AND show_id = :showId
SQL;
        Application_Common_Database::prepareAndExecute($sql, array(
            ':endDate' => $endDate, ':timestamp' => gmdate("Y-m-d H:i:s"),
            ':showId' => $showId), 'execute');
    }

    private function deleteInstancesBeforeDate($newStartDate, $showId)
    {
        $sql = <<<SQL
DELETE
FROM cc_show_instances
WHERE date(starts) < :newStartDate::DATE
  AND starts > :timestamp::TIMESTAMP
  AND show_id = :showId
SQL;

        Application_Common_Database::prepareAndExecute($sql, array(
            ":newStartDate" => $newStartDate, ":timestamp" =>  gmdate("Y-m-d H:i:s"),
            ":showId" => $showId), "execute");
    }

    /**
     * 
     * Enter description here ...
     * @param $daysRemoved array of days (days of the week) removed
     *     (days of the week are represented numerically
     *      0=>sunday, 1=>monday, 2=>tuesday, etc.)
     * @param $showDays array of ccShowDays objects
     * @param $showId
     */
    private function deleteRemovedShowDayInstances($daysRemoved, $showDays, $showId)
    {
        $daysRemovedUTC = array();

        //convert the start day of the week to UTC
        foreach ($showDays as $showDay) {
            if (in_array($showDay->getDbDay(), $daysRemoved)) {
               $showDay->reload();
               $startDay = $showDay->getUTCStartDateAndTime();
               $daysRemovedUTC[] = $startDay->format('w');
            }
        }

        foreach ($daysRemoved as $day) {
            //delete the cc_show_day entries as well
            CcShowDaysQuery::create()
                ->filterByDbShowId($showId)
                ->filterByDbDay($day)
                ->delete();
        }

        $uncheckedDays = pg_escape_string(implode(",", $daysRemovedUTC));

        $sql = <<<SQL
DELETE
FROM cc_show_instances
WHERE EXTRACT(DOW FROM starts) IN ($uncheckedDays)
  AND starts > :timestamp::TIMESTAMP
  AND show_id = :showId
SQL;

        Application_Common_Database::prepareAndExecute( $sql, array(
            ":timestamp" => gmdate("Y-m-d H:i:s"), ":showId"    => $showId),
            "execute");

    }

    private function deleteAllInstances($showId)
    {
        $sql = <<<SQL
DELETE
FROM cc_show_instances
WHERE starts > :timestamp::TIMESTAMP
  AND show_id = :showId
SQL;
        Application_Common_Database::prepareAndExecute( $sql,
            array( ':timestamp' => gmdate("Y-m-d H:i:s"),
                   ':showId'    => $showId), 'execute');
    }

    private function deleteAllRepeatInstances($currentShowDay, $showId)
    {
        $firstShow = $currentShowDay->getUTCStartDateAndTime();

        $sql = <<<SQL
DELETE
FROM cc_show_instances
WHERE starts > :timestamp::TIMESTAMP
  AND show_id = :showId
  AND starts != :firstShow
SQL;
        Application_Common_Database::prepareAndExecute( $sql,
            array( ':timestamp' => gmdate("Y-m-d H:i:s"),
                   ':showId'    => $showId,
                   ':firstShow' => $firstShow->format("Y-m-d H:i:s")), 'execute');
    }

    /**
     * 
     * Determines what the show end date should be based on
     * the form data
     * 
     * @param $showData add/edit show form data
     * @return DateTime object in user's local timezone
     */
    private function calculateEndDate($showData)
    {
        if ($showData['add_show_no_end']) {
            $endDate = NULL;
        } elseif ($showData['add_show_repeats']) {
            $endDate = new DateTime($showData['add_show_end_date']);
            $endDate->add(new DateInterval("P1D"));
        } else {
            $endDate = new DateTime($showData['add_show_start_date']);
            $endDate->add(new DateInterval("P1D"));
        }

        return $endDate;
    }

    private function applyShowStartEndDifference($showData)
    {
        $showId = $this->ccShow->getDbId();
        //CcShowDay object
        $currentShowDay = $this->ccShow->getFirstCcShowDay();

        //DateTime in user's local time
        $newStartDateTime = new DateTime($showData["add_show_start_date"]." ".
        $showData["add_show_start_time"],
            new DateTimeZone(Application_Model_Preference::GetTimezone()));

        $diff = $this->calculateShowStartDiff($newStartDateTime,
            $currentShowDay->getLocalStartDateAndTime());

        $this->updateInstanceStartEndTime($diff);
        $ccShowInstances = $this->ccShow->getFutureCcShowInstancess();
        $instanceIds = array();
        foreach ($ccShowInstances as $ccShowInstance) {
            array_push($instanceIds, $ccShowInstance->getDbId());
        }
        Application_Service_SchedulerService::updateScheduleStartTime($instanceIds, $diff);
    }

    /**
     * 
     * Returns the difference in seconds between a show's new and
     * old start time
     * 
     * @param $newStartDateTime DateTime object
     * @param $oldStartDateTime DateTime object
     */
    private function calculateShowStartDiff($newStartDateTime, $oldStartDateTime)
    {
        return $newStartDateTime->getTimestamp() - $oldStartDateTime->getTimestamp();
    }

    /**
     * 
     * Updates the start and end time for cc_show_instances
     * 
     * @param $showData edit show form data
     */
    private function updateInstanceStartEndTime($diff)
    {
        $sql = <<<SQL
UPDATE cc_show_instances
SET starts = starts + :diff1::INTERVAL,
    ends = ends + :diff2::INTERVAL
WHERE show_id = :showId
  AND starts > :timestamp::TIMESTAMP
SQL;

        Application_Common_Database::prepareAndExecute($sql,
            array(':diff1' => $diff, ':diff2' => $diff, 
                ':showId' => $this->ccShow->getDbId(), ':timestamp' => gmdate("Y-m-d H:i:s")),
            'execute');
    }

    /**
     * 
     * Enter description here ...
     * @param ccShowDays $showDay
     * @param DateTime $showStartDate user's local time
     * @param $instanceId
     */
    private function createRebroadcastInstances($showDay, $showStartDate, $instanceId)
    {
        $currentUtcTimestamp = gmdate("Y-m-d H:i:s");
        $showId = $this->ccShow->getDbId();

        $sql = "SELECT * FROM cc_show_rebroadcast WHERE show_id=:show_id";
        $rebroadcasts = Application_Common_Database::prepareAndExecute($sql,
            array( ':show_id' => $showId ), 'all');

        foreach ($rebroadcasts as $rebroadcast) {
            $days = explode(" ", $rebroadcast["day_offset"]);
            $time = explode(":", $rebroadcast["start_time"]);
            $offset = array("days"=>$days[0], "hours"=>$time[0], "mins"=>$time[1]);

            list($utcStartDateTime, $utcEndDateTime) = $this->createUTCStartEndDateTime(
                $showStartDate, $showDay->getDbDuration(), $offset);

            if ($utcStartDateTime->format("Y-m-d H:i:s") > $currentUtcTimestamp) {
                $ccShowInstance = new CcShowInstances();
                $ccShowInstance->setDbShowId($showId);
                $ccShowInstance->setDbStarts($utcStartDateTime);
                $ccShowInstance->setDbEnds($utcEndDateTime);
                $ccShowInstance->setDbRecord(0);
                $ccShowInstance->setDbRebroadcast(1);
                $ccShowInstance->setDbOriginalShow($instanceId);
                $ccShowInstance->save();
            }
        }
    }

    /**
     * 
     * Sets a single cc_show_instance table row
     * @param $showDay
     * @param $populateUntil
     */
    private function createNonRepeatingInstance($showDay, $populateUntil, $isRebroadcast, $isUpdate)
    {
        //DateTime object
        $start = $showDay->getLocalStartDateAndTime();

        list($utcStartDateTime, $utcEndDateTime) = $this->createUTCStartEndDateTime(
            $start, $showDay->getDbDuration());

        if ($utcStartDateTime->getTimestamp() < $populateUntil->getTimestamp()) {
            $ccShowInstance = new CcShowInstances();
            if ($isUpdate) {
                $ccShowInstance = $this->getInstance($utcStartDateTime);
            }
            $ccShowInstance->setDbShowId($this->ccShow->getDbId());
            $ccShowInstance->setDbStarts($utcStartDateTime);
            $ccShowInstance->setDbEnds($utcEndDateTime);
            $ccShowInstance->setDbRecord($showDay->getDbRecord());
            $ccShowInstance->save();

            if ($isUpdate) {
                $con = Propel::getConnection(CcSchedulePeer::DATABASE_NAME);
                $ccShowInstance->updateScheduleStatus($con);
            }

            if ($isRebroadcast) {
                $this->createRebroadcastInstances($showDay, $start, $ccShowInstance->getDbId());
            }
        }
    }

    /**
     * 
     * Sets multiple cc_show_instances table rows
     * @param unknown_type $showDay
     * @param unknown_type $populateUntil
     * @param unknown_type $repeatInterval
     * @param unknown_type $isRebroadcast
     */
    private function createRepeatingInstances($showDay, $populateUntil,
        $repeatType, $repeatInterval, $isRebroadcast, $isUpdate)
    {
        $show_id       = $showDay->getDbShowId();
        $first_show    = $showDay->getDbFirstShow(); //non-UTC
        $last_show     = $showDay->getDbLastShow(); //non-UTC
        $duration      = $showDay->getDbDuration();
        $day           = $showDay->getDbDay();
        $record        = $showDay->getDbRecord();
        $timezone      = $showDay->getDbTimezone();

        //DateTime local
        $start = $this->getNextRepeatingPopulateStartDateTime($showDay);

        if (is_null($repeatInterval)&& $repeatType == REPEAT_MONTHLY_WEEKLY) {
            $repeatInterval = $this->getMonthlyWeeklyRepeatInterval($start, $timezone);
        }

        //DatePeriod in user's local time
        $datePeriod = $this->getDatePeriod($start, $timezone, $last_show,
            $repeatInterval, $populateUntil);

        $utcLastShowDateTime = $last_show ?
            Application_Common_DateHelper::ConvertToUtcDateTime($last_show, $timezone) : null;

        $utcEndDateTime = null;
        foreach ($datePeriod as $date) {
            list($utcStartDateTime, $utcEndDateTime) = $this->createUTCStartEndDateTime(
                $date, $duration);
            /*
             * Make sure start date is less than populate until date AND
             * last show date is null OR start date is less than last show date
             */
            if ($utcStartDateTime->getTimestamp() <= $populateUntil->getTimestamp() &&
               ( is_null($utcLastShowDateTime) ||
                 $utcStartDateTime->getTimestamp() < $utcLastShowDateTime->getTimestamp()) ) {

                /* There may not always be an instance when editing a show
                 * This will be the case when we are adding a new show day to
                 * a repeating show
                 */
                if ($isUpdate && $this->hasInstance($utcStartDateTime)) {
                    $ccShowInstance = $this->getInstance($utcStartDateTime);
                    $newInstance = false;
                    $updateScheduleStatus = true;
                } else {
                    $newInstance = true;
                    $ccShowInstance = new CcShowInstances();
                    $updateScheduleStatus = false;
                }

                /* When editing the start/end time of a repeating show, we don't want to
                 * change shows that started in the past. So check the start time.
                 */
                if ($newInstance || $ccShowInstance->getDbStarts() > gmdate("Y-m-d H:i:s")) {
                    $ccShowInstance->setDbShowId($show_id);
                    $ccShowInstance->setDbStarts($utcStartDateTime);
                    $ccShowInstance->setDbEnds($utcEndDateTime);
                    $ccShowInstance->setDbRecord($record);
                    $ccShowInstance->save();

                    if ($updateScheduleStatus) {
                        $con = Propel::getConnection(CcSchedulePeer::DATABASE_NAME);
                        $ccShowInstance->updateScheduleStatus($con);
                    }
                }

                if ($isRebroadcast) {
                    $this->createRebroadcastInstances($showDay, $date, $ccShowInstance->getDbId());
                }
            }
        }
        $nextDate = $utcEndDateTime->add($repeatInterval);
        $this->setNextRepeatingShowDate($nextDate->format("Y-m-d"), $day, $show_id);
    }

    private function createMonthlyMonthlyRepeatInstances($showDay, $populateUntil,
        $isRebroadcast, $isUpdate)
    {
        $show_id       = $showDay->getDbShowId();
        $first_show    = $showDay->getDbFirstShow(); //non-UTC
        $last_show     = $showDay->getDbLastShow(); //non-UTC
        $duration      = $showDay->getDbDuration();
        $day           = $showDay->getDbDay();
        $record        = $showDay->getDbRecord();
        $timezone      = $showDay->getDbTimezone();

        //DateTime local
        $start = $this->getNextRepeatingPopulateStartDateTime($showDay);
        if (isset($last_show)) {
            $end = new DateTime($last_show, new DateTimeZone($timezone));
        } else {
            $end = $populateUntil;
        }

        $utcLastShowDateTime = $last_show ?
            Application_Common_DateHelper::ConvertToUtcDateTime($last_show, $timezone) : null;

        while ($start->getTimestamp() < $end->getTimestamp()) {
            list($utcStartDateTime, $utcEndDateTime) = $this->createUTCStartEndDateTime(
                $start, $duration);
            /*
             * Make sure start date is less than populate until date AND
             * last show date is null OR start date is less than last show date
             */
            if ($utcStartDateTime->getTimestamp() <= $populateUntil->getTimestamp() &&
               ( is_null($utcLastShowDateTime) ||
                 $utcStartDateTime->getTimestamp() < $utcLastShowDateTime->getTimestamp()) ) {

                /* There may not always be an instance when editing a show
                 * This will be the case when we are adding a new show day to
                 * a repeating show
                 */
                if ($isUpdate && $this->hasInstance($utcStartDateTime)) {
                    $ccShowInstance = $this->getInstance($utcStartDateTime);
                    $newInstance = false;
                    $updateScheduleStatus = true;
                } else {
                    $newInstance = true;
                    $ccShowInstance = new CcShowInstances();
                    $updateScheduleStatus = false;
                }

                /* When editing the start/end time of a repeating show, we don't want to
                 * change shows that started in the past. So check the start time.
                 */
                if ($newInstance || $ccShowInstance->getDbStarts() > gmdate("Y-m-d H:i:s")) {
                    $ccShowInstance->setDbShowId($show_id);
                    $ccShowInstance->setDbStarts($utcStartDateTime);
                    $ccShowInstance->setDbEnds($utcEndDateTime);
                    $ccShowInstance->setDbRecord($record);
                    $ccShowInstance->save();

                    if ($updateScheduleStatus) {
                        $con = Propel::getConnection(CcSchedulePeer::DATABASE_NAME);
                        $ccShowInstance->updateScheduleStatus($con);
                    }
                }

                if ($isRebroadcast) {
                    $this->createRebroadcastInstances($showDay, $date, $ccShowInstance->getDbId());
                }
            }
            $start = $this->getNextMonthlyMonthlyRepeatDate($start, $timezone);
        }
        $this->setNextRepeatingShowDate($start->format("Y-m-d"), $day, $show_id);
    }

    /**
     * 
     * i.e. last thursday of each month
     * i.e. second monday of each month
     * 
     * @param string $showStart
     * @param string $timezone user's local timezone
     */
    private function getMonthlyWeeklyRepeatInterval($showStart)
    {
        $start = clone $showStart;

        $dayOfMonth = $start->format("j");
        $dayOfWeek = $start->format("l");
        $yearAndMonth = $start->format("Y-m");
        $firstDayOfWeek = strtotime($dayOfWeek." ".$yearAndMonth);
        // if $dayOfWeek is Friday, what number of the month does
        // the first Friday fall on
        $numberOfFirstDayOfWeek = date("j", $firstDayOfWeek);

        $weekCount = 0;
        while ($dayOfMonth >= $numberOfFirstDayOfWeek) {
            $weekCount++;
            $dayOfMonth -= 7;
        }

        switch ($weekCount) {
            case 1:
                $weekNumberOfMonth = "first";
                break;
            case 2:
                $weekNumberOfMonth = "second";
                break;
            case 3:
                $weekNumberOfMonth = "third";
                break;
            case 4:
                $weekNumberOfMonth = "fourth";
                break;
            case 5:
                $weekNumberOfMonth = "last";
                break;
        }

        return DateInterval::createFromDateString(
            $weekNumberOfMonth." ".$dayOfWeek." of next month");
    }

    /**
     * 
     * Enter description here ...
     * @param $start
     */
    private function getNextMonthlyMonthlyRepeatDate($start, $timezone)
    {
        $dt = new DateTime($start->format("Y-m"), new DateTimeZone($timezone));
        do {
            $dt->add(new DateInterval("P1M"));
        } while (!checkdate($dt->format("m"), $start->format("d"), $dt->format("Y")));

        $dt->setDate($dt->format("Y"), $dt->format("m"), $start->format("d"));
        return $dt;
    }

    private function getNextRepeatingPopulateStartDateTime($showDay)
    {
        $nextPopDate = $showDay->getDbNextPopDate();
        Logging::info($nextPopDate);
        $startTime = $showDay->getDbStartTime();

        if (isset($nextPopDate)) {
            return new DateTime($nextPopDate." ".$startTime, new DateTimeZone($showDay->getDbTimezone()));
        } else {
            return new DateTime($showDay->getDbFirstShow()." ".$startTime, new DateTimeZone($showDay->getDbTimezone()));
        }
    }

    /**
     * 
     * Create a DatePeriod object in the user's local time
     * It will get converted to UTC before the show instance gets created
     */
    private function getDatePeriod($start, $timezone, $lastShow, $repeatInterval, $populateUntil)
    {
        if (isset($lastShow)) {
            $endDatePeriod = new DateTime($lastShow, new DateTimeZone($timezone));
        } else {
            $endDatePeriod = $populateUntil;
        }

        return new DatePeriod($start, $repeatInterval, $endDatePeriod);
    }

    private function hasInstance($starts)
    {
        return $this->getInstance($starts) ? true : false;
    }

    /**
     * 
     * Attempts to retrieve the cc_show_instance belonging to a cc_show
     * that starts at $starts. We have to pass in the start
     * time in case the show is repeating
     * 
     * Returns the instance if one was found (one that is not a recording
     * and modified instance is false (has not been deleted))
     */
    private function getInstance($starts)
    {
        $ccShowInstance = CcShowInstancesQuery::create()
            ->filterByDbStarts($starts->format("Y-m-d H:i:s"), Criteria::EQUAL)
            ->filterByDbShowId($this->ccShow->getDbId(), Criteria::EQUAL)
            ->filterByDbModifiedInstance(false, Criteria::EQUAL)
            ->filterByDbRebroadcast(0, Criteria::EQUAL)
            ->limit(1)
            ->find();

        if ($ccShowInstance->isEmpty()) {
            return false;
        } else {
            return $ccShowInstance[0];
        }
    }

    private function hasCcShowDay($repeatType, $day)
    {
        return $this->getCcShowDay($repeatType, $day) ? true : false;
    }

    private function getCcShowDay($repeatType, $day)
    {
        $ccShowDay = CcShowDaysQuery::create()
            ->filterByDbShowId($this->ccShow->getDbId())
            ->filterByDbDay($day)
            ->filterByDbRepeatType($repeatType)
            ->limit(1)
            ->find();

        if ($ccShowDay->isEmpty()) {
            return false;
        } else {
            return $ccShowDay[0];
        }
    }

    /**
     * 
     * Sets the fields for a cc_show table row
     * @param $ccShow
     * @param $showData
     */
    private function setCcShow($showData, $isUpdate)
    {
        if (!$isUpdate) {
            $ccShow = new CcShow();
        } else {
            $ccShow = CcShowQuery::create()->findPk($showData["add_show_id"]);
        }

        $ccShow->setDbName($showData['add_show_name']);
        $ccShow->setDbDescription($showData['add_show_description']);
        $ccShow->setDbUrl($showData['add_show_url']);
        $ccShow->setDbGenre($showData['add_show_genre']);
        $ccShow->setDbColor($showData['add_show_color']);
        $ccShow->setDbBackgroundColor($showData['add_show_background_color']);
        $ccShow->setDbLiveStreamUsingAirtimeAuth($showData['cb_airtime_auth'] == 1);
        $ccShow->setDbLiveStreamUsingCustomAuth($showData['cb_custom_auth'] == 1);
        $ccShow->setDbLiveStreamUser($showData['custom_username']);
        $ccShow->setDbLiveStreamPass($showData['custom_password']);

        $ccShow->save();
        $this->ccShow = $ccShow;
    }

    /**
     * 
     * Sets the fields for a cc_show_days table row
     * @param $showData
     * @param $showId
     * @param $userId
     * @param $repeatType
     * @param $isRecorded
     * @param $showDay ccShowDay object we are setting values on
     */
    private function setCcShowDays($showData, $repeatType, $isRecorded)
    {
        $showId = $this->ccShow->getDbId();

        $startDateTime = new DateTime($showData['add_show_start_date']." ".$showData['add_show_start_time']);

        $endDateTime = $this->calculateEndDate($showData);
        if (!is_null($endDateTime)) {
            $endDate = $endDateTime->format("Y-m-d");
        } else {
            $endDate = $endDateTime;
        }

        /* What we are doing here is checking if the show repeats or if
         * any repeating days have been checked. If not, then by default
         * the "selected" DOW is the initial day.
         * DOW in local time.
         */
        $startDow = date("w", $startDateTime->getTimestamp());
        if (!$showData['add_show_repeats']) {
            $showData['add_show_day_check'] = array($startDow);
        } elseif ($showData['add_show_repeats'] && $showData['add_show_day_check'] == "") {
            $showData['add_show_day_check'] = array($startDow);
        }

        // Don't set day for monthly repeat type, it's invalid
        if ($showData['add_show_repeats'] && $showData['add_show_repeat_type'] == 2) {
            $showDay = new CcShowDays();
            $showDay->setDbFirstShow($startDateTime->format("Y-m-d"));
            $showDay->setDbLastShow($endDate);
            $showDay->setDbStartTime($startDateTime->format("H:i:s"));
            $showDay->setDbTimezone(Application_Model_Preference::GetTimezone());
            $showDay->setDbDuration($showData['add_show_duration']);
            $showDay->setDbRepeatType($repeatType);
            $showDay->setDbShowId($showId);
            $showDay->setDbRecord($isRecorded);
            //in case we are editing a show we need to set this to the first show
            //so when editing, the date period iterator will start from the beginning
            $showDay->setDbNextPopDate($startDateTime->format("Y-m-d"));
            $showDay->save();
        } else {
            foreach ($showData['add_show_day_check'] as $day) {
                $daysAdd=0;
                $startDateTimeClone = clone $startDateTime;
                if ($startDow !== $day) {
                    if ($startDow > $day)
                        $daysAdd = 6 - $startDow + 1 + $day;
                    else
                        $daysAdd = $day - $startDow;

                    $startDateTimeClone->add(new DateInterval("P".$daysAdd."D"));
                }
                if (is_null($endDate) || $startDateTimeClone->getTimestamp() <= $endDateTime->getTimestamp()) {
                    $showDay = new CcShowDays();
                    $showDay->setDbFirstShow($startDateTimeClone->format("Y-m-d"));
                    $showDay->setDbLastShow($endDate);
                    $showDay->setDbStartTime($startDateTimeClone->format("H:i"));
                    $showDay->setDbTimezone(Application_Model_Preference::GetTimezone());
                    $showDay->setDbDuration($showData['add_show_duration']);
                    $showDay->setDbDay($day);
                    $showDay->setDbRepeatType($repeatType);
                    $showDay->setDbShowId($showId);
                    $showDay->setDbRecord($isRecorded);
                    //in case we are editing a show we need to set this to the first show
                    //so when editing, the date period iterator will start from the beginning
                    $showDay->setDbNextPopDate($startDateTimeClone->format("Y-m-d"));
                    $showDay->save();
                }
            }
        }
    }

    /**
     * 
     * Deletes all the cc_show_rebroadcast entries for a specific show
     * that is currently being edited. They will get recreated with
     * the new show specs
     */
    private function deleteCcShowRebroadcasts()
    {
        CcShowRebroadcastQuery::create()->filterByDbShowId($this->ccShow->getDbId())->delete();
    }

    /**
     * 
     * Sets the fields for a cc_show_rebroadcast table row
     * @param $showData
     * @param $showId
     * @param $repeatType
     * @param $isRecorded
     */
    private function setCcShowRebroadcasts($showData, $repeatType, $isRecorded)
    {
        $showId = $this->ccShow->getDbId();

        if (($isRecorded && $showData['add_show_rebroadcast']) && ($repeatType != -1)) {
            for ($i = 1; $i <= MAX_REBROADCAST_DATES; $i++) {
                if ($showData['add_show_rebroadcast_date_'.$i]) {
                    $showRebroad = new CcShowRebroadcast();
                    $showRebroad->setDbDayOffset($showData['add_show_rebroadcast_date_'.$i]);
                    $showRebroad->setDbStartTime($showData['add_show_rebroadcast_time_'.$i]);
                    $showRebroad->setDbShowId($showId);
                    $showRebroad->save();
                }
            }
        } elseif ($isRecorded && $showData['add_show_rebroadcast'] && ($repeatType == -1)) {
            for ($i = 1; $i <= MAX_REBROADCAST_DATES; $i++) {
                if ($showData['add_show_rebroadcast_date_absolute_'.$i]) {
                    $rebroadcastDate = new DateTime($showData["add_show_rebroadcast_date_absolute_$i"]);
                    $startDate = new DateTime($showData['add_show_start_date']);
                    $offsetDays = $startDate->diff($rebroadcastDate);

                    $showRebroad = new CcShowRebroadcast();
                    $showRebroad->setDbDayOffset($offsetDays->format("%a days"));
                    $showRebroad->setDbStartTime($showData['add_show_rebroadcast_time_absolute_'.$i]);
                    $showRebroad->setDbShowId($showId);
                    $showRebroad->save();
                }
            }
        }
    }

    /**
     * 
     * Deletes all the cc_show_hosts entries for a specific show
     * that is currently being edited. They will get recreated with
     * the new show specs
     */
    private function deleteCcShowHosts()
    {
        CcShowHostsQuery::create()->filterByDbShow($this->ccShow->getDbId())->delete();
    }

    /**
     * 
     * Sets the fields for a cc_show_hosts table row
     * @param $showData
     * @param $showId
     */
    private function setCcShowHosts($showData)
    {
        if (is_array($showData['add_show_hosts'])) {
            foreach ($showData['add_show_hosts'] as $host) {
                $showHost = new CcShowHosts();
                $showHost->setDbShow($this->ccShow->getDbId());
                $showHost->setDbHost($host);
                $showHost->save();
            }
        }
    }

    /**
     * 
     * Gets the date and time shows (particularly repeating shows)
     * can be populated until.
     * 
     * @return DateTime object
     */
    private static function getPopulateShowUntilDateTIme()
    {
        $populateUntil = Application_Model_Preference::GetShowsPopulatedUntil();

        if (is_null($populateUntil)) {
            $populateUntil = new DateTime("now", new DateTimeZone('UTC'));
            Application_Model_Preference::SetShowsPopulatedUntil($populateUntil);
        }
        return $populateUntil;
    }

    /**
     * 
     * Enter description here ...
     * @param DateTime $showStart user's local time
     * @param string $duration time interval (h)h:(m)m(:ss)
     * @param string $timezone "Europe/Prague"
     * @param array $offset (days, hours, mins) used for rebroadcast shows
     * 
     * @return array of 2 DateTime objects, start/end time of the show in UTC
     */
    private function createUTCStartEndDateTime($showStart, $duration, $offset=null)
    {
        $startDateTime = clone $showStart;

        if (isset($offset)) {
            //$offset["hours"] and $offset["mins"] represents the start time
            //of a rebroadcast show
            $startDateTime = new DateTime($startDateTime->format("Y-m-d")." ".
                $offset["hours"].":".$offset["mins"]);
            $startDateTime->add(new DateInterval("P{$offset["days"]}D"));
        }
        //convert time to UTC
        $startDateTime->setTimezone(new DateTimeZone('UTC'));

        $endDateTime = clone $startDateTime;
        $duration = explode(":", $duration);
        list($hours, $mins) = array_slice($duration, 0, 2);
        $endDateTime->add(new DateInterval("PT{$hours}H{$mins}M"));

        return array($startDateTime, $endDateTime);
    }

    /**
     * 
     * Show instances for repeating shows only get created up
     * until what is visible on the calendar. We need to set the
     * date for when the next repeating show instance should be created
     * as the user browses the calendar further.
     * 
     * @param $nextDate
     * @param $showId
     * @param $day
     */
    private function setNextRepeatingShowDate($nextDate, $day, $showId)
    {
        $nextInfo = explode(" ", $nextDate);

        $repeatInfo = CcShowDaysQuery::create()
            ->filterByDbShowId($showId)
            ->filterByDbDay($day)
            ->findOne();

        $repeatInfo->setDbNextPopDate($nextInfo[0])
            ->save();
    }
}