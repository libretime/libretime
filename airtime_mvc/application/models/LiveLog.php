<?php

class Application_Model_LiveLog
{
	
    public static function GetLiveShowDuration() {
        try {
            $con = Propel::getConnection();
            
            $data = self::GetNumLogs();
            if ($data['count'] > 1) {
            	$sql = "SELECT * FROM CC_LIVE_LOG"
            	     ." WHERE state = 'L'"
            	     ." and (start_time >= (now() - INTERVAL '1 day'))"
            	     ." ORDER BY id";
                
                $rows = $con->query($sql)->fetchAll();
                $duration = self::GetDuration($rows);
                return $duration;
            }
            else if ($data['count'] == 1 && $data['state'] == 'S') {
                $duration = new DateTime("00:00:00");
                return $duration->format("H:i:s");
            }
            else if ($data['count'] == 1 && $data['state'] == 'L') {
                $duration = new DateTime("23:59:59");
                return $duration->format("H:i:s");
            }
            
        } catch (Exception $e) {
            header('HTTP/1.0 503 Service Unavailable');
            Logging::log("Could not connect to database.");
            exit;            
        }	
    }
    
    public static function GetScheduledDuration() {
        try {
            $con = Propel::getConnection();
            
            if (self::GetNumLogs() > 1) {
            
    	        $sql = "SELECT * FROM CC_LIVE_LOG"
            	     ." WHERE state = 'S'"
            	     ." and (start_time >= (now() - INTERVAL '1 day'))"
            	     ." ORDER BY id";
                
                $rows = $con->query($sql)->fetchAll();
                $duration = self::GetDuration($rows);
                return $duration;
            }
            else {
                $duration = new DateTime("23:59:59");
                return $duration->format("H:i:s");
            }
            
        } catch (Exception $e) {
            header('HTTP/1.0 503 Service Unavailable');
            Logging::log("Could not connect to database.");
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
    
    /* Returns number of entries in cc_live_log
     * within the last 24 hours
     */
    public static function GetNumLogs() {
        try {
            $con = Propel::getConnection();
            
            $sql = "SELECT count(*), state FROM CC_LIVE_LOG"
                 ." WHERE (start_time >= (now() - INTERVAL '1 day'))"
                 ." GROUP BY state";
                 
            $row = $con->query($sql)->fetch();
            return $result;
            
        } catch (Exception $e) {
            header('HTTP/1.0 503 Service Unavailable');
            Logging::log("Could not connect to database.");
            exit; 
        }
    }
    
    public static function SetNewLogTime($state, $dateTime){
        try {
            $con = Propel::getConnection();
            
            $sql = "INSERT INTO CC_LIVE_LOG (state, start_time)" 
                 ." VALUES ('$state', '{$dateTime->format("Y-m-d H:i:s")}')";
                 
            $con->exec($sql);
            
        } catch (Exception $e) {
            header('HTTP/1.0 503 Service Unavailable');
            Logging::log("SetNewLogTime - Could not connect to database.");
            exit; 
        }
    }
    
    public static function SetEndTime($state, $dateTime){
        try {
            $con = Propel::getConnection();
            
            $sql = "SELECT max(id) FROM CC_LIVE_LOG"
                 ." WHERE state = '$state'";
                 
            $id = $con->query($sql)->fetchColumn(0);
            
            $update_sql = "UPDATE CC_LIVE_LOG"
                        ." SET end_time = '{$dateTime->format("Y-m-d H:i:s")}'"
                        ." WHERE id = '$id'";
            $con->exec($update_sql);
            
        } catch (Exception $e) {
            header('HTTP/1.0 503 Service Unavailable');
            Logging::log("SetEndTime - Could not connect to database.");
            exit; 
        }
    }
	
}