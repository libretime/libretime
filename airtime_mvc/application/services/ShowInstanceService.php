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
            switch ($day["repeat_type"]) {
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
        $start = $showDay["first_show"]." ".$showDay["start_time"];

        list($utcStartDateTime, $utcEndDateTime) = $this->service_show->createUTCStartEndDateTime(
            $start, $showDay["duration"], $showDay["timezone"]);

        if ($utcStartDateTime->getTimestamp() < $populateUntil->getTimestamp()) {
            $ccShowInstance = new CcShowInstances();
            $ccShowInstance->setDbShowId($showDay["show_id"]);
            $ccShowInstance->setDbStarts($utcStartDateTime);
            $ccShowInstance->setDbEnds($utcEndDateTime);
            $ccShowInstance->setDbRecord($showDay["record"]);
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
        $show_id       = $showDay["show_id"];
        $next_pop_date = $showDay["next_pop_date"];
        $first_show    = $showDay["first_show"]; //non-UTC
        $last_show     = $showDay["last_show"]; //non-UTC
        $start_time    = $showDay["start_time"]; //non-UTC
        $duration      = $showDay["duration"];
        $day           = $showDay["day"];
        $record        = $showDay["record"];
        $timezone      = $showDay["timezone"];

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
}