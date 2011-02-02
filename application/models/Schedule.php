<?php
require_once("StoredFile.php");
require_once("BasicStor.php");

class ScheduleGroup {

    private $groupId;

    public function __construct($p_groupId = null) {
        $this->groupId = $p_groupId;
    }

    /**
     * Return true if the schedule group exists in the DB.
     * @return boolean
     */
    public function exists() {
        global $CC_CONFIG, $CC_DBC;
        $sql = "SELECT COUNT(*) FROM ".$CC_CONFIG['scheduleTable']
                ." WHERE group_id=".$this->groupId;
        $result = $CC_DBC->GetOne($sql);
        if (PEAR::isError($result)) {
            return $result;
        }
        return $result != "0";
    }

    /**
     * Convert a date to an ID by stripping out all characters
     * and padding with zeros.
     *
     * @param string $p_dateStr
     */
    public static function dateToId($p_dateStr) {
        $p_dateStr = str_replace(":", "", $p_dateStr);
        $p_dateStr = str_replace(" ", "", $p_dateStr);
        $p_dateStr = str_replace(".", "", $p_dateStr);
        $p_dateStr = str_replace("-", "", $p_dateStr);
        $p_dateStr = substr($p_dateStr, 0, 17);
        $p_dateStr = str_pad($p_dateStr, 17, "0");
        return $p_dateStr;
    }

    /**
     * Add the two times together, return the result.
     *
     * @param string $p_baseTime
     *  Specified as YYYY-MM-DD HH:MM:SS
     *
     * @param string $p_addTime
     *  Specified as HH:MM:SS.nnnnnn
     *
     * @return string
     *    The end time, to the nearest second.
     */
    //  protected function calculateEndTime($p_startTime, $p_trackTime) {
    //    $p_trackTime = substr($p_startTime, 0, );
    //    $start = new DateTime();
    //    $interval = new DateInterval()
    //
    //  }

    /**
     * Add a music clip or playlist to the schedule.
     *
     * @param $p_datetime
     *    In the format YYYY-MM-DD HH:MM:SS.mmmmmm
     * @param $p_audioFileId
     *    (optional, either this or $p_playlistId must be set) DB ID of the audio file
     * @param $p_playlistId
     *    (optional, either this of $p_audioFileId must be set) DB ID of the playlist
     * @param $p_options
     *    Does nothing at the moment.
     *
     * @return int|PEAR_Error
     *    Return PEAR_Error if the item could not be added.
     *    Error code 555 is a scheduling conflict.
     */
    public function add($p_datetime, $p_audioFileId = null, $p_playlistId = null, $p_options = null) {
        global $CC_CONFIG, $CC_DBC;
        if (!is_null($p_audioFileId)) {
            // Schedule a single audio track

            // Load existing track
            $track = StoredFile::Recall($p_audioFileId);
            if (is_null($track)) {
                return new PEAR_Error("Could not find audio track.");
            }

            // Check if there are any conflicts with existing entries
            $metadata = $track->getMetadata();
            $length = trim($metadata["length"]);
            if (empty($length)) {
                return new PEAR_Error("Length is empty.");
            }
            if (!Schedule::isScheduleEmptyInRange($p_datetime, $length)) {
                return new PEAR_Error("Schedule conflict.", 555);
            }

            // Insert into the table
            $this->groupId = $CC_DBC->GetOne("SELECT nextval('schedule_group_id_seq')");
            $id = $this->dateToId($p_datetime);
            $sql = "INSERT INTO ".$CC_CONFIG["scheduleTable"]
            ." (id, playlist_id, starts, ends, clip_length, group_id, file_id)"
            ." VALUES ($id, 0, TIMESTAMP '$p_datetime', "
            ." (TIMESTAMP '$p_datetime' + INTERVAL '$length'),"
            ." '$length',"
            ." {$this->groupId}, $p_audioFileId)";
            $result = $CC_DBC->query($sql);
            if (PEAR::isError($result)) {
                //var_dump($sql);
                return $result;
            }
            return $this->groupId;

        } elseif (!is_null($p_playlistId)){
            // Schedule a whole playlist

            // Load existing playlist
            $playlist = Playlist::Recall($p_playlistId);
            if (is_null($playlist)) {
                return new PEAR_Error("Could not find playlist.");
            }

            // Check if there are any conflicts with existing entries
            $length = trim($playlist->getLength());
            //var_dump($length);
            if (empty($length)) {
                return new PEAR_Error("Length is empty.");
            }
            if (!Schedule::isScheduleEmptyInRange($p_datetime, $length)) {
                return new PEAR_Error("Schedule conflict.", 555);
            }

            // Insert all items into the schedule
            $this->groupId = $CC_DBC->GetOne("SELECT nextval('schedule_group_id_seq')");
            $id = $this->dateToId($p_datetime);
            $itemStartTime = $p_datetime;

            $plItems = $playlist->getContents();
            //var_dump($plItems);
            foreach ($plItems as $row) {
                $trackLength = $row["cliplength"];
                //var_dump($trackLength);
                $sql = "INSERT INTO ".$CC_CONFIG["scheduleTable"]
                ." (id, playlist_id, starts, ends, group_id, file_id,"
                ." clip_length, cue_in, cue_out, fade_in, fade_out)"
                ." VALUES ($id, $p_playlistId, TIMESTAMP '$itemStartTime', "
                ." (TIMESTAMP '$itemStartTime' + INTERVAL '$trackLength'),"
                ." '{$this->groupId}', '{$row['file_id']}', '$trackLength', '{$row['cuein']}',"
                ." '{$row['cueout']}', '{$row['fadein']}','{$row['fadeout']}')";
                $result = $CC_DBC->query($sql);
                if (PEAR::isError($result)) {
                    //var_dump($sql);
                    return $result;
                }
                $itemStartTime = $CC_DBC->getOne("SELECT TIMESTAMP '$itemStartTime' + INTERVAL '$trackLength'");
                $id = $this->dateToId($itemStartTime);
            }
            return $this->groupId;
        }
    }

