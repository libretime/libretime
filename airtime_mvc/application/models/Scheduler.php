<?php

class Application_Model_Scheduler {

    private $propSched;
    private $con;

    public function __construct($id = null) {

        if (is_null($id)) {
            $this->propSched = new CcSchedule();
        }
        else {
            $this->propSched = CcScheduleQuery::create()->findPK($id);
        }
    }

    /*
     * @param $id
     * @param $type
     *
     * @return $files
     */
    private static function retrieveMediaFiles($id, $type) {

        $fileInfo = array(
            "id" => "",
            "cliplength" => "",
            "cuein" => "00:00:00",
            "cueout" => "00:00:00",
            "fadein" => "00:00:00",
            "fadeout" => "00:00:00"
        );

        $files = array();

        if ($type === "file") {
            $file = CcFilesQuery::create()->findByPK($id);

            $data = $fileInfo;
            $data["id"] = $id;
            $data["cliplength"] = $file->getDbLength();

            $files[] = $data;
        }
        else if ($type === "playlist") {

        }

        return $files;
    }

    /*
     * @param array $scheduledIds
     * @param array $fileIds
     * @param array $playlistIds
     */
    public static function scheduleAfter($scheduledIds, $mediaIds, $adjustSched = true) {

        $con = Propel::getConnection(CcSchedulePeer::DATABASE_NAME);

        $con->beginTransaction();

        try {

            $schedFiles = array();
            foreach($mediaIds as $id => $type) {
                $schedFiles = array_merge($schedFiles, self::retrieveMediaFiles($id, $type));
            }

            foreach ($scheduledIds as $id) {

                $schedItem = CcScheduleQuery::create()->findByPK($id);

                if ($adjustSched === true) {
                    $followingSchedItems = CcScheduleQuery::create()
                        ->filterByDBStarts($schedItem->getDbStarts("Y-m-d H:i:s.u"), Criteria::GREATER_THAN)
                        ->filterByDbInstanceId($instance)
                        ->orderByDbStarts()
                        ->find();
                }

                $nextItemDT = $schedItem->getDbEnds(null);
                $instance = $schedItem->getDbInstanceId();

                foreach($schedFiles as $file) {

                    $durationDT = DateTime::createFromFormat("Y-m-d H:i:s.u", "1970-01-01 {$file['cliplength']}", new DateTimeZone("UTC"));
                    $endTimeEpoch = $nextItemDT->format("U.u") + $durationDT->format("U.u");
                    $endTimeDT = DateTime::createFromFormat("U.u", $endTimeEpoch, new DateTimeZone("UTC"));

                    $newItem = new CcSchedule();
                    $newItem->setDbStarts($nextItemDT);
                    $newItem->setDbEnds($endTimeDT);
                    $newItem->setDbFileId($file['id']);
                    $newItem->setDbCueIn($file['cuein']);
                    $newItem->setDbCueOut($file['cueout']);
                    $newItem->setDbFadeIn($file['fadein']);
                    $newItem->setDbFadeOut($file['fadeout']);
                    $newItem->setDbInstanceId($instance);
                    $newItem->save($con);

                    $nextItemDT = $endTimeDT;
                }

                if ($adjustSched === true) {

                    //recalculate the start/end times after the inserted items.
                    foreach($followingSchedItems as $followingItem) {

                        $durationDT = DateTime::createFromFormat("Y-m-d H:i:s.u", "1970-01-01 {$file['cliplength']}", new DateTimeZone("UTC"));
                        $endTimeEpoch = $nextItemDT->format("U.u") + $durationDT->format("U.u");
                        $endTimeDT = DateTime::createFromFormat("U.u", $endTimeEpoch, new DateTimeZone("UTC"));

                        $followingItem->setDbStarts($nextItemDT);
                        $followingItem->setDbEnds($endTimeDT);
                        $followingItem->save($con);

                        $nextItemDT = $endTimeDT;
                    }
                }
            }
        }
        catch (Exception $e) {
            $con->rollback();
            throw $e;
        }
    }

    public function removeItems($scheduledIds, $adjustSched = true) {

        $showInstances = array();

        $this->con = Propel::getConnection(CcSchedulePeer::DATABASE_NAME);

        $this->con->beginTransaction();

        try {

            $removedItems = CcScheduleQuery::create()->findPks($scheduledIds);
            $removedItems->delete($this->con);

            if ($adjustSched === true) {
                //get the show instances of the shows we must adjust times for.
                foreach ($removedItems as $item) {

                    $instance = $item->getDBInstanceId();
                    if (!in_array($instance, $showInstances)) {
                        $showInstances[] = $instance;
                    }
                }

                foreach($showInstances as $instance) {
                    self::removeGaps($instance);
                }
            }

            $this->con->commit();
        }
        catch (Exception $e) {
            $this->con->rollback();
            throw $e;
        }
    }

    public function removeGaps($showInstance) {

        Logging::log("removing gaps from show instance #".$showInstance);

        $instance = CcShowInstancesQuery::create()->findPK($showInstance);
        $itemStartDT = $instance->getDbStarts(null);

        $schedule = CcScheduleQuery::create()
            ->filterByDbInstanceId($showInstance)
            ->orderByDbStarts()
            ->find();


        foreach ($schedule as $item) {

            Logging::log("adjusting item #".$item->getDbId());

            if (!$item->isDeleted()) {
                Logging::log("item #".$item->getDbId()." is not deleted");

                $durationDT = new DateTime("1970-01-01 {$item->getDbClipLength()}", new DateTimeZone("UTC"));
                $startEpoch = $itemStartDT->format("U");
                Logging::log("new start time");
                Logging::log($itemStartDT->format("Y-m-d H:i:s"));

                Logging::log("duration");
                Logging::log($durationDT->format("Y-m-d H:i:s"));
                Logging::log($durationDT->format("U"). "seconds");

                $endEpoch = $itemStartDT->format("U") + $durationDT->format("U");
                $itemEndDT = DateTime::createFromFormat("U", $endEpoch, new DateTimeZone("UTC"));
                Logging::log("new end time");
                Logging::log($itemEndDT->format("Y-m-d H:i:s"));

                $item->setDbStarts($itemStartDT);
                $item->setDbEnds($itemEndDT);
                $item->save($this->con);

                $itemStartDT = $itemEndDT;
            }
        }
    }

    public function addScheduledItem($starts, $duration, $adjustSched = true) {

    }

    /*
     * @param DateTime $starts
     */
    public function updateScheduledItem($p_newStarts, $p_adjustSched = true) {

        $origStarts = $this->propSched->getDbStarts(null);

        $diff = $origStarts->diff($p_newStarts);

        //item is scheduled further in future
        if ($diff->format("%R") === "+") {

            CcScheduleQuery::create()
                ->filterByDbStarts($this->propSched->getDbStarts(), Criteria::GREATER_THAN)
                ->filterByDbId($this->propSched->getDbId(), Criteria::NOT_EQUAL)
                ->find();

        }
        //item has been scheduled earlier
        else {
            CcScheduleQuery::create()
                ->filterByDbStarts($this->propSched->getDbStarts(), Criteria::GREATER_THAN)
                ->filterByDbId($this->propSched->getDbId(), Criteria::NOT_EQUAL)
                ->find();
        }
    }

    public function removeScheduledItem($adjustSched = true) {

        if ($adjustSched === true) {
            $duration = $this->propSched->getDbEnds('U') - $this->propSched->getDbStarts('U');

            CcScheduleQuery::create()
                ->filterByDbInstanceId()
                ->filterByDbStarts()
                ->find();
        }

        $this->propSched->delete();
    }
}