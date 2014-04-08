<?php

use Airtime\CcShowHostsQuery;
use Airtime\CcShowInstancesQuery;

class Application_Model_ShowBuilder
{
    private $timezone;

    //in UTC timezone
    private $startDT;
    //in UTC timezone
    private $endDT;

    private $user;
    private $opts;

    private $contentDT;
    private $epoch_now;

    private $showInstances = array();

    private $defaultRowArray = array(
        "header"          => false,
        "footer"          => false,
        "empty"           => false,
        "allowed"         => false,
        "linked_allowed"  => true,
        "id"              => 0,
        "instance"        => "",
        "starts"          => "",
        "ends"            => "",
        "runtime"         => "",
        "title"           => "",
        "creator"         => "",
        "album"           => "",
        "timestamp"       => null,
        "cuein"           => "",
        "cueout"          => "",
        "fadein"          => "",
        "fadeout"         => "",
        "image"           => false,
        "mime"            => null,
        "color"           => "", //in hex without the '#' sign.
        "backgroundColor" => "", //in hex without the '#' sign.
    );

    /*
     * @param DateTime $p_startsDT
     * @param DateTime $p_endsDT
     */
    public function __construct($p_startDT, $p_endDT, $p_opts)
    {
        $this->startDT = $p_startDT;
        $this->endDT = $p_endDT;
        $this->timezone = Application_Model_Preference::GetUserTimezone();
        $this->user = Application_Model_User::getCurrentUser();
        $this->opts = $p_opts;
        $this->epoch_now = floatval(microtime(true));
    }

    private function getUsersShows()
    {
        $shows = array();

        $host_shows = CcShowHostsQuery::create()
            ->setFormatter(ModelCriteria::FORMAT_ON_DEMAND)
            ->filterByDbHost($this->user->getId())
            ->find();

        foreach ($host_shows as $host_show) {
            $shows[] = $host_show->getDbShow();
        }

        return $shows;
    }

    //check to see if this row should be editable by the user.
    private function isAllowed(&$showInstance, &$row)
    {
        //cannot schedule in a recorded show.
        if ($showInstance->isRecorded()) {
            return;
        }

        if ($this->user->canSchedule($showInstance->getDbShowId()) == true) {
            $row["allowed"] = true;
        }
        
        /* If any linked show instance is currently playing
         * we have to disable editing, or else the times
        * will not make sense for shows scheduled in the future
        */ 
        if ($showInstance->isCurrentShow($this->epoch_now) && $showInstance->isLinked()) {
        	$row["allowed"] = false;
        }
    }

    private function getItemColor(&$showInstance, &$row)
    {
        $defaultColor = "ffffff";
        $defaultBackground = "3366cc";

        $show = $showInstance->getCcShow();
        
        $color = $show->getDbColor();
        if ($color === '') {
            $color = $defaultColor;
        }
        
        $backgroundColor = $show->getDbBackgroundColor();
        if ($backgroundColor === '') {
            $backgroundColor = $defaultBackground;
        }

        $row["color"]           = $color;
        $row["backgroundColor"] = $backgroundColor;
    }

    private function getRowTimestamp(&$showInstance, &$row)
    {
    	$lastScheduled = $showInstance->getDbLastScheduled(null);
    	
        if (is_null($lastScheduled)) {
            $ts = 0;
        } 
        else {
            $dt = $lastScheduled;
            $ts = intval($dt->format("U"));
        }
        $row["timestamp"] = $ts;
    }

