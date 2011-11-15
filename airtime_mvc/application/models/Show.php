<?php

class Application_Model_Show {

    private $_showId;

    public function __construct($showId=NULL)
    {
        $this->_showId = $showId;
    }

    public function getName()
    {
        $show = CcShowQuery::create()->findPK($this->_showId);
        return $show->getDbName();
    }

    public function setName($name)
    {
        $show = CcShowQuery::create()->findPK($this->_showId);
        $show->setDbName($name);
        Application_Model_RabbitMq::PushSchedule();
    }

    public function getDescription()
    {
        $show = CcShowQuery::create()->findPK($this->_showId);
        return $show->getDbDescription();
    }

    public function setDescription($description)
    {
        $show = CcShowQuery::create()->findPK($this->_showId);
        $show->setDbDescription($description);
    }

    public function getColor()
    {
        $show = CcShowQuery::create()->findPK($this->_showId);
        return $show->getDbColor();
    }

    public function setColor($color)
    {
        $show = CcShowQuery::create()->findPK($this->_showId);
        $show->setDbColor($color);
    }

    public function getUrl()
    {
        $show = CcShowQuery::create()->findPK($this->_showId);
        return $show->getDbUrl();
    }

    public function setUrl($p_url)
    {
        $show = CcShowQuery::create()->findPK($this->_showId);
        $show->setDbUrl($p_url);
    }

    public function getGenre()
    {
        $show = CcShowQuery::create()->findPK($this->_showId);
        return $show->getDbGenre();
    }

    public function setGenre($p_genre)
    {
        $show = CcShowQuery::create()->findPK($this->_showId);
        $show->setDbGenre($p_genre);
    }

    public function getBackgroundColor()
    {
        $show = CcShowQuery::create()->findPK($this->_showId);
        return $show->getDbBackgroundColor();
    }

    public function setBackgroundColor($backgroundColor)
    {
        $show = CcShowQuery::create()->findPK($this->_showId);
        $show->setDbBackgroundColor($backgroundColor);
    }

    public function getId()
    {
        return $this->_showId;
    }

    public function getHosts()
    {
        global $CC_DBC;

        $sql = "SELECT first_name, last_name
                FROM cc_show_hosts LEFT JOIN cc_subjs ON cc_show_hosts.subjs_id = cc_subjs.id
                    WHERE show_id = {$this->_showId}";

        $hosts = $CC_DBC->GetAll($sql);

        $res = array();
        foreach($hosts as $host) {
            $res[] = $host['first_name']." ".$host['last_name'];
        }

        return $res;
    }

    public function cancelShow($day_timestamp)
    {
        global $CC_DBC;

        $timeinfo = explode(" ", $day_timestamp);

        CcShowDaysQuery::create()
            ->filterByDbShowId($this->_showId)
            ->update(array('DbLastShow' => $timeinfo[0]));

        $sql = "DELETE FROM cc_show_instances
                    WHERE starts >= '{$day_timestamp}' AND show_id = {$this->_showId}";

        $CC_DBC->query($sql);

        // check if we can safely delete the show
        $showInstancesRow = CcShowInstancesQuery::create()
            ->filterByDbShowId($this->_showId)
            ->findOne();

        if(is_null($showInstancesRow)){
            $sql = "DELETE FROM cc_show WHERE id = {$this->_showId}";
            $CC_DBC->query($sql);
        }

        Application_Model_RabbitMq::PushSchedule();
    }

    /**
     * This function is called when a repeating show is edited and the
     * days that is repeats on have changed. More specifically, a day
     * that the show originally repeated on has been "unchecked".
     *
     * Removes Show Instances that occur on days of the week specified
     * by input array. For example, if array contains one value of "0",
     * (0 = Sunday, 1=Monday) then all show instances that occur on
     * Sunday are removed.
     *
     * @param array p_uncheckedDays
     *      An array specifying which days should be removed.
     */
    public function removeUncheckedDaysInstances($p_uncheckedDays)
    {
        global $CC_DBC;

        $uncheckedDaysImploded = implode(",", $p_uncheckedDays);
        $showId = $this->getId();

        $date = new Application_Model_DateHelper;
        $timestamp = $date->getTimestamp();

        $sql = "DELETE FROM cc_show_instances"
            ." WHERE EXTRACT(DOW FROM starts) IN ($uncheckedDaysImploded)"
            ." AND starts > TIMESTAMP '$timestamp'"
            ." AND show_id = $showId";

        $CC_DBC->query($sql);
    }

    /**
     * Check whether the current show originated
     * from a recording.
     *
     * @return boolean
     *      true if originated from recording, otherwise false.
     */
    public function isRecorded(){
        $showInstancesRow = CcShowInstancesQuery::create()
        ->filterByDbShowId($this->getId())
        ->filterByDbRecord(1)
        ->findOne();

        return !is_null($showInstancesRow);
    }

    /**
     * Check whether the current show has rebroadcasts of a recorded
     * show. Should be used in conjunction with isRecorded().
     *
     * @return boolean
     *      true if show has rebroadcasts, otherwise false.
     */
    public function isRebroadcast()
    {
         $showInstancesRow = CcShowInstancesQuery::create()
        ->filterByDbShowId($this->_showId)
        ->filterByDbRebroadcast(1)
        ->findOne();

        return !is_null($showInstancesRow);
    }

    /**
     * Get start time and absolute start date for a recorded
     * shows rebroadcasts. For example start date format would be
     * YYYY-MM-DD and time would HH:MM
     *
     * @return array
     *      array of associate arrays containing "start_date" and "start_time"
     */
    public function getRebroadcastsAbsolute()
    {
        global $CC_DBC;

        $showId = $this->getId();
        $sql = "SELECT date(starts) "
                ."FROM cc_show_instances "
                ."WHERE show_id = $showId "
                ."AND record = 1";
        $baseDate = $CC_DBC->GetOne($sql);

        if (is_null($baseDate)){
            return array();
        }

        $sql = "SELECT date(DATE '$baseDate' + day_offset::INTERVAL) as start_date, start_time FROM cc_show_rebroadcast "
            ."WHERE show_id = $showId "
            ."ORDER BY start_date";

        return $CC_DBC->GetAll($sql);
    }

    /**
     * Get start time and relative start date for a recorded
     * shows rebroadcasts. For example start date format would be
     * "x days" and time would HH:MM:SS
     *
     * @return array
     *      array of associate arrays containing "day_offset" and "start_time"
     */
    public function getRebroadcastsRelative()
    {
        global $CC_DBC;

        $showId = $this->getId();
        $sql = "SELECT day_offset, start_time FROM cc_show_rebroadcast "
            ."WHERE show_id = $showId "
            ."ORDER BY day_offset";

        return $CC_DBC->GetAll($sql);
    }

