<?php

use Airtime\CcShowInstancesQuery;
use Airtime\CcShowQuery;
use Airtime\CcShowInstancesPeer;
use Airtime\CcScheduleQuery;
use Airtime\CcShowDaysQuery;
use Airtime\CcShowDays;

class Application_Model_ShowInstance
{
    private $_instanceId;
    private $_showInstance;

    public function __construct($instanceId)
    {
        $this->_instanceId = $instanceId;
        $this->_showInstance = CcShowInstancesQuery::create()->findPK($instanceId);

        if (is_null($this->_showInstance)) {
            throw new Exception();
        }
    }

    public function getShowId()
    {
        return $this->_showInstance->getDbShowId();
    }

    /* TODO: A little inconsistent because other models have a getId() method
        to get PK --RG */
    public function getShowInstanceId()
    {
        return $this->_instanceId;
    }

    public function getShow()
    {
        return new Application_Model_Show($this->getShowId());
    }

    public function deleteRebroadcasts()
    {
        $timestamp = gmdate("Y-m-d H:i:s");
        $instance_id = $this->getShowInstanceId();
        $sql = <<<SQL
DELETE FROM cc_show_instances
WHERE starts > :timestamp::TIMESTAMP
AND instance_id = :instanceId
AND rebroadcast = 1;
SQL;
        Application_Common_Database::prepareAndExecute( $sql, array(
            ':instanceId' => $instance_id,
            ':timestamp'  => $timestamp), 'execute');
    }

    /* This function is weird. It should return a boolean, but instead returns
     * an integer if it is a rebroadcast, or returns null if it isn't. You can convert
     * it to boolean by using is_null(isRebroadcast), where true means isn't and false
     * means that it is. */
    public function isRebroadcast()
    {
        return $this->_showInstance->getDbOriginalShow();
    }

    public function isRecorded()
    {
        return $this->_showInstance->getDbRecord();
    }

    public function getName()
    {
        $show = CcShowQuery::create()->findPK($this->getShowId());

        return $show->getDbName();
    }

    public function getGenre()
    {
        $show = CcShowQuery::create()->findPK($this->getShowId());

        return $show->getDbGenre();
    }

    /**
     * Return the start time of the Show (UTC time)
     * @return string in format "Y-m-d H:i:s" (PHP time notation)
     */
    public function getShowInstanceStart($format="Y-m-d H:i:s")
    {
        return $this->_showInstance->getDbStarts($format);
    }

    /**
     * Return the end time of the Show (UTC time)
     * @return string in format "Y-m-d H:i:s" (PHP time notation)
     */
    public function getShowInstanceEnd($format="Y-m-d H:i:s")
    {
        return $this->_showInstance->getDbEnds($format);
    }

    public function getStartDate()
    {
        $showStart = $this->getShowInstanceStart();
        $showStartExplode = explode(" ", $showStart);

        return $showStartExplode[0];
    }

    public function getStartTime()
    {
        $showStart = $this->getShowInstanceStart();
        $showStartExplode = explode(" ", $showStart);

        return $showStartExplode[1];
    }

    public function setSoundCloudFileId($p_soundcloud_id)
    {
        $file = Application_Model_StoredFile::RecallById($this->_showInstance->getDbRecordedFile());
        $file->setSoundCloudFileId($p_soundcloud_id);
    }

    public function getSoundCloudFileId()
    {
        $file = Application_Model_StoredFile::RecallById($this->_showInstance->getDbRecordedFile());

        return $file->getSoundCloudId();
    }

    public function getRecordedFile()
    {
        $file_id =  $this->_showInstance->getDbRecordedFile();

        if (isset($file_id)) {
            $file =  Application_Model_StoredFile::RecallById($file_id);

            if (isset($file) && file_exists($file->getFilePath())) {
                return $file;
            }
        }

        return null;
    }

    public function setShowStart($start)
    {
        $this->_showInstance->setDbStarts($start)
            ->save();
        Application_Model_RabbitMq::PushSchedule();
    }

    public function setShowEnd($end)
    {
        $this->_showInstance->setDbEnds($end)
            ->save();
        Application_Model_RabbitMq::PushSchedule();
    }

    public function updateScheduledTime()
    {
        $con = Propel::getConnection(CcShowInstancesPeer::DATABASE_NAME);
        $this->_showInstance->updateDbTimeFilled($con);
    }

    public function isDeleted()
    {
        $this->_showInstance->getDbModifiedInstance();
    }

