<?php

require_once 'formatters/LengthFormatter.php';

class Application_Model_ShowInstance {

    private $_instanceId;
    private $_showInstance;

    public function __construct($instanceId)
    {
        $this->_instanceId = $instanceId;
        $this->_showInstance = CcShowInstancesQuery::create()->findPK($instanceId);

        if (is_null($this->_showInstance)){
            throw new Exception();
        }
    }

    public function getShowId()
    {
        return $this->_showInstance->getDbShowId();
    }

    public function getShowInstanceId()
    {
        return $this->_instanceId;
    }

    public function getShow(){
        return new Application_Model_Show($this->getShowId());
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
        $file = Application_Model_StoredFile::Recall($this->_showInstance->getDbRecordedFile());
        $file->setSoundCloudFileId($p_soundcloud_id);
    }

    public function getSoundCloudFileId()
    {
        $file = Application_Model_StoredFile::Recall($this->_showInstance->getDbRecordedFile());
        return $file->getSoundCloudId();
    }

    public function getRecordedFile()
    {
        $file_id =  $this->_showInstance->getDbRecordedFile();

        if(isset($file_id)) {
            $file =  Application_Model_StoredFile::Recall($file_id);

            if (PEAR::isError($file)) {
                return null;
            }

            if(file_exists($file->getFilePath())) {
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

    public function correctScheduleStartTimes(){
        global $CC_DBC;

        $instance_id = $this->getShowInstanceId();
        $sql = "SELECT starts from cc_schedule"
            ." WHERE instance_id = $instance_id"
            ." ORDER BY starts"
            ." LIMIT 1";

        $scheduleStarts = $CC_DBC->GetOne($sql);

        if (!is_null($scheduleStarts)){
            $scheduleStartsEpoch = strtotime($scheduleStarts);
            $showStartsEpoch = strtotime($this->getShowInstanceStart());

            $diff = $showStartsEpoch - $scheduleStartsEpoch;

            if ($diff != 0){
                $sql = "UPDATE cc_schedule"
                ." SET starts = starts + INTERVAL '$diff' second,"
                ." ends = ends + INTERVAL '$diff' second"
                ." WHERE instance_id = $instance_id";

                $CC_DBC->query($sql);
            }
        }
        Application_Model_RabbitMq::PushSchedule();
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
    private static function addDeltas($dateTime, $deltaDay, $deltaMin) {

        $newDateTime = clone $dateTime;

        $days = abs($deltaDay);
        $mins = abs($deltaMin);

        $dayInterval = new DateInterval("P{$days}D");
        $minInterval = new DateInterval("PT{$mins}M");

        if ($deltaDay > 0) {
            $newDateTime->add($dayInterval);
        }
        else if ($deltaDay < 0){
            $newDateTime->sub($dayInterval);
        }

        if ($deltaMin > 0) {
            $newDateTime->add($minInterval);
        }
        else if ($deltaMin < 0) {
            $newDateTime->sub($minInterval);
        }

        return $newDateTime;
    }

    public function moveShow($deltaDay, $deltaMin)
    {
        if ($this->getShow()->isRepeating()){
            return "Can't drag and drop repeating shows";
        }

        $today_timestamp = time();
        $startsDateTime = new DateTime($this->getShowInstanceStart(), new DateTimeZone("UTC"));
        $endsDateTime = new DateTime($this->getShowInstanceEnd(), new DateTimeZone("UTC"));

        if ($today_timestamp > $startsDateTime->getTimestamp()) {
            return "Can't move a past show";
        }

        //the user is moving the show on the calendar from the perspective of local time.
        //incase a show is moved across a time change border offsets should be added to the localtime
        //stamp and then converted back to UTC to avoid show time changes!
        $startsDateTime->setTimezone(new DateTimeZone(date_default_timezone_get()));
        $endsDateTime->setTimezone(new DateTimeZone(date_default_timezone_get()));

        $newStartsDateTime = self::addDeltas($startsDateTime, $deltaDay, $deltaMin);
        $newEndsDateTime = self::addDeltas($endsDateTime, $deltaDay, $deltaMin);

        //convert our new starts/ends to UTC.
        $newStartsDateTime->setTimezone(new DateTimeZone("UTC"));
        $newEndsDateTime->setTimezone(new DateTimeZone("UTC"));

        if ($today_timestamp > $newStartsDateTime->getTimestamp()) {
            return "Can't move show into past";
        }

        if ($this->isRecorded()) {

            //rebroadcasts should start at max 1 hour after a recorded show has ended.
            $minRebroadcastStart = self::addDeltas($newEndsDateTime, 0, 60);
            //check if we are moving a recorded show less than 1 hour before any of its own rebroadcasts.
            $rebroadcasts = CcShowInstancesQuery::create()
                ->filterByDbOriginalShow($this->_instanceId)
                ->filterByDbStarts($minRebroadcastStart->format('Y-m-d H:i:s'), Criteria::LESS_THAN)
                ->find();

            if (count($rebroadcasts) > 0) {
                return "Can't move a recorded show less than 1 hour before its rebroadcasts.";
            }
        }

        if ($this->isRebroadcast()) {

            try {
                $recordedShow = new Application_Model_ShowInstance($this->_showInstance->getDbOriginalShow());
            }
            //recorded show doesn't exist.
            catch (Exception $e) {
                $this->_showInstance->delete();
                return "Show was deleted because recorded show does not exist!";
            }

            $recordEndDateTime = new DateTime($recordedShow->getShowInstanceEnd(), new DateTimeZone("UTC"));
            $newRecordEndDateTime = self::addDeltas($recordEndDateTime, 0, 60);

            if ($newStartsDateTime->getTimestamp() < $newRecordEndDateTime->getTimestamp()) {
                return "Must wait 1 hour to rebroadcast.";
            }
        }

        $this->setShowStart($newStartsDateTime);
        $this->setShowEnd($newEndsDateTime);
        $this->correctScheduleStartTimes();

        $show = new Application_Model_Show($this->getShowId());
        if(!$show->isRepeating() && is_null($this->isRebroadcast())){
            $show->setShowFirstShow($newStartsDateTime);
            $show->setShowLastShow($newEndsDateTime);
        }

        Application_Model_RabbitMq::PushSchedule();
    }

    /*
     * FUNCTION SHOULD NOT BE CALLED
     * - we are removing ability to resize just a single show instance
     * -please use the resize method on the Show.php class.
     */
    public function resizeShow($deltaDay, $deltaMin)
    {
        global $CC_DBC;

        $hours = $deltaMin/60;
        if($hours > 0)
            $hours = floor($hours);
        else
            $hours = ceil($hours);

        $mins = abs($deltaMin%60);

        $today_timestamp = gmdate("Y-m-d H:i:s");
        $starts = $this->getShowInstanceStart();
        $ends = $this->getShowInstanceEnd();

        if(strtotime($today_timestamp) > strtotime($starts)) {
            return "can't resize a past show";
        }

        $sql = "SELECT timestamp '{$ends}' + interval '{$deltaDay} days' + interval '{$hours}:{$mins}'";
        $new_ends = $CC_DBC->GetOne($sql);

        //only need to check overlap if show increased in size.
        if(strtotime($new_ends) > strtotime($ends)) {

            $utcStartDateTime = new DateTime($ends, new DateTimeZone("UTC"));
            $utcEndDateTime = new DateTime($new_ends, new DateTimeZone("UTC"));

            $overlap =  Application_Model_Show::getShows($utcStartDateTime, $utcEndDateTime);

            if(count($overlap) > 0) {
                return "Should not overlap shows";
            }
        }
        //with overbooking no longer need to check already scheduled content still fits.

        //must update length of all rebroadcast instances.
        if($this->isRecorded()) {
            $sql = "UPDATE cc_show_instances SET ends = (ends + interval '{$deltaDay} days' + interval '{$hours}:{$mins}')
                    WHERE rebroadcast = 1 AND instance_id = {$this->_instanceId}";
            $CC_DBC->query($sql);
        }

        $this->setShowEnd($new_ends);
        Application_Model_RabbitMq::PushSchedule();
    }

    /**
     * Add a playlist as the last item of the current show.
     *
     * @param int $plId
     *         Playlist ID.
     */
    public function addPlaylistToShow($pl_id, $checkUserPerm = true)
    {
        $ts = intval($this->_showInstance->getDbLastScheduled("U")) ? : 0;
        $id = $this->_showInstance->getDbId();

        $scheduler = new Application_Model_Scheduler();
        $scheduler->scheduleAfter(
            array(array("id" => 0, "instance" => $id, "timestamp" => $ts)),
            array(array("id" => $pl_id, "type" => "playlist"))
        );
    }

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
    public function scheduleShow($plIds)
    {
        foreach ($plIds as $plId) {
            $this->addPlaylistToShow($plId);
        }
    }

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

        if (is_null($showInstances)){
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

    public function delete()
    {
        global $CC_DBC;

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


                if ($this->checkToDeleteShow($showId)){
                    CcShowQuery::create()
                        ->filterByDbId($showId)
                        ->delete();
                }
            }
            else {
                if ($this->isRebroadcast()) {
                    $this->_showInstance->delete();
                }
                else {
                    $show->delete();
                }
            }
        }

        Application_Model_RabbitMq::PushSchedule();
        if($recording){
            Application_Model_RabbitMq::SendMessageToShowRecorder("cancel_recording");
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
            }
            catch (Exception $e) {
                Logging::log("{$e->getFile()}");
                Logging::log("{$e->getLine()}");
                Logging::log("{$e->getMessage()}");
            }
        }
    }

    public function getTimeScheduled()
    {
        $time = $this->_showInstance->getDbTimeFilled();
		
        if ($time != "00:00:00") {
            $time_arr = explode(".", $time);
            if (count($time_arr) > 1) {
                $time_arr[1] = "." . $time_arr[1];
                $milliseconds = number_format(round($time_arr[1], 2), 2);
                $time = $time_arr[0] . substr($milliseconds, 1);
            }
            else {
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
        return Application_Model_Playlist::playlistTimeToSeconds($time_filled);
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

        $percent = ceil(($timeSeconds / $durationSeconds) * 100);

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

    public function getShowListContent()
    {
        global $CC_DBC;

        $sql = "SELECT *
            FROM (cc_schedule AS s LEFT JOIN cc_files AS f ON f.id = s.file_id)
            WHERE s.instance_id = '{$this->_instanceId}' AND s.playout_status >= 0
            ORDER BY starts";

        //Logging::log($sql);

        $results = $CC_DBC->GetAll($sql);

        foreach ($results as &$row) {

            $dt = new DateTime($row["starts"], new DateTimeZone("UTC"));
            $dt->setTimezone(new DateTimeZone(date_default_timezone_get()));
            $row["starts"] = $dt->format("Y-m-d H:i:s");

            $formatter = new LengthFormatter($row["clip_length"]);
            $row["clip_length"] = $formatter->format();
        }


        return $results;
    }
    
    public function getLastAudioItemEnd(){
		global $CC_DBC;

		$sql = "SELECT ends FROM cc_schedule "
			."WHERE instance_id = {$this->_instanceId} "
			."ORDER BY ends DESC "
			."LIMIT 1";

		return $CC_DBC->GetOne($sql);
	}

    public function getShowEndGapTime(){
		$showEnd = $this->getShowInstanceEnd();
		$lastItemEnd = $this->getLastAudioItemEnd();

		if (is_null($lastItemEnd)){
			$lastItemEnd = $this->getShowInstanceStart();
		}


		$diff = strtotime($showEnd) - strtotime($lastItemEnd);

		return ($diff < 0) ? 0 : $diff;
	}

    public static function GetLastShowInstance($p_timeNow){
        global $CC_CONFIG, $CC_DBC;

        $sql = "SELECT si.id"
        ." FROM $CC_CONFIG[showInstances] si"
        ." WHERE si.ends < TIMESTAMP '$p_timeNow'"
        ." AND si.modified_instance = 'f'"
        ." ORDER BY si.ends DESC"
        ." LIMIT 1";

        $id = $CC_DBC->GetOne($sql);
        if (is_null($id)){
            return null;
        } else {
            return new Application_Model_ShowInstance($id);
        }
    }

    public static function GetCurrentShowInstance($p_timeNow){
        global $CC_CONFIG, $CC_DBC;

        /* Orderby si.starts descending, because in some cases
         * we can have multiple shows overlapping each other. In
         * this case, the show that started later is the one that
         * is actually playing, and so this is the one we want.
         */

        $sql = "SELECT si.id"
        ." FROM $CC_CONFIG[showInstances] si"
        ." WHERE si.starts <= TIMESTAMP '$p_timeNow'"
        ." AND si.ends > TIMESTAMP '$p_timeNow'"
        ." AND si.modified_instance = 'f'"
        ." ORDER BY si.starts DESC"
        ." LIMIT 1";

        $id = $CC_DBC->GetOne($sql);
        if (is_null($id)){
            return null;
        } else {
            return new Application_Model_ShowInstance($id);
        }
    }

    public static function GetNextShowInstance($p_timeNow){
        global $CC_CONFIG, $CC_DBC;

        $sql = "SELECT si.id"
        ." FROM $CC_CONFIG[showInstances] si"
        ." WHERE si.starts > TIMESTAMP '$p_timeNow'"
        ." AND si.modified_instance = 'f'"
        ." ORDER BY si.starts"
        ." LIMIT 1";

        $id = $CC_DBC->GetOne($sql);
        if (is_null($id)){
            return null;
        } else {
            return new Application_Model_ShowInstance($id);
        }
    }

    // returns number of show instances that ends later than $day
    public static function GetShowInstanceCount($day){
        global $CC_CONFIG, $CC_DBC;
        $sql = "SELECT count(*) as cnt FROM $CC_CONFIG[showInstances] WHERE ends < '$day'";
        return $CC_DBC->GetOne($sql);
    }
    
    // this returns end timestamp of all shows that are in the range and has live DJ set up
    public static function GetEndTimeOfNextShowWithLiveDJ($p_startTime, $p_endTime){
        global $CC_CONFIG, $CC_DBC;
        
        $sql = "SELECT ends
				FROM cc_show_instances as si
                JOIN cc_show as sh ON si.show_id = sh.id
        		WHERE si.ends > '$p_startTime' and si.ends < '$p_endTime' and (sh.live_stream_using_airtime_auth or live_stream_using_custom_auth)
        		ORDER BY si.ends";
        
        return $CC_DBC->GetAll($sql);
    }
    
    function isRepeating(){
        if ($this->getShow()->isRepeating()){
            return true;
        }else{
            return false;
        }
    }
}
