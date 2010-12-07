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

	public function addShow() {

		$sql = 'INSERT INTO cc_show 
			("name", "first_show", "last_show", "start_time", "end_time", 
			"repeats", "day", "description", "show_id")
			VALUES ()';

	}

	public function getShows($start=NULL, $end=NULL, $weekday=NULL) {
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
		if(!is_null($weekday)){
			$sql_day = "day = {$weekday}";
				
			$sql = $sql_gen ." WHERE (". $sql_day ." AND (". $sql_range ."))";
		}
		
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

				if($row["repeats"]) {

					while(true) {

						$diff = "SELECT date '{$newDate}' + integer '7'";
						$repeatDate = $CC_DBC->GetOne($diff);
						$repeat_epoch = strtotime($repeatDate);

						if ($repeat_epoch < $end_epoch ) {
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
