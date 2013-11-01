<?php

namespace Airtime\MediaItem\Webstream;

use \Exception;
use \Logging;

class PLSWebstreamParser implements WebstreamParser {
	
	public function getStreamUrl($webstream) {
	
		try {
				
			$content = $webstream->getUrlData();	
			$ini = parse_ini_string($content, true);
			
			//TODO test the stream
			return $ini["playlist"]["File1"];	
		}
		catch (Exception $e) {
			Logging::warn($e->getMessage());
			throw new Exception(_("Could not parse PLS playlist"));
		}
	}
}