    /**
     * Check whether the current show is set to repeat
     * repeating shows.
     *
     * @return boolean
     *      true if repeating shows, otherwise false.
     */
    public function isRepeating()
    {
        $showDaysRow = CcShowDaysQuery::create()
        ->filterByDbShowId($this->_showId)
        ->findOne();

        if (!is_null($showDaysRow)){
            return ($showDaysRow->getDbRepeatType() != -1);
        } else
            return false;
    }

    /**
     * Get the repeat type of the show. Show can have repeat
     * type of "weekly", "bi-weekly" and "monthly". These values
     * are represented by 0, 1, and 2 respectively.
     *
     * @return int
     *      Return the integer corresponding to the repeat type.
     */
    public function getRepeatType()
    {
        $showDaysRow = CcShowDaysQuery::create()
        ->filterByDbShowId($this->_showId)
        ->findOne();

        if (!is_null($showDaysRow)){
            return $showDaysRow->getDbRepeatType();
        } else
            return -1;
    }

    /**
     * Get the end date for a repeating show in the format yyyy-mm-dd
     *
     * @return string
     *      Return the end date for the repeating show or the empty
     *      string if there is no end.
     */
    public function getRepeatingEndDate(){
        global $CC_DBC;

        $showId = $this->getId();
        $sql = "SELECT last_show FROM cc_show_days"
            ." WHERE show_id = $showId"
            ." ORDER BY last_show DESC";

        $endDate = $CC_DBC->GetOne($sql);

        if (is_null($endDate)){
            return "";
        } else {
            return $endDate;
        }
    }

    /**
     * Deletes all future instances of the current show object
     * from the show_instances table. This function is used when
     * a show is being edited - in some cases, when a show is edited
     * we just destroy all future show instances, and let another function
     * regenerate them later on. Note that this isn't always the most
     * desirable thing to do. Deleting a show instance and regenerating
     * it cause any scheduled playlists within those show instances to
     * be gone for good.
     */
    public function deleteAllInstances(){
        global $CC_DBC;

        $date = new Application_Model_DateHelper;
        $timestamp = $date->getTimestamp();

        $showId = $this->getId();
        $sql = "DELETE FROM cc_show_instances"
                ." WHERE starts > TIMESTAMP '$timestamp'"
                ." AND show_id = $showId";

        $CC_DBC->query($sql);
    }

    /**
     * Deletes all future rebroadcast instances of the current
     * show object from the show_instances table.
     */
    public function deleteAllRebroadcasts(){
        global $CC_DBC;

        $date = new Application_Model_DateHelper;
        $timestamp = $date->getTimestamp();

        $showId = $this->getId();
        $sql = "DELETE FROM cc_show_instances"
                ." WHERE starts > TIMESTAMP '$timestamp'"
                ." AND show_id = $showId"
                ." AND rebroadcast = 1";

        $CC_DBC->query($sql);
    }

    /**
     * Deletes all show instances of current show after a
     * certain date. Note that although not enforced, $p_date
     * should never be in the past, as we never want to allow
     * deletion of shows that have already occured.
     *
     * @param string $p_date
     *      The date which to delete after, if null deletes from the current timestamp.
     */
    public function removeAllInstancesFromDate($p_date=null){
        global $CC_DBC;

        $date = new Application_Model_DateHelper;
        $timestamp = $date->getTimestamp();

        if(is_null($p_date)) {
            $date = new Application_Model_DateHelper;
            $p_date = $date->getDate();
        }

        $showId = $this->getId();
        $sql = "DELETE FROM cc_show_instances "
                ." WHERE date(starts) >= DATE '$p_date'"
                ." AND starts > TIMESTAMP '$timestamp'"
                ." AND show_id = $showId";

        $CC_DBC->query($sql);

    }

    /**
     * Deletes all show instances of current show before a
     * certain date.
     *
     * This function is used in the case where a repeating show is being
     * edited and the start date of the first show has been changed more
     * into the future. In this case, delete any show instances that
     * exist before the new start date.
     *
     * @param string $p_date
     *      The date which to delete before
     */
    public function removeAllInstancesBeforeDate($p_date){
        global $CC_DBC;

        $date = new Application_Model_DateHelper;
        $timestamp = $date->getTimestamp();

        $showId = $this->getId();
        $sql = "DELETE FROM cc_show_instances "
                ." WHERE date(starts) < DATE '$p_date'"
                ." AND starts > TIMESTAMP '$timestamp'"
                ." AND show_id = $showId";

        $CC_DBC->query($sql);
    }

    /**
     * Get the start date of the current show.
     *
     * @return string
     *      The start date in the format YYYY-MM-DD
     */
    public function getStartDate(){
        global $CC_DBC;

        $showId = $this->getId();
        $sql = "SELECT first_show FROM cc_show_days"
            ." WHERE show_id = $showId"
            ." ORDER BY first_show";

        $firstDate = $CC_DBC->GetOne($sql);

        if (is_null($firstDate)){
            return "";
        } else {
            return $firstDate;
        }
    }

    /**
     * Get the start time of the current show.
     *
     * @return string
     *      The start time in the format HH:MM:SS
     */
    public function getStartTime(){
        global $CC_DBC;

        $showId = $this->getId();
        $sql = "SELECT start_time FROM cc_show_days"
            ." WHERE show_id = $showId";

        $startTime = $CC_DBC->GetOne($sql);

        if (is_null($startTime)){
            return "";
        } else {
            return $startTime;
        }
    }

    /**
     * Get the end date of the current show.
     * Note that this is not the end date of repeated show
     *
     * @return string
     *      The end date in the format YYYY-MM-DD
     */
    public function getEndDate(){
        $startDate = $this->getStartDate();
        $startTime = $this->getStartTime();
        $duration = $this->getDuration();

        $startDateTime = new DateTime($startDate.' '.$startTime);
        $duration = explode(":", $duration);

        $endDate = $startDateTime->add(new DateInterval('PT'.$duration[0].'H'.$duration[1].'M'));

        return $endDate->format('Y-m-d');
    }

    /**
     * Get the end time of the current show.
     *
     * @return string
     *      The start time in the format HH:MM:SS
     */
    public function getEndTime(){
        $startDate = $this->getStartDate();
        $startTime = $this->getStartTime();
        $duration = $this->getDuration();

        $startDateTime = new DateTime($startDate.' '.$startTime);
        $duration = explode(":", $duration);

        $endDate = $startDateTime->add(new DateInterval('PT'.$duration[0].'H'.$duration[1].'M'));

        return $endDate->format('H:i:s');
    }

    /**
     * Indicate whether the starting point of the show is in the
     * past.
     *
     * @return boolean
     *      true if the StartDate is in the past, false otherwise
     */
    public function isStartDateTimeInPast(){
        $date = new Application_Model_DateHelper;
        $current_timestamp = $date->getTimestamp();
        return ($current_timestamp > ($this->getStartDate()." ".$this->getStartTime()));
    }

