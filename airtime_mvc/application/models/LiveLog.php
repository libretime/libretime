<?php

class Application_Model_LiveLog
{

    public static function GetLiveShowDuration($p_keepData=false) {
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

            $hours = 0;
            $minutes = 0;
            $seconds = 0;

            foreach ($rows as $row) {
                $end = new DateTime($row['end_time']);
                $start = new DateTime($row['start_time']);
                $duration = $start->diff($end);
                $duration = $duration->format("%H:%i:%s");
                $intervals = explode(":", $duration);
                // Trim milliseconds (DateInterval does not support)
                $sec = explode(".", $intervals[2]);
                $intervals[2] = $sec[0];

                $seconds += $intervals[2];
                if ($seconds / 60 >= 1) {
                    $minutes += 1;
                    $seconds -= 60;
                }

                $minutes += $intervals[1];
                if ($minutes / 60 >= 1) {
                    $hours += 1;
                    $minutes -= 60;
                }

                $hours += $intervals[0];

                if (!$p_keepData) {
                    // Delete data we just used to start a new log history
                    $sql_delete = "DELETE FROM CC_LIVE_LOG"
                    ." WHERE id = '{$row['id']}'";
                    $con->exec($sql_delete);
                }
            }
            //Trim milliseconds
            $seconds = explode(".", $seconds);
            $minutes = (double)(($hours*60)+$minutes . "." . $seconds[0]);
            //$duration = new DateInterval("PT" . $minutes . "M" . $seconds[0] ."S");
            //return $duration->format("%i.%s");
            return $minutes;
        } catch (Exception $e) {
            header('HTTP/1.0 503 Service Unavailable');
            Logging::log("GetLiveShowDuration - Could not connect to database.");
            exit;
        }
    }

    public static function GetScheduledDuration($p_keepData = false) 
    {
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

            $hours = 0;
            $minutes = 0;
            $seconds = 0;

            /* Get all shows and tracks from cc_schedule that played
             * during a scheduled state
             */
            foreach ($rows as $row) {
                $sql_get_tracks = "SELECT * FROM cc_schedule"
                ." WHERE starts >= '{$row['start_time']}'"
                ." AND starts < '{$row['end_time']}'"
                ." AND file_id IS NOT NULL"
                ." AND media_item_played IS TRUE";
                $tracks = $con->query($sql_get_tracks)->fetchAll();
                foreach ($tracks as $track) {
                    if ($track['ends'] > $row['end_time']) {
                        $scheduled_ends = new DateTime($row['end_time']);
                        $track_ends = new DateTime($track['ends']);
                        $extra_time = $scheduled_ends->diff($track_ends);

                        /* Get difference between clip_length
                         * and the extra time. We need to subtract
                         * this difference from the track's
                         * clip length.
                         */
                        $clip_length = $track['clip_length'];
                        //Convert clip_length into seconds
                        $clip_length_intervals = explode(":", $clip_length);
                        $clip_length_seconds = $clip_length_intervals[0]*3600 + $clip_length_intervals[1]*60 + $clip_length_intervals[2];
                         
                        $extra_time = $extra_time->format("%H:%i:%s");
                        //Convert extra_time into seconds;
                        $extra_time_intervals = explode(":", $extra_time);
                        $extra_time_seconds = $extra_time_intervals[0]*3600 + $extra_time_intervals[1]*60 + $extra_time_intervals[2];

                        $clip_length_seconds -= $extra_time_seconds;
                         
                        //Convert new clip_length into "H-i-s" format
                        $clip_length_arr = array();
                        if ($clip_length_seconds / 3600 >= 1) {
                            array_push($clip_length_arr, str_pad(floor($clip_length_seconds / 3600), 2, "0", STR_PAD_LEFT));
                            $clip_length_seconds -= floor($clip_length_seconds / 3600);
                        }
                        else {
                            array_push($clip_length_arr, "00");
                        }
                        if ($clip_length_seconds / 60 >= 1) {
                            array_push($clip_length_arr, str_pad(floor($clip_length_seconds / 60), 2, "0", STR_PAD_LEFT));
                            $clip_length_seconds -= floor($clip_length_seconds / 60);
                        }
                        else {
                            array_push($clip_length_arr, "00");
                        }

                        array_push($clip_length_arr, str_pad($clip_length_seconds, 2, "0", STR_PAD_LEFT));
                        $clip_length = implode(":", $clip_length_arr);
                    }
                    else {
                        $clip_length = $track['clip_length'];
                    }

                    $intervals = explode(":", $clip_length);
                    // Trim milliseconds (DateInteral does not support)
                    $sec = explode(".", $intervals[2]);
                    $intervals[2] = $sec[0];

                    $seconds += $intervals[2];
                    if ($seconds / 60 >= 1) {
                        $minutes += 1;
                        $seconds -= 60;
                    }

                    $minutes += $intervals[1];
                    if ($minutes / 60 >= 1) {
                        $hours += 1;
                        $minutes -= 60;
                    }

                    $hours += $intervals[0];
                }

                if (!$p_keepData) {
                    //Delete row because we do not need data anymore
                    $sql_delete = "DELETE FROM CC_LIVE_LOG"
                                ." WHERE id = '{$row['id']}'";
                    $con->exec($sql_delete);
                }
            }


            $seconds = explode(".", $seconds);
            $minutes = (double)(($hours*60)+$minutes . "." . $seconds[0]);
            //$duration = new DateInterval("PT". $minutes . "M" . $seconds[0] ."S");
            //return $duration->format("%i.%s");
            return $minutes;
        } catch (Exception $e) {
            header('HTTP/1.0 503 Service Unavailable');
            Logging::log("GetScheduledDuration - Could not connect to database.");
            exit;
        }
    }

    public static function UpdateLastLogEndTime($log) {
        if ($log['end_time'] == null) {
            $current_time = new DateTime("now", new DateTimeZone('UTC'));
            $log['end_time'] = $current_time;
            $log['end_time'] = $log['end_time']->format("Y-m-d H:i:s");
            self::SetEndTime($log['state'], $current_time, true);
            self::SetNewLogTime($log['state'], $current_time);
        }
        return $log;
    }

    public static function SetNewLogTime($state, $dateTime){
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

    public static function SetEndTime($state, $dateTime, $override=false){
        try {
            $con = Propel::getConnection();

            $dj_live = Application_Model_Preference::GetSourceSwitchStatus('live_dj');
            $master_live = Application_Model_Preference::GetSourceSwitchStatus('master_dj');

            if (($dj_live=='off' && $master_live=='off') || $state == 'S' || $override) {
                $sql = "SELECT id, state from cc_live_log"
                ." where id in (select max(id) from cc_live_log)";
                $row = $con->query($sql)->fetch();

                /* Only set end time if state recevied ($state)
                 * is the last row in cc_live_log
                 */
                if ($row['state'] == $state) {
                    $update_sql = "UPDATE CC_LIVE_LOG"
                    ." SET end_time = '{$dateTime->format("Y-m-d H:i:s")}'"
                    ." WHERE id = '{$row['id']}'";
                    $con->exec($update_sql);
                }

                //If live broadcasting is off, turn scheduled play on
                $scheduled = Application_Model_Preference::GetSourceSwitchStatus('scheduled_play');
                if ($state == 'L' && $scheduled=='on' && !$override) {
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