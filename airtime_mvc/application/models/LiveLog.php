<?php

class Application_Model_LiveLog
{
	
    public static function GetLiveShowDuration() {
        try {
            $con = Propel::getConnection();
            
            $sql = "SELECT * FROM CC_LIVE_LOG"
                 ." WHERE state = 'L'"
            	 ." and (start_time >= (now() - INTERVAL '1 day'))"
            	 ." ORDER BY id";
                
            $rows = $con->query($sql)->fetchAll();
            
            /* Check if last log has end time.
             * If not, set end time to current time
             */
            if ($rows != null) {
            	$last_row = self::UpdateLastLogEndTime(array_pop($rows));
                array_push($rows, $last_row);
            }
            
            $duration = self::GetDuration($rows);
            return $duration;
        } catch (Exception $e) {
            header('HTTP/1.0 503 Service Unavailable');
            Logging::log("GetLiveShowDuration - Could not connect to database.");
            exit;            
        }	
    }
    
    public static function GetScheduledDuration() {
        try {
            $con = Propel::getConnection();
            
    	    $sql_get_logs = "SELECT * FROM CC_LIVE_LOG"
                          ." WHERE state = 'S'"
            	          ." and (start_time >= (now() - INTERVAL '1 day'))"
            	          ." ORDER BY id";
                
            $rows = $con->query($sql_get_logs)->fetchAll();
                
            /* Check if last log has end time.
             * If not, set end time to current time
             */
            if ($rows != null) {
            	$last_row = self::UpdateLastLogEndTime(array_pop($rows));
                array_push($rows, $last_row);
            }
                
            $duration = new DateTime("00:00:00");
                
            /* Get all tracks from cc_schedule that played
             * during a scheduled state
             */
            foreach ($rows as $row) {
                //Get all show instances
                $sql_get_shows = "SELECT * FROM cc_show_instances"
                               ." WHERE starts >= '{$row['start_time']}'"
                	           ." AND ends < '{$row['end_time']}'";
                $shows = $con->query($sql_get_shows)->fetchAll();
                	
                //Get all tracks from each show and calculate total duration
                foreach ($shows as $show) {
                    $sql_get_tracks = "SELECT * FROM cc_schedule"
                                    ." WHERE starts >= '{$show['starts']}'"
                                    ." AND starts < '{$show['ends']}'"
                                    ." AND file_id IS NOT NULL"
                                    ." AND media_item_played IS TRUE"
                                    ." AND instance_id = '{$show['show_id']}'";
                    $tracks = $con->query($sql_get_tracks)->fetchAll();
                    $last_track = array_pop($tracks);
                    foreach ($tracks as $track) {
                       	$track_start = new DateTime($track['starts']);
                       	$track_end = new DateTime($track['ends']);
                        $duration->add($track_start->diff($track_end));   
                    }
                    if ($last_track['ends'] > $show['ends']) {
                        /*
                        $show_end = new DateTime($show['ends']);
                        $last_track_start = new DateTime($last_track['starts']);
                        $last_track_end = new DateTime($last_track['ends']);
                        	
                        $last_track_length = new DateTime($last_track_start->diff($last_track_end));
                            
                        $trackdiff = new DateTime($show_end->diff($last_track_end));
                           
                        $new_track_length = new DateTime($trackdiff->diff($last_track_length));
                            
                        $duration->add($new_track_length);
                        */
                    }
                    else {
                        $last_track_start = new DateTime($last_track['starts']);
                      	$last_track_end = new DateTime($last_track['ends']);
                        $duration->add($last_track_start->diff($last_track_end));
                    }	
                }
            $duration = $duration->format("H:i:s");
            return $duration;
            }
        } catch (Exception $e) {
            header('HTTP/1.0 503 Service Unavailable');
            Logging::log("GetScheduledDuration - Could not connect to database.");
            exit;            
        }
    }
    
    public static function GetDuration($rows) {
        $duration = new DateTime("00:00:00");
        foreach($rows as $row) {
            $end = new DateTime($row['end_time']);
            $start = new DateTime($row['start_time']);
            $duration->add($start->diff($end));
        }
        $duration = $duration->format("H:i:s");
        return $duration;
    }
    
    public static function UpdateLastLogEndTime($log) {
        $current_time = new DateTime("now", new DateTimeZone('UTC'));
        $log['end_time'] = $current_time;
        $log['end_time'] = $log['end_time']->format("Y-m-d H:i:s");
        self::SetEndTime($log['state'], $current_time);
        self::SetNewLogTime($log['state'], $current_time);
        return $log;
    }
    
    public static function SetNewLogTime($state, $dateTime, $end_scheduled=true){
        try {
            $con = Propel::getConnection();
            
            if ($state == 'L') {
            	self::SetEndTime('S', $dateTime);
            }
            
            /* Only insert new state if last log
             * has ended
             */
            $sql_select = "SELECT max(id) from CC_LIVE_LOG"
                        ." WHERE (state='L' and end_time is NULL) or (state='S' and end_time is NULL)";
            $id = $con->query($sql_select)->fetchColumn(0);
            
            if ($id == null) {
                $sql_insert = "INSERT INTO CC_LIVE_LOG (state, start_time)" 
                            ." VALUES ('$state', '{$dateTime->format("Y-m-d H:i:s")}')";
                $con->exec($sql_insert);
            }
            
        } catch (Exception $e) {
            header('HTTP/1.0 503 Service Unavailable');
            Logging::log("SetNewLogTime - Could not connect to database.");
            exit; 
        }
    }
    
    public static function SetEndTime($state, $dateTime){
        try {
            $con = Propel::getConnection();
            
            //if ($state == 'L') {
                $dj_live = Application_Model_Preference::GetSourceSwitchStatus('live_dj');
                $master_live = Application_Model_Preference::GetSourceSwitchStatus('master_dj');
            //}
            
            if (($dj_live=='off' && $master_live=='off') || $state == 'S') {
            	$sql = "SELECT id, state from cc_live_log"
            	     ." where id in (select max(id) from cc_live_log)";
                $row = $con->query($sql)->fetch();
                
                /* Only set end time if state recevied ($state)
                 * is the last row in cc_live_log
                 */
                if ($row['state'] == $state) {
                    $update_sql = "UPDATE CC_LIVE_LOG"
                                ." SET end_time = '{$dateTime->format("Y-m-d H:i:s")}'"
                                ." WHERE id = '$row[0]'";
                    $con->exec($update_sql);
                }
                
                //If live broadcasting is off, turn scheduled play on
                $scheduled = Application_Model_Preference::GetSourceSwitchStatus('scheduled_play');
                if ($state == 'L' && $scheduled=='on') {
                    self::SetNewLogTime('S', $dateTime);
                }
            }
            
        } catch (Exception $e) {
            header('HTTP/1.0 503 Service Unavailable');
            Logging::log("SetEndTime - Could not connect to database.");
            exit; 
        }
    }
	
}