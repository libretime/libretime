<?php
define("NO_REPEAT", -1);
define("REPEAT_WEEKLY", 0);
define("REPEAT_BI_WEEKLY", 1);
define("REPEAT_MONTHLY_MONTHLY", 2);
define("REPEAT_MONTHLY_WEEKLY", 3);

class Application_Service_ShowInstanceService
{
    private $service_show;
    private $service_showDays;
    private $service_user;

    public function __construct()
    {
        $this->service_show = new Application_Service_ShowService();
        $this->service_user = new Application_Service_UserService();
    }

    /**
     * 
     * Receives a cc_show id and determines whether to create a 
     * single show instance or repeating show instances
     */
    public function delegateShowInstanceCreation($showId, $isRebroadcast)
    {
        $populateUntil = $this->service_show->getPopulateShowUntilDateTIme();

        $this->service_showDays = new Application_Service_ShowDaysService($showId);
        $showDays = $this->service_showDays->getShowDays();

        foreach ($showDays as $day) {
            switch ($day->getDbRepeatType()) {
                case NO_REPEAT:
                    $this->createNonRepeatingShowInstance($day, $populateUntil, $isRebroadcast);
                    break;
                case REPEAT_WEEKLY:
                    $this->createWeeklyRepeatingShowInstances($day, $populateUntil, "P7D", $isRebroadcast);
                    break;
                case REPEAT_BI_WEEKLY:
                    $this->createWeeklyRepeatingShowInstances($day, $populateUntil, "P14D", $isRebroadcast);
                    break;
                case REPEAT_MONTHLY_MONTHLY:
                    $this->createMonthlyRepeatingShowInstances($day, $populateUntil, "P1M", $isRebroadcast);
                    break;
                case REPEAT_MONTHLY_WEEKLY:
                    // do something here
                    break;
            }
        }
        Application_Model_RabbitMq::PushSchedule();
    }

