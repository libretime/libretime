<?php

namespace Airtime\MediaItem\Webstream;

interface WebstreamParser {
	
	//parses a m3u, pls, xspf etc format to get a valid stream url
	public function getStreamUrl($webstream);
	
}