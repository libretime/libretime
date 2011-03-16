<?php

class Show {

	private $_showId;

	public function __construct($showId=NULL)
    {
		$this->_showId = $showId;    
    }

    public function getName() {
        $show = CcShowQuery::create()->findPK($this->_showId);
        return $show->getDbName();
    }
    
    public function setName($name) {
        $show = CcShowQuery::create()->findPK($this->_showId);
        $show->setDbName($name);
    }

    public function getDescription() {
        $show = CcShowQuery::create()->findPK($this->_showId);
        return $show->getDbDescription();
    }
    
    public function setDescription($description) {
        $show = CcShowQuery::create()->findPK($this->_showId);
        $show->setDbDescription($description);
    }

    public function getColor() {
        $show = CcShowQuery::create()->findPK($this->_showId);
        return $show->getDbColor();
    }
    
    public function setColor($color) {
        $show = CcShowQuery::create()->findPK($this->_showId);
        $show->setDbColor($color);
    }

     public function getBackgroundColor() {
        $show = CcShowQuery::create()->findPK($this->_showId);
        return $show->getDbBackgroundColor();
    }
    
    public function setBackgroundColor($backgroundColor) {
        $show = CcShowQuery::create()->findPK($this->_showId);
        $show->setDbBackgroundColor($backgroundColor);
    }

    public function cancelShow($day_timestamp) {
        global $CC_DBC;

        $timeinfo = explode(" ", $day_timestamp);

        CcShowDaysQuery::create()
            ->filterByDbShowId($this->_showId)
            ->update(array('DbLastShow' => $timeinfo[0]));

        $sql = "DELETE FROM cc_show_instances
			        WHERE starts >= '{$day_timestamp}' AND show_id = {$this->_showId}";

        $CC_DBC->query($sql);
    }

	//end dates are non inclusive.
	public static function addShow($data) {
	
		$con = Propel::getConnection(CcShowPeer::DATABASE_NAME);

		$sql = "SELECT time '{$data['add_show_start_time']}' + INTERVAL '{$data['add_show_duration']} hour' ";
		$r = $con->query($sql);
        $endTime = $r->fetchColumn(0); 

		$sql = "SELECT EXTRACT(DOW FROM TIMESTAMP '{$data['add_show_start_date']} {$data['add_show_start_time']}')";
		$r = $con->query($sql);
        $startDow = $r->fetchColumn(0);  

		if($data['add_show_no_end']) {
			$endDate = NULL;
			$data['add_show_repeats'] = 1;
		}
		else if($data['add_show_repeats']) {
			$sql = "SELECT date '{$data['add_show_end_date']}' + INTERVAL '1 day' ";
			$r = $con->query($sql);
        	$endDate = $r->fetchColumn(0); 
		}
		else {
			$sql = "SELECT date '{$data['add_show_start_date']}' + INTERVAL '1 day' ";
			$r = $con->query($sql);
        	$endDate = $r->fetchColumn(0);
		} 

        //only want the day of the week from the start date.
		if(!$data['add_show_repeats']) {
			$data['add_show_day_check'] = array($startDow);
		}
        else if($data['add_show_repeats'] && $data['add_show_day_check'] == "") {
            $data['add_show_day_check'] = array($startDow);
        } 

        //find repeat type or set to a non repeating show.
        if($data['add_show_repeats']) {
            $repeat_type = $data["add_show_repeat_type"];
        }
        else {
            $repeat_type = -1;
        }

		$show = new CcShow();
		$show->setDbName($data['add_show_name']);
		$show->setDbDescription($data['add_show_description']);
		$show->setDbColor($data['add_show_color']);
		$show->setDbBackgroundColor($data['add_show_background_color']);
		$show->save();      

		$showId = $show->getDbId();

        if($data['add_show_record']){
            $isRecorded = 1;
        }
        else {
            $isRecorded = 0;
        }

        //don't set day for monthly repeat type, it's invalid.
        if($data['add_show_repeats'] && $data["add_show_repeat_type"] == 2) {

            $showDay = new CcShowDays();
	        $showDay->setDbFirstShow($data['add_show_start_date']);
	        $showDay->setDbLastShow($endDate);
	        $showDay->setDbStartTime($data['add_show_start_time']);
	        $showDay->setDbDuration($data['add_show_duration']);
            $showDay->setDbRepeatType($repeat_type);
	        $showDay->setDbShowId($showId);
            $showDay->setDbRecord($isRecorded);
	        $showDay->save();

        }
        else {

            foreach ($data['add_show_day_check'] as $day) {

			    if($startDow !== $day){
				
				    if($startDow > $day)
					    $daysAdd = 6 - $startDow + 1 + $day;
				    else
					    $daysAdd = $day - $startDow;				

				    $sql = "SELECT date '{$data['add_show_start_date']}' + INTERVAL '{$daysAdd} day' ";
				    $r = $con->query($sql);
				    $start = $r->fetchColumn(0); 
			    }
			    else {
				    $start = $data['add_show_start_date'];
			    }

                if(strtotime($start) < strtotime($endDate) || is_null($endDate)) {

			        $showDay = new CcShowDays();
			        $showDay->setDbFirstShow($start);
			        $showDay->setDbLastShow($endDate);
			        $showDay->setDbStartTime($data['add_show_start_time']);
			        $showDay->setDbDuration($data['add_show_duration']);
			        $showDay->setDbDay($day);
                    $showDay->setDbRepeatType($repeat_type);
			        $showDay->setDbShowId($showId);
                    $showDay->setDbRecord($isRecorded);
			        $showDay->save();
                }
		    }
        }

        //adding rows to cc_show_rebroadcast
        if($repeat_type != -1) {

            for($i=1; $i<=1; $i++) {

                $showRebroad = new CcShowRebroadcast();
                $showRebroad->setDbDayOffset($data['add_show_rebroadcast_date_'.$i]);
                $showRebroad->setDbStartTime($data['add_show_start_time_'.$i]);
                $showRebroad->setDbShowId($showId);
                $showRebroad->save();
            }
        }
        else {
            
            for($i=1; $i<=1; $i++) {

                if($data['add_show_rebroadcast_absolute_date_'.$i]) {

                    $sql = "SELECT date '{$data['add_show_rebroadcast_absolute_date_'.$i]}' - date '{$data['add_show_start_date']}' ";
				    $r = $con->query($sql);
				    $offset_days = $r->fetchColumn(0); 

                    $showRebroad = new CcShowRebroadcast();
                    $showRebroad->setDbDayOffset($offset_days." days");
                    $showRebroad->setDbStartTime($data['add_show_rebroadcast_absolute_time_'.$i]);
                    $showRebroad->setDbShowId($showId);
                    $showRebroad->save();
                }
            }
        }
		
        if(is_array($data['add_show_hosts'])) {
            //add selected hosts to cc_show_hosts table.
		    foreach ($data['add_show_hosts'] as $host) {
			    $showHost = new CcShowHosts();
			    $showHost->setDbShow($showId);
			    $showHost->setDbHost($host);
			    $showHost->save();
		    }
        }

        Show::populateShowUntilLastGeneratedDate($showId);
	}

