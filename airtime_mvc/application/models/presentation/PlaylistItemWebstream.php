<?php

class Presentation_PlaylistItemWebstream extends Presentation_PlaylistItem
{
	public function canEditCues() {
		return false;
	}
	
	public function canEditFades() {
		return false;
	}
	
	public function canPreview() {
		return true;
	}
	
	public function getTitle() {
		return $this->item->getName();
	}
	
	public function getCreator() {
		return $this->item->getUrl();
	}
	
	public function getCliplength() {
		$length = $this->item->getLength();
		$class = self::LENGTH_FORMATTER_CLASS;
		$formatter = new $class($length);
		return $formatter->format();
	}
}