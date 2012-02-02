<?php

class Application_Model_Scheduler {

    private $con;
    private $fileInfo = array(
            "id" => "",
            "cliplength" => "",
            "cuein" => "00:00:00",
            "cueout" => "00:00:00",
            "fadein" => "00:00:00",
            "fadeout" => "00:00:00",
            "sched_id" => null,
        );

    public function __construct($id = null) {

    }

    /*
     * @param $id
     * @param $type
     *
     * @return $files
     */
    private function retrieveMediaFiles($id, $type) {

        $files = array();

        if ($type === "audioclip") {
            $file = CcFilesQuery::create()->findPK($id);

            $data = $this->fileInfo;
            $data["id"] = $id;
            $data["cliplength"] = $file->getDbLength();

            $files[] = $data;
        }
        else if ($type === "playlist") {

        }

        return $files;
    }

    /*
     * @param DateTime startDT
     * @param string duration
     *      in format H:i:s.u (could be more that 24 hours)
     */
    private function findEndTime($startDT, $duration) {

    }

    /*
     * @param array $scheduledIds
     * @param array $fileIds
     * @param array $playlistIds
     */
    public function scheduleAfter($scheduleItems, $mediaItems, $adjustSched = true) {

        $this->con = Propel::getConnection(CcSchedulePeer::DATABASE_NAME);
        $this->con->beginTransaction();

        $schedFiles = array();

        try {

            foreach($mediaItems as $media) {
                Logging::log("Media Id ".$media["id"]);
                Logging::log("Type ".$media["type"]);

                $schedFiles = array_merge($schedFiles, $this->retrieveMediaFiles($media["id"], $media["type"]));
            }
            $this->insertAfter($scheduleItems, $schedFiles, $adjustSched);

            $this->con->commit();
        }
        catch (Exception $e) {
            $this->con->rollback();
            throw $e;
        }
    }

    /*
     * @param array $scheduledIds
     * @param array $fileIds
     * @param array $playlistIds
     */
    private function insertAfter($scheduleItems, $schedFiles, $adjustSched = true) {

        try {

            foreach ($scheduleItems as $schedule) {
                $id = intval($schedule["id"]);

                Logging::log("scheduling after scheduled item: ".$id);

                if ($id !== 0) {
                    $schedItem = CcScheduleQuery::create()->findPK($id);
                    $instance = $schedItem->getDbInstanceId();

                    //user has an old copy of the time line opened.
                    if ($instance !== intval($schedule["instance"])) {
                        Logging::log("items have been since updated");
                        return;
                    }

                    $nextStartDT = $schedItem->getDbEnds(null);
                }
                //selected empty row to add after
                else {
                    $showInstance = CcShowInstancesQuery::create()->findPK($schedule["instance"]);
                    $nextStartDT = $showInstance->getDbStarts(null);
                    $instance = intval($schedule["instance"]);
                }

                if ($id !== 0 && $adjustSched === true) {
                    $followingSchedItems = CcScheduleQuery::create()
                        ->filterByDBStarts($schedItem->getDbStarts("Y-m-d H:i:s.u"), Criteria::GREATER_THAN)
                        ->filterByDbInstanceId($instance)
                        ->orderByDbStarts()
                        ->find();
                }

                foreach($schedFiles as $file) {

                    Logging::log("adding file with id: ".$file["id"]);

                    $durationDT = new DateTime("1970-01-01 {$file['cliplength']}", new DateTimeZone("UTC"));
                    $endTimeEpoch = $nextStartDT->format("U") + $durationDT->format("U");
                    $endTimeDT = DateTime::createFromFormat("U", $endTimeEpoch, new DateTimeZone("UTC"));

                    $sched = new CcSchedule();

                    //item existed previously and is being moved.
                    //TODO make it possible to insert the primary key of the item
                    //need to keep same id for resources if we want REST.
                    if (isset($file['sched_id'])) {
                        //$sched->setDbId($file['sched_id']);
                    }

                    $sched->setDbStarts($nextStartDT);
                    $sched->setDbEnds($endTimeDT);
                    $sched->setDbFileId($file['id']);
                    $sched->setDbCueIn($file['cuein']);
                    $sched->setDbCueOut($file['cueout']);
                    $sched->setDbFadeIn($file['fadein']);
                    $sched->setDbFadeOut($file['fadeout']);
                    $sched->setDbClipLength($durationDT->format("H:i:s.u"));
                    $sched->setDbInstanceId($instance);
                    $sched->save($this->con);

                    $nextStartDT = $endTimeDT;
                }

                if ($id !== 0 && $adjustSched === true) {

                    //recalculate the start/end times after the inserted items.
                    foreach($followingSchedItems as $item) {

                        $durationDT = new DateTime("1970-01-01 {$item->getDbClipLength()}", new DateTimeZone("UTC"));
                        $a = $nextStartDT->format("U");
                        $b = $durationDT->format("U");
                        $endTimeEpoch = $a + $b;
                        //$endTimeEpoch = $nextStartDT->format("U") + $durationDT->format("U");
                        $endTimeDT = DateTime::createFromFormat("U", $endTimeEpoch, new DateTimeZone("UTC"));

                        $item->setDbStarts($nextStartDT);
                        $item->setDbEnds($endTimeDT);
                        $item->save($this->con);

                        $nextStartDT = $endTimeDT;
                    }
                }
            }

        }
        catch (Exception $e) {
            throw $e;
        }
    }

    /*
     * @param array $selectedItem
     * @param array $afterItem
     */
    public function moveItem($selectedItem, $afterItem, $adjustSched = true) {

        $this->con = Propel::getConnection(CcSchedulePeer::DATABASE_NAME);

        $this->con->beginTransaction();

        try {

            $origSelIns = intval($selectedItem[0]["instance"]);
            $origAfterIns = intval($afterItem[0]["instance"]);

            Logging::log("Moving item {$selectedItem[0]["id"]}");
            Logging::log("After {$afterItem[0]["id"]}");

            $selected = CcScheduleQuery::create()->findPk($selectedItem[0]["id"]);
            $after = CcScheduleQuery::create()->findPk($afterItem[0]["id"]);

            if ($origSelIns !== $selected->getDBInstanceId()
                || $origAfterIns !== $after->getDBInstanceId()) {

                Logging::log("items have been since updated");
                return;
            }

            $selected->delete($this->con);
            $this->removeGaps($origSelIns);

            //moved to another show, remove gaps from original show.
            if ($adjustSched === true && $origSelIns !== $origAfterIns) {

            }

            $data = $this->fileInfo;
            $data["id"] = $selected->getDbFileId();
            $data["cliplength"] = $selected->getDbClipLength();
            $data["cuein"] = $selected->getDbCueIn();
            $data["cueout"] = $selected->getDbCueOut();
            $data["fadein"] = $selected->getDbFadeIn();
            $data["fadeout"] = $selected->getDbFadeOut();
            $data["sched_id"] = $selected->getDbId();

            $this->insertAfter($afterItem, array($data), $adjustSched);

            $this->con->commit();
        }
        catch (Exception $e) {
            $this->con->rollback();
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
                    $this->removeGaps($instance);
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
}