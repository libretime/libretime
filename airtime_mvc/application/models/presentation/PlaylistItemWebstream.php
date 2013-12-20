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
}