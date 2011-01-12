<?php

class Show {

	private $_user;
	private $_showId;

	public function __construct($user=NULL, $showId=NULL)
    {
		$this->_user = $user;
		$this->_showId = $showId;    
    }

	//end dates are non inclusive.
	public function addShow($data) {
	
		$con = Propel::getConnection(CcShowPeer::DATABASE_NAME);

		$sql = "SELECT time '{$data['start_time']}' + INTERVAL '{$data['duration']} hour' ";
		$r = $con->query($sql);
        $endTime = $r->fetchColumn(0); 

		$sql = "SELECT EXTRACT(DOW FROM TIMESTAMP '{$data['start_date']} {$data['start_time']}')";
		$r = $con->query($sql);
        $startDow = $r->fetchColumn(0);  

		if($data['no_end']) {
			$endDate = NULL;
			$data['repeats'] = 1;
		}
		else if($data['repeats']) {
			$sql = "SELECT date '{$data['end_date']}' + INTERVAL '1 day' ";
			$r = $con->query($sql);
        	$endDate = $r->fetchColumn(0); 
		}
		else {
			$sql = "SELECT date '{$data['start_date']}' + INTERVAL '1 day' ";
			$r = $con->query($sql);
        	$endDate = $r->fetchColumn(0);
		} 

		if($data['day_check'] === null) {
			$data['day_check'] = array($startDow);
		} 

		$overlap =  $this->getShows($data['start_date'], $endDate, $data['day_check'], $data['start_time'], $endTime);

		if(count($overlap) > 0) {
			return $overlap;
		}
		
		$show = new CcShow();
		$show->setDbName($data['name']);
		$show->setDbRepeats($data['repeats']);
		$show->setDbDescription($data['description']);
		$show->save();      

		$showId = $show->getDbId();

		foreach ($data['day_check'] as $day) {

			if($startDow !== $day){
				
				if($startDow > $day)
					$daysAdd = 6 - $startDow + 1 + $day;
				else
					$daysAdd = $day - $startDow;				

				$sql = "SELECT date '{$data['start_date']}' + INTERVAL '{$daysAdd} day' ";
				$r = $con->query($sql);
				$start = $r->fetchColumn(0); 
			}
			else {
				$start = $data['start_date'];
			}

			$showDay = new CcShowDays();
			$showDay->setDbFirstShow($start);
			$showDay->setDbLastShow($endDate);
			$showDay->setDbStartTime($data['start_time']);
			$showDay->setDbEndTime($endTime);
			$showDay->setDbDay($day);
			$showDay->setDbShowId($showId);
			$showDay->save();
		}
		
		foreach ($data['hosts'] as $host) {
			$showHost = new CcShowHosts();
			$showHost->setDbShow($showId);
			$showHost->setDbHost($host);
			$showHost->save();
		}
	}

	public function moveShow($showId, $deltaDay, $deltaMin){
		global $CC_DBC;

		$sql = "SELECT * FROM cc_show_days WHERE show_id = '{$showId}'";
		$res = $CC_DBC->GetAll($sql);

		$show = $res[0];
		$start = $show["first_show"];
		$end = $show["last_show"];
		$days = array();

		$hours = $deltaMin/60;
		if($hours > 0)
			$hours = floor($hours);
		else
			$hours = ceil($hours);

		$mins = abs($deltaMin%60);

		$sql = "SELECT time '{$show["start_time"]}' + interval '{$hours}:{$mins}'";
		$s_time = $CC_DBC->GetOne($sql);

		$sql = "SELECT time '{$show["end_time"]}' + interval '{$hours}:{$mins}'";
		$e_time = $CC_DBC->GetOne($sql);

		foreach($res as $show) {
			$days[] = $show["day"] + $deltaDay;
		}

		//need to check each specific day if times different then merge arrays.
		$overlap =  $this->getShows($start, $end, $days, $s_time, $e_time, array($showId));

		if(count($overlap) > 0) {
			return $overlap;
		}

		foreach($res as $row) {

			$sql = "SELECT date '{$show["first_show"]}' + interval '{$deltaDay} day'";
			$f_show = $CC_DBC->GetOne($sql);
			//get a timestamp back only need a date.
			$tmp = spliti(" ", $f_show);
			$f_show = $tmp[0];

			$show = CcShowDaysQuery::create()->findPK($row["id"]);
			$show->setDbStartTime($s_time);
			$show->setDbEndTime($e_time);
			$show->setDbFirstShow($f_show);
			$show->setDbDay($row['day'] + $deltaDay);
			$show->save();
		}		
	}

