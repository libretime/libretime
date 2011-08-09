<?php

class Application_Model_Nowplaying
{

	public static function CreateHeaderRow($p_showName, $p_showStart, $p_showEnd){
		return array("h", "", $p_showStart, $p_showEnd, $p_showName, "", "", "", "", "", "");
	}

	public static function CreateDatatableRows($p_dbRows){
        $dataTablesRows = array();
        
        $date = new DateHelper;
        $timeNow = $date->getTimestamp();
        
        foreach ($p_dbRows as $dbRow){
            $status = ($dbRow['show_ends'] < $dbRow['item_ends']) ? "x" : "";
            
            $type = "a";
            $type .= ($dbRow['item_ends'] > $timeNow && $dbRow['item_starts'] <= $timeNow) ? "c" : "";
            
            // remove millisecond from the time format
            $itemStart = explode('.', $dbRow['item_starts']);
            $itemEnd = explode('.', $dbRow['item_ends']);
            
            //format duration
            $duration = explode('.', $dbRow['clip_length']);
            $formated = Application_Model_Nowplaying::FormatDuration($duration[0]);
            $dataTablesRows[] = array($type, $dbRow['show_starts'], $itemStart[0], $itemEnd[0],
                $formated, $dbRow['track_title'], $dbRow['artist_name'], $dbRow['album_title'],
                $dbRow['playlist_name'], $dbRow['show_name'], $status);
        }

		return $dataTablesRows;
	}
	
	public static function CreateGapRow($p_gapTime){
		return array("g", "", "", "", $p_gapTime, "", "", "", "", "", "");
	}
	
	public static function CreateRecordingRow($p_showInstance){
		return array("r", "", "", "", $p_showInstance->getName(), "", "", "", "", "", "");
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
            $gapTime = Application_Model_Nowplaying::FormatDuration($si->getShowEndGapTime(), true);
            if ($si->isRecorded())
            	$data[] = Application_Model_Nowplaying::CreateRecordingRow($si);
            else if ($gapTime > 0)
            	$data[] = Application_Model_Nowplaying::CreateGapRow($gapTime);
        }

        return array("currentShow"=>Show_DAL::GetCurrentShow($timeNow), "rows"=>$data);
    }

    public static function ShouldShowPopUp(){
        $today = mktime(0, 0, 0, date("m")  , date("d"), date("Y"));
        $remindDate = Application_Model_Preference::GetRemindMeDate();
        if($remindDate == NULL || $today >= $remindDate){
            return true;
        }
    }
    /*
     * default $time format should be in format of 00:00:00
     * if $inSecond = true, then $time should be in seconds  
     */
    public static function FormatDuration($time, $inSecond=false){
        if($inSecond == false){
            $duration = explode(':', $time);
        }else{
            $duration = array();
            $duration[0] = intval(($time/3600)%24);
            $duration[1] = intval(($time/60)%60);
            $duration[2] = $time%60;
        }
        
        if($duration[2] == 0){
            $duration[2] = '';
        }else{
            $duration[2] = intval($duration[2],10).'s';
        }
        
        if($duration[1] == 0){
            if($duration[2] == ''){
                $duration[1] = '';
            }else{
                $duration[1] = intval($duration[1],10).'m ';
            }
        }else{
            $duration[1] = intval($duration[1],10).'m ';
        }
        
        if($duration[0] == 0){
            $duration[0] = '';
        }else{
            $duration[0] = intval($duration[0],10).'h ';
        }
        
        $out = $duration[0].$duration[1].$duration[2];
        return $out;
    }
}
