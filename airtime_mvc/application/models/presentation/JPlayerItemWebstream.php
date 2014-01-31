<?php

class Presentation_JPlayerItemWebstream extends Presentation_JPlayerItem
{
	public function hasMultiple() {
		return false;
	}
	
	public function hasDuration() {
		return false;
	}
}