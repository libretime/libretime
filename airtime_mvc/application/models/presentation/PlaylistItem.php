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
	abstract protected function getCliplength();
}