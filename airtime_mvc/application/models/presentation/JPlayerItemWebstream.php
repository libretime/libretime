<?php

class Presentation_JPlayerItemWebstream extends Presentation_JPlayerItem
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
            //"artist" => "Sourcefabric",
            //$mime => "http://sourcefabric.out.airtime.pro:8000/sourcefabric_b"
	    	//$mime => "http://206.190.135.28:8048/"
	    );

	    return array($item);
	}
}