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
class MediaItem extends BaseMediaItem
{
	public function preInsert(PropelPDO $con = null)
	{
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
}