    public static function getShows($start_timestamp, $end_timestamp, $excludeInstance=NULL, $onlyRecord=FALSE) {
        global $CC_DBC;

        $sql = "SELECT starts, ends, record, rebroadcast, instance_id, show_id, name, description, 
                color, background_color, cc_show_instances.id AS instance_id  
            FROM cc_show_instances 
            LEFT JOIN cc_show ON cc_show.id = cc_show_instances.show_id";

        //only want shows that are starting at the time or later.
        if($onlyRecord) {

            $sql = $sql." WHERE (starts >= '{$start_timestamp}' AND starts < timestamp '{$start_timestamp}' + interval '2 hours')";
            $sql = $sql." AND (record = TRUE)";
        }
        else {

            $sql = $sql." WHERE ((starts >= '{$start_timestamp}' AND starts < '{$end_timestamp}')
                OR (ends > '{$start_timestamp}' AND ends <= '{$end_timestamp}')
                OR (starts <= '{$start_timestamp}' AND ends >= '{$end_timestamp}'))";
        } 
            

        if(isset($excludeInstance)) {
            foreach($excludeInstance as $instance) {
                $sql_exclude[] = "cc_show_instances.id != {$instance}";
            }

            $exclude = join(" OR ", $sql_exclude);

            $sql = $sql." AND ({$exclude})";
        }

