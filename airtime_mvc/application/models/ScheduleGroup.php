<?php

class Application_Model_ScheduleGroup {

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
     * Add a music clip or playlist to the schedule.
     *
     * @param int $p_showInstance
     * 	  ID of the show.
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
    public function add($p_showInstance, $p_datetime, $p_audioFileId = null, $p_playlistId = null, $p_options = null) {
        global $CC_CONFIG, $CC_DBC;

        if (!is_null($p_audioFileId)) {
            // Schedule a single audio track

            // Load existing track
            $track = Application_Model_StoredFile::Recall($p_audioFileId);
            if (is_null($track)) {
                return new PEAR_Error("Could not find audio track.");
            }

            // Check if there are any conflicts with existing entries
            $metadata = $track->getMetadata();
            $length = $metadata['MDATA_KEY_DURATION'];
            if (empty($length)) {
                return new PEAR_Error("Length is empty.");
            }
            // Insert into the table
            $this->groupId = $CC_DBC->GetOne("SELECT nextval('schedule_group_id_seq')");

            $sql = "INSERT INTO ".$CC_CONFIG["scheduleTable"]
            ." (instance_id, starts, ends, clip_length, group_id, file_id, cue_out)"
            ." VALUES ($p_showInstance, TIMESTAMP '$p_datetime', "
            ." (TIMESTAMP '$p_datetime' + INTERVAL '$length'),"
            ." '$length',"
            ." {$this->groupId}, $p_audioFileId, '$length')";
            $result = $CC_DBC->query($sql);
            if (PEAR::isError($result)) {
                //var_dump($sql);
                return $result;
            }

        }
        elseif (!is_null($p_playlistId)){
            // Schedule a whole playlist

            // Load existing playlist
            $playlist = Application_Model_Playlist::Recall($p_playlistId);
            if (is_null($playlist)) {
                return new PEAR_Error("Could not find playlist.");
            }

            // Check if there are any conflicts with existing entries
            $length = trim($playlist->getLength());
            //var_dump($length);
            if (empty($length)) {
                return new PEAR_Error("Length is empty.");
            }

            // Insert all items into the schedule
            $this->groupId = $CC_DBC->GetOne("SELECT nextval('schedule_group_id_seq')");
            $itemStartTime = $p_datetime;

            $plItems = $playlist->getContents();
            //var_dump($plItems);
            foreach ($plItems as $row) {
                $trackLength = $row["cliplength"];
                //var_dump($trackLength);
                $sql = "INSERT INTO ".$CC_CONFIG["scheduleTable"]
                ." (instance_id, playlist_id, starts, ends, group_id, file_id,"
                ." clip_length, cue_in, cue_out, fade_in, fade_out)"
                ." VALUES ($p_showInstance, $p_playlistId, TIMESTAMP '$itemStartTime', "
                ." (TIMESTAMP '$itemStartTime' + INTERVAL '$trackLength'),"
                ." '{$this->groupId}', '{$row['file_id']}', '$trackLength', '{$row['cuein']}',"
                ." '{$row['cueout']}', '{$row['fadein']}','{$row['fadeout']}')";
                $result = $CC_DBC->query($sql);
                if (PEAR::isError($result)) {
                    //var_dump($sql);
                    return $result;
                }
                $itemStartTime = $CC_DBC->getOne("SELECT TIMESTAMP '$itemStartTime' + INTERVAL '$trackLength'");
            }
        }

        Application_Model_RabbitMq::PushSchedule();
        return $this->groupId;
    }

    public function addFileAfter($show_instance, $p_groupId, $p_audioFileId) {
        global $CC_CONFIG, $CC_DBC;
        // Get the end time for the given entry
        $sql = "SELECT MAX(ends) FROM ".$CC_CONFIG["scheduleTable"]
        ." WHERE group_id=$p_groupId";
        $startTime = $CC_DBC->GetOne($sql);
        return $this->add($show_instance, $startTime, $p_audioFileId);
    }

    public function addPlaylistAfter($show_instance, $p_groupId, $p_playlistId) {
        global $CC_CONFIG, $CC_DBC;
        // Get the end time for the given entry
        $sql = "SELECT MAX(ends) FROM ".$CC_CONFIG["scheduleTable"]
        ." WHERE group_id=$p_groupId";

        $startTime = $CC_DBC->GetOne($sql);
        return $this->add($show_instance, $startTime, null, $p_playlistId);
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
        $retVal = $CC_DBC->query($sql);
        Application_Model_RabbitMq::PushSchedule();
        return $retVal;
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
        $sql = "SELECT "
        ." st.id,"
        ." st.file_id,"
        ." st.cue_in,"
        ." st.cue_out,"
        ." st.clip_length,"
        ." st.fade_in,"
        ." st.fade_out,"
        ." st.starts,"
        ." st.ends"
        ." FROM $CC_CONFIG[scheduleTable] as st"
        ." LEFT JOIN $CC_CONFIG[showInstances] as si"
        ." ON st.instance_id = si.id"
        ." WHERE st.group_id=$this->groupId"
        ." AND st.starts < si.ends"
        ." ORDER BY st.starts";
        return $CC_DBC->GetAll($sql);
    }

    public function notifyGroupStartPlay() {
        global $CC_CONFIG, $CC_DBC;
        $sql = "UPDATE ".$CC_CONFIG['scheduleTable']
                ." SET schedule_group_played=TRUE"
                ." WHERE group_id=".$this->groupId;
        $retVal = $CC_DBC->query($sql);
        return $retVal;
    }

    public function notifyMediaItemStartPlay($p_fileId) {
        global $CC_CONFIG, $CC_DBC;
        $sql = "UPDATE ".$CC_CONFIG['scheduleTable']
                ." SET media_item_played=TRUE"
                ." WHERE group_id=".$this->groupId
                ." AND file_id=".pg_escape_string($p_fileId);
        $retVal = $CC_DBC->query($sql);
        return $retVal;
    }
}
