<?php

namespace Airtime\MediaItem\Webstream;

use \Exception;
use \Logging;

class M3UWebstreamParser implements WebstreamParser {
	
	public function getStreamUrl($webstream) {
		
		try {
			$content = $webstream->getUrlData();
			
			//split into lines:
			$delim = "\n";
			if (strpos($content, "\r\n") !== false) {
				$delim = "\r\n";
			}
			
			$lines = explode($delim, $content);
			
			//TODO test the stream
			return $lines[0];
			
		}
		catch (Exception $e) {
			Logging::warn($e->getMessage());
			throw new Exception(_("Could not parse M3U playlist"));
		}
	}
}