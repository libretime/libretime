<?php

class Presentation_PlaylistItemAudioFile extends Presentation_PlaylistItem
{
	
	public function canEditCues() {
		return true;
	}
	
	public function canEditFades() {
		return true;
	}
	
	public function canPreview() {
		return $this->item->isSchedulable();
	}
	
	public function getTitle() {
		return $this->item->getTrackTitle();
	}
	
	public function getCreator() {
		return $this->item->getCreator();
	}
	
	public function getCliplength() {
		$length = $this->item->getCueLength();
		$class = self::LENGTH_FORMATTER_CLASS;
		$formatter = new $class($length);
		return $formatter->format();
	}
	
	public function getCueIn() {
		return $this->item->getSchedulingCueIn();
	}
	
	public function getCueOut() {
		return $this->item->getSchedulingCueOut();
	}
	
	public function getFadeIn() {
		$fade = $this->item->getSchedulingFadeIn();
		
		$f = explode(":", $fade);
		$f = array_pop($f);
		return floatval($f);
	}
	
	public function getFadeOut() {
		$fade = $this->item->getSchedulingFadeOut();
		
		$f = explode(":", $fade);
		$f = array_pop($f);
		return floatval($f);
	}
	
	public function getLength() {
		$length = $this->item->getLength();
		$class = self::LENGTH_FORMATTER_CLASS;
		$formatter = new $class($length);
		return $formatter->format(3);
	}
	
	public function getCueInSec() {
		$cuein = $this->item->getSchedulingCueIn();
		return Application_Common_DateHelper::playlistTimeToSeconds($cuein);
	}
	
	public function getCueOutSec() {
		$cueout = $this->item->getSchedulingCueOut();
		return Application_Common_DateHelper::playlistTimeToSeconds($cueout);
	}
	
	public function getUrl() {
		return $this->item->getFileUrl();
	}
}