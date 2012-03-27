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

        $this->con = Propel::getConnection(CcSchedulePeer::DATABASE_NAME);
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
            $file = CcFilesQuery::create()->findPK($id, $this->con);

            if (is_null($file) || !$file->getDbFileExists()) {
                throw new Exception("A selected File does not exist!");
            }
            else {
                $data = $this->fileInfo;
                $data["id"] = $id;
                $data["cliplength"] = $file->getDbLength();
                $data["cueout"] = $file->getDbLength();

                $defaultFade = Application_Model_Preference::GetDefaultFade();
                if ($defaultFade !== "") {
                    //fade is in format SS.uuuuuu
                    $data["fadein"] = $defaultFade;
                    $data["fadeout"] = $defaultFade;
                }

                $files[] = $data;
            }
        }
        else if ($type === "playlist") {

            $contents = CcPlaylistcontentsQuery::create()
                ->orderByDbPosition()
                ->filterByDbPlaylistId($id)
                ->find($this->con);

            if (is_null($contents)) {
                throw new Exception("A selected Playlist does not exist!");
            }

            foreach ($contents as $plItem) {

                $file = $plItem->getCcFiles($this->con);
                if (isset($file) && $file->getDbFileExists()) {

                    $data = $this->fileInfo;
                    $data["id"] = $plItem->getDbFileId();
                    $data["cliplength"] = $plItem->getDbCliplength();
                    $data["cuein"] = $plItem->getDbCuein();
                    $data["cueout"] = $plItem->getDbCueout();
                    $data["fadein"] = $plItem->getDbFadein();
                    $data["fadeout"] = $plItem->getDbFadeout();

                    $files[] = $data;
                }
            }
        }

        return $files;
    }

    /*
     * @param DateTime startDT in UTC
     * @param string duration
     *      in format H:i:s.u (could be more that 24 hours)
     *
     * @return DateTime endDT in UTC
     */
    private static function findEndTime($p_startDT, $p_duration) {

        $startEpoch = $p_startDT->format("U.u");
        $durationSeconds = Application_Model_Playlist::playlistTimeToSeconds($p_duration);

        //add two float numbers to 6 subsecond precision
        //DateTime::createFromFormat("U.u") will have a problem if there is no decimal in the resulting number.
        $endEpoch = bcadd($startEpoch , (string) $durationSeconds, 6);

        Logging::log("start DateTime created {$p_startDT->format("Y-m-d H:i:s.u")}");
        Logging::log("start epoch is {$startEpoch}");
        Logging::log("duration in seconds is {$durationSeconds}");
        Logging::log("end epoch is {$endEpoch}");

        $dt = DateTime::createFromFormat("U.u", $endEpoch, new DateTimeZone("UTC"));

        Logging::log("end DateTime created {$dt->format("Y-m-d H:i:s.u")}");

        return $dt;
    }
    
    private function findNextStartTime($DT, $instance) {
        
        //check to see if the show has started.
        $nowDT = new DateTime("now", new DateTimeZone("UTC"));
        
        $sEpoch = intval($DT->format("U"));
        $nEpoch = intval($nowDT->format("U"));
        
        //check for if the show has started.
        if ($nEpoch > $sEpoch) {
            //need some kind of placeholder for cc_schedule.
            //playout_status will be -1.
            $nextDT = $nowDT;
        
            $length = $nEpoch - $sEpoch;
            $cliplength = Application_Model_Playlist::secondsToPlaylistTime($length);
        
            //fillers are for only storing a chunk of time space that has already passed.
            $filler = new CcSchedule();
            $filler->setDbStarts($DT)
                ->setDbEnds($nowDT)
                ->setDbClipLength($cliplength)
                ->setDbPlayoutStatus(-1)
                ->setDbInstanceId($instance->getDbId())
                ->save($this->con);
        }
        else {
            $nextDT = $DT;
        }
        
        return $nextDT;
    }

    /*
     * @param array $scheduledIds
     * @param array $fileIds
     * @param array $playlistIds
     */
    private function insertAfter($scheduleItems, $schedFiles, $adjustSched = true) {

        try {

            $affectedShowInstances = array();

            //dont want to recalculate times for moved items.
            $excludeIds = array();
            foreach ($schedFiles as $file) {
                if (isset($file["sched_id"])) {
                    $excludeIds[] = intval($file["sched_id"]);
                }
            }

            foreach ($scheduleItems as $schedule) {
                $id = intval($schedule["id"]);
                $ts = intval($schedule["timestamp"]);

                Logging::log("scheduling after scheduled item: ".$id);
                Logging::log("in show: ".intval($schedule["instance"]));

                if ($id !== 0) {
                    $schedItem = CcScheduleQuery::create()->findPK($id, $this->con);
                    if (is_null($schedItem)) {
                        throw new OutDatedScheduleException("The schedule you're viewing is out of date!");
                    }
                    $instance = $schedItem->getCcShowInstances($this->con);
                    if (intval($schedule["instance"]) !== $instance->getDbId()) {
                        throw new OutDatedScheduleException("The schedule you're viewing is out of date!");
                    }
                    $schedItemEndDT = $schedItem->getDbEnds(null);
                    $nextStartDT = $this->findNextStartTime($schedItemEndDT, $instance);
                }
                //selected empty row to add after
                else {
                    $instance = CcShowInstancesQuery::create()->findPK($schedule["instance"], $this->con);
                    
                    //check to see if the show has started.
                    $showStartDT = $instance->getDbStarts(null);
                    $nextStartDT = $this->findNextStartTime($showStartDT, $instance);
                }

                $currTs = intval($instance->getDbLastScheduled("U")) ? : 0;
                //user has an old copy of the time line opened.
                if ($ts !== $currTs) {
                    Logging::log("currTs {$currTs}, ts {$ts}");
                    $show = $instance->getCcShow($this->con);
                    throw new OutDatedScheduleException("The show {$show->getDbName()} has been previously updated!");
                }

                if (!in_array($instance->getDbId(), $affectedShowInstances)) {
                    $affectedShowInstances[] = $instance->getDbId();
                }

                Logging::log("finding items >= {$nextStartDT->format("Y-m-d H:i:s.u")}");
                if ($adjustSched === true) {
                    $followingSchedItems = CcScheduleQuery::create()
                        ->filterByDBStarts($nextStartDT->format("Y-m-d H:i:s.u"), Criteria::GREATER_EQUAL)
                        ->filterByDbInstanceId($instance->getDbId())
                        ->filterByDbId($excludeIds, Criteria::NOT_IN)
                        ->orderByDbStarts()
                        ->find($this->con);

                     foreach ($excludeIds as $id) {
                        Logging::log("Excluding id {$id}");
                     }
                }

                foreach($schedFiles as $file) {

                    Logging::log("adding file with id: ".$file["id"]);

                    $endTimeDT = self::findEndTime($nextStartDT, $file['cliplength']);

                    //item existed previously and is being moved.
                    //need to keep same id for resources if we want REST.
                    if (isset($file['sched_id'])) {
                        $sched = CcScheduleQuery::create()->findPK($file['sched_id'], $this->con);
                    }
                    else {
                        $sched = new CcSchedule();
                    }
                    Logging::log("id {$sched->getDbId()}");
                    Logging::log("start time {$nextStartDT->format("Y-m-d H:i:s.u")}");
                    Logging::log("end time {$endTimeDT->format("Y-m-d H:i:s.u")}");

                    $sched->setDbStarts($nextStartDT);
                    $sched->setDbEnds($endTimeDT);
                    $sched->setDbFileId($file['id']);
                    $sched->setDbCueIn($file['cuein']);
                    $sched->setDbCueOut($file['cueout']);
                    $sched->setDbFadeIn($file['fadein']);
                    $sched->setDbFadeOut($file['fadeout']);
                    $sched->setDbClipLength($file['cliplength']);
                    $sched->setDbInstanceId($instance->getDbId());
                    $sched->save($this->con);

                    $nextStartDT = $endTimeDT;
                }

                if ($adjustSched === true) {

                    //recalculate the start/end times after the inserted items.
                    foreach($followingSchedItems as $item) {

                        Logging::log("adjusting iterm {$item->getDbId()}");

                        $endTimeDT = self::findEndTime($nextStartDT, $item->getDbClipLength());

                        $item->setDbStarts($nextStartDT);
                        $item->setDbEnds($endTimeDT);
                        $item->save($this->con);

                        $nextStartDT = $endTimeDT;
                    }
                }
            }

            //update the status flag in cc_schedule.
            $instances = CcShowInstancesQuery::create()
                ->filterByPrimaryKeys($affectedShowInstances)
                ->find($this->con);

            foreach ($instances as $instance) {
                $instance->updateScheduleStatus($this->con);
            }

            //update the last scheduled timestamp.
            CcShowInstancesQuery::create()
                ->filterByPrimaryKeys($affectedShowInstances)
                ->update(array('DbLastScheduled' => new DateTime("now", new DateTimeZone("UTC"))), $this->con);
        }
        catch (Exception $e) {
            Logging::debug($e->getMessage());
            throw $e;
        }
    }

    /*
     * @param array $scheduleItems
     * @param array $mediaItems
     */
    public function scheduleAfter($scheduleItems, $mediaItems, $adjustSched = true) {

        $this->con->beginTransaction();

        $schedFiles = array();

        try {

            foreach ($mediaItems as $media) {
                Logging::log("Media Id ".$media["id"]);
                Logging::log("Type ".$media["type"]);

                $schedFiles = array_merge($schedFiles, $this->retrieveMediaFiles($media["id"], $media["type"]));
            }
            $this->insertAfter($scheduleItems, $schedFiles, $adjustSched);

            $this->con->commit();

            Application_Model_RabbitMq::PushSchedule();
        }
        catch (Exception $e) {
            $this->con->rollback();
            throw $e;
        }
    }

    /*
     * @param array $selectedItem
     * @param array $afterItem
     */
    public function moveItem($selectedItems, $afterItems, $adjustSched = true) {

        $this->con->beginTransaction();
      
        try {
            
            //checks on whether the item to move after is ok.
            Logging::log("Moving after item: {$afterItems[0]["id"]}");
            $origAfterTs = intval($afterItems[0]["timestamp"]);
            
            if (intval($afterItems[0]["id"]) === 0) {
                $afterInstance = CcShowInstancesQuery::create()->findPK($afterItems[0]["instance"], $this->con);
            }
            else {
                $after = CcScheduleQuery::create()->findPk($afterItems[0]["id"], $this->con);
                if (is_null($after)) {
                    throw new OutDatedScheduleException("The schedule you're viewing is out of date!");
                }
                $afterInstance = $after->getCcShowInstances($this->con);
            }
            
            $currTs = intval($afterInstance->getDbLastScheduled("U")) ? : 0;
            if ($origAfterTs !== $currTs) {
                $show = $afterInstance->getCcShow($this->con);
                throw new OutDatedScheduleException("The show {$show->getDbName()} has been previously updated!");
            }
            
            //map show instances to cc_schedule primary keys.
            $modifiedMap = array();
            $movedData = array();
            
            //prepare each of the selected items.
            for ($i = 0; $i < count($selectedItems); $i++) {
                
                Logging::log("Moving item {$selectedItems[$i]["id"]}");
                
                $origSelTs = intval($selectedItems[$i]["timestamp"]);
                $selected = CcScheduleQuery::create()->findPk($selectedItems[$i]["id"], $this->con);
                if (is_null($selected)) {
                    throw new OutDatedScheduleException("The schedule you're viewing is out of date!");
                }
                $selectedInstance = $selected->getCcShowInstances($this->con);
                
                if (is_null($selectedInstance) || is_null($afterInstance)) {
                    throw new OutDatedScheduleException("The schedule you're viewing is out of date!");
                }
                
                $currTs = intval($selectedInstance->getDbLastScheduled("U")) ? : 0;
                if ($origSelTs !== $currTs) {
                    $show = $selectedInstance->getCcShow($this->con);
                    throw new OutDatedScheduleException("The show {$show->getDbName()} has been previously updated!");
                }
                
                $data = $this->fileInfo;
                $data["id"] = $selected->getDbFileId();
                $data["cliplength"] = $selected->getDbClipLength();
                $data["cuein"] = $selected->getDbCueIn();
                $data["cueout"] = $selected->getDbCueOut();
                $data["fadein"] = $selected->getDbFadeIn();
                $data["fadeout"] = $selected->getDbFadeOut();
                $data["sched_id"] = $selected->getDbId();
                
                $movedData[] = $data;
                
                //figure out which items must be removed from calculated show times.
                $showInstanceId = $selectedInstance->getDbId();
                $schedId = $selected->getDbId();
                if (isset($modifiedMap[$showInstanceId])) {
                    array_push($modifiedMap[$showInstanceId], $schedId);   
                }
                else {
                    $modifiedMap[$showInstanceId] = array($schedId);
                }
            } 

            //calculate times excluding the to be moved items.
            foreach ($modifiedMap as $instance => $schedIds) {
                $this->removeGaps($instance, $schedIds);
            }
  
            $this->insertAfter($afterItems, $movedData, $adjustSched);
            $this->con->commit();

            Application_Model_RabbitMq::PushSchedule();
        }
        catch (Exception $e) {
            $this->con->rollback();
            throw $e;
        }
    }

    public function removeItems($scheduledItems, $adjustSched = true) {

        $showInstances = array();
        $this->con->beginTransaction();

        try {

            $scheduledIds = array();
            foreach ($scheduledItems as $item) {
                $scheduledIds[$item["id"]] = intval($item["timestamp"]);
            }

            $removedItems = CcScheduleQuery::create()->findPks(array_keys($scheduledIds));

            //check to see if the current item is being deleted so we can truncate the record.
            $currentItem = null;
            //check to make sure all items selected are up to date
            foreach ($removedItems as $removedItem) {
                $ts = $scheduledIds[$removedItem->getDbId()];
                $instance = $removedItem->getCcShowInstances($this->con);
                if (is_null($instance)) {
                    throw new OutDatedScheduleException("The schedule you're viewing is out of date!");
                }
                $currTs = intval($instance->getDbLastScheduled("U")) ? : 0;

                if ($ts !== $currTs) {
                    $show = $instance->getCcShow($this->con);
                    throw new OutDatedScheduleException("The show {$show->getDbName()} has been previously updated!");
                }
                
                //check to truncate the currently playing item instead of deleting it.
                if ($removedItem->isCurrentItem()) {
                    $now = new DateTime("now", new DateTimeZone("UTC"));
                    
                    $nEpoch = floatval($now->format('U.u'));
                    $sEpoch = floatval($removedItem->getDbStarts('U.u'));
                    $length = $nEpoch - $sEpoch;
                    $cliplength = Application_Model_Playlist::secondsToPlaylistTime($length);
                    
                    $removedItem->setDbClipLength($cliplength);
                    $removedItem->setDbEnds($now);   
                    $removedItem->save($this->con);
                }
                else {
                    $removedItem->delete($this->con);
                }
            }

            if ($adjustSched === true) {
                //get the show instances of the shows we must adjust times for.
                foreach ($removedItems as $item) {

                    $instance = $item->getDBInstanceId();
                    if (!in_array($instance, $showInstances)) {
                        $showInstances[] = $instance;
                    }
                }

                foreach ($showInstances as $instance) {
                    $this->removeGaps($instance);
                }
            }

            //update the status flag in cc_schedule.
            $instances = CcShowInstancesQuery::create()
                ->filterByPrimaryKeys($showInstances)
                ->find($this->con);

            foreach ($instances as $instance) {
                $instance->updateScheduleStatus($this->con);
            }

            //update the last scheduled timestamp.
            CcShowInstancesQuery::create()
                ->filterByPrimaryKeys($showInstances)
                ->update(array('DbLastScheduled' => new DateTime("now", new DateTimeZone("UTC"))), $this->con);

            $this->con->commit();

            Application_Model_RabbitMq::PushSchedule();
        }
        catch (Exception $e) {
            $this->con->rollback();
            throw $e;
        }
    }

    /*
     * @param int $showInstance
     * @param array $exclude
     *   ids of sched items to remove from the calulation.
     */
    private function removeGaps($showInstance, $exclude=null) {

        Logging::log("removing gaps from show instance #".$showInstance);

        $instance = CcShowInstancesQuery::create()->findPK($showInstance, $this->con);
        if (is_null($instance)) {
            throw new OutDatedScheduleException("The schedule you're viewing is out of date!");
        }

        $itemStartDT = $instance->getDbStarts(null);

        $schedule = CcScheduleQuery::create()
            ->filterByDbInstanceId($showInstance)
            ->filterByDbId($exclude, Criteria::NOT_IN)
            ->orderByDbStarts()
            ->find($this->con);


        foreach ($schedule as $item) {

            Logging::log("adjusting item #".$item->getDbId());

            $itemEndDT = self::findEndTime($itemStartDT, $item->getDbClipLength());

            $item->setDbStarts($itemStartDT);
            $item->setDbEnds($itemEndDT);
            $item->save($this->con);

            $itemStartDT = $itemEndDT;
        }
    }
}

class OutDatedScheduleException extends Exception {}