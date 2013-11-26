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
	
	public function preDelete(PropelPDO $con = null)
	{
		try {
			$service = new Application_Service_UserService();
			$user = $service->getCurrentUser();
			
			//only users who are admins, PMs or owners of the media can delete the item.
			return $user->isAdminOrPM() || $user->getId() === $this->getOwnerId();
		}
		catch(Exception $e) {
			Logging::warn("Unable to get current user while trying to delete media item {$this->getId()}");
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
	
	
	public function getSchedulingInfo() 
	{
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
	
	public function isSchedulable() {
		
		$obj = $this->getChildObject();
		return $obj->isSchedulable();
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