    /**
     * Get the ID's of future instance of the current show.
     *
     * @return array
     *      A simple array containing all ID's of show instance
     *  scheduled in the future.
     */
    public function getAllFutureInstanceIds(){
        global $CC_DBC;

        $date = new Application_Model_DateHelper;
        $timestamp = $date->getTimestamp();

        $showId = $this->getId();
        $sql = "SELECT id from cc_show_instances"
            ." WHERE show_id = $showId"
            ." AND starts > TIMESTAMP '$timestamp'";

        $rows = $CC_DBC->GetAll($sql);

        $instance_ids = array();
        foreach ($rows as $row){
            $instance_ids[] = $row["id"];
        }
        return $instance_ids;
    }

    /* Called when a show's duration is changed (edited).
     *
     * @param array $p_data
     *      array containing the POST data about the show from the
     *      browser.
     *
     */
    private function updateDurationTime($p_data){
        //need to update cc_show_instances, cc_show_days

        global $CC_DBC;

        $date = new Application_Model_DateHelper;
        $timestamp = $date->getTimestamp();

        $sql = "UPDATE cc_show_days "
                ."SET duration = '$p_data[add_show_duration]' "
                ."WHERE show_id = $p_data[add_show_id]";
        $CC_DBC->query($sql);

        $sql = "UPDATE cc_show_instances "
                ."SET ends = starts + INTERVAL '$p_data[add_show_duration]' "
                ."WHERE show_id = $p_data[add_show_id] "
                ."AND starts > TIMESTAMP '$timestamp'";
        $CC_DBC->query($sql);

    }

    private function updateStartDateTime($p_data, $p_endDate){
        //need to update cc_schedule, cc_show_instances, cc_show_days

        global $CC_DBC;

        $date = new Application_Model_DateHelper;
        $timestamp = $date->getTimestamp();

        $sql = "UPDATE cc_show_days "
                ."SET start_time = TIME '$p_data[add_show_start_time]', "
                ."first_show = DATE '$p_data[add_show_start_date]', ";
        if (strlen ($p_endDate) == 0){
            $sql .= "last_show = NULL ";
        } else {
            $sql .= "last_show = DATE '$p_endDate' ";
        }
        $sql .= "WHERE show_id = $p_data[add_show_id]";
        $CC_DBC->query($sql);

        $oldStartDateTimeEpoch = strtotime($this->getStartDate()." ".$this->getStartTime());
        $newStartDateTimeEpoch = strtotime($p_data['add_show_start_date']." ".$p_data['add_show_start_time']);
        $diff = $newStartDateTimeEpoch - $oldStartDateTimeEpoch;

        $sql = "UPDATE cc_show_instances "
                ."SET starts = starts + INTERVAL '$diff sec', "
                ."ends = ends + INTERVAL '$diff sec' "
                ."WHERE show_id = $p_data[add_show_id] "
                ."AND starts > TIMESTAMP '$timestamp'";
        $CC_DBC->query($sql);

        $showInstanceIds = $this->getAllFutureInstanceIds();
        if (count($showInstanceIds) > 0 && $diff != 0){
            $showIdsImploded = implode(",", $showInstanceIds);
            $sql = "UPDATE cc_schedule "
                    ."SET starts = starts + INTERVAL '$diff sec', "
                    ."ends = ends + INTERVAL '$diff sec' "
                    ."WHERE instance_id IN ($showIdsImploded)";
            $CC_DBC->query($sql);
        }
    }

    public function getDuration($format=false){
        $showDay = CcShowDaysQuery::create()->filterByDbShowId($this->getId())->findOne();
        if(!$format){
            return $showDay->getDbDuration();
        }else{
            $info = explode(':',$showDay->getDbDuration());
            return ($info[0] != 0 ? intval($info[0]).'h'.' ' : '').($info[1] != 0 ? intval($info[1]).'m' : '');
        }
    }

    public function getShowDays(){
        $showDays = CcShowDaysQuery::create()->filterByDbShowId($this->getId())->find();

        $days = array();
        foreach ($showDays as $showDay){
            array_push($days, $showDay->getDbDay());
        }

        return $days;
    }

    public function hasInstance(){
        return (!is_null($this->getInstance()));
    }

    public function getInstance(){
        $showInstances = CcShowInstancesQuery::create()->filterByDbShowId($this->getId())->findOne();
        return $showInstances;
    }

    public function hasInstanceOnDate($p_dateTime){
        return (!is_null($this->getInstanceOnDate($p_dateTime)));
    }

    public function getInstanceOnDate($p_dateTime){
        global $CC_DBC;
        $timestamp = $p_dateTime->format("Y-m-d H:i:s");

        $showId = $this->getId();
        $sql = "SELECT id FROM cc_show_instances"
            ." WHERE date(starts) = date(TIMESTAMP '$timestamp') "
            ." AND show_id = $showId";

        $row = $CC_DBC->GetOne($sql);
        return CcShowInstancesQuery::create()->findPk($row);
    }

    public function deletePossiblyInvalidInstances($p_data, $p_endDate, $isRecorded, $repeatType)
    {
        if ($p_data['add_show_repeats'] != $this->isRepeating()){
            //repeat option was toggled
            $this->deleteAllInstances();
        }

        if ($p_data['add_show_duration'] != $this->getDuration()){
            //duration has changed
            $this->updateDurationTime($p_data);
        }

        if ($isRecorded){
            //delete all rebroadcasts. They will simply be recreated later
            //in the execution of this PHP script. This simplifies having to
            //reason about whether we should keep individual rebroadcasts or
            //delete them or move them around etc.
            $this->deleteAllRebroadcasts();
        }

        if ($p_data['add_show_repeats']){
            if (($repeatType == 1 || $repeatType == 2) &&
                $p_data['add_show_start_date'] != $this->getStartDate()){

                //start date has changed when repeat type is bi-weekly or monthly.
                //This screws up the repeating positions of show instances, so lets
                //just delete them for now. (CC-2351)

                $this->deleteAllInstances();
            }

            if ($p_data['add_show_start_date'] != $this->getStartDate()
                || $p_data['add_show_start_time'] != $this->getStartTime()){
                //start date/time has changed

                $newDate = strtotime($p_data['add_show_start_date']);
                $oldDate = strtotime($this->getStartDate());
                if ($newDate > $oldDate){
                    $this->removeAllInstancesBeforeDate($p_data['add_show_start_date']);
                }

                $this->updateStartDateTime($p_data, $p_endDate);
            }

            if ($repeatType != $this->getRepeatType()){
                //repeat type changed.
                $this->deleteAllInstances();
            } else {
                //repeat type is the same, check if the days of the week are the same
                $repeatingDaysChanged = false;
                $showDaysArray = $this->getShowDays();
                if (count($p_data['add_show_day_check']) == count($showDaysArray)){
                    //same number of days checked, lets see if they are the same numbers
                    $intersect = array_intersect($p_data['add_show_day_check'], $showDaysArray);
                    if (count($intersect) != count($p_data['add_show_day_check'])){
                        $repeatingDaysChanged = true;
                    }
                }
                else {
                    $repeatingDaysChanged = true;
                }

                if ($repeatingDaysChanged){
                    $daysRemoved = array_diff($showDaysArray, $p_data['add_show_day_check']);

                    if (count($daysRemoved) > 0){
                        $this->removeUncheckedDaysInstances($daysRemoved);
                    }
                }
            }

            //Check if end date for the repeat option has changed. If so, need to take care
            //of deleting possible invalid Show Instances.
            if ((strlen($this->getRepeatingEndDate()) == 0) == $p_data['add_show_no_end']){
                //show "Never Ends" option was toggled.
                if ($p_data['add_show_no_end']){
                }
                else {
                    $this->removeAllInstancesFromDate($p_endDate);
                }
            }
            if ($this->getRepeatingEndDate() != $p_data['add_show_end_date']){
                //end date was changed.

                $newDate = strtotime($p_data['add_show_end_date']);
                $oldDate = strtotime($this->getRepeatingEndDate());
                if ($newDate < $oldDate){
                    $this->removeAllInstancesFromDate($p_endDate);
                }
            }
        }
    }