    /*
     * marks a row's status.
     * 0 = past
     * 1 = current
     * 2 = future
     * TODO : change all of the above to real constants -- RG
     */
    private function getScheduledStatus($p_epochItemStart, $p_epochItemEnd, &$row)
    {
        if ($row["footer"] === true && $this->epoch_now > $p_epochItemStart &&
            $this->epoch_now > $p_epochItemEnd) {
            $row["scheduled"] = 0;
        } 
        elseif ($row["footer"] === true && $this->epoch_now < $p_epochItemEnd) {
            $row["scheduled"] = 2;
        } 
        elseif ($row["header"] === true && $this->epoch_now >= $p_epochItemStart) {
            $row["scheduled"] = 0;
        } 
        elseif ($row["header"] === true && $this->epoch_now < $p_epochItemEnd) {
            $row["scheduled"] = 2;
        }

        //item is in the past.
        else if ($this->epoch_now > $p_epochItemEnd) {
            $row["scheduled"] = 0;
        }

        //item is the currently scheduled item.
        else if ($this->epoch_now >= $p_epochItemStart && $this->epoch_now < $p_epochItemEnd) {
            $row["scheduled"] = 1;
            //how many seconds the view should wait to redraw itself.
            $row["refresh"] = $p_epochItemEnd - $this->epoch_now;
        }

        //item is in the future.
        else if ($this->epoch_now < $p_epochItemStart) {
            $row["scheduled"] = 2;
        } 
        //else problem
        else {
            Logging::warn("No-op? is this what should happen...printing
                debug just in case");
            $d = array(
                '$p_epochItemStart' => $p_epochItemStart,
                '$p_epochItemEnd' => $p_epochItemEnd,
                '$row' => $row
            );
            Logging::warn($d);
        }
    }

    /*
     * @param $showInstance is a propel object
     */
    private function makeHeaderRow($showInstance)
    {
        $row = $this->defaultRowArray;
        
        $show = $showInstance->getCcShow();
        
        $this->getRowTimestamp($showInstance, $row);
        $this->getItemColor($showInstance, $row);

        $showStartDT = $showInstance->getDbStarts(null);
        $showStartDT->setTimezone(new DateTimeZone($this->timezone));
        $startsEpoch = floatval($showStartDT->format("U.u"));
        
        $showEndDT = $showInstance->getDbEnds(null);
        $showEndDT->setTimezone(new DateTimeZone($this->timezone));
        $endsEpoch = floatval($showEndDT->format("U.u"));

        //is a rebroadcast show
        if ($showInstance->isRebroadcast()) {
            $row["rebroadcast"] = true;

            $parentInstance = $showInstance->getCcShowInstancesRelatedByDbOriginalShow();
            $name = $parentInstance->getCcShow()->getDbName();
            $dt = $parentInstance->getDbStarts(null);
            $dt->setTimezone(new DateTimeZone($this->timezone));
            $time = $dt->format("Y-m-d H:i");

            $row["rebroadcast_title"] = sprintf(_("Rebroadcast of %s from %s"), $name, $time);
        } 
        elseif ($showInstance->isRecorded()) {
            $row["record"] = true;

            // at the time of creating on show, the recorded file is not in the DB yet.
            // therefore, 'si_file_id' is null. So we need to check it.
            
            //TODO fix how soundcloud/other media ids are stored.
            /*
            if (Application_Model_Preference::GetUploadToSoundcloudOption() && isset($p_item['si_file_id'])) {
                $file = Application_Model_StoredFile::RecallById($p_item['si_file_id']);
                if (isset($file)) {
                    $sid = $file->getSoundCloudId();
                    $row['soundcloud_id'] = $sid;
                }
            }
            */
        }

        if ($showInstance->isCurrentShow($this->epoch_now)) {
            $row["currentShow"] = true;
        }

        $this->isAllowed($showInstance, $row);

        $row["header"] = true;
        $row["starts"] = $showStartDT->format("Y-m-d H:i");
        $row["startDate"] = $showStartDT->format("Y-m-d");
        $row["startTime"] = $showStartDT->format("H:i");
        $row["refresh"] = floatval($showStartDT->format("U.u")) - $this->epoch_now;
        $row["ends"] = $showEndDT->format("Y-m-d H:i");
        $row["endDate"] = $showEndDT->format("Y-m-d");
        $row["endTime"] = $showEndDT->format("H:i");
        $row["duration"] = floatval($showEndDT->format("U.u")) - floatval($showStartDT->format("U.u"));
        $row["title"] = htmlspecialchars($show->getDbName());
        $row["instance"] = $showInstance->getDbId();
        $row["image"] = '';

        $this->getScheduledStatus($startsEpoch, $endsEpoch, $row);

        $this->contentDT = $showStartDT;

        return $row;
    }
    
    private function itemRowCheck(&$showInstance, &$row) {
    	//show is empty or is a special kind of show (recording etc)
    	if ($showInstance->isRecorded()) {
    		$row["record"] = true;
    		$row["instance"] = $instance;
    	
    		$showStartDT = $showInstance->getDbStarts(null);
    		$showEndDT = $showInstance->getDbEnds(null);
    		$startsEpoch  = floatval($showStartDT->format("U.u"));
    		$endsEpoch = floatval($showEndDT->format("U.u"));
    	
    		$this->getScheduledStatus($startsEpoch, $endsEpoch, $row);
    	}
    	else if ($showInstance->isRebroadcast()) {
    		$row["rebroadcast"] = true;
    	}
    	
    	if ($showInstance->isCurrentShow($this->epoch_now)) {
    		$row["currentShow"] = true;
    	}
    	
    	$this->getItemColor($showInstance, $row);
    	$this->getRowTimestamp($showInstance, $row);
    	$this->isAllowed($showInstance, $row);
    }

    private function makeScheduledItemRows($showInstance)
    {
    	$rows = array();
    	$row = $this->defaultRowArray;
    	
    	$instance = $showInstance->getDbId();
    	$showStartDT = $showInstance->getDbStarts(null);
    	$showEndDT = $showInstance->getDbEnds(null);
    	
    	$scheduledItems = $showInstance->getCcSchedules();
    	
    	foreach ($scheduledItems as $scheduledItem) {
    		
    		//if there's a filler row its status is -1, don't display it.
    		if ($scheduledItem->getDbPlayoutStatus() >= 0) {

    			$schedStartDT = $scheduledItem->getDbStarts(null);
    			$schedStartDT->setTimezone(new DateTimeZone($this->timezone));
    		
    			$schedEndDT = $scheduledItem->getDbEnds(null);
    			$schedEndDT->setTimezone(new DateTimeZone($this->timezone));

				//information about whether a track is inside|boundary|outside a show.
				$row["status"] = $scheduledItem->getDbPlayoutStatus();
    		
    			$startsEpoch = floatval($schedStartDT->format("U.u"));
    			$endsEpoch = floatval($schedEndDT->format("U.u"));
    			$showEndEpoch = floatval($showEndDT->format("U.u"));
    		
    			//don't want an overbooked item to stay marked as current.
    			$this->getScheduledStatus($startsEpoch, min($endsEpoch, $showEndEpoch), $row);
    		
    			$row["id"] = $scheduledItem->getDbId();
    			//TODO fix this.
    			$row["image"] = true; //$p_item["file_exists"];
    			$row["instance"] = $showInstance->getDbId();
    			$row["starts"] = $schedStartDT->format("H:i:s");
    			$row["ends"] = $schedEndDT->format("H:i:s");
    		
    			$mediaItem = $scheduledItem->getMediaItem();
    			$row["mediaId"] = $mediaItem->getId();
    			$row["title"] = htmlspecialchars($mediaItem->getName());
    			$row["creator"] = htmlspecialchars($mediaItem->getCreator());
    			$row["album"] = htmlspecialchars($mediaItem->getSource());
    			$row["mime"] = $mediaItem->getMime();
    		
    			$formatter = new Format_HHMMSSULength($scheduledItem->getDbCueIn());
    			$row["cuein"] = $formatter->format();
    			$formatter = new Format_HHMMSSULength($scheduledItem->getDbCueOut());
    			$row["cueout"] = $formatter->format();
    			$formatter = new Format_HHMMSSULength($scheduledItem->getDbClipLength());
    			$row["runtime"] = $formatter->format();
    			
    			$row["fadein"] = $scheduledItem->getDbFadeIn();
    			$row["fadeout"] = $scheduledItem->getDbFadeOut();
    			
    			self::itemRowCheck($showInstance, $row);
    		
    			$this->contentDT = $schedEndDT;
    			
    			$rows[] = $row;
    			$row = $this->defaultRowArray;
    		}
    	}
    	
    	//empty normal show.
    	if (count($scheduledItems) === 0 && !$showInstance->isRecorded()) {
    		$row["empty"] = true;
    		$row["id"] = 0;
    		$row["instance"] = $instance;
    		
    		self::itemRowCheck($showInstance, $row);
    		
    		$rows[] = $row;
    	}
    	//a show that's currently played all its scheduled content but is now empty, needs an empty row.
    	else if (count($scheduledItems) > 0 
    			&& $this->epoch_now < floatval($showEndDT->format("U.u"))
    			&& $this->epoch_now > floatval($schedEndDT->format("U.u"))) {
    		
    		$row["empty"] = true;
    		$row["id"] = $scheduledItem->getDbId();
    		$row["instance"] = $instance;
    		
    		self::itemRowCheck($showInstance, $row);
    		
    		$rows[] = $row;
    	}
    		
    	return $rows; 
    }

    private function makeFooterRow($showInstance)
    {
        $row = $this->defaultRowArray;
        $row["footer"] = true;
        $row["instance"] = $showInstance->getDbId();
        $this->getRowTimestamp($showInstance, $row);

        $showStartDT = $showInstance->getDbStarts(null);
        $showEndDT = $showInstance->getDbEnds(null);
        $contentDT = $this->contentDT;

        $runtime = bcsub($contentDT->format("U.u"), $showEndDT->format("U.u"), 6);
        $row["runtime"] = $runtime;

        $timeFilled = new Format_TimeFilled($runtime);
        $row["fRuntime"] = $timeFilled->format();

        $showStartDT->setTimezone(new DateTimeZone($this->timezone));
        $startsEpoch = floatval($showStartDT->format("U.u"));

        $showEndDT->setTimezone(new DateTimeZone($this->timezone));
        $endsEpoch = floatval($showEndDT->format("U.u"));

        $row["refresh"] = floatval($showEndDT->format("U.u")) - $this->epoch_now;

    	if ($showInstance->isCurrentShow($this->epoch_now)) {
    		$row["currentShow"] = true;
    	}

        $this->getScheduledStatus($startsEpoch, $endsEpoch, $row);
        $this->isAllowed($showInstance, $row);

        if ($showInstance->isRecorded()) {
            $row["record"] = true;
        }

        return $row;
    }

    /*
     * @param int $timestamp Unix timestamp in seconds.
     *
     * @return boolean whether the schedule in the show builder's range has
     * been updated.
     *
     */
    public function hasBeenUpdatedSince($timestamp, $instances)
    {
        $outdated = false;
        $shows = Application_Model_Show::getShows($this->startDT, $this->endDT);
       
        $include = array();
        if ($this->opts["showFilter"] !== 0) {
            $include[] = $this->opts["showFilter"];
        } 
        elseif ($this->opts["myShows"] === 1) {

            $include = $this->getUsersShows();
        }

        $currentInstances = array();

        foreach ($shows as $show) {

            if (empty($include) || in_array($show["show_id"], $include)) {
                $currentInstances[] = $show["instance_id"];

                if (isset($show["last_scheduled"])) {
                    $dt = new DateTime($show["last_scheduled"],
                        new DateTimeZone("UTC"));
                } else {
                    $dt = new DateTime($show["created"],
                        new DateTimeZone("UTC"));
                }

                //check if any of the shows have a more recent timestamp.
                $showTimeStamp = intval($dt->format("U"));
                if ($timestamp < $showTimeStamp) {
                    $outdated = true;
                    break;
                }
            }
        }

        //see if the displayed show instances have changed. (deleted,
        //empty schedule etc)
        if ($outdated === false && count($instances)
            !== count($currentInstances)) {
            Logging::debug("show instances have changed.");
            $outdated = true;
        }

        return $outdated;
    }

    public function getItems()
    {
    	//rows to return back to datatables.
        $display_items = array();

        $shows = array();
        $showInstance = array();
        if ($this->opts["myShows"] === 1) {
            $shows = $this->getUsersShows();
        } 
        elseif ($this->opts["showFilter"] !== 0) {
            $shows[] = $this->opts["showFilter"];
        } 
        elseif ($this->opts["showInstanceFilter"] !== 0) {
            $showInstance[] = $this->opts["showInstanceFilter"];
        }

        //Logging::enablePropelLogging();
        
        $scheduledShows = Application_Model_Schedule::GetScheduleDetailItems(
            $this->startDT, $this->endDT, false, $shows, $showInstance);
         
        //Logging::info($scheduledShows);
        
        foreach ($scheduledShows as $scheduleShow) {
        	
        	//keep track of the show instance ids for update requests.
			$this->showInstances[] = $scheduleShow->getDbId();
        	
            $header = $this->makeHeaderRow($scheduleShow);
            $display_items[] = $header;

            $content = $this->makeScheduledItemRows($scheduleShow);
            $display_items = array_merge($display_items, $content);
         
        	$footer = $this->makeFooterRow($scheduleShow);
        	$display_items[]= $footer;
        }
        
        //Logging::disablePropelLogging();
        
        return array(
            "schedule" => $display_items,
            "showInstances" => $this->showInstances
        );
    }
}