    public function addAfter($p_groupId, $p_audioFileId) {
        global $CC_CONFIG, $CC_DBC;
        // Get the end time for the given entry
        $sql = "SELECT MAX(ends) FROM ".$CC_CONFIG["scheduleTable"]
        ." WHERE group_id=$p_groupId";
        $startTime = $CC_DBC->GetOne($sql);
        return $this->add($startTime, $p_audioFileId);
    }

    public function addPlaylistAfter($p_groupId, $p_playlistId) {
        global $CC_CONFIG, $CC_DBC;
        // Get the end time for the given entry
        $sql = "SELECT MAX(ends) FROM ".$CC_CONFIG["scheduleTable"]
        ." WHERE group_id=$p_groupId";
    
        $startTime = $CC_DBC->GetOne($sql);
        return $this->add($startTime, null, $p_playlistId);
    }

    public function update() {

    }

    /**
     * Remove the group from the schedule.
     * Note: does not check if it is in the past, you can remove anything.
     *
     * @return boolean
     *    TRUE on success, false if there is no group ID defined.
     */
    public function remove() {
        global $CC_CONFIG, $CC_DBC;
        if (is_null($this->groupId) || !is_numeric($this->groupId)) {
            return false;
        }
        $sql = "DELETE FROM ".$CC_CONFIG["scheduleTable"]
        ." WHERE group_id = ".$this->groupId;
        //echo $sql;
        return $CC_DBC->query($sql);
    }

    /**
     * Return the number of items in this group.
     * @return string
     */
    public function count() {
        global $CC_CONFIG, $CC_DBC;
        $sql = "SELECT COUNT(*) FROM {$CC_CONFIG['scheduleTable']}"
        ." WHERE group_id={$this->groupId}";
        return $CC_DBC->GetOne($sql);
    }

    /*
     * Return the list of items in this group as a 2D array.
     * @return array
     */
    public function getItems() {
        global $CC_CONFIG, $CC_DBC;
        $sql = "SELECT * FROM {$CC_CONFIG['scheduleTable']}"
        ." WHERE group_id={$this->groupId}";
        return $CC_DBC->GetAll($sql);
    }

