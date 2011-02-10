<?php

class Application_Model_Nowplaying
{
    
    public static function InsertBlankRow($i, $rows){
        $startDateFull = $rows[$i-1][3];
        $endDateFull = $rows[$i][2];

        $startDate = explode(".", $startDateFull);
        $endDate = explode(".", $endDateFull);
        
        $epochStartMS =  strtotime($startDate[0])*1000;
        $epochEndMS =  strtotime($endDate[0])*1000;
        
        if (count($startDate) > 1)
            $epochStartMS += $startDate[1];
        if (count($endDate) > 1)
            $epochEndMS += $endDate[1];
        
        $blankRow = array(array("b", $startDateFull, $startDateFull, $endDate, Application_Model_DateHelper::ConvertMSToHHMMSSmm($epochEndMS - $epochStartMS), "-", "-", "-", "-" , "-", "", ""));
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
    
    public static function GetDataGridData($viewType, $dateString){
                
        //echo $dateString;
        if ($viewType == "now"){
            
            $date = new Application_Model_DateHelper;
            $timeNow = $date->getDate();
            
            /* When do "ORDER BY x DESC LIMIT 5" to ensure that we get the last 5 previously scheduled items.
             * However using DESC, puts our scheduled items in reverse order, so we need to reverse it again 
             * with array_reverse.
             */
            $previous = array_reverse(Schedule::Get_Scheduled_Item_Data($timeNow, -1, 1, "60 seconds"));
            $current = Schedule::Get_Scheduled_Item_Data($timeNow, 0);
            $next = Schedule::Get_Scheduled_Item_Data($timeNow, 1, 10, "24 hours");
        } else {
            $date = new Application_Model_DateHelper;
            $time = $date->getTime();
            $date->setDate($dateString." ".$time);
            $timeNow = $date->getDate();
            
            $previous = array_reverse(Schedule::Get_Scheduled_Item_Data($timeNow, -1, "ALL", $date->getNowDayStartDiff()." seconds"));
            $current = Schedule::Get_Scheduled_Item_Data($timeNow, 0);
            $next = Schedule::Get_Scheduled_Item_Data($timeNow, 1, "ALL", $date->getNowDayEndDiff()." seconds");                        
        }
        
        $rows = array();
                
        foreach ($previous as $item){
            array_push($rows, array("p", $item["starts"], $item["starts"], $item["ends"], $item["clip_length"], $item["track_title"], $item["artist_name"],
                $item["album_title"], $item["name"], $item["show_name"], $item["current_show"], $item["group_id"]));
        }
        
        foreach ($current as $item){
            array_push($rows, array("c", $item["starts"], $item["starts"], $item["ends"], $item["clip_length"], $item["track_title"], $item["artist_name"],
                $item["album_title"], $item["name"], $item["show_name"], $item["current_show"], $item["group_id"]));    
        }
        
        foreach ($next as $item){
            array_push($rows, array("n", $item["starts"], $item["starts"], $item["ends"], $item["clip_length"], $item["track_title"], $item["artist_name"],
                $item["album_title"], $item["name"], $item["show_name"], $item["current_show"], $item["group_id"]));
        }
        
        $rows = Application_Model_Nowplaying::FindGaps($rows);
        $data = array("rows"=>$rows);
        
        return $data;
    }
}