    /*
     * @param $dateTime
     *      php Datetime object to add deltas to
     *
     * @param $deltaDay
     *      php int, delta days show moved
     *
     * @param $deltaMin
     *      php int, delta mins show moved
     *
     * @return $newDateTime
     *      php DateTime, $dateTime with the added time deltas.
     */
    public static function addDeltas($dateTime, $deltaDay, $deltaMin)
    {
        $newDateTime = clone $dateTime;

        $days = abs($deltaDay);
        $mins = abs($deltaMin);

        $dayInterval = new DateInterval("P{$days}D");
        $minInterval = new DateInterval("PT{$mins}M");

        if ($deltaDay > 0) {
            $newDateTime->add($dayInterval);
        } elseif ($deltaDay < 0) {
            $newDateTime->sub($dayInterval);
        }

        if ($deltaMin > 0) {
            $newDateTime->add($minInterval);
        } elseif ($deltaMin < 0) {
            $newDateTime->sub($minInterval);
        }

        return $newDateTime;
    }

    /**
     * Add a playlist as the last item of the current show.
     *
     * @param int $plId
     *         Playlist ID.
     */
    /*public function addPlaylistToShow($pl_id, $checkUserPerm = true)
    {
        $ts = intval($this->_showInstance->getDbLastScheduled("U")) ? : 0;
        $id = $this->_showInstance->getDbId();

        $scheduler = new Application_Model_Scheduler();
        $scheduler->scheduleAfter(
            array(array("id" => 0, "instance"  => $id, "timestamp" => $ts)),
            array(array("id" => $pl_id, "type" => "playlist"))
        );
    }*/

    /**
     * Add a media file as the last item in the show.
     *
     * @param int $file_id
     */
    public function addFileToShow($file_id, $checkUserPerm = true)
    {
        $ts = intval($this->_showInstance->getDbLastScheduled("U")) ? : 0;
        $id = $this->_showInstance->getDbId();

        $scheduler = new Application_Model_Scheduler();
        $scheduler->setCheckUserPermissions($checkUserPerm);
        $scheduler->scheduleAfter(
            array(array("id" => 0, "instance" => $id, "timestamp" => $ts)),
            array(array("id" => $file_id, "type" => "audioclip"))
        );
    }

    /**
     * Add the given playlists to the show.
     *
     * @param array $plIds
     *         An array of playlist IDs.
     */
    /*public function scheduleShow($plIds)
    {
        foreach ($plIds as $plId) {
            $this->addPlaylistToShow($plId);
        }
    }*/

    public function clearShow()
    {
        CcScheduleQuery::create()
            ->filterByDbInstanceId($this->_instanceId)
            ->delete();
        Application_Model_RabbitMq::PushSchedule();
        $this->updateScheduledTime();
    }

    private function checkToDeleteShow($showId)
    {
        //UTC DateTime object
        $showsPopUntil = Application_Model_Preference::GetShowsPopulatedUntil();

        $showDays = CcShowDaysQuery::create()
            ->filterByDbShowId($showId)
            ->findOne();

        $showEnd = $showDays->getDbLastShow();

        //there will always be more shows populated.
        if (is_null($showEnd)) {
            return false;
        }

        $lastShowStartDateTime = new DateTime("{$showEnd} {$showDays->getDbStartTime()}", new DateTimeZone($showDays->getDbTimezone()));
        //end dates were non inclusive.
        $lastShowStartDateTime = self::addDeltas($lastShowStartDateTime, -1, 0);

        //there's still some shows left to be populated.
        if ($lastShowStartDateTime->getTimestamp() > $showsPopUntil->getTimestamp()) {
            return false;
        }

        // check if there are any non deleted show instances remaining.
        $showInstances = CcShowInstancesQuery::create()
            ->filterByDbShowId($showId)
            ->filterByDbModifiedInstance(false)
            ->filterByDbRebroadcast(0)
            ->find();

        if (is_null($showInstances)) {
            return true;
        }
        //only 1 show instance left of the show, make it non repeating.
        else if (count($showInstances) === 1) {
            $showInstance = $showInstances[0];

            $showDaysOld = CcShowDaysQuery::create()
                ->filterByDbShowId($showId)
                ->find();

            $tz = $showDaysOld[0]->getDbTimezone();

            $startDate = new DateTime($showInstance->getDbStarts(), new DateTimeZone("UTC"));
            $startDate->setTimeZone(new DateTimeZone($tz));
            $endDate = self::addDeltas($startDate, 1, 0);

            //make a new rule for a non repeating show.
            $showDayNew = new CcShowDays();
            $showDayNew->setDbFirstShow($startDate->format("Y-m-d"));
            $showDayNew->setDbLastShow($endDate->format("Y-m-d"));
            $showDayNew->setDbStartTime($startDate->format("H:i:s"));
            $showDayNew->setDbTimezone($tz);
            $showDayNew->setDbDay($startDate->format('w'));
            $showDayNew->setDbDuration($showDaysOld[0]->getDbDuration());
            $showDayNew->setDbRepeatType(-1);
            $showDayNew->setDbShowId($showDaysOld[0]->getDbShowId());
            $showDayNew->setDbRecord($showDaysOld[0]->getDbRecord());
            $showDayNew->save();

            //delete the old rules for repeating shows
            $showDaysOld->delete();

            //remove the old repeating deleted instances.
            $showInstances = CcShowInstancesQuery::create()
                ->filterByDbShowId($showId)
                ->filterByDbModifiedInstance(true)
                ->delete();
        }

        return false;
    }

