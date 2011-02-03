<?php

class Application_Model_Nowplaying
{
	
	public static function InsertBlankRow($i, $rows){
		$startDate = explode(".", $rows[$i-1][3]);
		$endDate = explode(".", $rows[$i][2]);
		
		$epochStartMS =  strtotime($startDate[0])*1000;
		$epochEndMS =  strtotime($endDate[0])*1000;
		
		if (count($startDate) > 1)
			$epochStartMS += $startDate[1];
		if (count($endDate) > 1)
			$epochEndMS += $endDate[1];
		
		$blankRow = array(array("b", "-", "-", "-", TimeDateHelper::ConvertMSToHHMMSSmm($epochEndMS - $epochStartMS), "-", "-", "-", "-" , "-", "", ""));
		array_splice($rows, $i, 0, $blankRow);
		return $rows;
	}
	
	public static function FindGaps($rows){
		$n = count($rows);
		
		$blankRowIndices = array();
		$arrayIndexOffset = 0;
		
		if ($n < 2)
			return $rows;
		
		for ($i=1; $i<$n; $i++){
			if ($rows[$i-1][3] != $rows[$i][2])
				array_push($blankRowIndices, $i);
		}
		
		for ($i=0, $n=count($blankRowIndices); $i<$n; $i++){
			$rows = Application_Model_Nowplaying::InsertBlankRow($blankRowIndices[$i]+$arrayIndexOffset, $rows);
			$arrayIndexOffset++;
		}
		
		return $rows;
	}
	
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
		
		$previous = Schedule::Get_Scheduled_Item_Data($timeNow, -1, 1, "60 seconds");
		$current = Schedule::Get_Scheduled_Item_Data($timeNow, 0);
		$next = Schedule::Get_Scheduled_Item_Data($timeNow, 1, 10, "48 hours");
		
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
		
		$rows = Application_Model_Nowplaying::FindGaps($rows);
		$data = array("columnHeaders"=>$columnHeaders, "rows"=>$rows);
		
		return $data;
	}
}

class TimeDateHelper
{

    public static function ConvertMSToHHMMSSmm($time){      
        $hours = floor($time / 3600000);
        $time -= 3600000*$hours;
            
        $minutes = floor($time / 60000);
        $time -= 60000*$minutes;
        
        $seconds = floor($time / 1000);
        $time -= 1000*$seconds;
        
        $ms = $time;
        
        if (strlen($hours) == 1)
            $hours = "0".$hours;
        if (strlen($minutes) == 1)
            $minutes = "0".$minutes;
        if (strlen($seconds) == 1)
            $seconds = "0".$seconds;
            
        return $hours.":".$minutes.":".$seconds.".".$ms;
    }
}


