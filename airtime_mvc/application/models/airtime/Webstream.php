<?php

namespace Airtime\MediaItem;

use Airtime\MediaItem\om\BaseWebstream;
use \PropelPDO;


/**
 * Skeleton subclass for representing a row from the 'webstream' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.airtime
 */
class Webstream extends BaseWebstream
{
	public function preInsert(PropelPDO $con = null) {
		
		parent::preInsert($con);
		
		try {
			$creator = $this->getCcSubjs()->getDbLogin();
			$this->setCreator($creator);
		}
		catch(Exception $e) {
			Logging::warn("Unable to set the creator for the webstream");
		}
		
		return true;
	}
	
	public function getHoursMins() {

		return explode(":", $this->getLength());
	}
	
	public function getURI() {
		return $this->getUrl();
	}
	
	public function getUrlData() {
		
		$url = $this->getUrl();
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	
		// grab URL and pass it to the browser
		//TODO: What if invalid url?
		$content = curl_exec($ch);
	
		// close cURL resource, and free up system resources
		curl_close($ch);
	
		return $content;
	}
	
	public function setUrl($v) {
		
		parent::setUrl($v);
		
		//get an associative array of headers.
		$headers = get_headers($v, 1);
		$mime = $headers["Content-Type"];
		
		$this->setMime($mime);
		$this->setSource($v);
		
		return $this;
	}
	
	public function isScheduable() {
		return true;
	}
	
	public function getSchedulingLength() {
		return $this->getLength();
	}
	
	public function getSchedulingCueIn() {
		return "00:00:00";
	}
	
	public function getSchedulingCueOut() {
		return $this->getLength();
	}
	
	public function getSchedulingFadeIn() {
		return \Application_Model_Preference::GetDefaultFadeIn();
	}
	
	public function getSchedulingFadeOut() {
		return \Application_Model_Preference::GetDefaultFadeOut();
	}
	
	public function getScheduledContent(PropelPDO $con = null) {
	
		return array(
			array (
				"id" => $this->getId(),
				"cliplength" => $this->getSchedulingLength(),
				"cuein" => $this->getSchedulingCueIn(),
				"cueout" => $this->getSchedulingCueOut(),
				"fadein" => $this->getSchedulingFadeIn(),
				"fadeout" => $this->getSchedulingFadeOut(),
			)
		);
	}
}