    public function delete($rabbitmqPush = true)
    {
        // see if it was recording show
        $recording = $this->isRecorded();
        // get show id
        $showId = $this->getShowId();

        $show = $this->getShow();

        $current_timestamp = gmdate("Y-m-d H:i:s");

        if ($current_timestamp <= $this->getShowInstanceEnd()) {
            if ($show->isRepeating()) {

                CcShowInstancesQuery::create()
                    ->findPK($this->_instanceId)
                    ->setDbModifiedInstance(true)
                    ->save();

                if ($this->isRebroadcast()) {
                    return;
                }

                //delete the rebroadcasts of the removed recorded show.
                if ($recording) {
                    CcShowInstancesQuery::create()
                        ->filterByDbOriginalShow($this->_instanceId)
                        ->delete();
                }

                /* Automatically delete all files scheduled in cc_schedules table. */
                CcScheduleQuery::create()
                    ->filterByDbInstanceId($this->_instanceId)
                    ->delete();


                if ($this->checkToDeleteShow($showId)) {
                    CcShowQuery::create()
                        ->filterByDbId($showId)
                        ->delete();
                }
            } else {
                if ($this->isRebroadcast()) {
                    $this->_showInstance->delete();
                } else {
                    $show->delete();
                }
            }
        }

        if ($rabbitmqPush) {
            Application_Model_RabbitMq::PushSchedule();
        }
    }

    public function setRecordedFile($file_id)
    {
        $showInstance = CcShowInstancesQuery::create()
            ->findPK($this->_instanceId);
        $showInstance->setDbRecordedFile($file_id)
            ->save();

        $rebroadcasts = CcShowInstancesQuery::create()
            ->filterByDbOriginalShow($this->_instanceId)
            ->find();

        foreach ($rebroadcasts as $rebroadcast) {

            try {
                $rebroad = new Application_Model_ShowInstance($rebroadcast->getDbId());
                $rebroad->addFileToShow($file_id, false);
            } catch (Exception $e) {
                Logging::info($e->getMessage());
            }
        }
    }

    public function getTimeScheduled()
    {
        $time = $this->_showInstance->getDbTimeFilled();

        if ($time != "00:00:00" && !empty($time)) {
            $time_arr = explode(".", $time);
            if (count($time_arr) > 1) {
                $time_arr[1] = "." . $time_arr[1];
                $milliseconds = number_format(round($time_arr[1], 2), 2);
                $time = $time_arr[0] . substr($milliseconds, 1);
            } else {
                $time = $time_arr[0] . ".00";
            }
        } else {
            $time = "00:00:00.00";
        }

        return $time;
    }


    public function getTimeScheduledSecs()
    {
        $time_filled = $this->getTimeScheduled();

        return Application_Common_DateHelper::playlistTimeToSeconds($time_filled);
    }

    public function getDurationSecs()
    {
        $ends = $this->getShowInstanceEnd(null);
        $starts = $this->getShowInstanceStart(null);

        return intval($ends->format('U')) - intval($starts->format('U'));
    }

    public function getPercentScheduled()
    {
        $durationSeconds = $this->getDurationSecs();
        $timeSeconds = $this->getTimeScheduledSecs();
    
        if ($durationSeconds != 0) { //Prevent division by zero if the show duration is somehow zero.
            $percent = ceil(($timeSeconds / $durationSeconds) * 100);
        } else {
            $percent = 0;
        }
        return $percent;
    }

    public function getShowLength()
    {
        $start = $this->getShowInstanceStart(null);
        $end = $this->getShowInstanceEnd(null);

        $interval = $start->diff($end);
        $days = $interval->format("%d");
        $hours = sprintf("%02d" ,$interval->format("%h"));

        if ($days > 0) {
            $totalHours = $days * 24 + $hours;
            //$interval object does not have milliseconds so hard code to .00
            $returnStr = $totalHours . ":" . $interval->format("%I:%S") . ".00";
        } else {
            $returnStr = $hours . ":" . $interval->format("%I:%S") . ".00";
        }

        return $returnStr;
    }

