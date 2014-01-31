<?php

class Presentation_JPlayerItemPlaylist extends Presentation_JPlayerItem
{
	public function hasMultiple() {
		return true;
	}
	
	public function hasDuration() {
		//TODO check if something in it actually has duration.
		//what happens if it only contains webstreams?
		return true;
	}
}