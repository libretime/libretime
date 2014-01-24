<?php

use Airtime\CcScheduleQuery;
use Airtime\CcShowInstancesQuery;

class Application_Model_Schedule
{

    public static function getAllFutureScheduledFiles($instanceId=null)
    {
        $sql = <<<SQL
SELECT distinct(file_id)
FROM cc_schedule
WHERE ends > now() AT TIME ZONE 'UTC'
AND file_id is not null
SQL;

        $files = Application_Common_Database::prepareAndExecute( $sql, array());

        $real_files = array();
        foreach ($files as $f) {
            $real_files[] = $f['file_id'];
        }

        return $real_files;
    }

    /**
     * Returns data related to the scheduled items.
     *
     * @param  int  $p_prev
     * @param  int  $p_next
     * @return date
     */
    public static function GetPlayOrderRange($p_prev = 1, $p_next = 1)
    {
        //Everything in this function must be done in UTC. You will get a swift kick in the pants if you mess that up.
        
        if (!is_int($p_prev) || !is_int($p_next)) {
            //must enter integers to specify ranges
            Logging::info("Invalid range parameters: $p_prev or $p_next");

            return array();
        }

        $utcNow = new DateTime("now", new DateTimeZone("UTC"));
        
        $shows = Application_Model_Show::getPrevCurrentNext($utcNow);
        $previousShowID = count($shows['previousShow'])>0?$shows['previousShow'][0]['instance_id']:null;
        $currentShowID = count($shows['currentShow'])>0?$shows['currentShow'][0]['instance_id']:null;
        $nextShowID = count($shows['nextShow'])>0?$shows['nextShow'][0]['instance_id']:null;
        $results = self::GetPrevCurrentNext($previousShowID, $currentShowID, $nextShowID, $utcNow);

        $range = array("env"=>APPLICATION_ENV,
            "schedulerTime"=> $utcNow->format("Y-m-d H:i:s"),
            //Previous, current, next songs!
            "previous"=>$results['previous'] !=null?$results['previous']:(count($shows['previousShow'])>0?$shows['previousShow'][0]:null),
            "current"=>$results['current'] !=null?$results['current']:((count($shows['currentShow'])>0 && $shows['currentShow'][0]['record'] == 1)?$shows['currentShow'][0]:null),
            "next"=> $results['next'] !=null?$results['next']:(count($shows['nextShow'])>0?$shows['nextShow'][0]:null),
            //Current and next shows
            "currentShow"=>$shows['currentShow'],
            "nextShow"=>$shows['nextShow'],
        );

        return $range;
    }

