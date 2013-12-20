<?php

use Airtime\MediaItem\PlaylistPeer;

use Airtime\MediaItemQuery;

use Airtime\MediaItem\MediaContent;

class Application_Service_PlaylistService
{
	private function buildContentItem($info) {
		$item = new MediaContent();
		
		if (isset($info["cuein"])) {
			$item->setCuein($info["cuein"]);
		}
		if (isset($info["cueout"])) {
			$item->setCueout($info["cueout"]);
		}
		if (isset($info["fadein"])) {
			$item->setFadein($info["fadein"]);
		}
		if (isset($info["fadeout"])) {
			$item->setFadeout($info["fadeout"]);
		}
		
		$item->generateCliplength();
		$item->setMediaId($info["id"]);
		
		return $item;
	}
	
	/*
	 * @param $playlist playlist item to add the files to.
	 * @param $ids list of media ids to add to the end of the playlist.
	 */
	public function addMedia($playlist, $ids, $doSave = false) {

		$con = Propel::getConnection(PlaylistPeer::DATABASE_NAME);
		$con->beginTransaction();
		
		Logging::enablePropelLogging();
		
		try {
			$position = $playlist->countMediaContents(null, false, $con);
			$mediaToAdd = MediaItemQuery::create()->findPks($ids, $con);
			
			foreach ($mediaToAdd as $media) {
				$info = $media->getSchedulingInfo();
				Logging::info($info);
				$mediaContent = $this->buildContentItem($info);
				$mediaContent->setPosition($position);
				
				$playlist->addMediaContent($mediaContent);
				
				$position++;
			}

			if ($doSave) {
				$playlist->save($con);
				$con->commit();
			}
		}
		catch (Exception $e) {
			$con->rollBack();
			Logging::error($e->getMessage());
			throw $e;
		}
		
		Logging::disablePropelLogging();
	}
	
	/*
	 * [16] => Array
       (
	       [id] => 5
	       [cuein] => 00:00:00
	       [cueout] => 00:04:12.917551
	       [fadein] => 0.5
	       [fadeout] => 0.5
       )
	 */
	public function savePlaylist($playlist, $data) {
		
		$con = Propel::getConnection(PlaylistPeer::DATABASE_NAME);
		$con->beginTransaction();
		
		Logging::enablePropelLogging();
		
		try {
			
			$playlist->setName($data["name"]);
			$playlist->setDescription($data["description"]);
			
			$contents = isset($data["contents"]) ? $data["contents"] : array();
			$position = 0;
			$m = array();
			foreach ($contents as $item) {
				$mediaContent = $this->buildContentItem($item);
				$mediaContent->setPosition($position);
				
				$res = $mediaContent->validate();
				if ($res === true) {
					$m[] = $mediaContent;
				}
				else {
					Logging::info($res);
					throw new Exception("invalid media content");
				}
				
				$position++;
			}
			
			$c = new PropelCollection($m);
			$playlist->setMediaContents($c, $con);
			$playlist->save($con);
			
			$con->commit();
		}
		catch (Exception $e) {
			$con->rollBack();
			Logging::error($e->getMessage());
			throw $e;
		}
		
		Logging::disablePropelLogging();
	}
	
	public function clearPlaylist($playlist) {
	
		$con = Propel::getConnection(PlaylistPeer::DATABASE_NAME);
		$con->beginTransaction();
	
		Logging::enablePropelLogging();
	
		try {
			$c = new PropelCollection(array());
			$playlist->setMediaContents($c, $con);
			$playlist->save($con);
				
			$con->commit();
		}
		catch (Exception $e) {
			$con->rollBack();
			Logging::error($e->getMessage());
			throw $e;
		}
	
		Logging::disablePropelLogging();
	}
	
	public function shufflePlaylist($playlist) {
	
		$con = Propel::getConnection(PlaylistPeer::DATABASE_NAME);
		$con->beginTransaction();
	
		Logging::enablePropelLogging();
	
		try {
			$contents = $playlist->getContents(null, $con);
			$count = count($contents);
			$order = array();
			
			for ($i = 0; $i < $count; $i++) {
				$order[] = $i;
			}
			shuffle($order);
			
			$i = 0;
			foreach ($contents as $content) {
				$content->setPosition($order[$i]);	
				$i++;
			}
			
			$playlist->setMediaContents($contents, $con);
			$playlist->save($con);
	
			$con->commit();
		}
		catch (Exception $e) {
			$con->rollBack();
			Logging::error($e->getMessage());
			throw $e;
		}
	
		Logging::disablePropelLogging();
	}
	
	public function deletePlaylist($playlist) {
		
		$playlist->delete();
	}
}