<?php

class Application_Model_Dashboard
{

    public static function GetPreviousItem($p_timeNow){
        //get previous show and previous item in the schedule table.
        //Compare the two and if the last show was recorded and started
        //after the last item in the schedule table, then return the show's
        //name. Else return the last item from the schedule.

        $showInstance = ShowInstance::GetLastShowInstance($p_timeNow);
        $row = Schedule::GetLastScheduleItem($p_timeNow);

        if (is_null($showInstance)){
            if (count($row) == 0){
                return null;
            } else {
                //should never reach here. Doesnt make sense to have
                //a schedule item not within a show_instance.
            }
        } else {
            if (count($row) == 0){
                //last item is a show instance
                return array("name"=>$showInstance->getName(),
                            "starts"=>$showInstance->getShowStart(),
                            "ends"=>$showInstance->getShowEnd());
            } else {
                //return the one that started later.
                if ($row[0]["starts"] >= $showInstance->getShowStart()){
                    return array("name"=>$row[0]["artist_name"]." - ".$row[0]["track_title"],
                            "starts"=>$row[0]["starts"],
                            "ends"=>$row[0]["ends"]);
                } else {
                    return array("name"=>$showInstance->getName(),
                                "starts"=>$showInstance->getShowStart(),
                                "ends"=>$showInstance->getShowEnd());
                }
            }
        }
    }

    public static function GetCurrentItem($p_timeNow){
        //get previous show and previous item in the schedule table.
        //Compare the two and if the last show was recorded and started
        //after the last item in the schedule table, then return the show's
        //name. Else return the last item from the schedule.

        $showInstance = ShowInstance::GetCurrentShowInstance($p_timeNow);
        $row = Schedule::GetCurrentScheduleItem($p_timeNow);

        if (is_null($showInstance)){
            if (count($row) == 0){
                return null;
            } else {
                //should never reach here. Doesnt make sense to have
                //a schedule item not within a show_instance.
            }
        } else {
            if (count($row) == 0){
                //last item is a show instance
                return array("name"=>$showInstance->getName(),
                            "starts"=>$showInstance->getShowStart(),
                            "ends"=>$showInstance->getShowEnd(),
                            "media_item_played"=>false, //TODO
                            "record"=>$showInstance->isRecorded()); //TODO
            } else {
                 return array("name"=>$row[0]["artist_name"]." - ".$row[0]["track_title"],
                        "starts"=>$row[0]["starts"],
                        "ends"=>$row[0]["ends"],
                        "media_item_played"=>$row[0]["media_item_played"],
                        "record"=>0);
            }
        }
    }

    public static function GetNextItem($p_timeNow){
        //get previous show and previous item in the schedule table.
        //Compare the two and if the last show was recorded and started
        //after the last item in the schedule table, then return the show's
        //name. Else return the last item from the schedule.

        $showInstance = ShowInstance::GetNextShowInstance($p_timeNow);
        $row = Schedule::GetNextScheduleItem($p_timeNow);

        if (is_null($showInstance)){
            if (count($row) == 0){
                return null;
            } else {
                return array("name"=>$row[0]["artist_name"]." - ".$row[0]["track_title"],
                            "starts"=>$row[0]["starts"],
                            "ends"=>$row[0]["ends"]);
            }
        } else {
            if (count($row) == 0){
                //last item is a show instance
                return array("name"=>$showInstance->getName(),
                            "starts"=>$showInstance->getShowStart(),
                            "ends"=>$showInstance->getShowEnd());
            } else {
                //return the one that starts sooner.
                
                if ($row[0]["starts"] <= $showInstance->getShowStart()){
                    return array("name"=>$row[0]["artist_name"]." - ".$row[0]["track_title"],
                            "starts"=>$row[0]["starts"],
                            "ends"=>$row[0]["ends"]);
                } else {
                    return array("name"=>$showInstance->getName(),
                                "starts"=>$showInstance->getShowStart(),
                                "ends"=>$showInstance->getShowEnd());
                }
            }
        }
    }

}
