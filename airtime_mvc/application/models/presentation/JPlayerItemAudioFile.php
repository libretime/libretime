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
			$mime => $this->media->getURI()
		    //"mp3" => "http://sourcefabric.airtime.pro/airtime_web_241/public/api/get-media/file/6864.mp3"
		);

		return array($item);
	}
}