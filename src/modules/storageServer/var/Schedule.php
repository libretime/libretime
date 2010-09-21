<?php

class ScheduleItem {

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
   *
   * @param $p_audioFileId
   * @param $p_playlistId
   * @param $p_datetime
   * @param $p_options
   * @return int|null
   *    Return null if the item could not be added.
   */
  public function add($p_datetime, $p_audioFileId = null, $p_playlistId = null, $p_options = null) {
    global $CC_CONFIG, $CC_DBC;
    if (!is_null($p_audioFileId)) {
      // Schedule a single audio track

      // Load existing track
      $track = StoredFile::Recall($p_audioFileId);
      if (is_null($track)) {
        return null;
      }

      // Check if there are any conflicts with existing entries
      $metadata = $track->getMetadata();
      $length = trim($metadata["length"]);
      if (!Schedule::isScheduleEmptyInRange($p_datetime, $length)) {
        return null;
      }

      // Insert into the table
      $this->groupId = $CC_DBC->GetOne("SELECT nextval('schedule_group_id_seq')");
      $id = $this->dateToId($p_datetime);
      $sql = "INSERT INTO ".$CC_CONFIG["scheduleTable"]
        ." (id, playlist_id, starts, ends, group_id, file_id)"
        ." VALUES ($id, 0, TIMESTAMP '$p_datetime', "
        ." (TIMESTAMP '$p_datetime' + INTERVAL '$length'),"
        ." {$this->groupId}, $p_audioFileId)";
      $CC_DBC->query($sql);
      return $this->groupId;

    } elseif (!is_null($p_playlistId)){
      // Schedule a whole playlist
    }

    // return group ID
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
   */
  public static function isScheduleEmptyInRange($p_datetime, $p_length) {
    global $CC_CONFIG, $CC_DBC;
    $sql = "SELECT COUNT(*) FROM ".$CC_CONFIG["scheduleTable"]
      ." WHERE (starts <= '$p_datetime') "
      ." AND (ends >= (TIMESTAMP '$p_datetime' + INTERVAL '$p_length'))";
    $count = $CC_DBC->GetOne($sql);
    return ($count == '0');
  }

  public function onAddTrackToPlaylist($playlistId, $audioTrackId) {

  }

  public function onRemoveTrackFromPlaylist($playlistId, $audioTrackId) {

  }

  /**
   * Returns array indexed numberically of:
   *    "playlistId" (gunid)
   *    "start"
   *    "end"
   *    "id" (DB id)
   *
   * @param $fromDateTime
   * @param $toDateTime
   */
  public static function GetItems($fromDateTime, $toDateTime, $playlistsOnly = true) {
    global $CC_CONFIG, $CC_DBC;
    $sql = "SELECT * FROM ".$CC_CONFIG["scheduleTable"]
          ." WHERE (starts >= TIMESTAMP '$fromDateTime') "
          ." AND (ends <= TIMESTAMP '$toDateTime')";
    $rows = $CC_DBC->GetAll($sql);
    foreach ($rows as &$row) {
      $row["playlistId"] = $row["playlist_id"];
      $row["start"] = $row["starts"];
      $row["end"] = $row["ends"];
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