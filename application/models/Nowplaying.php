<?php

class Application_Model_Nowplaying
{
    /*
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
    */

    
/*
    public static function FindGapsBetweenShows($showsMap){

        $previousShow = null;
        foreach($showsMap as $k => $show){
            $currentShow = $showsMap[$k];

            if (!is_null($previousShow)){
                $diff = strtotime($currentShow['starts']) - strtotime($previousShow['ends'])
                if ($$diff != 0){
                    //array_splice($showsMap, $i, 0, $blankRow);

                }
            }

            $previousShow = $showsMap[$k];
        }
        
        return $showsMap;
    }
*/
    public static function FindGapAtEndOfShow($show, $rows){
        $showStartTime = $show['starts'];
        $showEndTime = $show['ends'];

        if (count($rows) > 1){
            $lastItem = $rows[count($rows)-1];
            $lastItemEndTime = $lastItem['ends'];
        } else {
            $lastItemEndTime = $showStartTime;
        }

        $diff = Application_Model_DateHelper::TimeDiff($lastItemEndTime, $showEndTime);

        if ($diff <= 0){
            //ok!
            return null;
        } else {
            //There is a gap at the end of the show. Return blank row
            return array("b", $diff, "-", "-", "-", "-", "-", "-", "-", "-", "-", "-");
        }
    }

    public static function GetDataGridData($viewType, $dateString){

        if ($viewType == "now"){
            $date = new Application_Model_DateHelper;
            $timeNow = $date->getDate();

            $startCutoff = 60;
            $endCutoff = 86400; //60*60*24 - seconds in a day
        } else {
            $date = new Application_Model_DateHelper;
            $time = $date->getTime();
            $date->setDate($dateString." ".$time);
            $timeNow = $date->getDate();

            $startCutoff = $date->getNowDayStartDiff();
            $endCutoff = $date->getNowDayEndDiff();            
        }


        $showsMap = Show_DAL::GetShowsInRange($timeNow, $startCutoff, $endCutoff);

        //iterate over each show, and calculate information for it.
        foreach($showsMap as $k => $show){
            $rows = Schedule::GetShowInstanceItems($k);
            $gapRow = Application_Model_Nowplaying::FindGapAtEndOfShow($showsMap[$k], $rows);
            foreach ($rows as $item){
                //check if this item is in todays date range
                if (strtotime($item['ends']) > $date->getEpochTime() - $startCutoff
                    && strtotime($item['starts']) < $date->getEpochTime() + $endCutoff){

                    if ($item['ends'] < $timeNow){
                        $type = "p";
                    } else if ($item['starts'] < $timeNow && $timeNow < $item['ends']){
                        $type = "c";
                    } else {
                        $type = "n";
                    }
                    
                    array_push($showsMap[$k]['items'], array($type, $item["starts"], $item["starts"], $item["ends"], $item["clip_length"], $item["track_title"], $item["artist_name"],
                        $item["album_title"], $item["name"], $item["show_name"], $item["instance_id"], $item["group_id"]));
                }
            }

            if (!is_null($gapRow))
                array_push($showsMap[$k]['items'], $gapRow);
        }

        //$showsMap = Application_Model_Nowplaying::FindGapsBetweenShows($showsMap);

       
        $date = new Application_Model_DateHelper;
        $timeNow = $date->getDate();

        return array("currentShow"=>Show_DAL::GetCurrentShow($timeNow), "rows"=>$showsMap);
    }
}
