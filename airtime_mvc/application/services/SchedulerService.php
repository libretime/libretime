<?php
class Application_Service_SchedulerService
{
    private $con;
    private $fileInfo = array(
            "id" => "",
            "cliplength" => "",
            "cuein" => "00:00:00",
            "cueout" => "00:00:00",
            "fadein" => "00:00:00",
            "fadeout" => "00:00:00",
            "sched_id" => null,
            "type" => 0 //default type of '0' to represent files. type '1' represents a webstream
        );

    private $epochNow;
    private $nowDT;
    private $currentUser;
    private $checkUserPermissions = true;

    public function __construct()
    {
        $this->con = Propel::getConnection(CcSchedulePeer::DATABASE_NAME);

        //subtracting one because sometimes when we cancel a track, we set its end time
        //to epochNow and then send the new schedule to pypo. Sometimes the currently cancelled
        //track can still be included in the new schedule because it may have a few ms left to play.
        //subtracting 1 second from epochNow resolves this issue.
        $this->epochNow = microtime(true)-1;
        $this->nowDT = DateTime::createFromFormat("U.u", $this->epochNow, new DateTimeZone("UTC"));

        if ($this->nowDT === false) {
            // DateTime::createFromFormat does not support millisecond string formatting in PHP 5.3.2 (Ubuntu 10.04).
            // In PHP 5.3.3 (Ubuntu 10.10), this has been fixed.
            $this->nowDT = DateTime::createFromFormat("U", time(), new DateTimeZone("UTC"));
        }

        $user_service = new Application_Service_UserService();
        $this->currentUser = $user_service->getCurrentUser();
    }

    /**
     * 
     * Enter description here ...
     * @param array $instanceIds
     */
    public static function updateScheduleStartTime($instanceIds, $diff=null, $newStart=null)
    {
        $con = Propel::getConnection();
        if (count($instanceIds) > 0) {
            $showIdList = implode(",", $instanceIds);

            if (is_null($diff)) {
                $ccSchedule = CcScheduleQuery::create()
                    ->filterByDbInstanceId($instanceIds, Criteria::IN)
                    ->orderByDbStarts()
                    ->limit(1)
                    ->findOne();

                if (!is_null($ccSchedule)) {
                    $scheduleStartsEpoch = strtotime($ccSchedule->getDbStarts());
                    $showStartsEpoch     = strtotime($newStart->format("Y-m-d H:i:s"));

                    $diff = $showStartsEpoch - $scheduleStartsEpoch;
                }
            }

            $ccSchedules = CcScheduleQuery::create()
                ->filterByDbInstanceId($instanceIds, Criteria::IN)
                ->find();
            foreach ($ccSchedules as $ccSchedule) {
                $ccSchedule
                    ->setDbStarts($ccSchedule->getDbStarts() + $diff)
                    ->setDbEnds($ccSchedule->getDbEnds() + $diff)
                    ->save();
            }
        }
    }

    /**
     * 
     * Removes any time gaps in shows
     * 
     * @param array $schedIds schedule ids to exclude
     */
    public function removeGaps($showId, $schedIds=null)
    {
        $ccShowInstances = CcShowInstancesQuery::create()->filterByDbShowId($showId)->find();

        foreach ($ccShowInstances as $instance) {
            Logging::info("Removing gaps from show instance #".$instance->getDbId());
            //DateTime object
            $itemStart = $instance->getDbStarts(null);

            $ccScheduleItems = CcScheduleQuery::create()
                ->filterByDbInstanceId($instance->getDbId())
                ->filterByDbId($schedIds, Criteria::NOT_IN)
                ->orderByDbStarts()
                ->find();

            foreach ($ccScheduleItems as $ccSchedule) {
                //DateTime object
                $itemEnd = $this->findEndTime($itemStart, $ccSchedule->getDbClipLength());

                $ccSchedule->setDbStarts($itemStart)
                    ->setDbEnds($itemEnd);

                $itemStart = $itemEnd;
            }
            $ccScheduleItems->save();
        }
    }

    /**
     * 
     * Enter description here ...
     * @param DateTime $instanceStart
     * @param string $clipLength
     */
    private static function findEndTime($instanceStart, $clipLength)
    {
        $startEpoch = $instanceStart->format("U.u");
        $durationSeconds = Application_Common_DateHelper::playlistTimeToSeconds($clipLength);

        //add two float numbers to 6 subsecond precision
        //DateTime::createFromFormat("U.u") will have a problem if there is no decimal in the resulting number.
        $endEpoch = bcadd($startEpoch , (string) $durationSeconds, 6);

        $dt = DateTime::createFromFormat("U.u", $endEpoch, new DateTimeZone("UTC"));

        if ($dt === false) {
            //PHP 5.3.2 problem
            $dt = DateTime::createFromFormat("U", intval($endEpoch), new DateTimeZone("UTC"));
        }

        return $dt;
    }

    /**
     * 
     * Enter description here ...
     * @param $nextStartDT
     * @param $showStamp array of ccSchedule objects
     */
    private static function insertItems($showStamp, $ccShow)
    {
        
    }

    public static function fillLinkedShows($ccShow)
    {
        if ($ccShow->isLinked()) {
            /* First check if any linked instances have content
             * If all instances are empty then we don't need to fill
             * any other instances with content
             */
            $instanceIds = $ccShow->getInstanceIds();
            $ccSchedules = CcScheduleQuery::create()
                ->filterByDbInstanceId($instanceIds, Criteria::IN)
                ->find();
            if (!$ccSchedules->isEmpty()) {
                /* Find the show contents of just one of the instances. It doesn't
                 * matter which instance we use since all the content is the same
                 */
                $ccSchedule = $ccSchedules->getFirst();
                $showStamp = CcScheduleQuery::create()
                    ->filterByDbInstanceId($ccSchedule->getDbInstanceId())
                    ->orderByDbStarts()
                    ->find();
            } else {
                break;
            }

            //need to find out which linked instances are empty
            foreach ($ccShow->getCcShowInstancess() as $ccShowInstance) {
                $ccSchedules = CcScheduleQuery::create()
                    ->filterByDbInstanceId($ccShowInstance->getDbId())
                    ->find();
                if ($ccSchedules->isEmpty()) {
                    $nextStartDT = $ccShowInstance->getDbStarts(null);

                    foreach ($showStamp as $item) {
                        $endTimeDT = self::findEndTime($nextStartDT, $item->getDbClipLength());

                        $ccSchedule = new CcSchedule();
                        $ccSchedule
                            ->setDbStarts($nextStartDT)
                            ->setDbEnds($endTimeDT)
                            ->setDbFileId($item->getDbFileId())
                            ->setDbStreamId($item->getDbStreamId())
                            ->setDbClipLength($item->getDbClipLength())
                            ->setDbFadeIn($item->getDbFadeIn())
                            ->setDbFadeOut($item->getDbFadeOut())
                            ->setDbCuein($item->getDbCueIn())
                            ->setDbCueOut($item->getDbCueOut())
                            ->setDbInstanceId($ccShowInstance->getDbId())
                            ->setDbPosition($item->getDbPosition())
                            ->save();

                        $nextStartDT = $endTimeDT;
                    } //foreach show item
                }
            } //foreach linked instance
        }
    }
}