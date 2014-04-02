<?php

class Presentation_JPlayerItemAudioFile extends Presentation_JPlayerItem
{
	protected function compute() {

		$mime = parent::convertMime($this->media->getMime());

		if (is_null($mime)) {
			return array();
		}

		$item =  array(
			"title" => $this->media->getName(),
			"artist" => $this->media->getCreator(),
			$mime => $this->media->getURI(),
			"mime" => $this->media->getMime()
		);

		return array($item);
	}
}