    public function reschedule($toDateTime) {
        global $CC_CONFIG, $CC_DBC;
        //    $sql = "UPDATE ".$CC_CONFIG["scheduleTable"]. " SET id=, starts=,ends="
    }

    public function notifyGroupStartPlay() {
        global $CC_CONFIG, $CC_DBC;
        $sql = "UPDATE ".$CC_CONFIG['scheduleTable']
                ." SET schedule_group_played=TRUE"
                ." WHERE group_id=".$this->groupId;
        return $CC_DBC->query($sql);
    }

    public function notifyMediaItemStartPlay($p_fileId) {
        global $CC_CONFIG, $CC_DBC;
        $sql = "UPDATE ".$CC_CONFIG['scheduleTable']
                ." SET media_item_played=TRUE"
                ." WHERE group_id=".$this->groupId
                ." AND file_id=".pg_escape_string($p_fileId);
        return $CC_DBC->query($sql);
    }
}

class Schedule {

    function __construct() {

    }

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
            return new PEAR_Error("Schedule::isSchedulerEmptyInRange: param p_length is empty.");
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

    public static function getTimeUnScheduledInRange($s_datetime, $e_datetime) {
        global $CC_CONFIG, $CC_DBC;

        $sql = "SELECT timestamp '{$s_datetime}' > timestamp '{$e_datetime}'";
        $isNextDay = $CC_DBC->GetOne($sql);

        if($isNextDay === 't') {
            $sql = "SELECT date '{$e_datetime}' + interval '1 day'";
            $e_datetime = $CC_DBC->GetOne($sql);
        }

        $sql = "SELECT SUM(clip_length) FROM ".$CC_CONFIG["scheduleTable"]." 
            WHERE (starts >= '{$s_datetime}')  
            AND (ends <= '{$e_datetime}')";

        $time = $CC_DBC->GetOne($sql);

        if(is_null($time))
            $time = 0;

        $sql = "SELECT TIMESTAMP '{$e_datetime}' - TIMESTAMP '{$s_datetime}'";
        $length = $CC_DBC->GetOne($sql);

        $sql = "SELECT INTERVAL '{$length}' - INTERVAL '{$time}'";
        $time_left =$CC_DBC->GetOne($sql);

        return $time_left;
    }

    public static function getTimeScheduledInRange($s_datetime, $e_datetime) {
        global $CC_CONFIG, $CC_DBC;

        $sql = "SELECT timestamp '{$s_datetime}' > timestamp '{$e_datetime}'";
        $isNextDay = $CC_DBC->GetOne($sql);

        if($isNextDay === 't') {
            $sql = "SELECT date '{$e_datetime}' + interval '1 day'";
            $e_datetime = $CC_DBC->GetOne($sql);
        }

        $sql = "SELECT SUM(clip_length) FROM ".$CC_CONFIG["scheduleTable"]." 
            WHERE (starts >= '{$s_datetime}')  
            AND (ends <= '{$e_datetime}')";

        $res = $CC_DBC->GetOne($sql);

        if(is_null($res))
            return 0;

        return $res;
    }