    /**
     * Create a show.
     *
     * Note: end dates are non inclusive.
     *
     * @param array $data
     * @return int
     *     Show ID
     */
    public static function create($data)
    {
        $startDateTime = new DateTime($data['add_show_start_date']." ".$data['add_show_start_time']);
        $utcStartDateTime = clone $startDateTime;
        $utcStartDateTime->setTimezone(new DateTimeZone('UTC'));

        if ($data['add_show_no_end']) {
            $endDate = NULL;
        }
        else if ($data['add_show_repeats']) {
            $endDateTime = new DateTime($data['add_show_end_date']);
            //$endDateTime->setTimezone(new DateTimeZone('UTC'));
            $endDateTime->add(new DateInterval("P1D"));
            $endDate = $endDateTime->format("Y-m-d");
        }
        else {
            $endDateTime = new DateTime($data['add_show_start_date']);
            //$endDateTime->setTimezone(new DateTimeZone('UTC'));
            $endDateTime->add(new DateInterval("P1D"));
            $endDate = $endDateTime->format("Y-m-d");
        }

        //What we are doing here is checking if the show repeats or if
        //any repeating days have been checked. If not, then by default
        //the "selected" DOW is the initial day.
        $startDow = date("w", $utcStartDateTime->getTimestamp());
        if (!$data['add_show_repeats']) {
            $data['add_show_day_check'] = array($startDow);
        } else if ($data['add_show_repeats'] && $data['add_show_day_check'] == "") {
            $data['add_show_day_check'] = array($startDow);
        }

        //find repeat type or set to a non repeating show.
        $repeatType = ($data['add_show_repeats']) ? $data['add_show_repeat_type'] : -1;

        if ($data['add_show_id'] == -1){
            $ccShow = new CcShow();
        } else {
            $ccShow = CcShowQuery::create()->findPK($data['add_show_id']);
        }
        $ccShow->setDbName($data['add_show_name']);
        $ccShow->setDbDescription($data['add_show_description']);
        $ccShow->setDbUrl($data['add_show_url']);
        $ccShow->setDbGenre($data['add_show_genre']);
        $ccShow->setDbColor($data['add_show_color']);
        $ccShow->setDbBackgroundColor($data['add_show_background_color']);
        $ccShow->save();

        $showId = $ccShow->getDbId();

        $isRecorded = (isset($data['add_show_record']) && $data['add_show_record']) ? 1 : 0;

        if ($data['add_show_id'] != -1){
            $show = new Application_Model_Show($showId);
            $show->deletePossiblyInvalidInstances($data, $endDate, $isRecorded, $repeatType);
        }

        //check if we are adding or updating a show, and if updating
        //erase all the show's show_days information first.
        if ($data['add_show_id'] != -1){
            CcShowDaysQuery::create()->filterByDbShowId($data['add_show_id'])->delete();
        }

        //don't set day for monthly repeat type, it's invalid.
        if ($data['add_show_repeats'] && $data['add_show_repeat_type'] == 2){
            $showDay = new CcShowDays();
            $showDay->setDbFirstShow($startDateTime->format("Y-m-d"));
            $showDay->setDbLastShow($endDate);
            $showDay->setDbStartTime($startDateTime->format("H:i:s"));
            $showDay->setDbTimezone(date_default_timezone_get());
            $showDay->setDbDuration($data['add_show_duration']);
            $showDay->setDbRepeatType($repeatType);
            $showDay->setDbShowId($showId);
            $showDay->setDbRecord($isRecorded);
            $showDay->save();
        } else {
            foreach ($data['add_show_day_check'] as $day) {
                $daysAdd=0;
                $startDateTimeClone = clone $startDateTime;
                if ($startDow !== $day){
                    if ($startDow > $day)
                        $daysAdd = 6 - $startDow + 1 + $day;
                    else
                        $daysAdd = $day - $startDow;

                    $startDateTimeClone->add(new DateInterval("P".$daysAdd."D"));
                }
                if (is_null($endDate) || $startDateTimeClone->getTimestamp() <= $endDateTime->getTimestamp()) {
                    $showDay = new CcShowDays();
                    $showDay->setDbFirstShow($startDateTimeClone->format("Y-m-d"));
                    $showDay->setDbLastShow($endDate);
                    $showDay->setDbStartTime($startDateTimeClone->format("H:i"));
                    $showDay->setDbTimezone(date_default_timezone_get());
                    $showDay->setDbDuration($data['add_show_duration']);
                    $showDay->setDbDay($day);
                    $showDay->setDbRepeatType($repeatType);
                    $showDay->setDbShowId($showId);
                    $showDay->setDbRecord($isRecorded);
                    $showDay->save();
                }
            }
        }

        //check if we are adding or updating a show, and if updating
        //erase all the show's future show_rebroadcast information first.
        if (($data['add_show_id'] != -1) && $data['add_show_rebroadcast']){
            CcShowRebroadcastQuery::create()
                ->filterByDbShowId($data['add_show_id'])
                ->delete();
        }
        //adding rows to cc_show_rebroadcast
        if (($isRecorded && $data['add_show_rebroadcast']) && ($repeatType != -1)) {
            for ($i=1; $i<=10; $i++) {
                if ($data['add_show_rebroadcast_date_'.$i]) {
                    $showRebroad = new CcShowRebroadcast();
                    $showRebroad->setDbDayOffset($data['add_show_rebroadcast_date_'.$i]);
                    $showRebroad->setDbStartTime($data['add_show_rebroadcast_time_'.$i]);
                    $showRebroad->setDbShowId($showId);
                    $showRebroad->save();
                }
            }
        }
        else if ($isRecorded && $data['add_show_rebroadcast'] && ($repeatType == -1)){
            for ($i=1; $i<=10; $i++) {
                if ($data['add_show_rebroadcast_date_absolute_'.$i]) {
                    $con = Propel::getConnection(CcShowPeer::DATABASE_NAME);
                    $sql = "SELECT date '{$data['add_show_rebroadcast_date_absolute_'.$i]}' - date '{$data['add_show_start_date']}' ";
                    $r = $con->query($sql);
                    $offset_days = $r->fetchColumn(0);

                    $showRebroad = new CcShowRebroadcast();
                    $showRebroad->setDbDayOffset($offset_days." days");
                    $showRebroad->setDbStartTime($data['add_show_rebroadcast_time_absolute_'.$i]);
                    $showRebroad->setDbShowId($showId);
                    $showRebroad->save();
                }
            }
        }

        //check if we are adding or updating a show, and if updating
        //erase all the show's show_rebroadcast information first.
        if ($data['add_show_id'] != -1){
            CcShowHostsQuery::create()->filterByDbShow($data['add_show_id'])->delete();
        }
        if (is_array($data['add_show_hosts'])) {
            //add selected hosts to cc_show_hosts table.
            foreach ($data['add_show_hosts'] as $host) {
                $showHost = new CcShowHosts();
                $showHost->setDbShow($showId);
                $showHost->setDbHost($host);
                $showHost->save();
            }
        }

        Application_Model_Show::populateShowUntil($showId);
        Application_Model_RabbitMq::PushSchedule();
        return $showId;
    }