		//echo $sql;
		return $CC_DBC->GetAll($sql);
    }

    private static function setNextPop($next_date, $show_id, $day) {

        $nextInfo = explode(" ", $next_date);

        $repeatInfo = CcShowDaysQuery::create()
            ->filterByDbShowId($show_id)
            ->filterByDbDay($day)
            ->findOne();

        $repeatInfo->setDbNextPopDate($nextInfo[0])
            ->save();
    }

    //for a show with repeat_type == -1
    private static function populateNonRepeatingShow($show_id, $first_show, $start_time, $duration, $day, $record, $end_timestamp) {
        global $CC_DBC;
       
        $next_date = $first_show." ".$start_time;
       
        if(strtotime($next_date) < strtotime($end_timestamp)) {
            
            $start = $next_date;
            
            $sql = "SELECT timestamp '{$start}' + interval '{$duration}'";
		    $end = $CC_DBC->GetOne($sql);

            $newShow = new CcShowInstances();
		    $newShow->setDbShowId($show_id);
            $newShow->setDbStarts($start);
            $newShow->setDbEnds($end);
            $newShow->setDbRecord($record);
		    $newShow->save();

            $show_instance_id = $newShow->getDbId();

            $sql = "SELECT * FROM cc_show_rebroadcast WHERE show_id={$show_id}";
		    $rebroadcasts = $CC_DBC->GetAll($sql);

            foreach($rebroadcasts as $rebroadcast) {

                $timeinfo = explode(" ", $start);
                
                $sql = "SELECT timestamp '{$timeinfo[0]}' + interval '{$rebroadcast["day_offset"]}' + interval '{$rebroadcast["start_time"]}'";
		        $rebroadcast_start_time = $CC_DBC->GetOne($sql);
               
                $sql = "SELECT timestamp '{$rebroadcast_start_time}' + interval '{$duration}'";
		        $rebroadcast_end_time = $CC_DBC->GetOne($sql);

                $newRebroadcastInstance = new CcShowInstances();
                $newRebroadcastInstance->setDbShowId($show_id);
                $newRebroadcastInstance->setDbStarts($rebroadcast_start_time);
                $newRebroadcastInstance->setDbEnds($rebroadcast_end_time);
                $newRebroadcastInstance->setDbRecord(0);
                $newRebroadcastInstance->setDbRebroadcast(1);
                $newRebroadcastInstance->setDbOriginalShow($show_instance_id);
		        $newRebroadcastInstance->save();
            }
        }
    }

    //for a show with repeat_type == 0,1,2
    private static function populateRepeatingShow($show_id, $next_pop_date, $first_show, $last_show, 
                                $start_time, $duration, $day, $record, $end_timestamp, $interval) {
        global $CC_DBC;        

        if(isset($next_pop_date)) {
            $next_date = $next_pop_date." ".$start_time;
        }
        else {
            $next_date = $first_show." ".$start_time;
        }

        $sql = "SELECT * FROM cc_show_rebroadcast WHERE show_id={$show_id}";
		$rebroadcasts = $CC_DBC->GetAll($sql);

        while(strtotime($next_date) < strtotime($end_timestamp) && (strtotime($last_show) > strtotime($next_date) || is_null($last_show))) {
            
            $start = $next_date;
            
            $sql = "SELECT timestamp '{$start}' + interval '{$duration}'";
		    $end = $CC_DBC->GetOne($sql);

            $newShow = new CcShowInstances();
		    $newShow->setDbShowId($show_id);
            $newShow->setDbStarts($start);
            $newShow->setDbEnds($end);
            $newShow->setDbRecord($record);
		    $newShow->save();

            $show_instance_id = $newShow->getDbId();

            foreach($rebroadcasts as $rebroadcast) {

                $timeinfo = explode(" ", $next_date);
                
                $sql = "SELECT timestamp '{$timeinfo[0]}' + interval '{$rebroadcast["day_offset"]}' + interval '{$rebroadcast["start_time"]}'";
		        $rebroadcast_start_time = $CC_DBC->GetOne($sql);
               
                $sql = "SELECT timestamp '{$rebroadcast_start_time}' + interval '{$duration}'";
		        $rebroadcast_end_time = $CC_DBC->GetOne($sql);

                $newRebroadcastInstance = new CcShowInstances();
                $newRebroadcastInstance->setDbShowId($show_id);
                $newRebroadcastInstance->setDbStarts($rebroadcast_start_time);
                $newRebroadcastInstance->setDbEnds($rebroadcast_end_time);
                $newRebroadcastInstance->setDbRecord(0);
                $newRebroadcastInstance->setDbRebroadcast(1);
                $newRebroadcastInstance->setDbOriginalShow($show_instance_id);
		        $newRebroadcastInstance->save();
            }

            $sql = "SELECT timestamp '{$start}' + interval '{$interval}'";
		    $next_date = $CC_DBC->GetOne($sql);
        }

        Show::setNextPop($next_date, $show_id, $day);
    }

    private static function populateShow($repeat_type, $show_id, $next_pop_date, 
                $first_show, $last_show, $start_time, $duration, $day, $record, $end_timestamp) {

        if($repeat_type == -1) {
            Show::populateNonRepeatingShow($show_id, $first_show, $start_time, $duration, $day, $record, $end_timestamp);
        }
        else if($repeat_type == 0) {
            Show::populateRepeatingShow($show_id, $next_pop_date, $first_show, $last_show, 
                    $start_time, $duration, $day, $record, $end_timestamp, '7 days');
        }
        else if($repeat_type == 1) {
            Show::populateRepeatingShow($show_id, $next_pop_date, $first_show, $last_show, 
                    $start_time, $duration, $day, $record, $end_timestamp, '14 days');
        }
        else if($repeat_type == 2) {
            Show::populateRepeatingShow($show_id, $next_pop_date, $first_show, $last_show, 
                    $start_time, $duration, $day, $record, $end_timestamp, '1 month');
        }
    } 

    //used to catch up a newly added show
    private static function populateShowUntilLastGeneratedDate($show_id) {
        global $CC_DBC;
        $showsPopUntil = Application_Model_Preference::GetShowsPopulatedUntil();
  
        $sql = "SELECT * FROM cc_show_days WHERE show_id = {$show_id}";
		$res = $CC_DBC->GetAll($sql); 

        foreach($res as $row) {
            Show::populateShow($row["repeat_type"], $row["show_id"], $row["next_pop_date"], $row["first_show"], 
                                    $row["last_show"], $row["start_time"], $row["duration"], $row["day"], $row["record"], $showsPopUntil);    
        } 
    }

    public static function populateShowsUntil($pop_timestamp, $end_timestamp) {
        global $CC_DBC;

        if($pop_timestamp != "") {
            $sql = "SELECT * FROM cc_show_days 
                WHERE last_show IS NULL 
                    OR first_show < '{$end_timestamp}' AND last_show > '{$pop_timestamp}'";
        }
        else {
            $today_timestamp = date("Y-m-d");

            $sql = "SELECT * FROM cc_show_days 
                WHERE last_show IS NULL 
                    OR first_show < '{$end_timestamp}' AND last_show > '{$today_timestamp}'";
        }

		$res = $CC_DBC->GetAll($sql); 

        foreach($res as $row) {
            Show::populateShow($row["repeat_type"], $row["show_id"], $row["next_pop_date"], $row["first_show"], 
                                    $row["last_show"], $row["start_time"], $row["duration"], $row["day"], $row["record"], $end_timestamp);    
        }    
    }

    public static function getFullCalendarEvents($start, $end, $editable=false) {

        $events = array();
        $showsPopUntil = Application_Model_Preference::GetShowsPopulatedUntil();

        //if fullcalendar is requesting shows past our previous populated until date, generate shows up until this point.
        if($showsPopUntil == "" || strtotime($showsPopUntil) < strtotime($end)) {

            Show::populateShowsUntil($showsPopUntil, $end);
            Application_Model_Preference::SetShowsPopulatedUntil($end);
        }

        $shows = Show::getShows($start, $end);

        $today_timestamp = date("Y-m-d H:i:s");
        foreach ($shows as $show) {
            if($editable && strtotime($today_timestamp) < strtotime($show["starts"]))
			    $events[] = Show::makeFullCalendarEvent($show, array("editable" => true));
            else
                $events[] = Show::makeFullCalendarEvent($show);
        }

        return $events;
    }

	private static function makeFullCalendarEvent($show, $options=array()) {
		global $CC_DBC;
    
        $event = array();

        if($show["rebroadcast"]) {
            $title = "REBROADCAST ".$show["name"];
            $event["disableResizing"] = true;
        }
        else {
            $title = $show["name"];
        }

		$event["id"] = $show["instance_id"];
		$event["title"] = $title;
		$event["start"] = $show["starts"];
		$event["end"] = $show["ends"];
		$event["allDay"] = false;
		$event["description"] = $show["description"];
		$event["color"] = $show["color"];
		$event["backgroundColor"] = $show["background_color"];
        $event["showId"] = $show["show_id"];
        $event["record"] = intval($show["record"]);
        $event["rebroadcast"] = intval($show["rebroadcast"]);

		foreach($options as $key=>$value) {
			$event[$key] = $value;
		}

		$percent = Schedule::GetPercentScheduled($show["instance_id"], $show["starts"], $show["ends"]);
		$event["percent"] = $percent;

		return $event;
	}
}

