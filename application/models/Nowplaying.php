<?php

class Application_Model_Nowplaying
{
	
	public static function GetDataGridData(){
		$timeNow = Schedule::GetSchedulerTime();
		$previous = Schedule::GetPreviousItems($timeNow, 1);
		$current = Schedule::GetCurrentlyPlaying($timeNow);
		$next = Schedule::GetNextItems($timeNow, 10);
		
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
		$rows = array();

        $current_group_id = -1;
        if (count($current) != 0){
            $current_group_id = $current[0]["group_id"];
        }
		
		foreach ($previous as $item){
			array_push($rows, array("p", $item["starts"], $item["starts"], $item["ends"], $item["clip_length"], $item["track_title"], $item["artist_name"],
				$item["album_title"], "x" , $item["name"], ($item["group_id"] == $current_group_id ? $item["background_color"] : ""), $item["group_id"]));
		}
		
		
		foreach ($current as $item){
			array_push($rows, array("c", $item["starts"], $item["starts"], $item["ends"], $item["clip_length"], $item["track_title"], $item["artist_name"],
				$item["album_title"], "x" , $item["name"], $item["background_color"], $item["group_id"]));		
		}
		
		foreach ($next as $item){
			array_push($rows, array("n", $item["starts"], $item["starts"], $item["ends"], $item["clip_length"], $item["track_title"], $item["artist_name"],
				$item["album_title"], "x" , $item["name"], ($item["group_id"] == $current_group_id ? $item["background_color"] : ""), $item["group_id"]));
		}
		
		return array("columnHeaders"=>$columnHeaders, "rows"=>$rows);
	}

}

