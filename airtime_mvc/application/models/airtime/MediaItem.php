<?php

namespace Airtime;

use Airtime\om\BaseMediaItem;
use \Application_Service_UserService;
use \Exception;
use \Logging;
use \PropelPDO;

/**
 * Skeleton subclass for representing a row from the 'media_item' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.airtime
 */
class MediaItem extends BaseMediaItem implements \Interface_Schedulable
{
	public function preInsert(PropelPDO $con = null)
	{
		//TODO, don't overwrite owner here if already set (from media monitor request)
		try {
			$service = new Application_Service_UserService();
			$this->setCcSubjs($service->getCurrentUser());
		}
		catch(Exception $e) {
			Logging::warn("Unable to get current user for the inserted media item");
		}

		//have to return true for the insert to work.
		return true;
	}
	
	/*
	 * only users who are admins, PMs or owners of the media can delete the item.
	 * if the item is scheduled in the future it cannot be deleted.
	 */
	public function preDelete(PropelPDO $con = null)
	{
		try {
			$service = new Application_Service_UserService();
			$user = $service->getCurrentUser();

			$userTest =  $user->isAdminOrPM() || $user->getId() === $this->getOwnerId();
			$scheduleTest = $this->isScheduledInFuture();
			
			return ($userTest && $scheduleTest);
		}
		catch(Exception $e) {
			Logging::warn("Failed to delete media item {$this->getId()}");
			Logging::warn($e->getMessage());
		}
		
		return false;
	}
	
	public function getType() {
		$class = $this->getDescendantClass();
		$a = explode("\\", $class);
		
		return array_pop($a);
	}
	
	public function getCreator() {
		$obj = $this->getChildObject();
		return $obj->getCreator();
	}
	
	/*
	 * TODO if we change to not unwrapping playlists/blocks this method should be used to get the info
	 * needed when scheduling shows. 
	 * The scheduler itself will then unroll content as needed when sending the information to pypo.
	 * Otherwise in the meantime we must implement another method that unwraps playlists and blocks.
	 */
	public function getSchedulingInfo() {
		$obj = $this->getChildObject();
		
		return array (
			"id" => $obj->getId(),
			"cuein" => $obj->getSchedulingCueIn(),
			"cueout" => $obj->getSchedulingCueOut(),
			"fadein" => $obj->getSchedulingFadeIn(),
			"fadeout" => $obj->getSchedulingFadeOut(),
			"length" => $obj->getSchedulingLength(),
			"crossfadeDuration" => 0
		);	
	}
	
	/*
	 * TODO remove this method and just rely on method getSchedulingInfo
	 * in the future if we don't unroll playlists/blocks when scheduling them.
	 */
	public function getScheduledContent() {
		$obj = $this->getChildObject();
		
		return $obj->getScheduledContent();
	}
	
	public function isSchedulable() {
		
		$obj = $this->getChildObject();
		return $obj->isSchedulable();
	}
	
	public function isScheduled() {
		
		$count = CcScheduleQuery::create()
			->filterByMediaItem($this)
			->count();
		
		return ($count > 0) ? true : false;
	}
	
	public function isScheduledInFuture() {
		
		$utcNow = new DateTime("now", new DateTimeZone("UTC"));
		
		$count = CcScheduleQuery::create()
			->filterByMediaItem($this)
			->filterByDbStarts($utcNow->format("Y-m-d H:i:s"), Criteria::GREATER_EQUAL)
			->count();
		
		return ($count > 0) ? true : false; 
	}
	
	public function getSchedulingLength() {
		
		$obj = $this->getChildObject();
		return $obj->getSchedulingLength();
	}
	
	public function getSchedulingCueIn() {
		
		$obj = $this->getChildObject();
		return $obj->getSchedulingCueIn();
	}
	
	public function getSchedulingCueOut() {
		
		$obj = $this->getChildObject();
		return $obj->getSchedulingCueOut();
	}
	
	public function getSchedulingFadeIn() {
		
		$obj = $this->getChildObject();
		return $obj->getSchedulingFadeIn();
	}
	
	public function getSchedulingFadeOut() {
		
		$obj = $this->getChildObject();
		return $obj->getSchedulingFadeOut();
	}
}
