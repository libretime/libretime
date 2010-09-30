<?php

class ScheduleGroup {

  private $groupId;

  public function __construct($p_groupId = null) {
    $this->groupId = $p_groupId;
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
        var_dump($sql);
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
      foreach ($plItems as $row) {
        $trackLength = $row["cliplength"];
        $sql = "INSERT INTO ".$CC_CONFIG["scheduleTable"]
          ." (id, playlist_id, starts, ends, group_id, file_id,"
          ." clip_length, cue_in, cue_out, fade_in, fade_out)"
          ." VALUES ($id, $p_playlistId, TIMESTAMP '$itemStartTime', "
          ." (TIMESTAMP '$itemStartTime' + INTERVAL '$trackLength'),"
          ." {$this->groupId}, {$row['file_id']}, '$trackLength', '{$row['cuein']}',"
          ." '{$row['cueout']}', '{$row['fadein']}','{$row['fadeout']}')";
        $result = $CC_DBC->query($sql);
        if (PEAR::isError($result)) {
          var_dump($sql);
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
    $sql = "SELECT ends FROM ".$CC_CONFIG["scheduleTable"]
          ." WHERE group_id=$p_groupId";
    $startTime = $CC_DBC->GetOne($sql);
    return $this->add($startTime, $p_audioFileId);
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

}

class Schedule {

  function __construct() {

  }

  /**
   * Return true if there is nothing in the schedule for the given times.
   *
   * @param string $p_datetime
   * @param string $p_length
   *
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
    $count = $CC_DBC->GetOne($sql);
    return ($count == '0');
  }

  public function onAddTrackToPlaylist($playlistId, $audioTrackId) {

  }

  public function onRemoveTrackFromPlaylist($playlistId, $audioTrackId) {

  }

  /**
   * Returns array indexed numberically of:
   *    "playlistId"/"playlist_id" (aliases to the same thing)
   *    "start"/"starts" (aliases to the same thing)
   *    "end"/"ends" (aliases to the same thing)
   *    "group_id"/"id" (aliases to the same thing)
   *    "clip_length"
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
   *    Retreive playlists as a single item.
   */
  public static function GetItems($p_fromDateTime, $p_toDateTime, $p_playlistsOnly = true) {
    global $CC_CONFIG, $CC_DBC;
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
      $rows = $CC_DBC->GetAll($sql);
      foreach ($rows as &$row) {
        $row["playlistId"] = $row["playlist_id"];
        $row["start"] = $row["starts"];
        $row["end"] = $row["ends"];
        $row["id"] = $row["group_id"];
      }
    }
    return $rows;
  }

  function getSchedulerTime() {

  }

  function getCurrentlyPlaying() {

  }

  function getNextItem($nextCount = 1) {

  }

  function getStatus() {

  }

}

?>