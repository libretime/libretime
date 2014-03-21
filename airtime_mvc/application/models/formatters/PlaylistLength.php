<?php

class Format_PlaylistLength
{
	/**
	 * @string length
	 */
	private $_playlist;
	
	public function __construct($playlist)
	{
		$this->_playlist = $playlist;
	}
	
	public function getLength()
	{
		if ($this->_playlist->isStatic()) {
			$formatter = new Format_HHMMSSULength($this->_playlist->getLength());
			return $formatter->format();
		}
		else {
			$rules = $this->_playlist->getRules();
			$value = $rules["limit"]["value"];
			$unit = $rules["limit"]["unit"];
			$unit = _($unit);
				
			return "~ {$value} {$unit}";
		}
	}
}