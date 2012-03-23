<?php

class Application_Model_Schedule {

    /**
     * Return true if there is nothing in the schedule for the given start time
     * up to the length of time after that.
     *
     * @param string $p_datetime
     *    In the format YYYY-MM-DD HH:MM:SS.mmmmmm
     * @param string $p_length
     *    In the format HH:MM:SS.mmmmmm
     * @return boolean|PEAR_Error
     */
    public static function isScheduleEmptyInRange($p_datetime, $p_length) {
        global $CC_CONFIG, $CC_DBC;
        if (empty($p_length)) {
            return new PEAR_Error("Application_Model_Schedule::isSchedulerEmptyInRange: param p_length is empty.");
        }
        $sql = "SELECT COUNT(*) FROM ".$CC_CONFIG["scheduleTable"]
        ." WHERE (starts >= '$p_datetime') "
        ." AND (ends <= (TIMESTAMP '$p_datetime' + INTERVAL '$p_length'))";
        //$_SESSION["debug"] = $sql;
        //echo $sql;
        $count = $CC_DBC->GetOne($sql);
        //var_dump($count);
        return ($count == '0');
    }

    /**
     * Return TRUE if file is going to be played in the future.
     *
     * @param string $p_fileId
     */
    public function IsFileScheduledInTheFuture($p_fileId)
    {
        global $CC_CONFIG, $CC_DBC;
        $sql = "SELECT COUNT(*) FROM ".$CC_CONFIG["scheduleTable"]
        ." WHERE file_id = {$p_fileId} AND starts > NOW()";
        $count = $CC_DBC->GetOne($sql);
        if (is_numeric($count) && ($count != '0')) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * Returns data related to the scheduled items.
     *
     * @param int $p_prev
     * @param int $p_next
     * @return date
     */
    public static function GetPlayOrderRange($p_prev = 1, $p_next = 1)
    {
        if (!is_int($p_prev) || !is_int($p_next)){
            //must enter integers to specify ranges
            return array();
        }

        $date = new Application_Model_DateHelper;
        $timeNow = $date->getTimestamp();
        $utcTimeNow = $date->getUtcTimestamp();
        
        $shows = Application_Model_Show::getPrevCurrentNext($utcTimeNow);
        $previousShowID = count($shows['previousShow'])>0?$shows['previousShow'][0]['id']:null;
        $currentShowID = count($shows['currentShow'])>0?$shows['currentShow'][0]['id']:null;
        $nextShowID = count($shows['nextShow'])>0?$shows['nextShow'][0]['id']:null;
        $results = Application_Model_Schedule::GetPrevCurrentNext($previousShowID, $currentShowID, $nextShowID, $utcTimeNow);
        
        $range = array("env"=>APPLICATION_ENV,
            "schedulerTime"=>$timeNow,
            "previous"=>$results['previous'] !=null?$results['previous']:(count($shows['previousShow'])>0?$shows['previousShow'][0]:null),
            "current"=>$results['current'] !=null?$results['current']:null,
            "next"=> $results['next'] !=null?$results['next']:(count($shows['nextShow'])>0?$shows['nextShow'][0]:null),
            "currentShow"=>$shows['currentShow'],
            "nextShow"=>$shows['nextShow'],
            "timezone"=> date("T"),
            "timezoneOffset"=> date("Z"));
        
        return $range;
    }
    
    /**
     * Queries the database for the set of schedules one hour before and after the given time.
     * If a show starts and ends within that time that is considered the current show. Then the
     * scheduled item before it is the previous show, and the scheduled item after it is the next
     * show. This way the dashboard getCurrentPlaylist is very fast. But if any one of the three
     * show types are not found through this mechanism a call is made to the old way of querying
     * the database to find the track info.
    **/
    public static function GetPrevCurrentNext($p_previousShowID, $p_currentShowID, $p_nextShowID, $p_timeNow)
    {
        if ($p_previousShowID == null && $p_currentShowID == null && $p_nextShowID == null)
            return;
        
        global $CC_CONFIG, $CC_DBC;
        $sql = 'Select ft.artist_name, ft.track_title, st.starts as starts, st.ends as ends, st.media_item_played as media_item_played
                FROM cc_schedule st LEFT JOIN cc_files ft ON st.file_id = ft.id 
                WHERE ';
                
         if (isset($p_previousShowID)){
            if (isset($p_nextShowID) || isset($p_currentShowID))
                $sql .= '(';
            $sql .= 'st.instance_id = '.$p_previousShowID;
        }
        if ($p_currentShowID != null){
            if ($p_previousShowID != null)
                $sql .= ' OR ';
            else if($p_nextShowID != null)
                $sql .= '(';
            $sql .= 'st.instance_id = '.$p_currentShowID;
        }
        if ($p_nextShowID != null) {
            if ($p_previousShowID != null || $p_currentShowID != null)
                $sql .= ' OR ';
            $sql .= 'st.instance_id = '.$p_nextShowID;
            if($p_previousShowID != null || $p_currentShowID != null)
                $sql .= ')';
        } else if($p_previousShowID != null && $p_currentShowID != null)
            $sql .= ')';
        
        $sql .= ' AND st.playout_status > 0 ORDER BY st.starts';
        
        $rows = $CC_DBC->GetAll($sql);
        $numberOfRows = count($rows);

        $results['previous'] = null;
        $results['current'] = null;
        $results['next'] = null;
        
        $timeNowAsMillis = strtotime($p_timeNow);
        for( $i = 0; $i < $numberOfRows; ++$i ){
           if ((strtotime($rows[$i]['starts']) <= $timeNowAsMillis) && (strtotime($rows[$i]['ends']) >= $timeNowAsMillis)){
                if ( $i - 1 >= 0){
                    $results['previous'] = array("name"=>$rows[$i-1]["artist_name"]." - ".$rows[$i-1]["track_title"],
                            "starts"=>$rows[$i-1]["starts"],
                            "ends"=>$rows[$i-1]["ends"]);
                }
                $results['current'] =  array("name"=>$rows[$i]["artist_name"]." - ".$rows[$i]["track_title"],
                            "starts"=>$rows[$i]["starts"],
                            "ends"=>$rows[$i]["ends"],
                            "media_item_played"=>$rows[$i]["media_item_played"],
                            "record"=>0);
                
                if ( isset($rows[$i+1])){
                    $results['next'] =  array("name"=>$rows[$i+1]["artist_name"]." - ".$rows[$i+1]["track_title"],
                            "starts"=>$rows[$i+1]["starts"],
                            "ends"=>$rows[$i+1]["ends"]);
                }
                break;
            }
            if (strtotime($rows[$i]['ends']) < $timeNowAsMillis ) {
                $previousIndex = $i;
            }
            if (strtotime($rows[$i]['starts']) > $timeNowAsMillis) {
                $results['next'] = array("name"=>$rows[$i]["artist_name"]." - ".$rows[$i]["track_title"],
                            "starts"=>$rows[$i]["starts"],
                            "ends"=>$rows[$i]["ends"]);
                break;
            }
        }
        //If we didn't find a a current show because the time didn't fit we may still have
        //found a previous show so use it.
        if ($results['previous'] === null && isset($previousIndex)) {
                $results['previous'] = array("name"=>$rows[$previousIndex]["artist_name"]." - ".$rows[$previousIndex]["track_title"],
                            "starts"=>$rows[$previousIndex]["starts"],
                            "ends"=>$rows[$previousIndex]["ends"]);;
        }
        return $results;
    }
    
    public static function GetLastScheduleItem($p_timeNow){
        global $CC_CONFIG, $CC_DBC;

        $sql = "SELECT"
        ." ft.artist_name, ft.track_title,"
        ." st.starts as starts, st.ends as ends"
        ." FROM $CC_CONFIG[scheduleTable] st"
        ." LEFT JOIN $CC_CONFIG[filesTable] ft"
        ." ON st.file_id = ft.id"
        ." LEFT JOIN $CC_CONFIG[showInstances] sit"
        ." ON st.instance_id = sit.id"
        ." WHERE st.ends < TIMESTAMP '$p_timeNow'"
        ." AND st.starts >= sit.starts" //this and the next line are necessary since we can overbook shows.
        ." AND st.starts < sit.ends"
        ." ORDER BY st.ends DESC"
        ." LIMIT 1";

        $row = $CC_DBC->GetAll($sql);
        return $row;
    }


    public static function GetCurrentScheduleItem($p_timeNow, $p_instanceId){
        global $CC_CONFIG, $CC_DBC;

        /* Note that usually there will be one result returned. In some
         * rare cases two songs are returned. This happens when a track
         * that was overbooked from a previous show appears as if it
         * hasnt ended yet (track end time hasn't been reached yet). For
         * this reason,  we need to get the track that starts later, as
         * this is the *real* track that is currently playing. So this
         * is why we are ordering by track start time. */
        $sql = "SELECT *"
        ." FROM $CC_CONFIG[scheduleTable] st"
        ." LEFT JOIN $CC_CONFIG[filesTable] ft"
        ." ON st.file_id = ft.id"
        ." WHERE st.starts <= TIMESTAMP '$p_timeNow'"
        ." AND st.instance_id = $p_instanceId"
        ." AND st.ends > TIMESTAMP '$p_timeNow'"
        ." ORDER BY st.starts DESC"
        ." LIMIT 1";

        $row = $CC_DBC->GetAll($sql);
        return $row;
    }

    public static function GetNextScheduleItem($p_timeNow){
        global $CC_CONFIG, $CC_DBC;

        $sql = "SELECT"
        ." ft.artist_name, ft.track_title,"
        ." st.starts as starts, st.ends as ends"
        ." FROM $CC_CONFIG[scheduleTable] st"
        ." LEFT JOIN $CC_CONFIG[filesTable] ft"
        ." ON st.file_id = ft.id"
        ." LEFT JOIN $CC_CONFIG[showInstances] sit"
        ." ON st.instance_id = sit.id"
        ." WHERE st.starts > TIMESTAMP '$p_timeNow'"
        ." AND st.starts >= sit.starts" //this and the next line are necessary since we can overbook shows.
        ." AND st.starts < sit.ends"
        ." ORDER BY st.starts"
        ." LIMIT 1";

        $row = $CC_DBC->GetAll($sql);
        return $row;
    }

    /**
     * Builds an SQL Query for accessing scheduled item information from
     * the database.
     *
     * @param int $timeNow
     * @param int $timePeriod
     * @param int $count
     * @param String $interval
     * @return date
     *
     * $timeNow is the the currentTime in the format "Y-m-d H:i:s".
     * For example: 2011-02-02 22:00:54
     *
     * $timePeriod can be either negative, zero or positive. This is used
     * to indicate whether we want items from the past, present or future.
     *
     * $count indicates how many results we want to limit ourselves to.
     *
     * $interval is used to indicate how far into the past or future we
     * want to search the database. For example "5 days", "18 hours", "60 minutes",
     * "30 seconds" etc.
     */
    public static function GetScheduledItemData($timeStamp, $timePeriod=0, $count = 0, $interval="0 hours")
    {
        global $CC_CONFIG, $CC_DBC;

        $sql = "SELECT DISTINCT"
        ." pt.name,"
        ." ft.track_title,"
        ." ft.artist_name,"
        ." ft.album_title,"
        ." st.starts,"
        ." st.ends,"
        ." st.clip_length,"
        ." st.media_item_played,"
        ." st.group_id,"
        ." show.name as show_name,"
        ." st.instance_id"
        ." FROM $CC_CONFIG[scheduleTable] st"
        ." LEFT JOIN $CC_CONFIG[filesTable] ft"
        ." ON st.file_id = ft.id"
        ." LEFT JOIN $CC_CONFIG[playListTable] pt"
        ." ON st.playlist_id = pt.id"
        ." LEFT JOIN $CC_CONFIG[showInstances] si"
        ." ON st.instance_id = si.id"
        ." LEFT JOIN $CC_CONFIG[showTable] show"
        ." ON si.show_id = show.id"
        ." WHERE st.starts < si.ends";

        if ($timePeriod < 0){
            $sql .= " AND st.ends < TIMESTAMP '$timeStamp'"
            ." AND st.ends > (TIMESTAMP '$timeStamp' - INTERVAL '$interval')"
            ." ORDER BY st.starts DESC"
           	." LIMIT $count";
        } else if ($timePeriod == 0){
            $sql .= " AND st.starts <= TIMESTAMP '$timeStamp'"
       	    ." AND st.ends >= TIMESTAMP '$timeStamp'";
        } else if ($timePeriod > 0){
           	$sql .= " AND st.starts > TIMESTAMP '$timeStamp'"
           	." AND st.starts < (TIMESTAMP '$timeStamp' + INTERVAL '$interval')"
           	." ORDER BY st.starts"
           	." LIMIT $count";
        }

        $rows = $CC_DBC->GetAll($sql);
        return $rows;
    }

    
    public static function getScheduleItemsInRange($starts, $ends)
    {
        global $CC_DBC, $CC_CONFIG;

        $sql = "SELECT"
        ." si.starts as show_starts,"
        ." si.ends as show_ends,"
        ." si.rebroadcast as rebroadcast,"
        ." st.starts as item_starts,"
        ." st.ends as item_ends,"
        ." st.clip_length as clip_length,"
        ." ft.track_title as track_title,"
        ." ft.artist_name as artist_name,"
        ." ft.album_title as album_title,"
        ." s.name as show_name,"
        ." si.id as instance_id,"
        ." pt.name as playlist_name"
        ." FROM ".$CC_CONFIG["showInstances"]." si"
        ." LEFT JOIN ".$CC_CONFIG["scheduleTable"]." st"
        ." ON st.instance_id = si.id"
        ." LEFT JOIN ".$CC_CONFIG["playListTable"]." pt"
        ." ON st.playlist_id = pt.id"
        ." LEFT JOIN ".$CC_CONFIG["filesTable"]." ft"
        ." ON st.file_id = ft.id"
        ." LEFT JOIN ".$CC_CONFIG["showTable"]." s"
        ." ON si.show_id = s.id"
        ." WHERE ((si.starts < TIMESTAMP '$starts' AND si.ends > TIMESTAMP '$starts')"
        ." OR (si.starts > TIMESTAMP '$starts' AND si.ends < TIMESTAMP '$ends')"
        ." OR (si.starts < TIMESTAMP '$ends' AND si.ends > TIMESTAMP '$ends'))"
        ." AND (st.starts < si.ends)"
        ." ORDER BY si.id, si.starts, st.starts";
        
        return $CC_DBC->GetAll($sql);
    }

	/*
	 *
	 * @param DateTime $p_startDateTime
	 *
	 * @param DateTime $p_endDateTime
	 *
	 * @return array $scheduledItems
	 *
	 */
    public static function GetScheduleDetailItems($p_start, $p_end, $p_shows)
    {
        global $CC_CONFIG, $CC_DBC;

        $sql = "SELECT DISTINCT

        showt.name AS show_name, showt.color AS show_color,
        showt.background_color AS show_background_color, showt.id AS show_id,

        si.starts AS si_starts, si.ends AS si_ends, si.time_filled AS si_time_filled,
        si.record AS si_record, si.rebroadcast AS si_rebroadcast, si.id AS si_id, si.last_scheduled AS si_last_scheduled,

        sched.starts AS sched_starts, sched.ends AS sched_ends, sched.id AS sched_id,
        sched.cue_in AS cue_in, sched.cue_out AS cue_out,
        sched.fade_in AS fade_in, sched.fade_out AS fade_out,
        sched.playout_status AS playout_status,

        ft.track_title AS file_track_title, ft.artist_name AS file_artist_name,
        ft.album_title AS file_album_title, ft.length AS file_length

        FROM
        ((cc_schedule AS sched JOIN cc_files AS ft ON (sched.file_id = ft.id)
        RIGHT OUTER JOIN cc_show_instances AS si ON (si.id = sched.instance_id))
        JOIN cc_show AS showt ON (showt.id = si.show_id)
        )

        WHERE si.modified_instance = false AND

        ((si.starts >= '{$p_start}' AND si.starts < '{$p_end}')
        OR (si.ends > '{$p_start}' AND si.ends <= '{$p_end}')
        OR (si.starts <= '{$p_start}' AND si.ends >= '{$p_end}'))";
        
        
        if (count($p_shows) > 0) {
            $sql .= " AND show_id IN (".implode(",", $p_shows).")";
        }

        $sql .= " ORDER BY si.starts, sched.starts;";

        Logging::log($sql);

        $rows = $CC_DBC->GetAll($sql);
        return $rows;
    }

    public static function GetShowInstanceItems($instance_id)
    {
        global $CC_CONFIG, $CC_DBC;

        $sql = "SELECT DISTINCT pt.name, ft.track_title, ft.artist_name, ft.album_title, st.starts, st.ends, st.clip_length, st.media_item_played, st.group_id, show.name as show_name, st.instance_id"
        ." FROM $CC_CONFIG[scheduleTable] st, $CC_CONFIG[filesTable] ft, $CC_CONFIG[playListTable] pt, $CC_CONFIG[showInstances] si, $CC_CONFIG[showTable] show"
        ." WHERE st.playlist_id = pt.id"
        ." AND st.file_id = ft.id"
        ." AND st.instance_id = si.id"
        ." AND si.show_id = show.id"
        ." AND instance_id = $instance_id"
        ." ORDER BY st.starts";

        $rows = $CC_DBC->GetAll($sql);
        return $rows;
    }

    public static function UpdateMediaPlayedStatus($p_id)
    {
        global $CC_CONFIG, $CC_DBC;
        $sql = "UPDATE ".$CC_CONFIG['scheduleTable']
                ." SET media_item_played=TRUE"
                ." WHERE id=$p_id";
        $retVal = $CC_DBC->query($sql);
        return $retVal;
    }

    public static function getSchduledPlaylistCount(){
       	global $CC_CONFIG, $CC_DBC;
        $sql = "SELECT count(*) as cnt FROM ".$CC_CONFIG['scheduleTable'];
        return $CC_DBC->GetOne($sql);
    }


    /**
     * Convert a time string in the format "YYYY-MM-DD HH:mm:SS"
     * to "YYYY-MM-DD-HH-mm-SS".
     *
     * @param string $p_time
     * @return string
     */
    private static function AirtimeTimeToPypoTime($p_time)
    {
        $p_time = substr($p_time, 0, 19);
        $p_time = str_replace(" ", "-", $p_time);
        $p_time = str_replace(":", "-", $p_time);
        return $p_time;
    }

    /**
     * Convert a time string in the format "YYYY-MM-DD-HH-mm-SS" to
     * "YYYY-MM-DD HH:mm:SS".
     *
     * @param string $p_time
     * @return string
     */
    private static function PypoTimeToAirtimeTime($p_time)
    {
        $t = explode("-", $p_time);
        return $t[0]."-".$t[1]."-".$t[2]." ".$t[3].":".$t[4].":00";
    }

    /**
     * Return true if the input string is in the format YYYY-MM-DD-HH-mm
     *
     * @param string $p_time
     * @return boolean
     */
    public static function ValidPypoTimeFormat($p_time)
    {
        $t = explode("-", $p_time);
        if (count($t) != 5) {
            return false;
        }
        foreach ($t as $part) {
            if (!is_numeric($part)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Converts a time value as a string (with format HH:MM:SS.mmmmmm) to
     * millisecs.
     *
     * @param string $p_time
     * @return int
     */
    public static function WallTimeToMillisecs($p_time)
    {
        $t = explode(":", $p_time);
        $millisecs = 0;
        if (strpos($t[2], ".")) {
            $secParts = explode(".", $t[2]);
            $millisecs = $secParts[1];
            $millisecs = substr($millisecs, 0, 3);
            $millisecs = intval($millisecs);
            $seconds = intval($secParts[0]);
        } else {
            $seconds = intval($t[2]);
        }
        $ret = $millisecs + ($seconds * 1000) + ($t[1] * 60 * 1000) + ($t[0] * 60 * 60 * 1000);
        return $ret;
    }


    /**
     * Compute the difference between two times in the format "HH:MM:SS.mmmmmm".
     * Note: currently only supports calculating millisec differences.
     *
     * @param string $p_time1
     * @param string $p_time2
     * @return double
     */
    private static function TimeDiff($p_time1, $p_time2)
    {
        $parts1 = explode(".", $p_time1);
        $parts2 = explode(".", $p_time2);
        $diff = 0;
        if ( (count($parts1) > 1) && (count($parts2) > 1) ) {
            $millisec1 = substr($parts1[1], 0, 3);
            $millisec1 = str_pad($millisec1, 3, "0");
            $millisec1 = intval($millisec1);
            $millisec2 = substr($parts2[1], 0, 3);
            $millisec2 = str_pad($millisec2, 3, "0");
            $millisec2 = intval($millisec2);
            $diff = abs($millisec1 - $millisec2)/1000;
        }
        return $diff;
    }

    /**
     * Returns an array of schedule items from cc_schedule table. Tries
     * to return at least 3 items (if they are available). The parameters
     * $p_startTime and $p_endTime specify the range. Schedule items returned
     * do not have to be entirely within this range. It is enough that the end
     * or beginning of the scheduled item is in the range.
     * 
     *
     * @param string $p_startTime
     *    In the format YYYY-MM-DD HH:MM:SS.nnnnnn
     * @param string $p_endTime
     *    In the format YYYY-MM-DD HH:MM:SS.nnnnnn
     * @return array
     *    Returns null if nothing found, else an array of associative
     *    arrays representing each row.
     */
    public static function GetItems($p_startTime, $p_endTime) {
        global $CC_CONFIG, $CC_DBC;
        
        $baseQuery = "SELECT st.file_id AS file_id,"
            ." st.id as id,"
            ." st.starts AS start,"
            ." st.ends AS end,"
            ." st.cue_in AS cue_in,"
            ." st.cue_out AS cue_out,"
            ." st.fade_in AS fade_in,"
            ." st.fade_out AS fade_out,"
            ." si.starts as show_start,"
            ." si.ends as show_end"
            ." FROM $CC_CONFIG[scheduleTable] as st"
            ." LEFT JOIN $CC_CONFIG[showInstances] as si"
            ." ON st.instance_id = si.id";
   

        $predicates = " WHERE st.ends > '$p_startTime'"
        ." AND st.starts < '$p_endTime'"
        ." ORDER BY st.starts";
        
        $sql = $baseQuery.$predicates;

        $rows = $CC_DBC->GetAll($sql);
        if (PEAR::isError($rows)) {
            return null;
        }
        
        if (count($rows) < 3){
            Logging::debug("Get Schedule: Less than 3 results returned. Doing another query since we need a minimum of 3 results.");
            
            $dt = new DateTime("@".time());
            $dt->add(new DateInterval("PT30M"));
            $range_end = $dt->format("Y-m-d H:i:s");
                      
            $predicates = " WHERE st.ends > '$p_startTime'"
            ." AND st.starts < '$range_end'"
            ." ORDER BY st.starts"
            ." LIMIT 3";
            
            $sql = $baseQuery.$predicates;
            $rows = $CC_DBC->GetAll($sql);
            if (PEAR::isError($rows)) {
                return null;
            }
        }
        
        return $rows;
    }

    public static function GetScheduledPlaylists($p_fromDateTime = null, $p_toDateTime = null){

        global $CC_CONFIG, $CC_DBC;

        /* if $p_fromDateTime and $p_toDateTime function parameters are null, then set range
         * from "now" to "now + 24 hours". */
        if (is_null($p_fromDateTime)) {
            $t1 = new DateTime("@".time());
            $range_start = $t1->format("Y-m-d H:i:s");
        } else {
            $range_start = Application_Model_Schedule::PypoTimeToAirtimeTime($p_fromDateTime);
        }
        if (is_null($p_fromDateTime)) {
            $t2 = new DateTime("@".time());
            
            $cache_ahead_hours = $CC_CONFIG["cache_ahead_hours"];
            
            if (is_numeric($cache_ahead_hours)){
                //make sure we are not dealing with a float
                $cache_ahead_hours = intval($cache_ahead_hours);
            } else {
                $cache_ahead_hours = 1;
            }
                        
            $t2->add(new DateInterval("PT".$cache_ahead_hours."H"));
            $range_end = $t2->format("Y-m-d H:i:s");
        } else {
            $range_end = Application_Model_Schedule::PypoTimeToAirtimeTime($p_toDateTime);
        }

        // Scheduler wants everything in a playlist
        $items = Application_Model_Schedule::GetItems($range_start, $range_end);

        $data = array();
        $utcTimeZone = new DateTimeZone("UTC");

        $data["status"] = array();
        $data["media"] = array();
        $data["harbor"] = array();
        
        
        $data["harbor"]['next_live_dj_show_end'] = Application_Model_ShowInstance::GetEndTimeOfNextShowWithLiveDJ();
        $data["harbor"]['transition_fade'] = Application_Model_Preference::GetDefaultTransitionFade();

        foreach ($items as $item){

            $storedFile = Application_Model_StoredFile::Recall($item["file_id"]);
            $uri = $storedFile->getFilePath();
            
            $showEndDateTime = new DateTime($item["show_end"], $utcTimeZone);
            $trackStartDateTime = new DateTime($item["start"], $utcTimeZone);
            $trackEndDateTime = new DateTime($item["end"], $utcTimeZone);

            /* Note: cue_out and end are always the same. */
            /* TODO: Not all tracks will have "show_end" */

            if ($trackEndDateTime->getTimestamp() > $showEndDateTime->getTimestamp()){
                $di = $trackStartDateTime->diff($showEndDateTime);
                
                $item["cue_out"] = $di->format("%H:%i:%s").".000";
            }
            
            

            $start = Application_Model_Schedule::AirtimeTimeToPypoTime($item["start"]);
            $data["media"][$start] = array(
                'id' => $storedFile->getGunid(),
                'row_id' => $item["id"],
                'uri' => $uri,
                'fade_in' => Application_Model_Schedule::WallTimeToMillisecs($item["fade_in"]),
                'fade_out' => Application_Model_Schedule::WallTimeToMillisecs($item["fade_out"]),
                'cue_in' => Application_Model_DateHelper::CalculateLengthInSeconds($item["cue_in"]),
                'cue_out' => Application_Model_DateHelper::CalculateLengthInSeconds($item["cue_out"]),
                'start' => $start,
                'end' => Application_Model_Schedule::AirtimeTimeToPypoTime($item["end"])
            );
        }

        return $data;
    }

    /**
     * Export the schedule in json formatted for pypo (the liquidsoap scheduler)
     *
     * @param string $p_fromDateTime
     *      In the format "YYYY-MM-DD-HH-mm-SS"
     * @param string $p_toDateTime
     *      In the format "YYYY-MM-DD-HH-mm-SS"
     */
    public static function GetScheduledPlaylistsOld($p_fromDateTime = null, $p_toDateTime = null)
    {
        global $CC_CONFIG, $CC_DBC;

        if (is_null($p_fromDateTime)) {
            $t1 = new DateTime("@".time());
            $range_start = $t1->format("Y-m-d H:i:s");
        } else {
            $range_start = Application_Model_Schedule::PypoTimeToAirtimeTime($p_fromDateTime);
        }
        if (is_null($p_fromDateTime)) {
            $t2 = new DateTime("@".time());
            $t2->add(new DateInterval("PT24H"));
            $range_end = $t2->format("Y-m-d H:i:s");
        } else {
            $range_end = Application_Model_Schedule::PypoTimeToAirtimeTime($p_toDateTime);
        }

        // Scheduler wants everything in a playlist
        $data = Application_Model_Schedule::GetItems($range_start, $range_end, true);
        $playlists = array();

        if (is_array($data)){
            foreach ($data as $dx){
                $start = $dx['start'];

                //chop off subseconds
                $start = substr($start, 0, 19);

                //Start time is the array key, needs to be in the format "YYYY-MM-DD-HH-mm-ss"
                $pkey = Application_Model_Schedule::AirtimeTimeToPypoTime($start);
                $timestamp =  strtotime($start);
                $playlists[$pkey]['source'] = "PLAYLIST";
                $playlists[$pkey]['x_ident'] = $dx['group_id'];
                $playlists[$pkey]['timestamp'] = $timestamp;
                $playlists[$pkey]['duration'] = $dx['clip_length'];
                $playlists[$pkey]['played'] = '0';
                $playlists[$pkey]['schedule_id'] = $dx['group_id'];
                $playlists[$pkey]['show_name'] = $dx['show_name'];
                $playlists[$pkey]['show_start'] = Application_Model_Schedule::AirtimeTimeToPypoTime($dx['show_start']);
                $playlists[$pkey]['show_end'] = Application_Model_Schedule::AirtimeTimeToPypoTime($dx['show_end']);
                $playlists[$pkey]['user_id'] = 0;
                $playlists[$pkey]['id'] = $dx['group_id'];
                $playlists[$pkey]['start'] = Application_Model_Schedule::AirtimeTimeToPypoTime($dx["start"]);
                $playlists[$pkey]['end'] = Application_Model_Schedule::AirtimeTimeToPypoTime($dx["end"]);
            }
        }
        ksort($playlists);

        foreach ($playlists as &$playlist)
        {
            $scheduleGroup = new Application_Model_ScheduleGroup($playlist["schedule_id"]);
            $items = $scheduleGroup->getItems();
            $medias = array();
            foreach ($items as $item)
            {
                $storedFile = Application_Model_StoredFile::Recall($item["file_id"]);
                $uri = $storedFile->getFileUrlUsingConfigAddress();

                $starts = Application_Model_Schedule::AirtimeTimeToPypoTime($item["starts"]);
                $medias[$starts] = array(
                    'id' => $storedFile->getGunid(),
                    'uri' => $uri,
                    'fade_in' => Application_Model_Schedule::WallTimeToMillisecs($item["fade_in"]),
                    'fade_out' => Application_Model_Schedule::WallTimeToMillisecs($item["fade_out"]),
                    'fade_cross' => 0,
                    'cue_in' => Application_Model_DateHelper::CalculateLengthInSeconds($item["cue_in"]),
                    'cue_out' => Application_Model_DateHelper::CalculateLengthInSeconds($item["cue_out"]),
                    'start' => $starts,
                    'end' => Application_Model_Schedule::AirtimeTimeToPypoTime($item["ends"])
                );
            }
            ksort($medias);
            $playlist['medias'] = $medias;
        }

        $result = array();
        $result['status'] = array('range' => array('start' => $range_start, 'end' => $range_end),
                                  'version' => AIRTIME_REST_VERSION);
        $result['playlists'] = $playlists;
        $result['check'] = 1;
        $result['stream_metadata'] = array();
        $result['stream_metadata']['format'] = Application_Model_Preference::GetStreamLabelFormat();
        $result['stream_metadata']['station_name'] = Application_Model_Preference::GetStationName();

        return $result;
    }

    public static function deleteAll()
    {
        global $CC_CONFIG, $CC_DBC;
        $CC_DBC->query("TRUNCATE TABLE ".$CC_CONFIG["scheduleTable"]);
    }

    public static function createNewFormSections($p_view){
        $isSaas = Application_Model_Preference::GetPlanLevel() == 'disabled'?false:true;

        $formWhat = new Application_Form_AddShowWhat();
		$formWho = new Application_Form_AddShowWho();
		$formWhen = new Application_Form_AddShowWhen();
		$formRepeats = new Application_Form_AddShowRepeats();
		$formStyle = new Application_Form_AddShowStyle();
		$formLive = new Application_Form_AddShowLiveStream();

		$formWhat->removeDecorator('DtDdWrapper');
		$formWho->removeDecorator('DtDdWrapper');
		$formWhen->removeDecorator('DtDdWrapper');
		$formRepeats->removeDecorator('DtDdWrapper');
		$formStyle->removeDecorator('DtDdWrapper');
		$formLive->removeDecorator('DtDdWrapper');

        $p_view->what = $formWhat;
        $p_view->when = $formWhen;
        $p_view->repeats = $formRepeats;
        $p_view->who = $formWho;
        $p_view->style = $formStyle;
        $p_view->live = $formLive;

        $formWhat->populate(array('add_show_id' => '-1'));
        $formWhen->populate(array('add_show_start_date' => date("Y-m-d"),
                                      'add_show_start_time' => '00:00',
                                      'add_show_end_date_no_repeate' => date("Y-m-d"),
                                      'add_show_end_time' => '01:00',
                                      'add_show_duration' => '1h'));

        $formRepeats->populate(array('add_show_end_date' => date("Y-m-d")));

        if(!$isSaas){
            $formRecord = new Application_Form_AddShowRR();
            $formAbsoluteRebroadcast = new Application_Form_AddShowAbsoluteRebroadcastDates();
            $formRebroadcast = new Application_Form_AddShowRebroadcastDates();

            $formRecord->removeDecorator('DtDdWrapper');
            $formAbsoluteRebroadcast->removeDecorator('DtDdWrapper');
            $formRebroadcast->removeDecorator('DtDdWrapper');

            $p_view->rr = $formRecord;
            $p_view->absoluteRebroadcast = $formAbsoluteRebroadcast;
            $p_view->rebroadcast = $formRebroadcast;
        }
        $p_view->addNewShow = true;
    }
}
