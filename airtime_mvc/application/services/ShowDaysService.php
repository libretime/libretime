<?php

class Application_Service_ShowDaysService
{
    private $showId;

    public function __construct($id)
    {
        $this->showId = $id;
    }
    /**
     * 
     * Sets the fields for a cc_show_days table row
     * @param $showData
     * @param $showId
     * @param $userId
     * @param $repeatType
     * @param $isRecorded
     */
    public function setShowDays($showData, $userId, $repeatType, $isRecorded)
    {
        $startDateTime = new DateTime($showData['add_show_start_date']." ".$showData['add_show_start_time']);

        if ($showData['add_show_no_end']) {
            $endDate = NULL;
        } elseif ($showData['add_show_repeats']) {
            $endDateTime = new DateTime($showData['add_show_end_date']);
            $endDateTime->add(new DateInterval("P1D"));
            $endDate = $endDateTime->format("Y-m-d");
        } else {
            $endDateTime = new DateTime($showData['add_show_start_date']);
            $endDateTime->add(new DateInterval("P1D"));
            $endDate = $endDateTime->format("Y-m-d");
        }

        /* What we are doing here is checking if the show repeats or if
         * any repeating days have been checked. If not, then by default
         * the "selected" DOW is the initial day.
         * DOW in local time.
         */
        $startDow = date("w", $startDateTime->getTimestamp());
        if (!$showData['add_show_repeats']) {
            $showData['add_show_day_check'] = array($startDow);
        } elseif ($showData['add_show_repeats'] && $showData['add_show_day_check'] == "") {
            $showData['add_show_day_check'] = array($startDow);
        }

        // Don't set day for monthly repeat type, it's invalid
        if ($showData['add_show_repeats'] && $showData['add_show_repeat_type'] == 2) {
            $showDay = new CcShowDays();
            $showDay->setDbFirstShow($startDateTime->format("Y-m-d"));
            $showDay->setDbLastShow($endDate);
            $showDay->setDbStartTime($startDateTime->format("H:i:s"));
            $showDay->setDbTimezone(Application_Model_Preference::GetUserTimezone($userId));
            $showDay->setDbDuration($showData['add_show_duration']);
            $showDay->setDbRepeatType($repeatType);
            $showDay->setDbShowId($this->showId);
            $showDay->setDbRecord($isRecorded);
            $showDay->save();
        } else {
            foreach ($showData['add_show_day_check'] as $day) {
                $daysAdd=0;
                $startDateTimeClone = clone $startDateTime;
                if ($startDow !== $day) {
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
                    $showDay->setDbTimezone(Application_Model_Preference::GetUserTimezone($userId));
                    $showDay->setDbDuration($showData['add_show_duration']);
                    $showDay->setDbDay($day);
                    $showDay->setDbRepeatType($repeatType);
                    $showDay->setDbShowId($this->showId);
                    $showDay->setDbRecord($isRecorded);
                    $showDay->save();
                }
            }
        }
    }

    /**
     * 
     * Gets the cc_show_days entries for a specific show
     * 
     * @return array of ccShowDays objects
     */
    public function getShowDays()
    {
        $sql = "SELECT * FROM cc_show_days WHERE show_id = :show_id";

        return Application_Common_Database::prepareAndExecute(
            $sql, array(":show_id" => $this->showId), 'all');
    }

    public function getStartDateAndTime()
    {
        //CcShowDays object
        $showDay = $this->getCurrentShowDay();

        $dt = new DateTime($showDay->getDbFirstShow()." ".$showDay->getDbStartTime(),
            new DateTimeZone($showDay->getDbTimezone()));
        $dt->setTimezone(new DateTimeZone("UTC"));

        return $dt->format("Y-m-d H:i");
    }

    /**
     * 
     * Returns a CcShowDays object of the show that
     * is currently being edited.
     */
    public function getCurrentShowDay()
    {
        return CcShowDaysQuery::create()->filterByDbShowId($this->showId)
            ->findOne();
    }
}