    /**
     * Generate repeating show instances for a single show up to the given date.
     * If no date is given, use the one in the user's preferences, which is stored
     * automatically by FullCalendar as the furthest date in the future the user
     * has looked at.
     *
     * @param int $p_showId
     * @param DateTime $p_dateTime
     *        DateTime object in UTC time.
     */
    public static function populateShowUntil($p_showId, $p_dateTime = NULL)
    {
        global $CC_DBC;
        if (is_null($p_dateTime)) {
            $date = Application_Model_Preference::GetShowsPopulatedUntil();

            if (is_null($date)) {
                $p_dateTime = new DateTime("now", new DateTimeZone('UTC'));
                Application_Model_Preference::SetShowsPopulatedUntil($p_dateTime);
            } else {
                $p_dateTime = $date;
            }
        }

        $sql = "SELECT * FROM cc_show_days WHERE show_id = $p_showId";
        $res = $CC_DBC->GetAll($sql);

        foreach ($res as $showRow) {
            Application_Model_Show::populateShow($showRow, $p_dateTime);
        }
    }

    /**
     * We are going to use cc_show_days as a template, to generate Show Instances. This function
     * is basically a dispatcher that looks at the show template, and sends it to the correct function
     * so that Show Instance generation can begin. After the all show instances have been created, pushes
     * the schedule to Pypo.
     *
     * @param array $p_showRow
     *        A row from cc_show_days table
     * @param DateTime $p_dateTime
     *        DateTime object in UTC time.
     */
    private static function populateShow($p_showRow, $p_dateTime) {
        if($p_showRow["repeat_type"] == -1) {
            Application_Model_Show::populateNonRepeatingShow($p_showRow, $p_dateTime);
        }
        else if($p_showRow["repeat_type"] == 0) {
            Application_Model_Show::populateRepeatingShow($p_showRow, $p_dateTime, 'P7D');
        }
        else if($p_showRow["repeat_type"] == 1) {
            Application_Model_Show::populateRepeatingShow($p_showRow, $p_dateTime, 'P14D');
        }
        else if($p_showRow["repeat_type"] == 2) {
            Application_Model_Show::populateRepeatingShow($p_showRow, $p_dateTime, 'P1M');
        }
        Application_Model_RabbitMq::PushSchedule();
    }

    /**
     * Creates a single show instance. If the show is recorded, it may have multiple
     * rebroadcast dates, and so this function will create those as well.
     *
     * @param array $p_showRow
     *        A row from cc_show_days table
     * @param DateTime $p_dateTime
     *        DateTime object in UTC time.
     */
    private static function populateNonRepeatingShow($p_showRow, $p_dateTime)
    {
        global $CC_DBC;

        $show_id = $p_showRow["show_id"];
        $first_show = $p_showRow["first_show"]; //non-UTC
        $start_time = $p_showRow["start_time"]; //non-UTC
        $duration = $p_showRow["duration"];
        $day = $p_showRow["day"];
        $record = $p_showRow["record"];
        $timezone = $p_showRow["timezone"];

        $start = $first_show." ".$start_time;
        $utcStartDateTime = Application_Model_DateHelper::ConvertToUtcDateTime($start, $timezone);

        if ($utcStartDateTime->getTimestamp() < $p_dateTime->getTimestamp()) {

            $utcStart = $utcStartDateTime->format("Y-m-d H:i:s");
            $sql = "SELECT timestamp '{$utcStart}' + interval '{$duration}'";
            $utcEndDateTime = new DateTime($CC_DBC->GetOne($sql), new DateTimeZone("UTC"));

            $date = new Application_Model_DateHelper();
            $currentUtcTimestamp = $date->getUtcTimestamp();

            $show = new Application_Model_Show($show_id);
            if ($show->hasInstance()){
                $ccShowInstance = $show->getInstance();
                $newInstance = false;
            } else {
                $ccShowInstance = new CcShowInstances();
                $newInstance = true;
            }

            if ($newInstance || $ccShowInstance->getDbStarts() > $currentUtcTimestamp){
                $ccShowInstance->setDbShowId($show_id);
                $ccShowInstance->setDbStarts($utcStartDateTime);
                $ccShowInstance->setDbEnds($utcEndDateTime);
                $ccShowInstance->setDbRecord($record);
                $ccShowInstance->save();
            }

            $show_instance_id = $ccShowInstance->getDbId();
            $showInstance = new Application_Model_ShowInstance($show_instance_id);

            if (!$newInstance){
                $showInstance->correctScheduleStartTimes();
            }

            $sql = "SELECT * FROM cc_show_rebroadcast WHERE show_id={$show_id}";
            $rebroadcasts = $CC_DBC->GetAll($sql);

            Logging::log('$start time of non repeating record '.$start);

            self::createRebroadcastInstances($rebroadcasts, $currentUtcTimestamp, $show_id, $show_instance_id, $start, $duration, $timezone);
        }
    }

