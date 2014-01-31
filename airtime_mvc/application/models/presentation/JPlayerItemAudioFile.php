<?php

class Presentation_JPlayerItemAudioFile extends Presentation_JPlayerItem
{
	public function hasMultiple() {
		return false;
	}
	
	public function hasDuration() {
		return true;
	}
	
	protected function compute() {
		
		$mime = parent::convertMime();
		
		if (is_null($mime)) {
			return array();
		}
		
		$item =  array(
			"title" => $this->media->getName(),
			"artist" => $this->media->getCreator(),
			$mime => $this->media->getURI()
		);
			
		return array($item);
	}
}