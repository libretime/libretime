<?php

class Application_Model_Nowplaying
{
	
	public static function GetDataGridData(){
		$timeNow = Schedule::GetSchedulerTime();
		$previous = Schedule::GetPreviousItems($timeNow, 1);
		$current = Schedule::GetCurrentlyPlaying($timeNow);
		$next = Schedule::GetNextItems($timeNow, 10);
		
		$columnHeaders = array(array("sTitle"=>"Date"), 
					array("sTitle"=>"Start"),
					array("sTitle"=>"End"),
					array("sTitle"=>"Duration"),
					array("sTitle"=>"Song"),
					array("sTitle"=>"Artist"),
					array("sTitle"=>"Album"),
					array("sTitle"=>"Creator"),
					array("sTitle"=>"Playlist"));
		$rows = array();
		
		foreach ($previous as $item){
			array_push($rows, array($item["starts"], $item["starts"], $item["ends"], $item["clip_length"], $item["track_title"], $item["artist_name"],
				$item["album_title"], "x" , "y"));
		}
		
		
		foreach ($current as $item){
			array_push($rows, array($item["starts"], $item["starts"], $item["ends"], $item["clip_length"], $item["track_title"], $item["artist_name"],
				$item["album_title"], "x" , "y"));		
		}
		
		foreach ($next as $item){
			array_push($rows, array($item["starts"], $item["starts"], $item["ends"], $item["clip_length"], $item["track_title"], $item["artist_name"],
				$item["album_title"], "x" , "y"));
		}
		
		return array("columnHeaders"=>$columnHeaders, "rows"=>$rows);
	}

}