    public static function getContentCount($p_start, $p_end) 
    {
        $sql = <<<SQL
SELECT instance_id,
       count(*) AS instance_count
FROM cc_schedule
WHERE ends > :p_start::TIMESTAMP
  AND starts < :p_end::TIMESTAMP
GROUP BY instance_id
SQL;

        $counts = Application_Common_Database::prepareAndExecute($sql, array(
            ':p_start' => $p_start->format("Y-m-d G:i:s"),
            ':p_end' => $p_end->format("Y-m-d G:i:s"))
        , 'all');

        $real_counts = array();
        foreach ($counts as $c) {
            $real_counts[$c['instance_id']] = $c['instance_count'];
        }
        return $real_counts;

    }

    //TODO this sucks.
    public static function getIsFull($p_start, $p_end)
    {
        $sql = <<<SQL
SELECT id, ends-starts-'00:00:05' < time_filled as filled
from cc_show_instances
WHERE ends > :p_start::TIMESTAMP
AND starts < :p_end::TIMESTAMP
SQL;

        $res = Application_Common_Database::prepareAndExecute($sql, array(
            ':p_start' => $p_start->format("Y-m-d G:i:s"),
            ':p_end' => $p_end->format("Y-m-d G:i:s"))
        , 'all');

        $isFilled = array();
        foreach ($res as $r) {
            $isFilled[$r['id']] = $r['filled'];
        }

        return $isFilled;
    }

    public function getLastAudioItemEnd()
    {
        $con = Propel::getConnection();

        $sql = "SELECT ends FROM cc_schedule "
            ."WHERE instance_id = :instanceId"
            ."ORDER BY ends DESC "
            ."LIMIT 1";

        $query = Application_Common_Database::prepareAndExecute( $sql,
            array(':instanceId' => $this->_instanceId), 'column');

        return ($query !== false) ? $query : null;
    }

    public static function GetLastShowInstance($p_timeNow)
    {
        $sql = <<<SQL
SELECT si.id
FROM cc_show_instances si
WHERE si.ends < :timeNow::TIMESTAMP
  AND si.modified_instance = 'f'
ORDER BY si.ends DESC LIMIT 1;
SQL;
        $id = Application_Common_Database( $sql, array(
            ':timeNow' => $p_timeNow ), 'column' );

        return ($id ? new Application_Model_ShowInstance($id) : null );
    }

    public static function GetCurrentShowInstance($p_timeNow)
    {
        /* Orderby si.starts descending, because in some cases
         * we can have multiple shows overlapping each other. In
         * this case, the show that started later is the one that
         * is actually playing, and so this is the one we want.
         */

        $sql = <<<SQL
SELECT si.id
FROM cc_show_instances si
WHERE si.starts <= :timeNow1::TIMESTAMP
  AND si.ends > :timeNow2::TIMESTAMP
  AND si.modified_instance = 'f'
ORDER BY si.starts DESC LIMIT 1
SQL;

        $id = Application_Common_Database( $sql, array(
            ':timeNow1' => $p_timeNow,
            ':timeNow2' => $p_timeNow ), 'column');

        return ( $id ? new Application_Model_ShowInstance($id) : null );
    }

    public static function GetNextShowInstance($p_timeNow)
    {
        $sql = <<<SQL
SELECT si.id
FROM cc_show_instances si
WHERE si.starts > :timeNow::TIMESTAMP
AND si.modified_instance = 'f'
ORDER BY si.starts
LIMIT 1
SQL;
        $id = Application_Common_Database::prepareAndExecute( $sql,
            array( 'timeNow' => $p_timeNow ), 'column' );
        return ( $id ? new Application_Model_ShowInstance($id) : null );
    }

    // returns number of show instances that ends later than $day
    public static function GetShowInstanceCount($day)
    {
        $sql = <<<SQL
SELECT count(*) AS cnt
FROM cc_show_instances
WHERE ends < :day
SQL;
        return Application_Common_Database::prepareAndExecute( $sql,
            array( ':day' => $day ), 'column' );
    }

    // this returns end timestamp of all shows that are in the range and has live DJ set up
    public static function GetEndTimeOfNextShowWithLiveDJ($p_startTime, $p_endTime)
    {
        $sql = <<<SQL
SELECT ends
FROM cc_show_instances AS si
JOIN cc_show AS sh ON si.show_id = sh.id
WHERE si.ends > :startTime::TIMESTAMP
  AND si.ends < :endTime::TIMESTAMP
  AND (sh.live_stream_using_airtime_auth
       OR live_stream_using_custom_auth)
ORDER BY si.ends
SQL;
        return Application_Common_Database::prepareAndExecute( $sql, array(
            ':startTime' => $p_startTime,
            ':endTime'   => $p_endTime), 'all');
    }

    public function isRepeating()
    {
        return $this->getShow()->isRepeating();
    }
}
