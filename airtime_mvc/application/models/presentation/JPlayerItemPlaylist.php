<?php

class Presentation_JPlayerItemPlaylist extends Presentation_JPlayerItem
{
	protected function compute() {
		
		$playlist = $this->media->getChildObject();
		$contents = $playlist->getContents();
		$jPlayerContents = array();
		
		foreach ($contents as $content) {
			
			$mediaItem = $content->getMediaItem();
			$type = $mediaItem->getType();
			$class = "Presentation_JPlayerItem{$type}";
			
			$jPlayerItem = new $class($mediaItem);
			$jPlayerContents = array_merge($jPlayerContents, $jPlayerItem->compute());
		}
	
		return $jPlayerContents;
	}
}