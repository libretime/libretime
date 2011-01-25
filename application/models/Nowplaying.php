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
					array("sTitle"=>"Playlist"));
		$rows = array();
		
		foreach ($previous as $item){
			array_push($rows, array("p", $item["starts"], $item["starts"], $item["ends"], $item["clip_length"], $item["track_title"], $item["artist_name"],
				$item["album_title"], "x" , $item["playlistname"]));
		}
		
		
		foreach ($current as $item){
			array_push($rows, array("c", $item["starts"], $item["starts"], $item["ends"], $item["clip_length"], $item["track_title"], $item["artist_name"],
				$item["album_title"], "x" , $item["playlistname"]));		
		}
		
		foreach ($next as $item){
			array_push($rows, array("n", $item["starts"], $item["starts"], $item["ends"], $item["clip_length"], $item["track_title"], $item["artist_name"],
				$item["album_title"], "x" , $item["playlistname"]));
		}
		
		return array("columnHeaders"=>$columnHeaders, "rows"=>$rows);
	}

}

