<?php

class Application_Model_Nowplaying
{

    private static function CreateHeaderRow($p_showName, $p_showStart, $p_showEnd){
        return array("h", "", $p_showStart, $p_showEnd, $p_showName, "", "", "", "", "", "");
    }

    private static function CreateDatatableRows($p_dbRows){
        $dataTablesRows = array();

        $epochNow = time();

        $lastRow = end($p_dbRows);
        
        //Information about show is true for all rows in parameter so only check the last row's show
        //start and end times.
        if (isset($lastRow)){
            $showStartDateTime = Application_Model_DateHelper::ConvertToLocalDateTime($lastRow['show_starts']);
            $showEndDateTime = Application_Model_DateHelper::ConvertToLocalDateTime($lastRow['show_ends']);
            $showStarts = $showStartDateTime->format("Y-m-d H:i:s");
            $showEnds = $showEndDateTime->format("Y-m-d H:i:s");
        }
        
        foreach ($p_dbRows as $dbRow) {

            $itemStartDateTime = Application_Model_DateHelper::ConvertToLocalDateTime($dbRow['item_starts']);
            $itemEndDateTime = Application_Model_DateHelper::ConvertToLocalDateTime($dbRow['item_ends']);
            
            $itemStarts = $itemStartDateTime->format("Y-m-d H:i:s");
            $itemEnds = $itemEndDateTime->format("Y-m-d H:i:s");

            $status = ($dbRow['show_ends'] < $dbRow['item_ends']) ? "x" : "";

            $type = "a";
            $type .= ($itemStartDateTime->getTimestamp() <= $epochNow
                    && $epochNow < $itemEndDateTime->getTimestamp()
                    && $epochNow < $showEndDateTime->getTimestamp()) ? "c" : "";

            // remove millisecond from the time format
            $itemStart = explode('.', $dbRow['item_starts']);
            $itemEnd = explode('.', $dbRow['item_ends']);

            //format duration
            $duration = explode('.', $dbRow['clip_length']);
            $formatted = self::FormatDuration($duration[0]);
            $dataTablesRows[] = array($type, $itemStarts, $itemStarts, $itemEnds,
                $formatted, $dbRow['track_title'], $dbRow['artist_name'], $dbRow['album_title'],
                $dbRow['playlist_name'], $dbRow['show_name'], $status);
        }

        //Modify the last entry in the data table to adjust its end time to be equal to the
        //shows end time if it exceeds it.
        $lastRow = end($dataTablesRows);
        if (isset($lastRow) && strtotime($showEnds) < strtotime($lastRow[3])){
            $dataTablesRows[sizeof($dataTablesRows)-1][3] = $showEnds;
        }
        return $dataTablesRows;
    }

    private static function CreateGapRow($p_gapTime){
        return array("g", "", "", "", $p_gapTime, "", "", "", "", "", "");
    }

    private static function CreateRecordingRow($p_showInstance){
        return array("r", "", "", "", $p_showInstance->getName(), "", "", "", "", "", "");
    }


    /*
     * The purpose of this function is to return an array of scheduled
     * items. There are two parameters. $p_viewType can be either "now"
     * or "day". If "now", we show all scheduled items in the near future.
     * 
     * If "day" we need to find what day was requested by the user, and return
     * scheduled items for that day.
     * 
     * $p_dateString is only used when $p_viewType is "day" it is in the format
     * "2012-12-31". In this case it tells us which day to use. 
     */
    public static function GetDataGridData($p_viewType,  $p_dateString){

        if ($p_viewType == "now"){
            $start_dt = new DateTime("now", new DateTimeZone("UTC"));
            $end_dt = clone $start_dt;
            
            $start_dt->sub(new DateInterval("PT60S"));
            $end_dt->add(new DateInterval("PT24H"));
        } else {
            //convert to UTC
            $utc_dt = Application_Model_DateHelper::ConvertToUtcDateTime($p_dateString);
            $start_dt = $utc_dt;
            
            $end_dt = clone $utc_dt;
            $end_dt->add(new DateInterval("PT24H"));
        }
        
        $starts = $start_dt->format("Y-m-d H:i:s");
        $ends = $end_dt->format("Y-m-d H:i:s");

        $showIds = Application_Model_ShowInstance::GetShowsInstancesIdsInRange($starts, $ends);
            
        //get all the pieces to be played between the start cut off and the end cut off.
        $scheduledItems = Application_Model_Schedule::getScheduleItemsInRange($starts, $ends);

        $orderedScheduledItems;
        foreach ($scheduledItems as $scheduledItem){
            $orderedScheduledItems[$scheduledItem['instance_id']][] = $scheduledItem;
        }

        $data = array();
        foreach ($showIds as $showId){
            $instanceId = $showId['id'];

            //gets the show information
            $si = new Application_Model_ShowInstance($instanceId);

            $showId = $si->getShowId();
            $show = new Application_Model_Show($showId);
            
            $showStartDateTime = Application_Model_DateHelper::ConvertToLocalDateTime($si->getShowInstanceStart());
            $showEndDateTime = Application_Model_DateHelper::ConvertToLocalDateTime($si->getShowInstanceEnd());

            //append show header row
            $data[] = self::CreateHeaderRow($show->getName(), $showStartDateTime->format("Y-m-d H:i:s"), $showEndDateTime->format("Y-m-d H:i:s"));

            $dataTablesRows = self::CreateDatatableRows($orderedScheduledItems[$instanceId]);

            //append show audio item rows
            $data = array_merge($data, $dataTablesRows);

            //append show gap time row
            $gapTime = self::FormatDuration($si->getShowEndGapTime(), true);
            if ($si->isRecorded())
               	$data[] = self::CreateRecordingRow($si);
            else if ($gapTime > 0)
               	$data[] = self::CreateGapRow($gapTime);
        }

        $timeNow = gmdate("Y-m-d H:i:s");
        $rows = Application_Model_Show::GetCurrentShow($timeNow);
        Application_Model_Show::ConvertToLocalTimeZone($rows, array("starts", "ends", "start_timestamp", "end_timestamp"));
        return array("currentShow"=>$rows, "rows"=>$data);
    }

    public static function ShouldShowPopUp(){
        $today = mktime(0, 0, 0, gmdate("m"), gmdate("d"), gmdate("Y"));
        $remindDate = Application_Model_Preference::GetRemindMeDate();
        if($remindDate == NULL || $today >= $remindDate){
            return true;
        }
    }
    /*
     * default $time format should be in format of 00:00:00
     * if $inSecond = true, then $time should be in seconds
     */
    private static function FormatDuration($time, $inSecond=false){
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
