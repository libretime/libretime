<?php

class Application_Model_Nowplaying
{

	public static function CreateHeaderRow($p_showName, $p_showStart, $p_showEnd){
		return array("h", $p_showName, $p_showStart, $p_showEnd, "", "", "", "", "", "", "");
	}

	public static function CreateDatatableRows($p_dbRows){
		
		$dataTablesRows = array();
		
		$date = new DateHelper;
		$timeNow = $date->getTimestamp();
		
		
		foreach ($p_dbRows as $dbRow){
			$status = ($dbRow['show_ends'] < $dbRow['item_ends']) ? "x" : "";
			
			$type = "a";
			$type .= ($dbRow['item_ends'] > $timeNow && $dbRow['item_starts'] <= $timeNow) ? "c" : "";
			
			$dataTablesRows[] = array($type, $dbRow['show_starts'], $dbRow['item_starts'], $dbRow['item_ends'],
					$dbRow['clip_length'], $dbRow['track_title'], $dbRow['artist_name'], $dbRow['album_title'],
					$dbRow['playlist_name'], $dbRow['show_name'], $status);
		}

		return $dataTablesRows;
	}
	
	public static function CreateGapRow($p_gapTime){
		return array("g", $p_gapTime, "", "", "", "", "", "", "", "", "");
	}

	public static function GetDataGridData($viewType, $dateString){

        if ($viewType == "now"){
            $date = new DateHelper;
            $timeNow = $date->getTimestamp();

            $startCutoff = 60;
            $endCutoff = 86400; //60*60*24 - seconds in a day
        } else {
            $date = new DateHelper;
            $time = $date->getTime();
            $date->setDate($dateString." ".$time);
            $timeNow = $date->getTimestamp();

            $startCutoff = $date->getNowDayStartDiff();
            $endCutoff = $date->getNowDayEndDiff();
        }
        
		$data = array();

		$showIds = ShowInstance::GetShowsInstancesIdsInRange($timeNow, $startCutoff, $endCutoff);
		foreach ($showIds as $showId){
			$instanceId = $showId['id'];

			$si = new ShowInstance($instanceId);
			
			$showId = $si->getShowId();
			$show = new Show($showId);
			
			//append show header row
			$data[] = Application_Model_Nowplaying::CreateHeaderRow($show->getName(), $si->getShowStart(), $si->getShowEnd());
			
			$scheduledItems = $si->getScheduleItemsInRange($timeNow, $startCutoff, $endCutoff);
			$dataTablesRows = Application_Model_Nowplaying::CreateDatatableRows($scheduledItems);
			
			//append show audio item rows
			$data = array_merge($data, $dataTablesRows);
			
			//append show gap time row
			$gapTime = $si->getShowEndGapTime();
			if ($gapTime > 0)
				$data[] = Application_Model_Nowplaying::CreateGapRow($gapTime);
		}
		
		return array("currentShow"=>Show_DAL::GetCurrentShow($timeNow), "rows"=>$data);
	}
}
