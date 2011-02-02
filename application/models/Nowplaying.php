<?php

class Application_Model_Nowplaying
{
	
	public static function GetDataGridData(){
						
		$columnHeaders = array(array("sTitle"=>"type", "bVisible"=>false),
					array("sTitle"=>"Date"), 
					array("sTitle"=>"Start"),
					array("sTitle"=>"End"),
					array("sTitle"=>"Duration"),
					array("sTitle"=>"Song"),
					array("sTitle"=>"Artist"),
					array("sTitle"=>"Album"),
					array("sTitle"=>"Creator"),
					array("sTitle"=>"Playlist"),
                    array("sTitle"=>"bgcolor", "bVisible"=>false),
                    array("sTitle"=>"group_id", "bVisible"=>false));

		$timeNow = Schedule::GetSchedulerTime();
		$currentShow = Schedule::GetCurrentShow($timeNow);
		
		if (count($currentShow) > 0){
			$dbRows = Schedule::GetCurrentShowGroupIDs($currentShow[0]["id"]);
			$groupIDs = array();
			
			foreach ($dbRows as $row){
				array_push($groupIDs, $row["group_id"]);
			}
		}
		
		$previous = Schedule::GetPreviousItems($timeNow, 1);
		$current = Schedule::GetCurrentlyPlaying($timeNow);
		$next = Schedule::GetNextItems($timeNow, 10);
		
		$rows = array();
		
		foreach ($previous as $item){
			$color = (count($currentShow) > 0) && in_array($item["group_id"], $groupIDs) ? "x" : "";
			array_push($rows, array("p", $item["starts"], $item["starts"], $item["ends"], $item["clip_length"], $item["track_title"], $item["artist_name"],
				$item["album_title"], "x" , $item["name"], $color, $item["group_id"]));
		}
		
		foreach ($current as $item){
			array_push($rows, array("c", $item["starts"], $item["starts"], $item["ends"], $item["clip_length"], $item["track_title"], $item["artist_name"],
				$item["album_title"], "x" , $item["name"], "", $item["group_id"]));		
		}
		
		foreach ($next as $item){
			$color = (count($currentShow) > 0) && in_array($item["group_id"], $groupIDs) ? "x" : "";
			array_push($rows, array("n", $item["starts"], $item["starts"], $item["ends"], $item["clip_length"], $item["track_title"], $item["artist_name"],
				$item["album_title"], "x" , $item["name"], $color, $item["group_id"]));
		}
		$data = array("columnHeaders"=>$columnHeaders, "rows"=>$rows);
		
		return $data;
	}

}

