<?php

class Show {

	private $_user;
	private $_showId;

	public function __construct($user=NULL, $showId=NULL)
    {
		$this->_user = $user;
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
	public function addShow($data) {
	
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

		if(!isset($data['add_show_day_check'])) {
			$data['add_show_day_check'] = array($startDow);
		} 

        /*
		$overlap =  $this->getShows($data['add_show_start_date'], $endDate, $data['add_show_day_check'], $data['add_show_start_time'], $endTime);

		if(count($overlap) > 0) {
			return $overlap;
		}
        */

        if($data['add_show_repeats']) {
            $repeat_type = 0; //chnage this when supporting more than just a weekly show option.
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

        $this->populateShowUntilLastGeneratedDate($showId);
	}

	public function moveShow($showInstanceId, $deltaDay, $deltaMin){
		global $CC_DBC;

		$showInstance = CcShowInstancesQuery::create()->findPK($showInstanceId);

		$hours = $deltaMin/60;
		if($hours > 0)
			$hours = floor($hours);
		else
			$hours = ceil($hours);

		$mins = abs($deltaMin%60);

        $starts = $showInstance->getDbStarts();
        $ends = $showInstance->getDbEnds(); 

		$sql = "SELECT timestamp '{$starts}' + interval '{$deltaDay} days' + interval '{$hours}:{$mins}'";
		$new_starts = $CC_DBC->GetOne($sql);

		$sql = "SELECT timestamp '{$ends}' + interval '{$deltaDay} days' + interval '{$hours}:{$mins}'";
		$new_ends = $CC_DBC->GetOne($sql);

		$overlap =  $this->getShows($new_starts, $new_ends, array($showInstanceId));

		if(count($overlap) > 0) {
			return $overlap;
		}

        $showInstance
            ->setDbStarts($new_starts)
            ->setDbEnds($new_ends)
            ->save();	
	}

	public function resizeShow($showInstanceId, $deltaDay, $deltaMin){
		global $CC_DBC;

        $showInstance = CcShowInstancesQuery::create()->findPK($showInstanceId);

		$hours = $deltaMin/60;
		if($hours > 0)
			$hours = floor($hours);
		else
			$hours = ceil($hours);

		$mins = abs($deltaMin%60);

        $starts = $showInstance->getDbStarts();
        $ends = $showInstance->getDbEnds();

		$sql = "SELECT timestamp '{$ends}' + interval '{$hours}:{$mins}'";
		$new_ends = $CC_DBC->GetOne($sql);

        //only need to check overlap if show increased in size.
        if(strtotime($new_ends) > strtotime($ends)) {
		    $overlap =  $this->getShows($ends, $new_ends);

            if(count($overlap) > 0) {
			    return $overlap;
		    }
        }

        $showInstance
            ->setDbEnds($new_ends)
            ->save();

        //needed if option is for all future shows.
        /*
		foreach($res as $row) {
			$show = CcShowDaysQuery::create()->findPK($row["id"]);
			$show->setDbStartTime($s_time);
			$show->setDbEndTime($e_time);
			$show->save();
		}
        */		
	}

	private function getNextPos($day) {
		global $CC_DBC;

		$timeinfo = explode(" ", $day);

		$sql = "SELECT MAX(position)+1 from cc_show_schedule WHERE show_id = '{$this->_showId}' AND show_day = '{$timeinfo[0]}'";
		$res = $CC_DBC->GetOne($sql);	

		if(is_null($res))
            return 0;

		return $res;
	}

	private function getLastGroupId($start_timestamp) {
		global $CC_DBC;

		$timeinfo = explode(" ", $start_timestamp);

		$sql = "SELECT MAX(group_id) from cc_show_schedule WHERE show_id = '{$this->_showId}' AND show_day = '{$timeinfo[0]}'";
		$res = $CC_DBC->GetOne($sql);	

		return $res;
	}

	public function addPlaylistToShow($start_timestamp, $plId) {
		
		$sched = new ScheduleGroup();
		$lastGroupId = $this->getLastGroupId($start_timestamp);

		if(is_null($lastGroupId)) {

			$groupId = $sched->add($start_timestamp, null, $plId);		
		}
		else {
			$groupId = $sched->addPlaylistAfter($lastGroupId, $plId);
		}

		$timeinfo = explode(" ", $start_timestamp);
		$day = $timeinfo[0];
		$pos = $this->getNextPos($day);

		$groupsched = new CcShowSchedule();
		$groupsched->setDbShowId($this->_showId);
		$groupsched->setDbGroupId($groupId);
		$groupsched->setDbShowDay($day);
		$groupsched->setDbPosition($pos);
		$groupsched->save();
	}

	public function scheduleShow($start_timestamp, $plIds) {
		if($this->_user->isHost($this->_showId)) {

			foreach($plIds as $plId) {
				$this->addPlaylistToShow($start_timestamp, $plId);
			}
		}
	}

	public function removeGroupFromShow($start_timestamp, $group_id){
		global $CC_DBC, $CC_CONFIG;

		$timeinfo = explode(" ", $start_timestamp);

		$group = CcShowScheduleQuery::create()
			->filterByDbShowId($this->_showId)
			->filterByDbGroupId($group_id)
			->filterByDbShowDay($timeinfo[0])
			->findOne();

		$position = $group->getDbPosition();

		$sql = "SELECT group_id FROM cc_show_schedule 
					WHERE show_id = '{$this->_showId}' AND show_day = '{$timeinfo[0]}'
					AND position > '{$position}'";
		$followingGroups = $CC_DBC->GetAll($sql);

		$sql = "SELECT SUM(clip_length) FROM ".$CC_CONFIG["scheduleTable"]." WHERE group_id='{$group_id}'";
		$group_length = $CC_DBC->GetOne($sql);

		$sql = "DELETE FROM ".$CC_CONFIG["scheduleTable"]." WHERE group_id = '{$group_id}'";
		$CC_DBC->query($sql);

		if(!is_null($followingGroups)) {
			$sql_opt = array();
			foreach ($followingGroups as $row) {
				$sql_opt[] = "group_id = {$row["group_id"]}";
			}
			$sql_group_ids = join(" OR ", $sql_opt);

			$sql = "UPDATE ".$CC_CONFIG["scheduleTable"]." 
						SET starts = (starts - INTERVAL '{$group_length}'), ends = (ends - INTERVAL '{$group_length}') 
						WHERE " . $sql_group_ids;
			$CC_DBC->query($sql);
		}

		$group->delete();

	}

	public function getTimeScheduled($start_timestamp, $end_timestamp) {

		$time = Schedule::getTimeScheduledInRange($start_timestamp, $end_timestamp);

		return $time;
	}

	public function getTimeUnScheduled($start_timestamp, $end_timestamp) {

		$time = Schedule::getTimeUnScheduledInRange($start_timestamp, $end_timestamp);

		return $time;
	}

	public function showHasContent($start_timestamp, $end_timestamp) {

		$con = Propel::getConnection(CcShowPeer::DATABASE_NAME);
        $sql = "SELECT TIMESTAMP '{$end_timestamp}' - TIMESTAMP '{$start_timestamp}'";
		$r = $con->query($sql);
		$length = $r->fetchColumn(0);

		return !Schedule::isScheduleEmptyInRange($start_timestamp, $length);
	}

    public function getShowListContent($start_timestamp) {
        global $CC_DBC;

		$timeinfo = explode(" ", $start_timestamp);
	
		$sql = "SELECT * 
			FROM (cc_show_schedule AS ss LEFT JOIN cc_schedule AS s USING(group_id)
				LEFT JOIN cc_files AS f ON f.id = s.file_id
				LEFT JOIN cc_playlist AS p ON p.id = s.playlist_id )

			WHERE ss.show_day = '{$timeinfo[0]}' AND ss.show_id = '{$this->_showId}' ORDER BY starts";

		return $CC_DBC->GetAll($sql);	
    }

	public function getShowContent($start_timestamp) {
		global $CC_DBC;

        $res = $this->getShowListContent($start_timestamp);

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

	public function clearShow($day) {
		$timeinfo = explode(" ", $day);

		$groups = CcShowScheduleQuery::create()
					->filterByDbShowId($this->_showId)
					->filterByDbShowDay($timeinfo[0])
					->find();

		foreach($groups as $group) {
			$groupId = $group->getDbGroupId();
			CcScheduleQuery::create()
				->filterByDbGroupId($groupId)
				->delete();

			$group->delete();
		}
	}

	public function deleteShow($timestamp, $dayId=NULL) {
		global $CC_DBC;

		$today_timestamp = date("Y-m-d H:i:s");

		$timeinfo = explode(" ", $timestamp);
		$date = $timeinfo[0]; 
		$time = $timeinfo[1];

		$today_epoch = strtotime($today_timestamp);
		$date_epoch = strtotime($timestamp);

		//don't want someone to delete past shows.
		if($date_epoch < $today_epoch) {
			return;
		}

		$show = CcShowQuery::create()->findPK($this->_showId);
		
		$sql = "SELECT start_time, first_show FROM cc_show_days 
				WHERE show_id = '{$this->_showId}'
				ORDER BY first_show LIMIT 1";
		$res = $CC_DBC->GetRow($sql);

		$start_timestamp = $res["first_show"]." ".$res["start_time"];

		$start_epoch = strtotime($start_timestamp);
	
		// must not delete shows in the past
		if($show->getDbRepeats() && ($start_epoch < $date_epoch)) {

			$sql = "DELETE FROM cc_show_days WHERE first_show >= '{$date}' AND show_id = '{$this->_showId}'";
			$CC_DBC->query($sql);
			
			$sql = "UPDATE cc_show_days 
				SET last_show = '{$date}' 
				WHERE show_id = '{$this->_showId}' AND first_show <= '{$date}' ";
			$CC_DBC->query($sql);
			
			$sql = "SELECT group_id FROM cc_show_schedule WHERE show_day >= '{$date}' AND show_id = '{$this->_showId}'";
		    $rows = $CC_DBC->GetAll($sql);
			
			$sql_opt = array();
			foreach($rows as $row) {
				$sql_opt[] = "group_id = '{$row["group_id"]}' ";
			}
			$groups = join(' OR ', $sql_opt);
		
			$sql = "DELETE FROM cc_show_schedule 
			WHERE ($groups) AND show_id = '{$this->_showId}' AND show_day >= '{$date}' ";
			$CC_DBC->query($sql);
			
			$sql = "DELETE FROM cc_schedule WHERE ($groups)";
			$CC_DBC->query($sql);
		}
		else {
			$groups = CcShowScheduleQuery::create()->filterByDbShowId($this->_showId)->find();

			foreach($groups as $group) {
				$groupId = $group->getDbGroupId();
				CcScheduleQuery::create()->filterByDbGroupId($groupId)->delete();
			}

			$show->delete();
		}
	}

    public function getShows($start_timestamp, $end_timestamp, $excludeInstance=NULL) {
        global $CC_DBC;

        $sql = "SELECT starts, ends, show_id, name, description, color, background_color, cc_show_instances.id AS instance_id  
            FROM cc_show_instances 
            LEFT JOIN cc_show ON cc_show.id = cc_show_instances.show_id 
            WHERE ((starts >= '{$start_timestamp}' AND starts < '{$end_timestamp}')
                OR (ends > '{$start_timestamp}' AND ends <= '{$end_timestamp}'))";

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
    private function populateNonRepeatingShow($show_id, $first_show, $start_time, $duration, $day, $end_timestamp) {
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
    private function populateWeeklyShow($show_id, $next_pop_date, $first_show, $last_show, $start_time, $duration, $day, $end_timestamp) {
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

    private function populateShow($repeat_type, $show_id, $next_pop_date, $first_show, $last_show, $start_time, $duration, $day, $end_timestamp) {

        if($repeat_type == -1) {
            $this->populateNonRepeatingShow($show_id, $first_show, $start_time, $duration, $day, $end_timestamp);
        }
        else if($repeat_type == 0) {
            $this->populateWeeklyShow($show_id, $next_pop_date, $first_show, $last_show, $start_time, $duration, $day, $end_timestamp);
        }
    } 

    //used to catch up a newly added show
    private function populateShowUntilLastGeneratedDate($show_id) {
        global $CC_DBC;
        $showsPopUntil = Application_Model_Preference::GetShowsPopulatedUntil();
  
        $sql = "SELECT * FROM cc_show_days WHERE show_id = {$show_id}";
		$res = $CC_DBC->GetAll($sql); 

        foreach($res as $row) {
            $this->populateShow($row["repeat_type"], $row["show_id"], $row["next_pop_date"], $row["first_show"], 
                                    $row["last_show"], $row["start_time"], $row["duration"], $row["day"], $showsPopUntil);    
        } 
    }

    public function populateShowsUntil($pop_timestamp, $end_timestamp) {
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
            $this->populateShow($row["repeat_type"], $row["show_id"], $row["next_pop_date"], $row["first_show"], 
                                    $row["last_show"], $row["start_time"], $row["duration"], $row["day"], $end_timestamp);    
        }    
    }

    public function getFullCalendarEvents($start, $end) {

        $events = array();
        $showsPopUntil = Application_Model_Preference::GetShowsPopulatedUntil();

        //if fullcalendar is requesting shows past our previous populated until date, generate shows up until this point.
        if($showsPopUntil == "" || strtotime($showsPopUntil) < strtotime($end)) {

            $this->populateShowsUntil($showsPopUntil, $end);
            Application_Model_Preference::SetShowsPopulatedUntil($end);
        }

        $shows = $this->getShows($start, $end);

        foreach ($shows as $show) {
            $events[] = $this->makeFullCalendarEvent($show);
        }

        return $events;
    }

	private function makeFullCalendarEvent($show, $options=array()) {
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

		if($this->_user->isAdmin()) {
			$event["editable"] = true;
		}

		if($this->_user->isHost($show["show_id"])) {
			$event["isHost"] = true;
		}

		$percent = Schedule::getPercentScheduledInRange($show["starts"], $show["ends"]);
		$event["percent"] = $percent;

		return $event;
	}

	public function getShowLength($start_timestamp, $end_timestamp){
		global $CC_DBC;

		$sql = "SELECT TIMESTAMP '{$end_timestamp}' - TIMESTAMP '{$start_timestamp}' ";
		$length = $CC_DBC->GetOne($sql);

		return $length;
	}

	public function searchPlaylistsForShow($start_timestamp, $end_timestamp, $datatables){

		$length = $this->getTimeUnScheduled($start_timestamp, $end_timestamp);

		return StoredFile::searchPlaylistsForSchedule($length, $datatables);
	}
}

/* Show Data Access Layer */
class Show_DAL{
    
    /* Given a group_id, get all show data related to
     * id. This is useful in the case where you have an item
     * in the schedule table and you want to find out more about 
     * the show it is in without joining the schedule and show tables
     * (which causes problems with duplicate items)
     */
    public static function GetShowData($group_id){
        global $CC_DBC;
                
        $sql="SELECT * FROM cc_show_schedule as ss, cc_show as s"
        ." WHERE ss.show_id = s.id"
        ." AND ss.group_id = $group_id"
        ." LIMIT 1";
        
        return $CC_DBC->GetOne($sql);
    }
    
    /* Given a show ID, this function returns what group IDs
     * are present in this show. */
    public static function GetShowGroupIDs($showID){
        global $CC_CONFIG, $CC_DBC;
        
		$sql = "SELECT group_id"
		." FROM $CC_CONFIG[showSchedule]"
		." WHERE show_id = $showID";
		
        $rows = $CC_DBC->GetAll($sql);
        return $rows;
	}
    
}
