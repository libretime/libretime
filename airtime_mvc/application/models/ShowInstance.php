<?php

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

    public function getShowStart()
    {
        return $this->_showInstance->getDbStarts();
    }

    public function getShowEnd()
    {
        return $this->_showInstance->getDbEnds();
    }

    public function getStartDate()
    {
        $showStart = $this->getShowStart();
        $showStartExplode = explode(" ", $showStart);
        return $showStartExplode[0];
    }

    public function getStartTime()
    {
        $showStart = $this->getShowStart();
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
        RabbitMq::PushSchedule();
    }

    public function setShowEnd($end)
    {
        $this->_showInstance->setDbEnds($end)
            ->save();
        RabbitMq::PushSchedule();
    }

    public function updateScheduledTime()
    {
        $con = Propel::getConnection(CcShowInstancesPeer::DATABASE_NAME);
        $this->_showInstance->updateDbTimeFilled($con);
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
            $showStartsEpoch = strtotime($this->getShowStart());

            $diff = $showStartsEpoch - $scheduleStartsEpoch;

            if ($diff != 0){
                $sql = "UPDATE cc_schedule"
                ." SET starts = starts + INTERVAL '$diff' second,"
                ." ends = ends + INTERVAL '$diff' second"
                ." WHERE instance_id = $instance_id";

                $CC_DBC->query($sql);
            }
        }
        RabbitMq::PushSchedule();
    }

    public function moveShow($deltaDay, $deltaMin)
    {
        global $CC_DBC;
        
        if ($this->getShow()->isRepeating()){
            return "Can't drag and drop repeating shows";
        }

        $hours = $deltaMin/60;
        if($hours > 0)
            $hours = floor($hours);
        else
            $hours = ceil($hours);

        $mins = abs($deltaMin%60);

        $today_timestamp = time();
        $starts = $this->getShowStart();
        $ends = $this->getShowEnd();
        
        $startsDateTime = new DateTime($starts, new DateTimeZone("UTC"));

        if($today_timestamp > $startsDateTime->getTimestamp()) {
            return "Can't move a past show";
        }

        $sql = "SELECT timestamp '{$starts}' + interval '{$deltaDay} days' + interval '{$hours}:{$mins}'";
        $new_starts = $CC_DBC->GetOne($sql);

        $sql = "SELECT timestamp '{$ends}' + interval '{$deltaDay} days' + interval '{$hours}:{$mins}'";
        $new_ends = $CC_DBC->GetOne($sql);

        $newStartsDateTime = new DateTime($new_starts, new DateTimeZone("UTC"));
        if($today_timestamp > $newStartsDateTime->getTimestamp()) {
            return "Can't move show into past";
        }

        $overlap = Application_Model_Show::getShows($new_starts, $new_ends, array($this->_instanceId));
        
        if(count($overlap) > 0) {
            return "Should not overlap shows";
        }

        $rebroadcast = $this->isRebroadcast();
        if($rebroadcast) {
            $sql = "SELECT timestamp '{$new_starts}' < (SELECT starts FROM cc_show_instances WHERE id = {$rebroadcast})";
            $isBeforeRecordedOriginal = $CC_DBC->GetOne($sql);

            if($isBeforeRecordedOriginal === 't'){
                return "Cannot move a rebroadcast show before its original";
            }
        }

        $this->setShowStart($new_starts);
        $this->setShowEnd($new_ends);
        $this->correctScheduleStartTimes();
        
        $show = new Application_Model_Show($this->getShowId());
        if(!$show->isRepeating()){
            $show->setShowFirstShow($new_starts);
            $show->setShowLastShow($new_ends);
        }
        
        RabbitMq::PushSchedule();
    }

    public function resizeShow($deltaDay, $deltaMin)
    {
        global $CC_DBC;

        $hours = $deltaMin/60;
        if($hours > 0)
            $hours = floor($hours);
        else
            $hours = ceil($hours);

        $mins = abs($deltaMin%60);

        $today_timestamp = date("Y-m-d H:i:s");
        $starts = $this->getShowStart();
        $ends = $this->getShowEnd();

        if(strtotime($today_timestamp) > strtotime($starts)) {
            return "can't resize a past show";
        }

        $sql = "SELECT timestamp '{$ends}' + interval '{$deltaDay} days' + interval '{$hours}:{$mins}'";
        $new_ends = $CC_DBC->GetOne($sql);

        //only need to check overlap if show increased in size.
        if(strtotime($new_ends) > strtotime($ends)) {
            $overlap =  Application_Model_Show::getShows($ends, $new_ends);

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
        RabbitMq::PushSchedule();
    }

    /**
     * Get the group ID for this show.
     *
     */
    private function getLastGroupId()
    {
        global $CC_DBC;
        $sql = "SELECT group_id FROM cc_schedule WHERE instance_id = '{$this->_instanceId}' ORDER BY ends DESC LIMIT 1";
        $res = $CC_DBC->GetOne($sql);
        return $res;
    }

    /**
     * Add a playlist as the last item of the current show.
     *
     * @param int $plId
     *         Playlist ID.
     */
    public function addPlaylistToShow($plId)
    {
        $sched = new ScheduleGroup();
        $lastGroupId = $this->getLastGroupId();

        if (is_null($lastGroupId)) {
            $groupId = $sched->add($this->_instanceId, $this->getShowStart(), null, $plId);
        }
        else {
            $groupId = $sched->addPlaylistAfter($this->_instanceId, $lastGroupId, $plId);
        }
        RabbitMq::PushSchedule();
        $this->updateScheduledTime();
    }

    /**
     * Add a media file as the last item in the show.
     *
     * @param int $file_id
     */
    public function addFileToShow($file_id)
    {
        $sched = new ScheduleGroup();
        $lastGroupId = $this->getLastGroupId();

        if (is_null($lastGroupId)) {
            $groupId = $sched->add($this->_instanceId, $this->getShowStart(), $file_id);
        }
        else {
            $groupId = $sched->addFileAfter($this->_instanceId, $lastGroupId, $file_id);
        }
        RabbitMq::PushSchedule();
        $this->updateScheduledTime();
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

    public function removeGroupFromShow($group_id)
    {
        global $CC_DBC;

        $sql = "SELECT MAX(ends) as end_timestamp, (MAX(ends) - MIN(starts)) as length
                    FROM cc_schedule
                    WHERE group_id = '{$group_id}'";

        $groupBoundry = $CC_DBC->GetRow($sql);

        $group = CcScheduleQuery::create()
            ->filterByDbGroupId($group_id)
            ->delete();

        $sql = "UPDATE cc_schedule
                    SET starts = (starts - INTERVAL '{$groupBoundry["length"]}'), ends = (ends - INTERVAL '{$groupBoundry["length"]}')
                    WHERE starts >= '{$groupBoundry["end_timestamp"]}' AND instance_id = {$this->_instanceId}";

        $CC_DBC->query($sql);
        RabbitMq::PushSchedule();
        $this->updateScheduledTime();
    }

    public function clearShow()
    {
        CcScheduleQuery::create()
            ->filterByDbInstanceId($this->_instanceId)
            ->delete();
        RabbitMq::PushSchedule();
        $this->updateScheduledTime();
    }

    public function deleteShow()
    {
        // see if it was recording show
        $recording = CcShowInstancesQuery::create()
            ->findPK($this->_instanceId)
            ->getDbRecord();
        CcShowInstancesQuery::create()
            ->findPK($this->_instanceId)
            ->delete();
        RabbitMq::PushSchedule();
        if($recording){
            RabbitMq::SendMessageToShowRecorder("cancel_recording");
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

            $rebroad = new Application_Model_ShowInstance($rebroadcast->getDbId());
            $rebroad->addFileToShow($file_id);
        }
    }

    public function getTimeScheduled()
    {
        $time = $this->_showInstance->getDbTimeFilled();

        if(is_null($time)) {
            $time = "00:00:00";
        }
        return $time;
    }

    public function getPercentScheduled()
    {
        $start_timestamp = $this->getShowStart();
        $end_timestamp = $this->getShowEnd();
        $time_filled = $this->getTimeScheduled();

        $s_epoch = strtotime($start_timestamp);
        $e_epoch = strtotime($end_timestamp);
        $i_epoch = Schedule::WallTimeToMillisecs($time_filled) / 1000;

        $percent = ceil(($i_epoch / ($e_epoch - $s_epoch)) * 100);

        if ($percent > 100)
            $percent = 100;

        return $percent;
    }

    public function getShowLength()
    {
        global $CC_DBC;

        $start_timestamp = $this->getShowStart();
        $end_timestamp = $this->getShowEnd();

        $sql = "SELECT TIMESTAMP '{$end_timestamp}' - TIMESTAMP '{$start_timestamp}' ";
        $length = $CC_DBC->GetOne($sql);

        return $length;
    }

    public function searchPlaylistsForShow($datatables)
    {
        return Application_Model_StoredFile::searchPlaylistsForSchedule($datatables);
    }

    public function getShowListContent()
    {
        global $CC_DBC;

        $sql = "SELECT *
            FROM (cc_schedule AS s LEFT JOIN cc_files AS f ON f.id = s.file_id
                LEFT JOIN cc_playlist AS p ON p.id = s.playlist_id )

            WHERE s.instance_id = '{$this->_instanceId}' ORDER BY starts";

        return $CC_DBC->GetAll($sql);
    }

    public function getShowContent()
    {
        global $CC_DBC;

        $res = $this->getShowListContent();

        if(count($res) <= 0) {
            return $res;
        }

        $items = array();
        $currGroupId = -1;
        $pl_counter = -1;
        $f_counter = -1;
        foreach ($res as $row) {
            if($currGroupId != $row["group_id"]){
                $currGroupId = $row["group_id"];
                $pl_counter = $pl_counter + 1;
                $f_counter = -1;

                $items[$pl_counter]["pl_name"] = $row["name"];
                $items[$pl_counter]["pl_creator"] = $row["creator"];
                $items[$pl_counter]["pl_description"] = $row["description"];
                $items[$pl_counter]["pl_group"] = $row["group_id"];

                $sql = "SELECT SUM(clip_length) FROM cc_schedule WHERE group_id = '{$currGroupId}'";
                $length = $CC_DBC->GetOne($sql);

                $items[$pl_counter]["pl_length"] = $length;
            }
            $f_counter = $f_counter + 1;

            $items[$pl_counter]["pl_content"][$f_counter]["f_name"] = $row["track_title"];
            $items[$pl_counter]["pl_content"][$f_counter]["f_artist"] = $row["artist_name"];
            $items[$pl_counter]["pl_content"][$f_counter]["f_length"] = $row["length"];
        }

        return $items;
    }

    public static function GetShowsInstancesIdsInRange($p_timeNow, $p_start, $p_end)
    {
		global $CC_DBC;

		$sql = "SELECT id FROM cc_show_instances AS si "
			."WHERE ("
			."(si.starts < TIMESTAMP '$p_timeNow' - INTERVAL '$p_start seconds' "
			."AND si.ends > TIMESTAMP '$p_timeNow' - INTERVAL '$p_start seconds') "
			."OR (si.starts > TIMESTAMP '$p_timeNow' - INTERVAL '$p_start seconds' "
			."AND si.ends < TIMESTAMP '$p_timeNow' + INTERVAL '$p_end seconds') "
			."OR (si.starts < TIMESTAMP '$p_timeNow' + INTERVAL '$p_end seconds' "
			."AND si.ends > TIMESTAMP '$p_timeNow' + INTERVAL '$p_end seconds') "
			.") "
			." ORDER BY si.starts";

		$rows = $CC_DBC->GetAll($sql);
		return $rows;
	}

    public function getScheduleItemsInRange($timeNow, $start, $end)
    {
        global $CC_DBC, $CC_CONFIG;

        $instanceId = $this->_instanceId;

        $sql = "SELECT"
        ." si.starts as show_starts,"
        ." si.ends as show_ends,"
        ." si.rebroadcast as rebroadcast,"
        ." st.starts as item_starts,"
        ." st.ends as item_ends,"
        ." st.clip_length as clip_length,"
        ." ft.track_title as track_title,"
        ." ft.artist_name as artist_name,"
        ." ft.album_title as album_title,"
        ." s.name as show_name,"
        ." si.id as instance_id,"
        ." pt.name as playlist_name"
        ." FROM $CC_CONFIG[showInstances] si"
        ." LEFT JOIN $CC_CONFIG[scheduleTable] st"
        ." ON st.instance_id = si.id"
        ." LEFT JOIN $CC_CONFIG[playListTable] pt"
        ." ON st.playlist_id = pt.id"
        ." LEFT JOIN $CC_CONFIG[filesTable] ft"
        ." ON st.file_id = ft.id"
        ." LEFT JOIN $CC_CONFIG[showTable] s"
        ." ON si.show_id = s.id"
        ." WHERE ((si.starts < TIMESTAMP '$timeNow' - INTERVAL '$start seconds' AND si.ends > TIMESTAMP '$timeNow' - INTERVAL '$start seconds')"
        ." OR (si.starts > TIMESTAMP '$timeNow' - INTERVAL '$start seconds' AND si.ends < TIMESTAMP '$timeNow' + INTERVAL '$end seconds')"
        ." OR (si.starts < TIMESTAMP '$timeNow' + INTERVAL '$end seconds' AND si.ends > TIMESTAMP '$timeNow' + INTERVAL '$end seconds'))"
        ." AND (st.starts < si.ends)"
        ." AND si.id = $instanceId"
        ." ORDER BY si.starts, st.starts";

        return $CC_DBC->GetAll($sql);
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
		$showEnd = $this->getShowEnd();
		$lastItemEnd = $this->getLastAudioItemEnd();

		if (is_null($lastItemEnd)){
			$lastItemEnd = $this->getShowStart();
		}


		$diff = strtotime($showEnd) - strtotime($lastItemEnd);

		return ($diff < 0) ? 0 : $diff;
	}

    public static function GetLastShowInstance($p_timeNow){
        global $CC_CONFIG, $CC_DBC;

        $sql = "SELECT si.id"
        ." FROM $CC_CONFIG[showInstances] si"
        ." WHERE si.ends < TIMESTAMP '$p_timeNow'"
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

        $sql = "SELECT si.id"
        ." FROM $CC_CONFIG[showInstances] si"
        ." WHERE si.starts <= TIMESTAMP '$p_timeNow'"
        ." AND si.ends > TIMESTAMP '$p_timeNow'"
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
}