    public static function getPercentScheduledInRange($s_datetime, $e_datetime) {

        $time = Schedule::getTimeScheduledInRange($s_datetime, $e_datetime);

        $con = Propel::getConnection(CcSchedulePeer::DATABASE_NAME);

        $sql = "SELECT EXTRACT(EPOCH FROM TIMESTAMP WITH TIME ZONE '{$s_datetime}')";
        $r = $con->query($sql);
        $s_epoch = $r->fetchColumn(0);

        $sql = "SELECT EXTRACT(EPOCH FROM TIMESTAMP WITH TIME ZONE '{$e_datetime}')";
        $r = $con->query($sql);
        $e_epoch = $r->fetchColumn(0);

        $sql = "SELECT EXTRACT(EPOCH FROM INTERVAL '{$time}')";
        $r = $con->query($sql);
        $i_epoch = $r->fetchColumn(0);

        $percent = ceil(($i_epoch / ($e_epoch - $s_epoch)) * 100);

        return $percent;
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
     * Returns array indexed by:
     *    "playlistId"/"playlist_id" (aliases to the same thing)
     *    "start"/"starts" (aliases to the same thing) as YYYY-MM-DD HH:MM:SS.nnnnnn
     *    "end"/"ends" (aliases to the same thing) as YYYY-MM-DD HH:MM:SS.nnnnnn
     *    "group_id"/"id" (aliases to the same thing)
     *    "clip_length" (for audio clips this is the length of the audio clip,
     *                   for playlists this is the length of the entire playlist)
     *    "name" (playlist only)
     *    "creator" (playlist only)
     *    "file_id" (audioclip only)
     *    "count" (number of items in the playlist, always 1 for audioclips.
     *      Note that playlists with one item will also have count = 1.
     *
     * @param string $p_fromDateTime
     *    In the format YYYY-MM-DD HH:MM:SS.nnnnnn
     * @param string $p_toDateTime
     *    In the format YYYY-MM-DD HH:MM:SS.nnnnnn
     * @param boolean $p_playlistsOnly
     *    Retrieve playlists as a single item.
     * @return array
     *      Returns empty array if nothing found
     */
    public static function GetItems($p_fromDateTime, $p_toDateTime, $p_playlistsOnly = true) {
        global $CC_CONFIG, $CC_DBC;
        $rows = array();
        if (!$p_playlistsOnly) {
            $sql = "SELECT * FROM ".$CC_CONFIG["scheduleTable"]
            ." WHERE (starts >= TIMESTAMP '$p_fromDateTime') "
            ." AND (ends <= TIMESTAMP '$p_toDateTime')";
            $rows = $CC_DBC->GetAll($sql);
            foreach ($rows as &$row) {
                $row["count"] = "1";
                $row["playlistId"] = $row["playlist_id"];
                $row["start"] = $row["starts"];
                $row["end"] = $row["ends"];
                $row["id"] = $row["group_id"];
            }
        } else {
            $sql = "SELECT MIN(name) AS name, MIN(creator) AS creator, group_id, "
            ." SUM(clip_length) AS clip_length,"
            ." MIN(file_id) AS file_id, COUNT(*) as count,"
            ." MIN(playlist_id) AS playlist_id, MIN(starts) AS starts, MAX(ends) AS ends"
            ." FROM ".$CC_CONFIG["scheduleTable"]
            ." LEFT JOIN ".$CC_CONFIG["playListTable"]." ON playlist_id = ".$CC_CONFIG["playListTable"].".id"
            ." WHERE (starts >= TIMESTAMP '$p_fromDateTime') AND (ends <= TIMESTAMP '$p_toDateTime')"
            ." GROUP BY group_id"
            ." ORDER BY starts";
            //var_dump($sql);
            $rows = $CC_DBC->GetAll($sql);
            if (!PEAR::isError($rows)) {
                foreach ($rows as &$row) {
                    $row["playlistId"] = $row["playlist_id"];
                    $row["start"] = $row["starts"];
                    $row["end"] = $row["ends"];
                    $row["id"] = $row["group_id"];
                }
            }
        }
        return $rows;
    }

    
    /**
     * Returns the date of the server in the format
     * "YYYY-MM-DD HH:mm:SS".
     *
     * Note: currently assuming that Web Server and Scheduler are on the 
     * same host.
     *
     * @return date Current server time.
     */
    public static function GetSchedulerTime() {
        return date("Y-m-d H:i:s"); 
    }

    /**
     * Returns current playlist.
     *
     * Note: Total playlist length is prev + next + 1
     *
     * @param int $prev
     * @param int $next
     * @return date
     */
    public static function GetPlayOrderRange($prev = 1, $next = 1) {
        if (!is_int($prev) || !is_int($next)){
            //must enter integers to specify ranges
            return "{}";
        }

        $timeNow = Schedule::GetSchedulerTime();
        return array("schedulerTime"=>gmdate("Y-m-d H:i:s"),
            "previous"=>Schedule::GetPreviousItems($timeNow),
            "current"=>Schedule::GetCurrentlyPlaying($timeNow),
            "next"=>Schedule::GetNextItems($timeNow),
            "showStartEndTime"=>Schedule::GetCurrentShow($timeNow),
            "timezone"=> date("T"),
            "timezoneOffset"=> date("Z"));
    }

    public static function GetPreviousItems($timeNow, $prevCount = 1){
        global $CC_CONFIG, $CC_DBC;
        $sql = "SELECT pt.name, ft.track_title, ft.artist_name, ft.album_title, st.starts, st.ends, st.clip_length, st.group_id"
        ." FROM $CC_CONFIG[scheduleTable] st, $CC_CONFIG[filesTable] ft, $CC_CONFIG[playListTable] pt"
        ." WHERE st.ends < TIMESTAMP '$timeNow'"
        ." AND st.ends > (TIMESTAMP '$timeNow' - INTERVAL '24 hours')"
        ." AND st.playlist_id = pt.id"
        ." AND st.file_id = ft.id"
        ." ORDER BY st.starts DESC"
        ." LIMIT $prevCount";
        $rows = $CC_DBC->GetAll($sql);
        return $rows;
    }

    public static function GetCurrentlyPlaying($timeNow){
        global $CC_CONFIG, $CC_DBC;
        
        $sql = "SELECT pt.name, ft.track_title, ft.artist_name, ft.album_title, st.starts, st.ends, st.clip_length, st.group_id"
        ." FROM $CC_CONFIG[scheduleTable] st,"
        ."$CC_CONFIG[filesTable] ft, $CC_CONFIG[playListTable] pt"
        ." WHERE st.starts < TIMESTAMP '$timeNow'"
        ." AND st.ends > TIMESTAMP '$timeNow'"
        ." AND st.playlist_id = pt.id"
        ." AND st.file_id = ft.id";
        $rows = $CC_DBC->GetAll($sql);
        return $rows;
    }

    public static function GetNextItems($timeNow, $nextCount = 1) {
        global $CC_CONFIG, $CC_DBC;
        $sql = "SELECT pt.name, ft.track_title, ft.artist_name, ft.album_title, st.starts, st.ends, st.clip_length, st.group_id" 
        ." FROM $CC_CONFIG[scheduleTable] st, $CC_CONFIG[filesTable] ft, $CC_CONFIG[playListTable] pt"
        ." WHERE st.starts > TIMESTAMP '$timeNow'"
        ." AND st.ends < (TIMESTAMP '$timeNow' + INTERVAL '24 hours')"
        ." AND st.playlist_id = pt.id"
        ." AND st.file_id = ft.id"
        ." ORDER BY st.starts"
        ." LIMIT $nextCount";
        $rows = $CC_DBC->GetAll($sql);
        return $rows;
    }

    public static function GetCurrentShow($timeNow) {
        global $CC_CONFIG, $CC_DBC;
        
		$timestamp = preg_split("/ /", $timeNow);
		$date = $timestamp[0];
		$time = $timestamp[1];
        
        $sql = "SELECT current_date + sd.start_time as start_timestamp, current_date + sd.end_time as end_timestamp, s.name, s.id"
        ." FROM $CC_CONFIG[showDays] sd, $CC_CONFIG[showTable] s"
        ." WHERE sd.show_id = s.id"
        ." AND sd.first_show <= DATE '$date'"
        ." AND sd.start_time <= TIME '$time'"
        ." AND sd.last_show > DATE '$date'"
        ." AND sd.end_time > TIME '$time'";
        
        $rows = $CC_DBC->GetAll($sql);
        return $rows;
    }
    
    public static function GetCurrentShowGroupIDs($showID){
        global $CC_CONFIG, $CC_DBC;
        
		$sql = "SELECT group_id"
		." FROM $CC_CONFIG[showSchedule]"
		." WHERE show_id = $showID";
		
        $rows = $CC_DBC->GetAll($sql);
        return $rows;
	}

    /**
     * Convert a time string in the format "YYYY-MM-DD HH:mm:SS"
     * to "YYYY-MM-DD-HH-mm-SS".
     *
     * @param string $p_time
     * @return string
     */
    private static function CcTimeToPypoTime($p_time)
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
    private static function PypoTimeToCcTime($p_time)
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
    private static function WallTimeToMillisecs($p_time)
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
     * Export the schedule in json formatted for pypo (the liquidsoap scheduler)
     *
     * @param string $range
     *      In the format "YYYY-MM-DD HH:mm:ss"
     * @param string $source
     *      In the format "YYYY-MM-DD HH:mm:ss"
     */
    public static function ExportRangeAsJson($p_fromDateTime, $p_toDateTime)
    {
        global $CC_CONFIG, $CC_DBC;
        $range_start = Schedule::PypoTimeToCcTime($p_fromDateTime);
        $range_end = Schedule::PypoTimeToCcTime($p_toDateTime);
        $range_dt = array('start' => $range_start, 'end' => $range_end);
        //var_dump($range_dt);

        // Scheduler wants everything in a playlist
        $data = Schedule::GetItems($range_start, $range_end, true);
        //echo "<pre>";var_dump($data);
        $playlists = array();

        if (is_array($data) && count($data) > 0)
        {
            foreach ($data as $dx)
            {
                // Is this the first item in the playlist?
                $start = $dx['start'];
                // chop off subseconds
                $start = substr($start, 0, 19);

                // Start time is the array key, needs to be in the format "YYYY-MM-DD-HH-mm-ss"
                $pkey = Schedule::CcTimeToPypoTime($start);
                $timestamp =  strtotime($start);
                $playlists[$pkey]['source'] = "PLAYLIST";
                $playlists[$pkey]['x_ident'] = $dx["playlist_id"];
                $playlists[$pkey]['subtype'] = '1'; // Just needs to be between 1 and 4 inclusive
                $playlists[$pkey]['timestamp'] = $timestamp;
                $playlists[$pkey]['duration'] = $dx['clip_length'];
                $playlists[$pkey]['played'] = '0';
                $playlists[$pkey]['schedule_id'] = $dx['group_id'];
                $playlists[$pkey]['user_id'] = 0;
                $playlists[$pkey]['id'] = $dx["playlist_id"];
                $playlists[$pkey]['start'] = Schedule::CcTimeToPypoTime($dx["start"]);
                $playlists[$pkey]['end'] = Schedule::CcTimeToPypoTime($dx["end"]);
            }
        }

        foreach ($playlists as &$playlist)
        {
            $scheduleGroup = new ScheduleGroup($playlist["schedule_id"]);
            $items = $scheduleGroup->getItems();
            $medias = array();
            $playlist['subtype'] = '1';
            foreach ($items as $item)
            {
                $storedFile = StoredFile::Recall($item["file_id"]);
                $uri = $storedFile->getFileUrl();

                // For pypo, a cueout of zero means no cueout
                $cueOut = "0";
                if (Schedule::TimeDiff($item["cue_out"], $item["clip_length"]) > 0.001) {
                    $cueOut = Schedule::WallTimeToMillisecs($item["cue_out"]);
                }
                $medias[] = array(
                    'id' => $storedFile->getGunid(), //$item["file_id"],
                    'uri' => $uri,
                    'fade_in' => Schedule::WallTimeToMillisecs($item["fade_in"]),
                    'fade_out' => Schedule::WallTimeToMillisecs($item["fade_out"]),
                    'fade_cross' => 0,
                    'cue_in' => Schedule::WallTimeToMillisecs($item["cue_in"]),
                    'cue_out' => $cueOut,
                    'export_source' => 'scheduler'
                );
            }
            $playlist['medias'] = $medias;
        }

        $result = array();
        $result['status'] = array('range' => $range_dt, 'version' => "0.2");
        $result['playlists'] = $playlists;
        $result['check'] = 1;

        print json_encode($result);
    }


    /**
     * Remove all items from the schedule in the given range.
     *
     * @param string $p_start
     *    In the format YYYY-MM-DD HH:MM:SS.nnnnnn
     * @param string $p_end
     *    In the format YYYY-MM-DD HH:MM:SS.nnnnnn
     */
    public static function RemoveItemsInRange($p_start, $p_end)
    {
        $items = Schedule::GetItems($p_start, $p_end);
        foreach ($items as $item) {
            $scheduleGroup = new ScheduleGroup($item["group_id"]);
            $scheduleGroup->remove();
        }
    }

}

?>