	public function resizeShow($showId, $deltaDay, $deltaMin){
		global $CC_DBC;

		$sql = "SELECT * FROM cc_show_days WHERE show_id = '{$showId}'";
		$res = $CC_DBC->GetAll($sql);

		$show = $res[0];
		$start = $show["first_show"];
		$end = $show["last_show"];
		$days = array();

		$hours = $deltaMin/60;
		if($hours > 0)
			$hours = floor($hours);
		else
			$hours = ceil($hours);

		$mins = abs($deltaMin%60);

		$s_time = $show["start_time"];

		$sql = "SELECT time '{$show["end_time"]}' + interval '{$hours}:{$mins}'";
		$e_time = $CC_DBC->GetOne($sql);

		foreach($res as $show) {
			$days[] = $show["day"] + $deltaDay;
		}

		//need to check each specific day if times different then merge arrays.
		$overlap =  $this->getShows($start, $end, $days, $s_time, $e_time, array($showId));

		if(count($overlap) > 0) {
			return $overlap;
		}

		foreach($res as $row) {
			$show = CcShowDaysQuery::create()->findPK($row["id"]);
			$show->setDbStartTime($s_time);
			$show->setDbEndTime($e_time);
			$show->save();
		}		

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

	public function getTimeScheduled($start_timestamp, $end_timestamp) {

		$time = Schedule::getTimeScheduledInRange($start_timestamp, $end_timestamp);

		return $time;
	}

	public function getTimeUnScheduled($start_date, $end_date, $start_time, $end_time) {

		$start_timestamp = $start_date ." ".$start_time;
		$end_timestamp = $end_date ." ".$end_time;

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

	public function getShowContent($day) {
		global $CC_DBC;

		$timeinfo = explode(" ", $day);
	
		$sql = "SELECT * 
			FROM (cc_show_schedule AS ss LEFT JOIN cc_schedule AS s USING(group_id)
				LEFT JOIN cc_files AS f ON f.id = s.file_id
				LEFT JOIN cc_playlist AS p ON p.id = s.playlist_id )

			WHERE ss.show_day = '{$timeinfo[0]}' AND ss.show_id = '{$this->_showId}'";

		return $CC_DBC->GetAll($sql);	
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

	public function deleteShow($showId, $dayId=NULL) {
		$groups = CcShowScheduleQuery::create()->filterByDbShowId($showId)->find();

		foreach($groups as $group) {
			$groupId = $group->getDbGroupId();
			CcScheduleQuery::create()->filterByDbGroupId($groupId)->delete();
		}

		CcShowQuery::create()->filterByDbId($showId)->delete();
	}

	public function getShows($start=NULL, $end=NULL, $days=NULL, $s_time=NULL, $e_time=NULL, $exclude_shows=NULL) {
		global $CC_DBC;

		$sql;
	
		$sql_gen = "SELECT cc_show_days.id AS day_id, name, repeats, description, 
			first_show, last_show, start_time, end_time, day, show_id  
			FROM (cc_show LEFT JOIN cc_show_days ON cc_show.id = cc_show_days.show_id)";

		$sql = $sql_gen;

		if(!is_null($start) && !is_null($end)) {
			$sql_range = "(first_show < '{$start}' AND last_show IS NULL) 
					OR (first_show >= '{$start}' AND first_show < '{$end}') 
					OR (last_show >= '{$start}' AND last_show < '{$end}')
					OR (first_show < '{$start}' AND last_show >= '{$end}')";

			$sql = $sql_gen ." WHERE ". $sql_range;
		}
		if(!is_null($start) && is_null($end)) {
			$sql_range = "(first_show <= '{$start}' AND last_show IS NULL) 
					OR (first_show <= '{$start}' AND last_show > '{$start}')";

			$sql = $sql_gen ." WHERE ". $sql_range;
		}
		if(!is_null($days)){

			$sql_opt = array();
			foreach ($days as $day) {
				$sql_opt[] = "day = {$day}";
			}
			$sql_day = join(" OR ", $sql_opt);
				
			$sql = $sql_gen ." WHERE ((". $sql_day .") AND (". $sql_range ."))";
		}
		if(!is_null($s_time) && !is_null($e_time)) {
			$sql_time = "(start_time <= '{$s_time}' AND end_time >= '{$e_time}' AND start_time < end_time AND '{$s_time}' < '{$e_time}')
				OR (start_time >= '{$s_time}' AND end_time <= '{$e_time}' AND start_time > end_time AND '{$s_time}' > '{$e_time}')
				OR (start_time >= '{$s_time}' AND end_time <= '{$e_time}' AND start_time < end_time)
				OR (start_time <= '{$s_time}' AND end_time >= '{$e_time}' AND start_time > end_time)
				OR (start_time <= '{$s_time}' AND end_time <= '{$e_time}' AND start_time > end_time AND '{$s_time}' > '{$e_time}')
				OR (start_time >= '{$s_time}' AND end_time >= '{$e_time}' AND start_time > end_time AND '{$s_time}' > '{$e_time}')
				OR (end_time > '{$s_time}' AND end_time <= '{$e_time}')
				OR (start_time >= '{$s_time}' AND start_time < '{$e_time}')";

			$sql = $sql_gen ." WHERE ((". $sql_day .") AND (". $sql_range .") AND (". $sql_time ."))";
		}
		if(!is_null($exclude_shows)){

			$sql_opt = array();
			foreach ($exclude_shows as $showid) {
				$sql_opt[] = "show_id = {$showid}";
			}
			$sql_showid = join(" OR ", $sql_opt);
			
			$sql = $sql_gen ." WHERE ((". $sql_day .") AND NOT (". $sql_showid .") AND (". $sql_range .") AND (". $sql_time ."))";	
		}

		//echo $sql;

		return $CC_DBC->GetAll($sql);	
	}

	public function getFullCalendarEvents($start, $end, $weekday=NULL) {
		global $CC_DBC;
		$shows = array();

		$res = $this->getShows($start, $end, $weekday);

		foreach($res as $row) {

			$timeDiff = "SELECT date '{$start}' - date '{$row["first_show"]}' as diff";
			$diff = $CC_DBC->GetOne($timeDiff);

			if($diff > 0) {

				$add = ($diff % 7 === 0) ? $diff : $diff + (7 - $diff % 7);

				$new = "SELECT date '{$row["first_show"]}' + integer '{$add}'";
				$newDate = $CC_DBC->GetOne($new); 
			}
			else {
				$newDate = $row["first_show"];
			}

			$shows[] = $this->makeFullCalendarEvent($row, $newDate);
			
			$end_epoch = strtotime($end);

			//add repeating events until the show end is reached or fullcalendar's end date is reached.
			if($row["repeats"]) {

				if(!is_null($row["last_show"])) {
					$show_end_epoch = strtotime($row["last_show"]);
				}

				while(true) {

					$diff = "SELECT date '{$newDate}' + integer '7'";
					$repeatDate = $CC_DBC->GetOne($diff);
					$repeat_epoch = strtotime($repeatDate);

					//show has finite duration.
					if (isset($show_end_epoch) && $repeat_epoch < $show_end_epoch && $repeat_epoch < $end_epoch) {
						$shows[] = $this->makeFullCalendarEvent($row, $repeatDate);
					}
					//case for non-ending shows.
					else if(!isset($show_end_epoch) && $repeat_epoch < $end_epoch) {
						$shows[] = $this->makeFullCalendarEvent($row, $repeatDate);
					}
					else {
						break;
					}

					$newDate = $repeatDate;
				}					
			}			
		}

		return $shows;
	}

	private function makeFullCalendarEvent($show, $date, $options=array()) {
		global $CC_DBC;

		$start_ts = $date." ".$show["start_time"];
		$end_ts = $date." ".$show["end_time"];
		
		$sql = "SELECT timestamp '{$start_ts}' > timestamp '{$end_ts}'";
		$isNextDay = $CC_DBC->GetOne($sql);

		if($isNextDay === 't') {
			$sql = "SELECT date '{$date}' + interval '1 day {$show["end_time"]}'";
			$end_ts = $CC_DBC->GetOne($sql);
		}

		$event = array(
			"id" => $show["show_id"],
			"title" => $show["name"],
			"start" => $start_ts,
			"end" => $end_ts,
			"allDay" => false,
			"description" => $show["description"]
		);

		foreach($options as $key=>$value) {
			$event[$key] = $value;
		}

		if($this->_user->isAdmin()) {
			//$event["editable"] = true;
		}

		if($this->_user->isHost($show["show_id"])) {
			$event["isHost"] = true;
		}

		$percent = Schedule::getPercentScheduledInRange($start_ts, $end_ts);
		$event["percent"] = $percent;

		return $event;
	}

	public function searchPlaylistsForShow($day, $search){
		global $CC_DBC;

		$sql = "SELECT * FROM cc_show_days WHERE show_id = '{$this->_showId}' AND day = '{$day}'";
		$row = $CC_DBC->GetAll($sql);
		$row = $row[0];

		$start_date = $row["first_show"];
		$end_date = $row["last_show"];
		$start_time = $row["start_time"];
		$end_time = $row["end_time"];

		$length = $this->getTimeUnScheduled($start_date, $start_date, $start_time, $end_time);

		return Playlist::searchPlaylists($length, $search);

	}
}