    /**
     * Creates a 1 or more than 1 show instances (user has stated this show repeats). If the show
     * is recorded, it may have multiple rebroadcast dates, and so this function will create
     * those as well.
     *
     * @param array $p_showRow
     *        A row from cc_show_days table
     * @param DateTime $p_dateTime
     *        DateTime object in UTC time. "shows_populated_until" date YY-mm-dd in cc_pref
     * @param string $p_interval
     *        Period of time between repeating shows (in php DateInterval notation 'P7D')
     */
    private static function populateRepeatingShow($p_showRow, $p_dateTime, $p_interval)
    {
        global $CC_DBC;

        $show_id = $p_showRow["show_id"];
        $next_pop_date = $p_showRow["next_pop_date"];
        $first_show = $p_showRow["first_show"]; //non-UTC
        $last_show = $p_showRow["last_show"]; //non-UTC
        $start_time = $p_showRow["start_time"]; //non-UTC
        $duration = $p_showRow["duration"];
        $day = $p_showRow["day"];
        $record = $p_showRow["record"];
        $timezone = $p_showRow["timezone"];

        $date = new Application_Model_DateHelper();
        $currentUtcTimestamp = $date->getUtcTimestamp();

        if(isset($next_pop_date)) {
            $start = $next_pop_date." ".$start_time;
        } else {
            $start = $first_show." ".$start_time;
        }

        $utcStartDateTime = Application_Model_DateHelper::ConvertToUtcDateTime($start, $timezone);
        //convert $last_show into a UTC DateTime object, or null if there is no last show.
        $utcLastShowDateTime = $last_show ? Application_Model_DateHelper::ConvertToUtcDateTime($last_show, $timezone) : null;

        $sql = "SELECT * FROM cc_show_rebroadcast WHERE show_id={$show_id}";
        $rebroadcasts = $CC_DBC->GetAll($sql);

        $show = new Application_Model_Show($show_id);

        while($utcStartDateTime->getTimestamp() <= $p_dateTime->getTimestamp()
                && (is_null($utcLastShowDateTime) || $utcStartDateTime->getTimestamp() < $utcLastShowDateTime->getTimestamp())){

            $utcStart = $utcStartDateTime->format("Y-m-d H:i:s");
            $sql = "SELECT timestamp '{$utcStart}' + interval '{$duration}'";
            $utcEndDateTime = new DateTime($CC_DBC->GetOne($sql), new DateTimeZone("UTC"));

            if ($show->hasInstanceOnDate($utcStartDateTime)){
                $ccShowInstance = $show->getInstanceOnDate($utcStartDateTime);
                $newInstance = false;
            } else {
                $ccShowInstance = new CcShowInstances();
                $newInstance = true;
            }

            /* When editing the start/end time of a repeating show, we don't want to
             * change shows that started in the past. So check the start time.
             */
            if ($newInstance || $ccShowInstance->getDbStarts() > $currentUtcTimestamp){
                $ccShowInstance->setDbShowId($show_id);
                $ccShowInstance->setDbStarts($utcStartDateTime);
                $ccShowInstance->setDbEnds($utcEndDateTime);
                $ccShowInstance->setDbRecord($record);
                $ccShowInstance->save();
            }


            $show_instance_id = $ccShowInstance->getDbId();
            $showInstance = new Application_Model_ShowInstance($show_instance_id);

            /* If we are updating a show then make sure that the scheduled content within
             * the show is updated to the correct time. */
            if (!$newInstance){
                $showInstance->correctScheduleStartTimes();
            }

            self::createRebroadcastInstances($rebroadcasts, $currentUtcTimestamp, $show_id, $show_instance_id, $start, $duration, $timezone);

            $dt = new DateTime($start, new DateTimeZone($timezone));
            $dt->add(new DateInterval($p_interval));
            $start = $dt->format("Y-m-d H:i:s");

            $dt->setTimezone(new DateTimeZone('UTC'));
            $utcStartDateTime = $dt;

        }

        Application_Model_Show::setNextPop($start, $show_id, $day);
    }

    /*  Create rebroadcast instances for a created show marked for recording
     *
     *  @param $p_rebroadcasts rows gotten from the db table cc_show_rebroadcasts, tells airtime when to schedule the rebroadcasts.
     *  @param $p_currentUtcTimestamp a timestring in format "Y-m-d H:i:s", current UTC time.
     *  @param $p_showId int of the show it belongs to (from cc_show)
     *  @param $p_showInstanceId the instance id of the created recorded show instance
     *      (from cc_show_instances), used to associate rebroadcasts to this show.
     *  @param $p_startTime a timestring in format "Y-m-d H:i:s" in the timezone, not UTC of the rebroadcasts' parent recorded show.
     *  @param $p_duration duration of the show in format 1:0
     *  @param $p_timezone string of user's timezone "Europe/Prague"
     *
     */
    private static function createRebroadcastInstances($p_rebroadcasts, $p_currentUtcTimestamp, $p_showId, $p_showInstanceId, $p_startTime, $p_duration, $p_timezone=null){
        global $CC_DBC;

        Logging::log('Count of rebroadcasts '. count($p_rebroadcasts));

        foreach($p_rebroadcasts as $rebroadcast) {

            //use only the date part of the show start time stamp for the offsets to work properly.
            $sql = "SELECT date '{$p_startTime}' + interval '{$rebroadcast["day_offset"]}' + interval '{$rebroadcast["start_time"]}'";
            $rebroadcast_start_time = $CC_DBC->GetOne($sql);
            Logging::log('rebroadcast start '.$rebroadcast_start_time);

            $sql = "SELECT timestamp '{$rebroadcast_start_time}' + interval '{$p_duration}'";
            $rebroadcast_end_time = $CC_DBC->GetOne($sql);
            Logging::log('rebroadcast end '.$rebroadcast_end_time);

            //convert to UTC, after we have used the defined offsets to calculate rebroadcasts.
            $utc_rebroadcast_start_time = Application_Model_DateHelper::ConvertToUtcDateTime($rebroadcast_start_time, $p_timezone);
            $utc_rebroadcast_end_time = Application_Model_DateHelper::ConvertToUtcDateTime($rebroadcast_end_time, $p_timezone);

            Logging::log('UTC rebroadcast start '.$utc_rebroadcast_start_time->format("Y-m-d H:i:s"));
            Logging::log('UTC rebroadcast end '.$utc_rebroadcast_end_time->format("Y-m-d H:i:s"));
            Logging::log('Current UTC timestamp '.$p_currentUtcTimestamp);

            if ($utc_rebroadcast_start_time->format("Y-m-d H:i:s") > $p_currentUtcTimestamp){
                Logging::log('Creating rebroadcast show starting at UTC '.$utc_rebroadcast_start_time->format("Y-m-d H:i:s"));

                $newRebroadcastInstance = new CcShowInstances();
                $newRebroadcastInstance->setDbShowId($p_showId);
                $newRebroadcastInstance->setDbStarts($utc_rebroadcast_start_time->format("Y-m-d H:i:s"));
                $newRebroadcastInstance->setDbEnds($utc_rebroadcast_end_time->format("Y-m-d H:i:s"));
                $newRebroadcastInstance->setDbRecord(0);
                $newRebroadcastInstance->setDbRebroadcast(1);
                $newRebroadcastInstance->setDbOriginalShow($p_showInstanceId);
                $newRebroadcastInstance->save();
            }
        }
    }