class ShowInstance {

	private $_instanceId;

	public function __construct($instanceId)
    {
		$this->_instanceId = $instanceId;    
    }

    public function getShowId() {
        $showInstance = CcShowInstancesQuery::create()->findPK($this->_instanceId);
        return $showInstance->getDbShowId();
    }

    public function getShowInstanceId() {
        return $this->_instanceId;
    }

    public function isRebroadcast() {
        $showInstance = CcShowInstancesQuery::create()->findPK($this->_instanceId);
        return $showInstance->getDbRebroadcast();
    }

    public function isRecorded() {
        $showInstance = CcShowInstancesQuery::create()->findPK($this->_instanceId);
        return $showInstance->getDbRecord();
    }

    public function getName() {
        $show = CcShowQuery::create()->findPK($this->getShowId());
        return $show->getDbName();
    }

    public function getShowStart() {
        $showInstance = CcShowInstancesQuery::create()->findPK($this->_instanceId);
        return $showInstance->getDbStarts();
    }

    public function getShowEnd() {
        $showInstance = CcShowInstancesQuery::create()->findPK($this->_instanceId);
        return $showInstance->getDbEnds();
    }

    public function setShowStart($start) {
        $showInstance = CcShowInstancesQuery::create()->findPK($this->_instanceId);
        $showInstance->setDbStarts($start)
            ->save();
    }

