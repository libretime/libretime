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
    
    private $epochNow;
    private $nowDT;
    private $user;

    public function __construct($id = null) {

        $this->con = Propel::getConnection(CcSchedulePeer::DATABASE_NAME);
        
        $this->epochNow = microtime(true);
        $this->nowDT = DateTime::createFromFormat("U.u", $this->epochNow, new DateTimeZone("UTC"));
         
        $this->user = Application_Model_User::GetCurrentUser();
    }
    
    /*
     * make sure any incoming requests for scheduling are ligit.
    *
    * @param array $items, an array containing pks of cc_schedule items.
    */
    private function validateRequest($items) {
    
        $nowEpoch = floatval($this->nowDT->format("U.u"));
        
        for ($i = 0; $i < count($items); $i++) {
            $id = $items[$i]["id"];
            
            //could be added to the beginning of a show, which sends id = 0;
            if ($id > 0) {
                $schedInfo[$id] = $items[$i]["instance"];
            }

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
            throw new OutDatedScheduleException("The schedule you're viewing is out of date! (sched mismatch)");
        }
        
        //a show has been deleted
        if (count($instanceIds) !== count($showInstances)) {
            throw new OutDatedScheduleException("The schedule you're viewing is out of date! (instance mismatch)");
        }
        
        foreach ($schedItems as $schedItem) {
            $id = $schedItem->getDbId();
            $instance = $schedItem->getCcShowInstances($this->con);
            
            if (intval($schedInfo[$id]["instance"]) !== $instance->getDbId()) {
                throw new OutDatedScheduleException("The schedule you're viewing is out of date!");
            }
        }
        
        foreach ($showInstances as $instance) {
           
            $id = $instance->getDbId();
            $show = $instance->getCcShow($this->con);
            
            if ($this->user->canSchedule($show->getDbId()) === false) {
                throw new Exception("You are not allowed to schedule show {$show->getDbName()}.");
            }
            
            $showEndEpoch = floatval($instance->getDbEnds("U.u"));
            
            if ($showEndEpoch < $nowEpoch) {   
                throw new OutDatedScheduleException("The show {$show->getDbName()} is over and cannot be scheduled.");
            }
            
            $ts = intval($instanceInfo[$id]);
            $lastSchedTs = intval($instance->getDbLastScheduled("U")) ? : 0;
            if ($ts < $lastSchedTs) {
                Logging::log("ts {$ts} last sched {$lastSchedTs}");
                throw new OutDatedScheduleException("The show {$show->getDbName()} has been previously updated!");
            }
        }
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
    private function findEndTime($p_startDT, $p_duration) {

        $startEpoch = $p_startDT->format("U.u");
        $durationSeconds = Application_Model_Playlist::playlistTimeToSeconds($p_duration);

        //add two float numbers to 6 subsecond precision
        //DateTime::createFromFormat("U.u") will have a problem if there is no decimal in the resulting number.
        $endEpoch = bcadd($startEpoch , (string) $durationSeconds, 6);

        $dt = DateTime::createFromFormat("U.u", $endEpoch, new DateTimeZone("UTC"));
        return $dt;
    }
    
    private function findNextStartTime($DT, $instance) {
        
        $sEpoch = $DT->format("U.u");
        $nEpoch = $this->epochNow;
        
        //check for if the show has started.
        if (bccomp( $nEpoch , $sEpoch , 6) === 1) {
            //need some kind of placeholder for cc_schedule.
            //playout_status will be -1.
            $nextDT = $this->nowDT;
            
            $length = bcsub($nEpoch , $sEpoch , 6);
            $cliplength = Application_Model_Playlist::secondsToPlaylistTime($length);
        
            //fillers are for only storing a chunk of time space that has already passed.
            $filler = new CcSchedule();
            $filler->setDbStarts($DT)
                ->setDbEnds($this->nowDT)
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
    
            $itemEndDT = $this->findEndTime($itemStartDT, $item->getDbClipLength());
    
            $item->setDbStarts($itemStartDT)
                ->setDbEnds($itemEndDT)
                ->save($this->con);
    
            $itemStartDT = $itemEndDT;
        }
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
               
                if ($id !== 0) {
                    $schedItem = CcScheduleQuery::create()->findPK($id, $this->con);
                    $instance = $schedItem->getCcShowInstances($this->con);
                    
                    $schedItemEndDT = $schedItem->getDbEnds(null);
                    $nextStartDT = $this->findNextStartTime($schedItemEndDT, $instance);
                }
                //selected empty row to add after
                else {
                    
                    $instance = CcShowInstancesQuery::create()->findPK($schedule["instance"], $this->con);

                    $showStartDT = $instance->getDbStarts(null);
                    $nextStartDT = $this->findNextStartTime($showStartDT, $instance);
                }

                if (!in_array($instance->getDbId(), $affectedShowInstances)) {
                    $affectedShowInstances[] = $instance->getDbId();
                }

                if ($adjustSched === true) {
                    $followingSchedItems = CcScheduleQuery::create()
                        ->filterByDBStarts($nextStartDT->format("Y-m-d H:i:s.u"), Criteria::GREATER_EQUAL)
                        ->filterByDbInstanceId($instance->getDbId())
                        ->filterByDbId($excludeIds, Criteria::NOT_IN)
                        ->orderByDbStarts()
                        ->find($this->con);
                }

                foreach($schedFiles as $file) {

                    $endTimeDT = $this->findEndTime($nextStartDT, $file['cliplength']);

                    //item existed previously and is being moved.
                    //need to keep same id for resources if we want REST.
                    if (isset($file['sched_id'])) {
                        $sched = CcScheduleQuery::create()->findPK($file['sched_id'], $this->con);
                    }
                    else {
                        $sched = new CcSchedule();
                    }
                   
                    $sched->setDbStarts($nextStartDT)
                        ->setDbEnds($endTimeDT)
                        ->setDbFileId($file['id'])
                        ->setDbCueIn($file['cuein'])
                        ->setDbCueOut($file['cueout'])
                        ->setDbFadeIn($file['fadein'])
                        ->setDbFadeOut($file['fadeout'])
                        ->setDbClipLength($file['cliplength'])
                        ->setDbInstanceId($instance->getDbId())
                        ->save($this->con);

                    $nextStartDT = $endTimeDT;
                }

                if ($adjustSched === true) {

                    //recalculate the start/end times after the inserted items.
                    foreach ($followingSchedItems as $item) {

                        $endTimeDT = $this->findEndTime($nextStartDT, $item->getDbClipLength());

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
            
            $this->validateRequest($scheduleItems);

            foreach ($mediaItems as $media) {
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
            
            $this->validateRequest($selectedItems);
            $this->validateRequest($afterItems);
 
            $afterInstance = CcShowInstancesQuery::create()->findPK($afterItems[0]["instance"], $this->con);
           
            //map show instances to cc_schedule primary keys.
            $modifiedMap = array();
            $movedData = array();
            
            //prepare each of the selected items.
            for ($i = 0; $i < count($selectedItems); $i++) {
                
                $selected = CcScheduleQuery::create()->findPk($selectedItems[$i]["id"], $this->con);
                $selectedInstance = $selected->getCcShowInstances($this->con);
                
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
            
            $this->validateRequest($scheduledItems);
            
            $scheduledIds = array();
            foreach ($scheduledItems as $item) {
                $scheduledIds[] = $item["id"];
            }

            $removedItems = CcScheduleQuery::create()->findPks($scheduledIds);

            //check to make sure all items selected are up to date
            foreach ($removedItems as $removedItem) {
                
                $instance = $removedItem->getCcShowInstances($this->con);
                
                //check to truncate the currently playing item instead of deleting it.
                if ($removedItem->isCurrentItem($this->epochNow)) {
                    
                    $nEpoch = $this->epochNow;
                    $sEpoch = $removedItem->getDbStarts('U.u');
                    
                    $length = bcsub($nEpoch , $sEpoch , 6);
                    $cliplength = Application_Model_Playlist::secondsToPlaylistTime($length);
                    
                    $cueinSec = Application_Model_Playlist::playlistTimeToSeconds($removedItem->getDbCueIn());
                    $cueOutSec = bcadd($cueinSec , $length, 6);
                    $cueout = Application_Model_Playlist::secondsToPlaylistTime($cueOutSec);
                   
                    $removedItem->setDbCueOut($cueout)
                        ->setDbClipLength($cliplength)
                        ->setDbEnds($this->nowDT)  
                        ->save($this->con);
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
     * Used for cancelling the current show instance.
     * 
     * @param $p_id id of the show instance to cancel.
     */
    public function cancelShow($p_id) {
        
        $this->con->beginTransaction();
        
        try {
           
            $instance = CcShowInstancesQuery::create()->findPK($p_id);
            
            $items = CcScheduleQuery::create()
                ->filterByDbInstanceId($p_id)
                ->filterByDbEnds($this->nowDT, Criteria::GREATER_THAN)
                ->find($this->con);
            
            $remove = array();
            $ts = $this->nowDT->format('U');
            
            for($i = 0; $i < count($items); $i++) {
                $remove[$i]["instance"] = $p_id;
                $remove[$i]["timestamp"] = $ts;
                $remove[$i]["id"] = $items[$i]->getDbId();
            }
            
            $this->removeItems($remove, false);
            
            $instance->setDbEnds($this->nowDT);
            $instance->save($this->con);
            
            $this->con->commit();
        }
        catch (Exception $e) {
            $this->con->rollback();
            throw $e;
        }  
    }
}

class OutDatedScheduleException extends Exception {}