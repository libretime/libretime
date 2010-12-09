<?php

class Show {

	private $_userRole;

	public function __construct($userType='G')
    {
        $this->_userRole = $userType;
       
    }

	private function makeFullCalendarEvent($show, $date, $options=array()) {

		$start = $date."T".$show["start_time"];
		$end = $date."T".$show["end_time"];

		$event = array(
			"id" => $show["show_id"],
			"title" => $show["name"],
			"start" => $start,
			"end" => $end,
			"allDay" => false,
			"description" => $show["description"]
		);

		foreach($options as $key=>$value) {
			$event[$key] = $value;
		}

		if($this->_userRole === "A") {
			$event["editable"] = true;
		}

		return $event;
	}

	public function addShow($data) {
	
		$con = Propel::getConnection("campcaster");

		$sql = "SELECT time '{$data['start_time']}' + INTERVAL '{$data['duration']} hour' ";
		$r = $con->query($sql);
        $endTime = $r->fetchColumn(0); 

		$sql = "SELECT nextval('show_group_id_seq')";
		$r = $con->query($sql);
        $showId = $r->fetchColumn(0);

		$sql = "SELECT EXTRACT(DOW FROM TIMESTAMP '{$data['start_date']} {$data['start_time']}')";
		$r = $con->query($sql);
        $startDow = $r->fetchColumn(0);  

		if($data['no_end']) {
			$endDate = NULL;
			$data['repeats'] = 1;
		}
		else if($data['repeats']) {
			$endDate = $data['end_date'];
		}
		else {
			$endDate = $data['start_date'];
		} 

		if($data['day_check'] === null) {
			$data['day_check'] = array($startDow);
		}       

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

			$show = new CcShow();
			$show->setDbName($data['name']);
			$show->setDbFirstShow($start);
			$show->setDbLastShow($endDate);
			$show->setDbStartTime($data['start_time']);
			$show->setDbEndTime($endTime);
			$show->setDbRepeats($data['repeats']);
			$show->setDbDay($day);
			$show->setDbDescription($data['description']);
			$show->setDbShowId($showId);
			$show->save();
		}

	}

	public function moveShow($showId, $deltaDay, $deltaMin){
		global $CC_DBC;

		$sql = "SELECT * FROM cc_show WHERE show_id = '{$showId}'";
		$res = $CC_DBC->GetAll($sql);

		$show = $res[0];
		$start = $show["first_show"];
		$end = $show["last_show"];
		$days = array();
		$s_time = $show["start_time"];
		$e_time = $show["end_time"];

		foreach($res as $show) {
			$days[] = $show["day"];
		}

		$shows_overlap = $this->getShows($start, $end, $days, $s_time, $e_time);

		echo $shows_overlap;
	}

	public function getShows($start=NULL, $end=NULL, $days=NULL, $s_time=NULL, $e_time=NULL) {
		global $CC_DBC;

		$sql;
	
		$sql_gen = "SELECT * FROM cc_show";
		$sql = $sql_gen;

		if(!is_null($start) && !is_null($end)) {
			$sql_range = "(first_show < '{$start}' AND last_show IS NULL) 
					OR (first_show >= '{$start}' AND first_show < '{$end}') 
					OR (last_show >= '{$start}' AND last_show < '{$end}')
					OR (first_show < '{$start}' AND last_show >= '{$end}')";

			$sql = $sql_gen ." WHERE ". $sql_range;
		}
		if(!is_null($days)){

			$sql_opt = array();
			foreach ($days as $day) {
				$sql_opt[] = "day = {$day}";
			}
			$sql_day = join(" OR ", $sql_opt);
				
			$sql = $sql_gen ." WHERE (". $sql_day ." AND (". $sql_range ."))";
		}
		if(!is_null($s_time) && !is_null($e_time)) {
			$sql_time = "(start_time <= '{$s_time}' AND end_time >= '{$e_time}')
				OR (start_time >= '{$s_time}' AND end_time <= '{$e_time}')
				OR (end_time > '{$s_time}' AND end_time <= '{$e_time}')
				OR (start_time >= '{$s_time}' AND start_time < '{$e_time}')";

			$sql = $sql_gen ." WHERE (". $sql_day ." AND (". $sql_range .") AND (". $sql_time ."))";
		}

		//echo $sql;

		return  $CC_DBC->GetAll($sql);	
	}

	public function getFullCalendarEvents($start, $end, $weekday=NULL) {
		global $CC_DBC;
		$shows = array();

		$res = $this->getShows($start, $end, $weekday);

		foreach($res as $row) {

			if(!is_null($start)) { 

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
						$time = $row["last_show"] ." ".$row["end_time"];
						$show_end_epoch = strtotime($time);
					}

					while(true) {

						$diff = "SELECT date '{$newDate}' + integer '7'";
						$repeatDate = $CC_DBC->GetOne($diff);
						$repeat_epoch = strtotime($repeatDate);

						if (isset($show_end_epoch) && $repeat_epoch < $show_end_epoch && $repeat_epoch < $end_epoch) {
							$shows[] = $this->makeFullCalendarEvent($row, $repeatDate);
						}
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
		}

		return $shows;
	}
}
