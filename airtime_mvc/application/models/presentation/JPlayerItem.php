<?php

abstract class Presentation_JPlayerItem {
	
	private $_jPlayerPlaylist = null;
	
	public function __construct($mediaItem)
	{
		$this->media = $mediaItem;
	}
	
	abstract protected function hasMultiple();
	abstract protected function hasDuration();
	abstract protected function compute();
	
	/*
	 * @return string a MIME that jPlayer will understand.
	 */
	protected function convertMime() {
	
		$key = null;
		
		$mime = $this->media->getMime();
		
		if (preg_match("/mp3/i", $mime) || preg_match("/mpeg/i", $mime)) {
			$key = "mp3";
		}
		else if (preg_match("/og(g|a)/i", $mime) || preg_match("/vorbis/i", $mime)) {
			$key = "oga";
		}
		else if (preg_match("/mp4/i", $mime)) {
			$key = "m4a";
		}
		else if (preg_match("/wav/i", $mime)) {
			$key = "wav";
		}
		else if (preg_match("/flac/i", $mime)) {
			$key = "flac";
		}
		
		return $key;
	}
	
	public function getJPlayerPlaylist() {
		
		if (is_null($this->_jPlayerPlaylist)) {
			$this->_jPlayerPlaylist = $this->compute();
		}
		
		return $this->_jPlayerPlaylist;
	}
}