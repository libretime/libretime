<?php

abstract class Presentation_PlaylistItem {
	
	const LENGTH_FORMATTER_CLASS = "Format_HHMMSSULength";
	
	public function __construct($mediaContent)
	{
		$this->content = $mediaContent;
		$this->item = $mediaContent->getMediaItem()->getChildObject();
	}
	
	public function getId() {
		return $this->content->getId();
	}
	
	public function getMediaId() {
		return $this->item->getId();
	}
	
	public function getMime() {
		return $this->item->getMime();
	}
	
	abstract protected function canEditCues();
	abstract protected function canEditFades();
	abstract protected function canPreview();
	abstract protected function getTitle();
	abstract protected function getCreator();
	
	public function getCliplength() {
		$length = $this->content->getCliplength();
		$class = self::LENGTH_FORMATTER_CLASS;
		$formatter = new $class($length);
		return $formatter->format();
	}
	
	public function getCueIn() {
		return $this->content->getCueIn();
	}
	
	public function getCueOut() {
		return $this->content->getCueOut();
	}
	
	public function getFadeIn() {
		return $this->content->getFadeIn();
	}
	
	public function getFadeOut() {
		return $this->content->getFadeOut();
	}
	
	public function getLength() {
		$length = $this->item->getLength();
		$class = self::LENGTH_FORMATTER_CLASS;
		$formatter = new $class($length);
		return $formatter->format(3);
	}
	
	public function getCueInSec() {
		$cuein = $this->content->getCueIn();
		return Application_Common_DateHelper::playlistTimeToSeconds($cuein);
	}
	
	public function getCueOutSec() {
		$cueout = $this->content->getCueOut();
		return Application_Common_DateHelper::playlistTimeToSeconds($cueout);
	}
}