    public function setShowEnd($end) {
        $showInstance = CcShowInstancesQuery::create()->findPK($this->_instanceId);
        $showInstance->setDbEnds($end)
            ->save();   
    }

    public function moveScheduledShowContent($deltaDay, $deltaHours, $deltaMin) {
        global $CC_DBC;

        $sql = "UPDATE cc_schedule
                   SET starts = (starts + interval '{$deltaDay} days' + interval '{$deltaHours}:{$deltaMin}'),
                        ends = (ends + interval '{$deltaDay} days' + interval '{$deltaHours}:{$deltaMin}')
                   WHERE instance_id = '{$this->_instanceId}'";

        $CC_DBC->query($sql);
    }

    public function moveShow($deltaDay, $deltaMin){
		global $CC_DBC;

		$hours = $deltaMin/60;
		if($hours > 0)
			$hours = floor($hours);
		else
			$hours = ceil($hours);

		$mins = abs($deltaMin%60);

        $starts = $this->getShowStart();
        $ends = $this->getShowEnd(); 

		$sql = "SELECT timestamp '{$starts}' + interval '{$deltaDay} days' + interval '{$hours}:{$mins}'";
		$new_starts = $CC_DBC->GetOne($sql);

		$sql = "SELECT timestamp '{$ends}' + interval '{$deltaDay} days' + interval '{$hours}:{$mins}'";
		$new_ends = $CC_DBC->GetOne($sql);

        $today_timestamp = date("Y-m-d H:i:s");
        if(strtotime($today_timestamp) > strtotime($new_starts)) {
            return "can't move show into past";
        }

		$overlap = Show::getShows($new_starts, $new_ends, array($this->_instanceId));

		if(count($overlap) > 0) {
			return "Should not overlap shows";
		}
    
        $this->moveScheduledShowContent($deltaDay, $hours, $mins);
        $this->setShowStart($new_starts);
        $this->setShowEnd($new_ends);	
	}

