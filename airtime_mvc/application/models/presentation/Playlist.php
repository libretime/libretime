<?php

use Airtime\MediaItem\Playlist;

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
		
		$formatter = new Format_PlaylistLength($this->playlist);
		return $formatter->getLength();
	}
	
	public function hasContent() {
		
		$type = $this->playlist->getClassKey();
		
		return $type === intval(PlaylistPeer::CLASSKEY_0) ? true: false;
	}
	
	public function getContent() {
		
		if ($this->hasContent()) {
			return $this->playlist->getContents();
		}
	}
	
	public function getRules() {
		
		$form = new Application_Form_PlaylistRules();

		$rules = $this->playlist->getRules();
		
		if (isset($rules[Playlist::RULE_CRITERIA])) {
			$form->buildCriteriaOptions($rules[Playlist::RULE_CRITERIA]);
		}
		
		$criteriaFields = $form->getPopulateHelp();
		
		$playlistRules = array(
			"pl_repeat_tracks" => $rules[Playlist::RULE_REPEAT_TRACKS],
			"pl_my_tracks" => $rules[Playlist::RULE_USERS_TRACKS_ONLY],
			"pl_order_column" => $rules[Playlist::RULE_ORDER][Playlist::RULE_ORDER_COLUMN],
			"pl_order_direction" => $rules[Playlist::RULE_ORDER][Playlist::RULE_ORDER_DIRECTION],
			"pl_limit_value" => $rules["limit"]["value"],
			"pl_limit_options" => $rules["limit"]["unit"]
		);
		
		$data = array_merge($criteriaFields, $playlistRules);
		
		$form->populate($data);
		
		return $form;
	}
}