    /**
     * 
     * Sets a single cc_show_instance table row
     * @param $showDay
     * @param $populateUntil
     */
    private function createNonRepeatingShowInstance($showDay, $populateUntil, $isRebroadcast)
    {
        $start = $showDay->getDbFirstShow()." ".$showDay->getDbStartTime();

        list($utcStartDateTime, $utcEndDateTime) = $this->service_show->createUTCStartEndDateTime(
            $start, $showDay->getDbDuration(), $showDay->getDbTimezone());

        if ($utcStartDateTime->getTimestamp() < $populateUntil->getTimestamp()) {
            $ccShowInstance = new CcShowInstances();
            $ccShowInstance->setDbShowId($showDay->getDbShowId());
            $ccShowInstance->setDbStarts($utcStartDateTime);
            $ccShowInstance->setDbEnds($utcEndDateTime);
            $ccShowInstance->setDbRecord($showDay->getDbRecord());
            $ccShowInstance->save();

            if ($isRebroadcast) {
                $this->createRebroadcastShowInstances($showDay, $start, $ccShowInstance->getDbId());
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
    private function createWeeklyRepeatingShowInstances($showDay, $populateUntil,
        $repeatInterval, $isRebroadcast)
    {
        $show_id       = $showDay->getDbShowId();
        $next_pop_date = $showDay->getDbNextPopDate();
        $first_show    = $showDay->getDbFirstShow(); //non-UTC
        $last_show     = $showDay->getDbLastShow(); //non-UTC
        $start_time    = $showDay->getDbStartTime(); //non-UTC
        $duration      = $showDay->getDbDuration();
        $day           = $showDay->getDbDay();
        $record        = $showDay->getDbRecord();
        $timezone      = $showDay->getDbTimezone();

        $currentUtcTimestamp = gmdate("Y-m-d H:i:s");

        if (isset($next_pop_date)) {
            $start = $next_pop_date." ".$start_time;
        } else {
            $start = $first_show." ".$start_time;
        }

        $period = $this->getDatePeriod($start, $timezone, $last_show,
            $repeatInterval, $populateUntil);

        $utcStartDateTime = Application_Common_DateHelper::ConvertToUtcDateTime($start, $timezone);
        $utcLastShowDateTime = $last_show ?
            Application_Common_DateHelper::ConvertToUtcDateTime($last_show, $timezone) : null;

        $utcEndDateTime = null;
        foreach ($period as $date) {
            list($utcStartDateTime, $utcEndDateTime) = $this->service_show->createUTCStartEndDateTime(
                $date->format("Y-m-d H:i:s"), $duration, $timezone);
            /*
             * Make sure start date is less than populate until date AND
             * last show date is null OR start date is less than last show date
             */
            if ($utcStartDateTime->getTimestamp() <= $populateUntil->getTimestamp() &&
               ( is_null($utcLastShowDateTime) ||
                 $utcStartDateTime->getTimestamp() < $utcLastShowDateTime->getTimestamp()) ) {

                $ccShowInstance = new CcShowInstances();
                $ccShowInstance->setDbShowId($show_id);
                $ccShowInstance->setDbStarts($utcStartDateTime);
                $ccShowInstance->setDbEnds($utcEndDateTime);
                $ccShowInstance->setDbRecord($record);
                $ccShowInstance->save();

                if ($isRebroadcast) {
                    $this->createRebroadcastShowInstances($showDay, $date->format("Y-m-d"), $ccShowInstance->getDbId());
                }
            }
        }
        $nextDate = $utcEndDateTime->add(new DateInterval($repeatInterval));
        $this->service_show->setNextRepeatingShowDate($nextDate->format("Y-m-d"), $show_id, $day);
    }

    /**
     * 
     * Enter description here ...
     * @param $showDay
     */
    private function createRebroadcastShowInstances($showDay, $showStartDate, $instanceId)
    {
        $currentUtcTimestamp = gmdate("Y-m-d H:i:s");
        $showId = $showDay["show_id"];

        $sql = "SELECT * FROM cc_show_rebroadcast WHERE show_id=:show_id";
        $rebroadcasts = Application_Common_Database::prepareAndExecute($sql,
            array( ':show_id' => $showId ), 'all');

        foreach ($rebroadcasts as $rebroadcast) {
            $days = explode(" ", $rebroadcast["day_offset"]);
            $time = explode(":", $rebroadcast["start_time"]);
            $offset = array("days"=>$days[0], "hours"=>$time[0], "mins"=>$time[1]);

            list($utcStartDateTime, $utcEndDateTime) = $this->service_show->createUTCStartEndDateTime(
                $showStartDate, $showDay["duration"], $showDay["timezone"], $offset);

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

    private function deleteRebroadcastShowInstances()
    {

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

        return new DatePeriod(new DateTime($start, new DateTimeZone($timezone)),
            new DateInterval($repeatInterval), $endDatePeriod);
    }

    /**
     * 
     * Returns 2 DateTime objects, in the user's local time,
     * of the next future repeat show instance start and end time
     */
    public function getNextFutureRepeatShowTime($showId)
    {
        $sql = <<<SQL
SELECT starts, ends FROM cc_show_instances
WHERE ends > now() at time zone 'UTC'
AND show_id = :showId
ORDER BY starts
LIMIT 1
SQL;
        $result = Application_Common_Database::prepareAndExecute( $sql,
            array( 'showId' => $showId ), 'all' );
        
        foreach ($result as $r) {
            $show["starts"] = new DateTime($r["starts"], new DateTimeZone('UTC'));
            $show["ends"] = new DateTime($r["ends"], new DateTimeZone('UTC'));
        }

        $userTimezone = Application_Model_Preference::GetUserTimezone(
            $this->service_user->getCurrentUser()->getDbId());

        $show["starts"]->setTimezone(new DateTimeZone($userTimezone));
        $show["ends"]->setTimezone(new DateTimeZone($userTimezone));

        return $show;
    }

    /**
     * This function is messy. But sometimes there is no easy way to do it.
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
    public function updateShowInstances($showData, $isRecorded, $repeatType)
    {
        $showId = $showData["add_show_id"];

        $this->service_showDays = new Application_Service_ShowDaysService($showId);
        //ccShowDays object of the show being edited
        $currentShowDay = $this->service_showDays->getCurrentShowDay();

        $endDate = $this->service_showDays->calculateEndDate($showData);

        //repeat option was toggled
        if ($showData['add_show_repeats'] != $currentShowDay->isRepeating()) {
            $this->deleteAllRepeatInstances($currentShowDay, $showId);
        }

        //duration has changed
        if ($showData['add_show_duration'] != $currentShowDay->getDbDuration()) {
            $this->updateDuration($showData);
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

            if ($repeatType != $currentShowDay->getDbRepeatType()) {
                //repeat type changed
                $this->deleteAllInstances($showId);
            } else {
                //repeat type is the same, check if the days of the week are the same
                $repeatingDaysChanged = false;

                $ccShowDays = $this->service_showDays->getShowDays();
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
                    || $showData['add_show_start_time'] != $localShowStart->format("H:i:s")){

                    //start date has been pushed forward so we need to delete
                    //any instances of this show scheduled before the new start date
                    if ($showData['add_show_start_date'] > $localShowStart->format("Y-m-d")) {
                        $this->deleteInstancesBeforeDate($showData['add_show_start_date'], $showId);
                    }

                    $this->updateStartDateAndTime($showData, $currentShowDay);
                }
            }
/*
            //Check if end date for the repeat option has changed. If so, need to take care
            //of deleting possible invalid Show Instances.
            if ((strlen($this->getRepeatingEndDate()) == 0) == $showData['add_show_no_end']) {
                //show "Never Ends" option was toggled.
                if ($showData['add_show_no_end']) {
                } else {
                    $this->removeAllInstancesFromDate($p_endDate);
                }
            }
            if ($this->getRepeatingEndDate() != $showData['add_show_end_date']) {
                //end date was changed.

                $newDate = strtotime($showData['add_show_end_date']);
                $oldDate = strtotime($this->getRepeatingEndDate());
                if ($newDate < $oldDate) {
                    $this->removeAllInstancesFromDate($p_endDate);
                }
            }*/
        }
    }

    /**
     * 
     * Updates the start date and time for cc_show_instances
     * and entries in cc_schedule
     * 
     * @param $showData edit show form data
     */
    public function updateStartDateAndTime($showData, $currentShowDay)
    {
        $date = new Application_Common_DateHelper();
        //current time in UTC
        $timestamp = $date->getTimestamp();

        $dtOld = $currentShowDay->getUTCStartDateAndTime();
        $dtNew = new DateTime($showData['add_show_start_date']." ".$showData['add_show_start_time'],
            new DateTimeZone(date_default_timezone_get()));
        $diff = $dtOld->getTimestamp() - $dtNew->getTimestamp();
        $sql = <<<SQL
UPDATE cc_show_instances
SET starts = starts + :diff1::INTERVAL,
    ends = ends + :diff2::INTERVAL
WHERE show_id = :showId
  AND starts > :timestamp
SQL;

        Application_Common_Database::prepareAndExecute($sql,
            array(':diff1' => $diff, ':diff2' => $diff, 
                ':showId' => $showData["add_show_id"], ':timestamp' => $timestamp),
            'execute');

        /*$showInstanceIds = $this->getAllFutureInstanceIds();
        if (count($showInstanceIds) > 0 && $diff != 0) {
            $showIdsImploded = implode(",", $showInstanceIds);
            $sql = "UPDATE cc_schedule "
                    ."SET starts = starts + INTERVAL '$diff sec', "
                    ."ends = ends + INTERVAL '$diff sec' "
                    ."WHERE instance_id IN ($showIdsImploded)";
            $con->exec($sql);
        }*/
    }

    public function deleteAllRepeatInstances($currentShowDay, $showId)
    {
        $firstShow = $currentShowDay->getUTCStartDateAndTime();

        $sql = <<<SQL
DELETE
FROM cc_show_instances
WHERE starts > :timestamp::TIMESTAMP
  AND show_id = :showId
  AND date(starts) != :firstShow
SQL;
        Application_Common_Database::prepareAndExecute( $sql,
            array( ':timestamp' => gmdate("Y-m-d H:i:s"),
                   ':showId'    => $showId,
                   ':firstShow' => $firstShow->format("Y-m-d")), 'execute');
    }

    public function deleteAllInstances($showId)
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

    /**
     * 
     * Enter description here ...
     * @param $daysRemoved array of days (days of the week) removed
     *     (days of the week are represented numerically
     *      0=>sunday, 1=>monday, 2=>tuesday, etc.)
     * @param $showDays array of ccShowDays objects
     * @param $showId
     */
    public function deleteRemovedShowDayInstances($daysRemoved, $showDays, $showId)
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

        $uncheckedDays = pg_escape_string(implode(",", $daysRemovedUTC));

        $sql = <<<SQL
DELETE
FROM cc_show_instances
WHERE EXTRACT(DOW FROM starts) IN ($uncheckedDays)
  AND starts > :timestamp::TIMESTAMP
  AND show_id = :showId
SQL;

        Application_Common_Database::prepareAndExecute( $sql,
            array(
                ":timestamp" => gmdate("Y-m-d H:i:s"),
                ":showId"    => $showId,
            ), "execute");
    }

    public function deleteInstancesBeforeDate($newStartDate, $showId)
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

    public function updateDuration($showData)
    {
        $date = new Application_Common_DateHelper;
        $timestamp = $date->getUtcTimestamp();

        $sql = <<<SQL
UPDATE cc_show_instances
SET ends = starts + :add_show_duration::INTERVAL
WHERE show_id = :show_id
  AND ends > :timestamp::TIMESTAMP
SQL;
        
        Application_Common_Database::prepareAndExecute( $sql, array(
            ':add_show_duration' => $showData['add_show_duration'],
            ':show_id' => $showData['add_show_id'],
            ':timestamp' => $timestamp), "execute");
    }
}