	public function resizeShow($deltaDay, $deltaMin){
		global $CC_DBC;

		$hours = $deltaMin/60;
		if($hours > 0)
			$hours = floor($hours);
		else
			$hours = ceil($hours);

		$mins = abs($deltaMin%60);

        $starts = $this->getShowStart();
        $ends = $this->getShowEnd(); 

		$sql = "SELECT timestamp '{$ends}' + interval '{$deltaDay} days' + interval '{$hours}:{$mins}'";
		$new_ends = $CC_DBC->GetOne($sql);

        //only need to check overlap if show increased in size.
        if(strtotime($new_ends) > strtotime($ends)) {
		    $overlap =  Show::getShows($ends, $new_ends);

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
	}

	private function getLastGroupId() {
		global $CC_DBC;

		$sql = "SELECT group_id FROM cc_schedule WHERE instance_id = '{$this->_instanceId}' ORDER BY ends DESC LIMIT 1";
		$res = $CC_DBC->GetOne($sql);	

		return $res;
	}

	public function addPlaylistToShow($plId) {
		
		$sched = new ScheduleGroup();
		$lastGroupId = $this->getLastGroupId();
        
		if(is_null($lastGroupId)) {

			$groupId = $sched->add($this->_instanceId, $this->getShowStart(), null, $plId);		
		}
		else {
			$groupId = $sched->addPlaylistAfter($this->_instanceId, $lastGroupId, $plId);
		}
	}

	public function scheduleShow($plIds) {
		
		foreach($plIds as $plId) {
			$this->addPlaylistToShow($plId);
		}
	}

	public function removeGroupFromShow($group_id){
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
	}

    public function clearShow() {
		
		CcScheduleQuery::create()
			->filterByDbInstanceId($this->_instanceId)
			->delete();
	}

    public function deleteShow() {
		
        CcShowInstancesQuery::create()
            ->findPK($this->_instanceId)
            ->delete();
	}

    public function getTimeScheduled() {

        $instance_id = $this->getShowInstanceId();
		$time = Schedule::GetTotalShowTime($instance_id);

		return $time;
	}

	public function getTimeUnScheduled() {

        $start_timestamp = $this->getShowStart(); 
        $end_timestamp = $this->getShowEnd();
        $instance_id = $this->getShowInstanceId();

		$time = Schedule::getTimeUnScheduledInRange($instance_id, $start_timestamp, $end_timestamp);

		return $time;
	}

    public function getPercentScheduled() {

        $start_timestamp = $this->getShowStart(); 
        $end_timestamp = $this->getShowEnd();
        $instance_id = $this->getShowInstanceId();

        return Schedule::GetPercentScheduled($instance_id, $start_timestamp, $end_timestamp);
    }

    public function getShowLength() {
		global $CC_DBC;

        $start_timestamp = $this->getShowStart(); 
        $end_timestamp = $this->getShowEnd();

		$sql = "SELECT TIMESTAMP '{$end_timestamp}' - TIMESTAMP '{$start_timestamp}' ";
		$length = $CC_DBC->GetOne($sql);

		return $length;
	}

	public function searchPlaylistsForShow($datatables){

		$time_remaining = $this->getTimeUnScheduled();

		return StoredFile::searchPlaylistsForSchedule($time_remaining, $datatables);
	}

    public function getShowListContent() {
        global $CC_DBC;

		$sql = "SELECT * 
			FROM (cc_schedule AS s LEFT JOIN cc_files AS f ON f.id = s.file_id
				LEFT JOIN cc_playlist AS p ON p.id = s.playlist_id )

			WHERE s.instance_id = '{$this->_instanceId}' ORDER BY starts";

		return $CC_DBC->GetAll($sql);	
    }

	public function getShowContent() {
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
}

/* Show Data Access Layer */
class Show_DAL{
        
    public static function GetCurrentShow($timeNow) {
        global $CC_CONFIG, $CC_DBC;
        
		$timestamp = explode(" ", $timeNow);
		$date = $timestamp[0];
		$time = $timestamp[1];
        
        $sql = "SELECT si.starts as start_timestamp, si.ends as end_timestamp, s.name, s.id, si.id as instance_id, si.record"
        ." FROM $CC_CONFIG[showInstances] si, $CC_CONFIG[showTable] s"
        ." WHERE si.show_id = s.id"
        ." AND si.starts <= TIMESTAMP '$timeNow'"
        ." AND si.ends > TIMESTAMP '$timeNow'";
        
        $rows = $CC_DBC->GetAll($sql);
        return $rows;
    }
    
    public static function GetNextShow($timeNow) {
        global $CC_CONFIG, $CC_DBC;
        
		$sql = "SELECT *, si.starts as start_timestamp, si.ends as end_timestamp FROM "
		." $CC_CONFIG[showInstances] si, $CC_CONFIG[showTable] s"
		." WHERE si.show_id = s.id"
		." AND si.starts >= TIMESTAMP '$timeNow'"
		." AND si.starts < TIMESTAMP '$timeNow' + INTERVAL '48 hours'"
        ." ORDER BY si.starts"
        ." LIMIT 1";
                
        $rows = $CC_DBC->GetAll($sql);
        return $rows;
    }

    public static function GetShowsInRange($timeNow, $start, $end){
        global $CC_CONFIG, $CC_DBC;
		$sql = "SELECT"
        ." si.starts as show_starts,"
        ." si.ends as show_ends,"
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
        //checking for st.starts IS NULL so that the query also returns shows that do not have any items scheduled.
        ." AND (st.starts < si.ends OR st.starts IS NULL)"
        ." ORDER BY st.starts";

        return $CC_DBC->GetAll($sql);
    }
    
}
