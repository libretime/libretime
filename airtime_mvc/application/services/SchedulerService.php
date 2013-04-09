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
    private function findEndTime($instanceStart, $clipLength)
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
     * @param array $scheduleItems (schedule_id and instance_id it belongs to)
     * @param array $mediaItems (file|block|playlist|webstream)
     * @param $adjustSched
     */
    public function scheduleAdd($scheduleItems, $mediaItems, $adjustSched=true)
    {
        $this->con->beginTransaction();

        $filesToInsert = array();

        try {
            $this->validateRequest($scheduleItems);

            /*
             * create array of arrays
             * array of schedule item info
             * (sched_id is the cc_schedule id and is set if an item is being
             *  moved because it is already in cc_schedule)
             * [0] = Array(
             *     id => 1,
             *     cliplength => 00:04:32,
             *     cuein => 00:00:00,
             *     cueout => 00:04:32,
             *     fadein => 00.5,
             *     fadeout => 00.5,
             *     sched_id => ,
             *     type => 0)
             * [1] = Array(
             *     id => 2,
             *     cliplength => 00:05:07,
             *     cuein => 00:00:00,
             *     cueout => 00:05:07,
             *     fadein => 00.5,
             *     fadeout => 00.5,
             *     sched_id => ,
             *     type => 0)
             */
            foreach ($mediaItems as $media) {
                $filesToInsert = array_merge($filesToInsert, $this->retrieveMediaFiles($media["id"], $media["type"]));
            }

            //$this->insertAfter($scheduleItems, $filesToInsert, $adjustSched);
            $ccStamp = $this->prepareStamp($scheduleItems, $mediaItems, $adjustSched);

            $this->insertStamp($ccStamp);

            //keep track of which shows had their schedule change
            //dont forget about the linked shows

            $this->con->commit();

            Application_Model_RabbitMq::PushSchedule();
        } catch (Exception $e) {
            $this->con->rollback();
            throw $e;
        }
    }

    private function setCcStamp($ccStamp, $instanceId)
    {
        $ccShowInstance = CcShowInstancesQuery::create()->findPk($instanceId);
        $ccShow = $ccShowInstance->getCcShow();
        if ($ccShow->isLinked()) {
            $ccStamp
                ->setDbLinked(true)
                ->setDbShowId($ccShow->getDbId())
                ->save();
        } else {
            $ccStamp
                ->setDbLinked(false)
                ->setDbInstanceId($ccShowInstance->getDbId())
                ->save();
        }
    }

    /**
     * 
     * Enter description here ...
     * @param $scheduleItems
     *     cc_schedule items, where the items get inserted after
     * @param $filesToInsert
     *     array of schedule item info, what gets inserted into cc_schedule
     * @param $adjustSched
     */
    private function prepareStamp($scheduleItems, $itemsToInsert, $adjustSched = true)
    {
        try {

            foreach ($scheduleItems as $schedule) {
                $id = intval($schedule["id"]);

                if ($id == 0) {
                    //if we get here, we know the show is empty and therefore
                    //need to create a new stamp
                    $pos = 0;
                    $ccStamp = new CcStamp();
                    $this->setCcStamp($ccStamp, $schedule["instance"]);
                } else {
                    $ccStamp = $this->getStamp($id);
                    //get the cc_stamp_contents item of the scheduleItem($schedule)
                    //this is where we are inserting after so we have to start the
                    //position counter after it
                    $ccStampContent = $this->getCurrentStampItem($id);
                    $pos = $ccStampContent->getDbPosition() + 1;

                    //clear the positions of stamp items after the current
                    //item so we know we have to reassign their positions
                    //after inserting the new items
                    CcStampContentsQuery::create()
                        ->filterByDbStampId($ccStamp->getDbId())
                        ->filterByDbPosition($pos, Criteria::GREATER_EQUAL)
                        ->setDbPosition(null)
                        ->save();
                }

                $stampId = $ccStamp->getDbId();
                foreach ($itemsToInsert as $item) {
                    $ccStampContent = new CcStampContents();
                    $ccStampContent
                        ->setDbStampId($stampId)
                        ->setDbPosition($pos)
                        ->save();
                    switch ($item["type"]) {
                        case "playlist":
                            $ccStampContent->setDbPlaylistId($item["id"])->save();
                            break;
                        case "audioclip":
                            $ccStampContent->setDbFileId($item["id"])->save();

                            //update is_scheduled flag in cc_files
                            $ccFile = CcFilesQuery::create()->findPk($item['id']);
                            $ccFile->setDbIsScheduled(true)->save();
                            break;
                        case "block":
                            $ccStampContent->setDbBlockId($item["id"])->save();
                            break;
                        case "stream":
                            $ccStampContent->setDbStreamId($item["id"])->save();
                            break;
                    }
                    $pos++;
                }

                //reassign positions
                $ccStampContents = CcStampContentsQuery::create()
                    ->filterByDbStampId($stampId)
                    ->filterByDbPosition(null)
                    ->find();
                foreach ($ccStampContents as $ccStampContent) {
                    $ccStampContent->setDbPosition($pos)->save();
                    $pos++;
                }

                return $ccStamp;
            }
        } catch (Exception $e) {
            Logging::debug($e->getMessage());
            throw $e;
        }
    }

    private function insertStamp($ccStamp)
    {
        //delete cc_schedule entries
        //CcScheduleQuery::create()->filterByDbStampId($ccStamp->getDbId())->delete();
    }

    private function getStamp($scheduleId)
    {
        $ccSchedule = CcScheduleQuery::create()->findPk($scheduleId);
        return CcStamp::create()->findPk($ccSchedule->getDbStampId());
    }

    private function getCurrentStampItem($scheduleId)
    {
        $ccSchedule = CcScheduleQuery::create()->findPk($scheduleId);
        return CcStampContents::create()->findPk($ccSchedule->getDbStampContentsId());
    }

    /**
     * 
     * Enter description here ...
     * @param array $items (schedule_id and instance_id it belongs to)
     */
    private function validateRequest($items)
    {
        $nowEpoch = floatval($this->nowDT->format("U.u"));

        for ($i = 0; $i < count($items); $i++) {
            $id = $items[$i]["id"];

            //could be added to the beginning of a show, which sends id = 0;
            if ($id > 0) {
                //schedule_id of where we are inserting after?
                $schedInfo[$id] = $items[$i]["instance"];
            }

            //what is timestamp for?
            //format is instance_id => timestamp
            $instanceInfo[$items[$i]["instance"]] = $items[$i]["timestamp"];
        }

        if (count($instanceInfo) === 0) {
            throw new Exception("Invalid Request.");
        }

        $schedIds = array();
        if (isset($schedInfo)) {
            $schedIds = array_keys($schedInfo);
        }
        $schedItems = CcScheduleQuery::create()->findPKs($schedIds, $this->con);
        $instanceIds = array_keys($instanceInfo);
        $showInstances = CcShowInstancesQuery::create()->findPKs($instanceIds, $this->con);

        //an item has been deleted
        if (count($schedIds) !== count($schedItems)) {
            throw new OutDatedScheduleException(_("The schedule you're viewing is out of date! (sched mismatch)"));
        }

        //a show has been deleted
        if (count($instanceIds) !== count($showInstances)) {
            throw new OutDatedScheduleException(_("The schedule you're viewing is out of date! (instance mismatch)"));
        }

        foreach ($schedItems as $schedItem) {
            $id = $schedItem->getDbId();
            $instance = $schedItem->getCcShowInstances($this->con);

            if (intval($schedInfo[$id]) !== $instance->getDbId()) {
                throw new OutDatedScheduleException(_("The schedule you're viewing is out of date!"));
            }
        }

        foreach ($showInstances as $instance) {

            $id = $instance->getDbId();
            $show = $instance->getCcShow($this->con);

            if ($this->checkUserPermissions && $this->user->canSchedule($show->getDbId()) === false) {
                throw new Exception(sprintf(_("You are not allowed to schedule show %s."), $show->getDbName()));
            }
            
            if ($instance->getDbRecord()) {
                throw new Exception(_("You cannot add files to recording shows."));
            }

            $showEndEpoch = floatval($instance->getDbEnds("U.u"));

            if ($showEndEpoch < $nowEpoch) {
                throw new OutDatedScheduleException(sprintf(_("The show %s is over and cannot be scheduled."), $show->getDbName()));
            }

            $ts = intval($instanceInfo[$id]);
            $lastSchedTs = intval($instance->getDbLastScheduled("U")) ? : 0;
            if ($ts < $lastSchedTs) {
                Logging::info("ts {$ts} last sched {$lastSchedTs}");
                throw new OutDatedScheduleException(sprintf(_("The show %s has been previously updated!"), $show->getDbName()));
            }
        }
    }
}