<?php

namespace Airtime\MediaItem\Webstream;

use \Exception;
use \Logging;

class XSPFWebstreamParser implements WebstreamParser {
	
	public function getStreamUrl($webstream) {
	
		try {
	
			$content = $webstream->getUrlData();
			
			$dom = new DOMDocument;

	        $dom->loadXML($content);
	        $tracks = $dom->getElementsByTagName('track');
	
	        foreach ($tracks as $track) {
	            $locations = $track->getElementsByTagName('location');
	            foreach ($locations as $loc) {
	                return $loc->nodeValue;
	            }
	        }
		}
		catch (Exception $e) {
			Logging::warn($e->getMessage());
			throw new Exception(_("Could not parse XSPF playlist"));
		}
	}
}