    /**
     * Get all the show instances in the given time range (inclusive).
     *
     * @param DateTime $start_timestamp
     *      In UTC time.
     * @param DateTime $end_timestamp
     *      In UTC time.
     * @param unknown_type $excludeInstance
     * @param boolean $onlyRecord
     * @return array
     */
    public static function getShows($start_timestamp, $end_timestamp, $excludeInstance=NULL, $onlyRecord=FALSE)
    {
        global $CC_DBC;

        $showsPopUntil = Application_Model_Preference::GetShowsPopulatedUntil();

        //if application is requesting shows past our previous populated until date, generate shows up until this point.
        if (is_null($showsPopUntil) || $showsPopUntil->getTimestamp() < $end_timestamp->getTimestamp()) {
            Application_Model_Show::populateAllShowsInRange($showsPopUntil, $end_timestamp);
            Application_Model_Preference::SetShowsPopulatedUntil($end_timestamp);
        }

        $sql = "SELECT starts, ends, record, rebroadcast, instance_id, show_id, name, description,
                color, background_color, file_id, deleted_instance, cc_show_instances.id AS instance_id
            FROM cc_show_instances
            LEFT JOIN cc_show ON cc_show.id = cc_show_instances.show_id";

        //only want shows that are starting at the time or later.
        $start_string = $start_timestamp->format("Y-m-d H:i:s");
        $end_string = $end_timestamp->format("Y-m-d H:i:s");
        if ($onlyRecord) {

            $sql = $sql." WHERE (starts >= '{$start_string}' AND starts < timestamp '{$end_string}')";
            $sql = $sql." AND (record = 1)";
        }
        else {

            $sql = $sql." WHERE ((starts >= '{$start_string}' AND starts < '{$end_string}')
                OR (ends > '{$start_string}' AND ends <= '{$end_string}')
                OR (starts <= '{$start_string}' AND ends >= '{$end_string}'))";
        }


        if (isset($excludeInstance)) {
            foreach($excludeInstance as $instance) {
                $sql_exclude[] = "cc_show_instances.id != {$instance}";
            }

            $exclude = join(" OR ", $sql_exclude);

            $sql = $sql." AND ({$exclude})";
        }