    /**
     * Queries the database for the set of schedules one hour before
     * and after the given time. If a show starts and ends within that
     * time that is considered the current show. Then the scheduled item
     * before it is the previous show, and the scheduled item after it
     * is the next show. This way the dashboard getCurrentPlaylist is
     * very fast. But if any one of the three show types are not found
     * through this mechanism a call is made to the old way of querying
     * the database to find the track info.
    **/
    public static function GetPrevCurrentNext($p_previousShowID, $p_currentShowID, $p_nextShowID, $utcNow)
    {
        $timeZone = new DateTimeZone("UTC"); //This function works entirely in UTC.
        assert(get_class($utcNow) === "DateTime");
        assert($utcNow->getTimeZone() == $timeZone);
        
        if ($p_previousShowID == null && $p_currentShowID == null && $p_nextShowID == null) {
            return;
        }

        $sql = "SELECT %%columns%% st.starts as starts, st.ends as ends,
            st.media_item_played as media_item_played, si.ends as show_ends
            %%tables%% WHERE ";

        $fileColumns = "ft.artist_name, ft.track_title, ";
        $fileJoin = "FROM cc_schedule st JOIN cc_files ft ON st.file_id = ft.id
            LEFT JOIN cc_show_instances si ON st.instance_id = si.id";

        $streamColumns = "ws.name AS artist_name, wm.liquidsoap_data AS track_title, ";
        $streamJoin = <<<SQL
FROM cc_schedule AS st
JOIN cc_webstream ws ON st.stream_id = ws.id
LEFT JOIN cc_show_instances AS si ON st.instance_id = si.id
LEFT JOIN cc_subjs AS sub ON sub.id = ws.creator_id
LEFT JOIN
  (SELECT *
   FROM cc_webstream_metadata
   ORDER BY start_time DESC LIMIT 1) AS wm ON st.id = wm.instance_id
SQL;

        $predicateArr = array();
        $paramMap = array();
        if (isset($p_previousShowID)) {
            $predicateArr[] = 'st.instance_id = :previousShowId';
            $paramMap[':previousShowId'] = $p_previousShowID;
        }
        if (isset($p_currentShowID)) {
            $predicateArr[] = 'st.instance_id = :currentShowId';
            $paramMap[':currentShowId'] = $p_currentShowID;
        }
        if (isset($p_nextShowID)) {
            $predicateArr[] = 'st.instance_id = :nextShowId';
            $paramMap[':nextShowId'] = $p_nextShowID;
        }

        $sql .= " (".implode(" OR ", $predicateArr).") ";
        $sql .= ' AND st.playout_status > 0 ORDER BY st.starts';

        $filesSql = str_replace("%%columns%%", $fileColumns, $sql);
        $filesSql = str_replace("%%tables%%", $fileJoin, $filesSql);

        $streamSql = str_replace("%%columns%%", $streamColumns, $sql);
        $streamSql = str_replace("%%tables%%", $streamJoin, $streamSql);

        $sql = "SELECT * FROM (($filesSql) UNION ($streamSql)) AS unioned ORDER BY starts";

        $rows = Application_Common_Database::prepareAndExecute($sql, $paramMap);
        $numberOfRows = count($rows);

        $results['previous'] = null;
        $results['current']  = null;
        $results['next']     = null;

        for ($i = 0; $i < $numberOfRows; ++$i) {
            
            // if the show is overbooked, then update the track end time to the end of the show time.
            if ($rows[$i]['ends'] > $rows[$i]["show_ends"]) {
                $rows[$i]['ends'] = $rows[$i]["show_ends"];
            }
            
            $curShowStartTime = new DateTime($rows[$i]['starts'], $timeZone);
            $curShowEndTime   = new DateTime($rows[$i]['ends'], $timeZone);
            
            if (($curShowStartTime <= $utcNow) && ($curShowEndTime >= $utcNow)) {
                if ($i - 1 >= 0) {
                    $results['previous'] = array("name"=>$rows[$i-1]["artist_name"]." - ".$rows[$i-1]["track_title"],
                            "starts"=>$rows[$i-1]["starts"],
                            "ends"=>$rows[$i-1]["ends"],
                            "type"=>'track');
                }
                 $results['current'] =  array("name"=>$rows[$i]["artist_name"]." - ".$rows[$i]["track_title"],
                            "starts"=>$rows[$i]["starts"],
                            "ends"=> (($rows[$i]["ends"] > $rows[$i]["show_ends"]) ? $rows[$i]["show_ends"]: $rows[$i]["ends"]),
                            "media_item_played"=>$rows[$i]["media_item_played"],
                            "record"=>0,
                            "type"=>'track');
                if (isset($rows[$i+1])) {
                    $results['next'] =  array("name"=>$rows[$i+1]["artist_name"]." - ".$rows[$i+1]["track_title"],
                            "starts"=>$rows[$i+1]["starts"],
                            "ends"=>$rows[$i+1]["ends"],
                            "type"=>'track');
                }
                break;
            }
            if ($curShowEndTime < $utcNow ) {
                $previousIndex = $i;
            }
            if ($curShowStartTime > $utcNow) {
                $results['next'] = array("name"=>$rows[$i]["artist_name"]." - ".$rows[$i]["track_title"],
                            "starts"=>$rows[$i]["starts"],
                            "ends"=>$rows[$i]["ends"],
                            "type"=>'track');
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

    public static function GetLastScheduleItem($p_timeNow)
    {
        $sql = <<<SQL
SELECT ft.artist_name,
       ft.track_title,
       st.starts AS starts,
       st.ends AS ends
FROM cc_schedule st
LEFT JOIN cc_files ft ON st.file_id = ft.id
LEFT JOIN cc_show_instances sit ON st.instance_id = sit.id
-- this and the next line are necessary since we can overbook shows.
WHERE st.ends < TIMESTAMP :timeNow

  AND st.starts >= sit.starts
  AND st.starts < sit.ends
ORDER BY st.ends DESC LIMIT 1;
SQL;
        $row = Application_Common_Database::prepareAndExecute($sql, array(':timeNow'=>$p_timeNow));

        return $row;
    }

    public static function GetCurrentScheduleItem($p_timeNow, $p_instanceId)
    {
        /* Note that usually there will be one result returned. In some
         * rare cases two songs are returned. This happens when a track
         * that was overbooked from a previous show appears as if it
         * hasnt ended yet (track end time hasn't been reached yet). For
         * this reason,  we need to get the track that starts later, as
         * this is the *real* track that is currently playing. So this
         * is why we are ordering by track start time. */
        $sql = "SELECT *"
        ." FROM cc_schedule st"
        ." LEFT JOIN cc_files ft"
        ." ON st.file_id = ft.id"
        ." WHERE st.starts <= TIMESTAMP :timeNow1"
        ." AND st.instance_id = :instanceId"
        ." AND st.ends > TIMESTAMP :timeNow2"
        ." ORDER BY st.starts DESC"
        ." LIMIT 1";

        $row = Application_Common_Database::prepareAndExecute($sql, array(':timeNow1'=>$p_timeNow, ':instanceId'=>$p_instanceId, ':timeNow2'=>$p_timeNow,));

        return $row;
    }

    public static function GetNextScheduleItem($p_timeNow)
    {
        $sql = "SELECT"
        ." ft.artist_name, ft.track_title,"
        ." st.starts as starts, st.ends as ends"
        ." FROM cc_schedule st"
        ." LEFT JOIN cc_files ft"
        ." ON st.file_id = ft.id"
        ." LEFT JOIN cc_show_instances sit"
        ." ON st.instance_id = sit.id"
        ." WHERE st.starts > TIMESTAMP :timeNow"
        ." AND st.starts >= sit.starts" //this and the next line are necessary since we can overbook shows.
        ." AND st.starts < sit.ends"
        ." ORDER BY st.starts"
        ." LIMIT 1";

        $row = Application_Common_Database::prepareAndExecute($sql, array(':timeNow'=>$p_timeNow));

        return $row;
    }

    /*
     *
     * @param DateTime $start in UTC timezone
     * @param DateTime $end in UTC timezone
     *
     * @return array $scheduledItems
     *
     */
    public static function GetScheduleDetailItems($start, $end, $getOnlyPlayable = false, 
    		$getOnlyFuture = false, $showIds = array(), $showInstanceIds = array())
    {
    	$utcNow = new DateTime("now", new DateTimeZone("UTC"));
    	
    	//ordering first by show instance start time
    	//and then by scheduled item start time.
    	$items = CcShowInstancesQuery::create()
    		->filterByDbModifiedInstance(false)
    		->_if(isset($showIds) && count($showIds) > 0)
    			->filterByDbShowId($showIds)
    		->_endif()
    		->_if(isset($showInstanceIds) && count($showInstanceIds) > 0)
    			->filterByDbId($showInstanceIds)
    		->_endif()
    		->between($start, $end)
    		->orderByDbStarts()
    		//including these relations to prevent further database queries for 
    		->joinWith("CcShow", Criteria::LEFT_JOIN)
    		->useCcScheduleQuery(null, Criteria::LEFT_JOIN)
    			//only retrieve items that will get played in the future.
	    		->_if($getOnlyPlayable)
	    			->filterByDbPlayoutStatus(0, Criteria::GREATER_THAN)
	    			->filterByDbEnds($utcNow->format("Y-m-d H:i:s"), Criteria::GREATER_THAN)
	    		->_endif()
	    		->orderByDbStarts()
	    		->endUse()
	    	->with("CcSchedule")
    		->joinWith("CcSchedule.MediaItem", Criteria::LEFT_JOIN)
    		->joinWith("MediaItem.AudioFile", Criteria::LEFT_JOIN)
    		->joinWith("MediaItem.Webstream", Criteria::LEFT_JOIN)
    		->find();
    	
    	return $items;
    }

    public static function getSchduledPlaylistCount()
    {
        $sql = "SELECT count(*) as cnt FROM cc_schedule";

        $res = Application_Common_Database::prepareAndExecute($sql, array(),
        		Application_Common_Database::COLUMN);

        return $res;
    }

    /**
     * Convert a time string in the format "YYYY-MM-DD HH:mm:SS"
     * to "YYYY-MM-DD-HH-mm-SS".
     *
     * @param  string $p_time
     * @return string
     */
    public static function AirtimeTimeToPypoTime($p_time)
    {
        $p_time = substr($p_time, 0, 19);
        $p_time = str_replace(" ", "-", $p_time);
        $p_time = str_replace(":", "-", $p_time);

        return $p_time;
    }

    private static function createInputHarborKickTimes(&$data, $range_start, $range_end)
    {
        $utcTimeZone = new DateTimeZone("UTC");
        $kick_times = Application_Model_ShowInstance::GetEndTimeOfNextShowWithLiveDJ($range_start, $range_end);
        foreach ($kick_times as $kick_time_info) {
            $kick_time = $kick_time_info['ends'];
            $temp = explode('.', Application_Model_Preference::GetDefaultTransitionFade());
            // we round down transition time since PHP cannot handle millisecond. We need to
            // handle this better in the future
            $transition_time   = intval($temp[0]);
            $switchOffDataTime = new DateTime($kick_time, $utcTimeZone);
            $switch_off_time   = $switchOffDataTime->sub(new DateInterval('PT'.$transition_time.'S'));
            $switch_off_time   = $switch_off_time->format("Y-m-d H:i:s");

            $kick_start = self::AirtimeTimeToPypoTime($kick_time);
            $data["media"][$kick_start]['start'] = $kick_start;
            $data["media"][$kick_start]['end'] = $kick_start;
            $data["media"][$kick_start]['event_type'] = "kick_out";
            $data["media"][$kick_start]['type'] = "event";
            $data["media"][$kick_start]['independent_event'] = true;

            if ($kick_time !== $switch_off_time) {
                $switch_start = self::AirtimeTimeToPypoTime($switch_off_time);
                $data["media"][$switch_start]['start'] = $switch_start;
                $data["media"][$switch_start]['end'] = $switch_start;
                $data["media"][$switch_start]['event_type'] = "switch_off";
                $data["media"][$switch_start]['type'] = "event";
                $data["media"][$switch_start]['independent_event'] = true;
            }
        }
    }

    private static function getRangeStartAndEnd($p_fromDateTime, $p_toDateTime)
    {
        $CC_CONFIG = Config::getConfig();

        $utcTimeZone = new DateTimeZone('UTC');
        
        /* if $p_fromDateTime and $p_toDateTime function parameters are null,
            then set range * from "now" to "now + cache_ahead_hours". */
        if (is_null($p_fromDateTime)) {
            $p_fromDateTime = new DateTime("now", $utcTimeZone);
        } 
        else {
        	$p_fromDateTime->setTimezone($utcTimeZone);
        }
        if (is_null($p_toDateTime)) {
            $p_toDateTime = clone $p_fromDateTime;

            $cache_ahead_hours = $CC_CONFIG["cache_ahead_hours"];

            if (is_numeric($cache_ahead_hours)) {
                //make sure we are not dealing with a float
                $cache_ahead_hours = intval($cache_ahead_hours);
            } 
            else {
                $cache_ahead_hours = 1;
            }

            $p_toDateTime->add(new DateInterval("PT".$cache_ahead_hours."H"));
        } 
        else {
        	$p_toDateTime->setTimezone($utcTimeZone);
        }

        return array($p_fromDateTime, $p_toDateTime);
    }


    /*
     * @param array $data output array for events, contains key "media"
     * @param DateTime $startDT UTC start of schedule range
     * @param DateTime $endDT UTC end of schedule range
     */
    private static function createScheduledEvents(&$data, $startDT, $endDT)
    {
        $showInstances = self::GetScheduleDetailItems($startDT, $endDT, true);
        
        //Logging::info($showInstances);

        foreach ($showInstances as $showInstance) {
        	
        	foreach($showInstance->getCcSchedules() as $scheduleItem) {
        		
        		$event = $scheduleItem->createScheduleEvent($data);
        	}
        }
    }

    public static function getSchedule($p_fromDateTime = null, $p_toDateTime = null)
    {
    	//Logging::enablePropelLogging();
    	
        list($startDT, $endDT) = self::getRangeStartAndEnd($p_fromDateTime, $p_toDateTime);
        
        $data = array();
        $data["media"] = array();

        //Harbor kick times *MUST* be ahead of schedule events, so that pypo
        //executes them first.
        self::createInputHarborKickTimes($data, $startDT->format("Y-m-d H:i:s"), $endDT->format("Y-m-d H:i:s"));
        self::createScheduledEvents($data, $startDT, $endDT);
        
        //Logging::disablePropelLogging();

        return $data;
    }

    public static function checkOverlappingShows($show_start, $show_end,
        $update=false, $instanceId=null, $showId=null)
    {
        //if the show instance does not exist or was deleted, return false
        if (!is_null($showId)) {
            $ccShowInstance = CcShowInstancesQuery::create()
                ->filterByDbShowId($showId)
                ->filterByDbStarts($show_start->format("Y-m-d H:i:s"))
                ->findOne();
        } elseif (!is_null($instanceId)) {
            $ccShowInstance = CcShowInstancesQuery::create()
                ->filterByDbId($instanceId)
                ->findOne();
        }
        if ($update && ($ccShowInstance && $ccShowInstance->getDbModifiedInstance() == true)) {
            return false;
        }

        $overlapping = false;

        $params = array(
            ':show_end1'  => $show_end->format('Y-m-d H:i:s'),
            ':show_end2'  => $show_end->format('Y-m-d H:i:s'),
            ':show_end3'  => $show_end->format('Y-m-d H:i:s')
        );


        /* If a show is being edited, exclude it from the query
         * In both cases (new and edit) we only grab shows that
         * are scheduled 2 days prior
         */
        if ($update) {
            $sql = <<<SQL
SELECT id,
       starts,
       ends
FROM cc_show_instances
WHERE (ends <= :show_end1
       OR starts <= :show_end2)
  AND date(starts) >= (date(:show_end3) - INTERVAL '2 days')
  AND modified_instance = FALSE
SQL;
            if (is_null($showId)) {
                $sql .= <<<SQL
  AND id != :instanceId
ORDER BY ends
SQL;
                $params[':instanceId'] = $instanceId;
            } else {
                $sql .= <<<SQL
  AND show_id != :showId
ORDER BY ends
SQL;
                $params[':showId'] = $showId;
            }
            $rows = Application_Common_Database::prepareAndExecute($sql, $params, 'all');
        } else {
            $sql = <<<SQL
SELECT id,
       starts,
       ends
FROM cc_show_instances
WHERE (ends <= :show_end1
       OR starts <= :show_end2)
  AND date(starts) >= (date(:show_end3) - INTERVAL '2 days')
  AND modified_instance = FALSE
ORDER BY ends
SQL;

            $rows = Application_Common_Database::prepareAndExecute($sql, array(
                ':show_end1' => $show_end->format('Y-m-d H:i:s'),
                ':show_end2' => $show_end->format('Y-m-d H:i:s'),
                ':show_end3' => $show_end->format('Y-m-d H:i:s')), 'all');
        }

        foreach ($rows as $row) {
            $start = new DateTime($row["starts"], new DateTimeZone('UTC'));
            $end   = new DateTime($row["ends"], new DateTimeZone('UTC'));

            if ($show_start->getTimestamp() < $end->getTimestamp() &&
                $show_end->getTimestamp() > $start->getTimestamp()) {
                $overlapping = true;
                break;
            }
        }

        return $overlapping;
    }
}
