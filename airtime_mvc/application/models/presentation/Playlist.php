<?php

use Airtime\MediaItem\PlaylistPeer;

class Presentation_Playlist {
	
	const LENGTH_FORMATTER_CLASS = "Format_HHMMSSULength";
	
	public function __construct($playlist) {
		
		$this->playlist = $playlist;
	}
	
	public function getId() {
		
		return $this->playlist->getId();
	}
	
	public function getName() {
		
		return $this->playlist->getName();
	}
	
	public function getDescription() {
		
		return $this->playlist->getDescription();
	}
	
	public function getLastModifiedEpoch() {
		
		return $this->playlist->getUpdatedAt("U");
	}
	
	public function getLength() {
		
		$length = $this->playlist->getLength();
		$class = self::LENGTH_FORMATTER_CLASS;
		$formatter = new $class($length);
		
		return $formatter->format(3);
	}
	
	public function hasContent() {
		
		$type = $this->playlist->getClassKey();
		
		return $type === PlaylistPeer::CLASSKEY_0 ? true: false;
	}
	
	public function getContent() {
		
		return $this->playlist->getContents();
	}
	
	public function getRules() {
		
		$rules = new Application_Form_PlaylistRules();
		
		$rules->populate(array(
			//"pl_type" => $this->playlist->getType()		
		));
		
		return $rules;
	}
}