        return $CC_DBC->GetAll($sql);
    }

    private static function setNextPop($next_date, $show_id, $day)
    {
        $nextInfo = explode(" ", $next_date);

        $repeatInfo = CcShowDaysQuery::create()
            ->filterByDbShowId($show_id)
            ->filterByDbDay($day)
            ->findOne();

        $repeatInfo->setDbNextPopDate($nextInfo[0])
            ->save();
    }

    /**
     * Generate all the repeating shows in the given range.
     *
     * @param DateTime $p_startTimestamp
     *         In UTC format.
     * @param DateTime $p_endTimestamp
     *         In UTC format.
     */
    public static function populateAllShowsInRange($p_startTimestamp, $p_endTimestamp)
    {
        global $CC_DBC;

        $endTimeString = $p_endTimestamp->format("Y-m-d H:i:s");
        if (!is_null($p_startTimestamp)) {
            $startTimeString = $p_startTimestamp->format("Y-m-d H:i:s");
            $sql = "SELECT * FROM cc_show_days
                    WHERE last_show IS NULL
                    OR first_show < '{$endTimeString}' AND last_show > '{$startTimeString}'";
        }
        else {
            $today_timestamp = new DateTime("now", new DateTimeZone("UTC"));
            $today_timestamp_string = $today_timestamp->format("Y-m-d H:i:s");
            $sql = "SELECT * FROM cc_show_days
                    WHERE last_show IS NULL
                    OR first_show < '{$endTimeString}' AND last_show > '{$today_timestamp_string}'";
        }

        $res = $CC_DBC->GetAll($sql);

        foreach ($res as $row) {
            Application_Model_Show::populateShow($row, $p_endTimestamp);
        }
    }

    /**
     *
     * @param DateTime $start
     *          -in UTC time
     * @param DateTime $end
     *          -in UTC time
     * @param boolean $editable
     */
    public static function getFullCalendarEvents($start, $end, $editable=false)
    {
        $events = array();

        $interval = $start->diff($end);
        $days =  $interval->format('%a');

        $shows = Application_Model_Show::getShows($start, $end);

        $today_timestamp = Application_Model_DateHelper::ConvertToUtcDateTime(date("Y-m-d H:i:s"))->format("Y-m-d H:i:s");

        foreach ($shows as $show) {

            if ($show["deleted_instance"] != "t"){
                $options = array();

                //only bother calculating percent for week or day view.
                if(intval($days) <= 7) {
                    $show_instance = new Application_Model_ShowInstance($show["instance_id"]);
                    $options["percent"] =  $show_instance->getPercentScheduled();
                }

                if ($editable && (strtotime($today_timestamp) < strtotime($show["starts"]))) {
                    $options["editable"] = true;
                    $events[] = Application_Model_Show::makeFullCalendarEvent($show, $options);
                } else {
                    $events[] = Application_Model_Show::makeFullCalendarEvent($show, $options);
                }
            }
        }

        return $events;
    }

    private static function makeFullCalendarEvent($show, $options=array())
    {
        $event = array();

        if($show["rebroadcast"]) {
            $event["disableResizing"] = true;
        }

        $startDateTime = new DateTime($show["starts"], new DateTimeZone("UTC"));
        $startDateTime->setTimezone(new DateTimeZone(date_default_timezone_get()));

        $endDateTime = new DateTime($show["ends"], new DateTimeZone("UTC"));
        $endDateTime->setTimezone(new DateTimeZone(date_default_timezone_get()));

        $event["id"] = $show["instance_id"];
        $event["title"] = $show["name"];
        $event["start"] = $startDateTime->format("Y-m-d H:i:s");
        $event["end"] = $endDateTime->format("Y-m-d H:i:s");
        $event["allDay"] = false;
        $event["description"] = $show["description"];
        $event["showId"] = $show["show_id"];
        $event["record"] = intval($show["record"]);
        $event["deleted_instance"] = $show["deleted_instance"];
        $event["rebroadcast"] = intval($show["rebroadcast"]);

        // get soundcloud_id
        if(!is_null($show["file_id"])){
            $file = Application_Model_StoredFile::Recall($show["file_id"]);
            $soundcloud_id = $file->getSoundCloudId();
        }else{
            $soundcloud_id = null;
        }
        $event["soundcloud_id"] = (is_null($soundcloud_id) ? -1 : $soundcloud_id);

        //event colouring
        if($show["color"] != "") {
            $event["textColor"] = "#".$show["color"];
        }
        if($show["background_color"] != "") {
            $event["color"] = "#".$show["background_color"];
        }

        foreach($options as $key=>$value) {
            $event[$key] = $value;
        }

        return $event;
    }

    public function setShowFirstShow($s_date){
        $showDay = CcShowDaysQuery::create()
        ->filterByDbShowId($this->_showId)
        ->findOne();

        $showDay->setDbFirstShow($s_date)
        ->save();
    }

    public function setShowLastShow($e_date){
        $showDay = CcShowDaysQuery::create()
        ->filterByDbShowId($this->_showId)
        ->findOne();

        $showDay->setDbLastShow($e_date)
        ->save();
    }

    /**
     * Given local current time $timeNow, returns the show being played right now.
     *
     * @param type $timeNow     local current time
     * @return type             show being played right now
     */
    public static function GetCurrentShow($timeNow)
    {
        global $CC_CONFIG, $CC_DBC;

        // Need this in the query below, so that we are NOT comparing UTC
        // timestamps si.starts/si.ends with local timestamps $timeNow
        $timezoneInterval = Application_Model_Show::GetTimezoneIntervalString(true);

        $sql = "SELECT si.starts as start_timestamp, si.ends as end_timestamp, s.name, s.id, si.id as instance_id, si.record, s.url"
        ." FROM $CC_CONFIG[showInstances] si, $CC_CONFIG[showTable] s"
        ." WHERE si.show_id = s.id"
        ." AND si.starts <= TIMESTAMP '$timeNow' + $timezoneInterval"
        ." AND si.ends > TIMESTAMP '$timeNow' + $timezoneInterval";

        // Convert back to local timezone
        $rows = $CC_DBC->GetAll($sql);
        Application_Model_Show::ConvertToLocalTimeZone($rows, array("starts", "ends", "start_timestamp", "end_timestamp"));

        return $rows;
    }

    /**
     * Given local current time $timeNow, returns the next $limit number of
     * shows within 2 days if $timeEnd is not given; Otherwise, returns the
     * next $limit number of shows from $timeNow to $timeEnd, both in local time.
     *
     * @param type $timeNow     local current time
     * @param type $limit       number of shows to return
     * @param type $timeEnd     optional: interval end time
     * @return type             next $limit number of shows
     */
    public static function GetNextShows($timeNow, $limit, $timeEnd = "")
    {
        global $CC_CONFIG, $CC_DBC;

        // Need this in the query below, so that we are NOT comparing UTC
        // timestamps si.starts with local timestamps $timeNow
        $timezoneInterval = Application_Model_Show::GetTimezoneIntervalString(true);

        // defaults to retrieving shows from next 2 days if no end time has
        // been specified
        if($timeEnd == "") {
            $timeEnd = "'$timeNow' + INTERVAL '2 days' + $timezoneInterval";
        } else {
            $timeEnd = "'$timeEnd' + $timezoneInterval";
        }

        $sql = "SELECT *, si.starts as start_timestamp, si.ends as end_timestamp FROM "
        ." $CC_CONFIG[showInstances] si, $CC_CONFIG[showTable] s"
        ." WHERE si.show_id = s.id"
        ." AND si.starts >= TIMESTAMP '$timeNow' + $timezoneInterval"
        ." AND si.starts < TIMESTAMP $timeEnd"
        ." ORDER BY si.starts"
        ." LIMIT $limit";

        // Convert timestamps to local timezone
        $rows = $CC_DBC->GetAll($sql);
        Application_Model_Show::ConvertToLocalTimeZone($rows, array("starts", "ends", "start_timestamp", "end_timestamp"));

        return $rows;
    }

    /**
     * Given a day of the week variable $day, based in local time, returns the
     * shows being played on this day of the week we're in right now.
     *
     * @param type $day     day of the week
     * @return type         shows being played on this day
     */
    public static function GetShowsByDayOfWeek($day){
        //DOW FROM TIMESTAMP
        //The day of the week (0 - 6; Sunday is 0) (for timestamp values only)

        //SELECT EXTRACT(DOW FROM TIMESTAMP '2001-02-16 20:38:40');
        //Result: 5

        global $CC_CONFIG, $CC_DBC;

        // Need this in the query below, so that we are NOT extracting DOW and WEEK
        // information from UTC timestamps si.starts, and comparing with a local
        // timezone based variable $day and localtimestamp
        $timezoneInterval = Application_Model_Show::GetTimezoneIntervalString();

        $sql = "SELECT"
        ." si.starts as show_starts,"
        ." si.ends as show_ends,"
        ." s.name as show_name,"
        ." s.url as url"
        ." FROM $CC_CONFIG[showInstances] si"
        ." LEFT JOIN $CC_CONFIG[showTable] s"
        ." ON si.show_id = s.id"
        ." WHERE EXTRACT(DOW FROM si.starts + $timezoneInterval) = $day"
        ." AND EXTRACT(WEEK FROM si.starts + $timezoneInterval) = EXTRACT(WEEK FROM localtimestamp)"
        ." ORDER BY si.starts";

        // Convert result timestamps to local timezone
        $rows = $CC_DBC->GetAll($sql);
        Application_Model_Show::ConvertToLocalTimeZone($rows, array("show_starts", "show_ends"));

        return $rows;
    }

    /**
     * Convert the columns given in the array $columnsToConvert in the
     * database result $rows to local timezone.
     *
     * @param type $rows                arrays of arrays containing database query result
     * @param type $columnsToConvert    array of column names to convert
     */
    public static function ConvertToLocalTimeZone(&$rows, $columnsToConvert) {
        $timezone = date_default_timezone_get();

        foreach($rows as &$row) {
            foreach($columnsToConvert as $column) {
                $row[$column] = Application_Model_DateHelper::ConvertToLocalDateTimeString($row[$column]);
            }
        }
    }

    /**
     * Returns the timezone difference as an INTERVAL string that can be used
     * by SQL queries.
     *
     * E.g., if local timezone is -4:30, this function returns:
     * INTERVAL '-4 hours -30 minutes'
     *
     * Note that if $fromLocalToUtc is true, then it returns:
     * INTERVAL '4 hours 30 minutes'
     *
     * @param type $fromLocalToUtc  true if we're converting from local to UTC
     */
    public static function GetTimeZoneIntervalString($fromLocalToUtc = false) {
        $date = new Application_Model_DateHelper;
        $timezoneHour = $date->getLocalOffsetHour();
        $timezoneMin = $date->getLocalOffsetMinute();

        // negate the hour and min if converting from local to UTC
        if($fromLocalToUtc) {
            $timezoneHour = -$timezoneHour;
            $timezoneMin = -$timezoneMin;
        }

        return "INTERVAL '$timezoneHour hours $timezoneMin minutes'";
    }

    public static function GetMaxLengths() {
        global $CC_CONFIG, $CC_DBC;
        $sql = "SELECT column_name, character_maximum_length FROM information_schema.columns"
        ." WHERE table_name = 'cc_show' AND character_maximum_length > 0";
        $result = $CC_DBC->GetAll($sql);

        // store result into assoc array
        $assocArray = array();
        foreach($result as $row) {
            $assocArray[$row['column_name']] = $row['character_maximum_length'];
        }

        return $assocArray;
    }
}
