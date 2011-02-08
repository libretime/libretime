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
			    $showDay->save();
            }
		}
		
		foreach ($data['add_show_hosts'] as $host) {
			$showHost = new CcShowHosts();
			$showHost->setDbShow($showId);
			$showHost->setDbHost($host);
			$showHost->save();
		}

        Show::populateShowUntilLastGeneratedDate($showId);
	}

    public static function getShows($start_timestamp, $end_timestamp, $excludeInstance=NULL) {
        global $CC_DBC;

        $sql = "SELECT starts, ends, show_id, name, description, color, background_color, cc_show_instances.id AS instance_id  
            FROM cc_show_instances 
            LEFT JOIN cc_show ON cc_show.id = cc_show_instances.show_id 
            WHERE ((starts >= '{$start_timestamp}' AND starts < '{$end_timestamp}')
                OR (ends > '{$start_timestamp}' AND ends <= '{$end_timestamp}')
                OR (starts <= '{$start_timestamp}' AND ends >= '{$end_timestamp}'))";

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

     //for a show with repeat_type == -1
    private static function populateNonRepeatingShow($show_id, $first_show, $start_time, $duration, $day, $end_timestamp) {
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
		    $newShow->save();
        }
    }

    //for a show with repeat_type == 0
    private static function populateWeeklyShow($show_id, $next_pop_date, $first_show, $last_show, $start_time, $duration, $day, $end_timestamp) {
        global $CC_DBC;        

        if(isset($next_pop_date)) {
            $next_date = $next_pop_date." ".$start_time;
        }
        else {
            $next_date = $first_show." ".$start_time;
        }

        while(strtotime($next_date) < strtotime($end_timestamp) && (strtotime($last_show) > strtotime($next_date) || is_null($last_show))) {
            
            $start = $next_date;
            
            $sql = "SELECT timestamp '{$start}' + interval '{$duration}'";
		    $end = $CC_DBC->GetOne($sql);

            $newShow = new CcShowInstances();
		    $newShow->setDbShowId($show_id);
            $newShow->setDbStarts($start);
            $newShow->setDbEnds($end);
		    $newShow->save();

            $sql = "SELECT timestamp '{$start}' + interval '7 days'";
		    $next_date = $CC_DBC->GetOne($sql);
        }

        $nextInfo = explode(" ", $next_date);

        $repeatInfo = CcShowDaysQuery::create()
            ->filterByDbShowId($show_id)
            ->filterByDbDay($day)
            ->findOne();

        $repeatInfo->setDbNextPopDate($nextInfo[0])
            ->save();
    }

    //for a show with repeat_type == 1
    private static function populateBiWeeklyShow($show_id, $next_pop_date, $first_show, $last_show, $start_time, $duration, $day, $end_timestamp) {
        global $CC_DBC;        

        if(isset($next_pop_date)) {
            $next_date = $next_pop_date." ".$start_time;
        }
        else {
            $next_date = $first_show." ".$start_time;
        }

        while(strtotime($next_date) < strtotime($end_timestamp) && (strtotime($last_show) > strtotime($next_date) || is_null($last_show))) {
            
            $start = $next_date;
            
            $sql = "SELECT timestamp '{$start}' + interval '{$duration}'";
		    $end = $CC_DBC->GetOne($sql);

            $newShow = new CcShowInstances();
		    $newShow->setDbShowId($show_id);
            $newShow->setDbStarts($start);
            $newShow->setDbEnds($end);
		    $newShow->save();

            $sql = "SELECT timestamp '{$start}' + interval '14 days'";
		    $next_date = $CC_DBC->GetOne($sql);
        }

        $nextInfo = explode(" ", $next_date);

        $repeatInfo = CcShowDaysQuery::create()
            ->filterByDbShowId($show_id)
            ->filterByDbDay($day)
            ->findOne();

        $repeatInfo->setDbNextPopDate($nextInfo[0])
            ->save();
    }

    private static function populateShow($repeat_type, $show_id, $next_pop_date, $first_show, $last_show, $start_time, $duration, $day, $end_timestamp) {

        if($repeat_type == -1) {
            Show::populateNonRepeatingShow($show_id, $first_show, $start_time, $duration, $day, $end_timestamp);
        }
        else if($repeat_type == 0) {
            Show::populateWeeklyShow($show_id, $next_pop_date, $first_show, $last_show, $start_time, $duration, $day, $end_timestamp);
        }
        else if($repeat_type == 1) {
            Show::populateBiWeeklyShow($show_id, $next_pop_date, $first_show, $last_show, $start_time, $duration, $day, $end_timestamp);
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
                                    $row["last_show"], $row["start_time"], $row["duration"], $row["day"], $showsPopUntil);    
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
                                    $row["last_show"], $row["start_time"], $row["duration"], $row["day"], $end_timestamp);    
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

		$event = array(
			"id" => $show["instance_id"],
			"title" => $show["name"],
			"start" => $show["starts"],
			"end" => $show["ends"],
			"allDay" => false,
			"description" => $show["description"],
			"color" => $show["color"],
			"backgroundColor" => $show["background_color"],
            "showId" => $show["show_id"]
		);

		foreach($options as $key=>$value) {
			$event[$key] = $value;
		}

		$percent = Schedule::getPercentScheduledInRange($show["starts"], $show["ends"]);
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
                    WHERE (starts >= '{$this->getShowStart()}')  
                        AND (ends <= '{$this->getShowEnd()}')";

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
        //have to check if any scheduled content still fits.
        else{
            $scheduledTime = $this->getTimeScheduled();
            $sql = "SELECT (timestamp '{$new_ends}' - timestamp '{$starts}') >= interval '{$scheduledTime}'";
		    $scheduledContentFits = $CC_DBC->GetOne($sql);

            if($scheduledContentFits != "t") {
                return "Must removed some scheduled content.";
            }
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

        $start_timestamp = $this->getShowStart(); 
        $end_timestamp = $this->getShowEnd();

		$time = Schedule::getTimeScheduledInRange($start_timestamp, $end_timestamp);

		return $time;
	}

	public function getTimeUnScheduled() {

        $start_timestamp = $this->getShowStart(); 
        $end_timestamp = $this->getShowEnd();

		$time = Schedule::getTimeUnScheduledInRange($start_timestamp, $end_timestamp);

		return $time;
	}

    public function getPercentScheduledInRange(){

        $start_timestamp = $this->getShowStart(); 
        $end_timestamp = $this->getShowEnd();

        return Schedule::getPercentScheduledInRange($start_timestamp, $end_timestamp);
    }

    public function getShowLength(){
		global $CC_DBC;

        $start_timestamp = $this->getShowStart(); 
        $end_timestamp = $this->getShowEnd();

		$sql = "SELECT TIMESTAMP '{$end_timestamp}' - TIMESTAMP '{$start_timestamp}' ";
		$length = $CC_DBC->GetOne($sql);

		return $length;
	}

	public function searchPlaylistsForShow($datatables){

		$length = $this->getTimeUnScheduled();

		return StoredFile::searchPlaylistsForSchedule($length, $datatables);
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
        
        $sql = "SELECT si.starts as start_timestamp, si.ends as end_timestamp, s.name, s.id"
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
		." AND si.starts > TIMESTAMP '$timeNow'"
		." AND si.starts < TIMESTAMP '$timeNow' + INTERVAL '48 hours'"
        ." ORDER BY si.starts"
        ." LIMIT 1";
                
        $rows = $CC_DBC->GetAll($sql);
        return $rows;
    }
    
}
