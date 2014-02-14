<?php

use Airtime\MediaItem\MediaContentQuery;

use Airtime\MediaItem\PlaylistPeer;

use Airtime\MediaItemQuery;

use Airtime\MediaItem\MediaContent;

class Application_Service_PlaylistService
{
	private function buildContentItem($info, $position) {
		$item = new MediaContent();
		
		$defaultCrossfade = Application_Model_Preference::GetDefaultCrossfadeDuration();
		$defaultFadeIn = Application_Model_Preference::GetDefaultFadeIn();
		$defaultFadeOut = Application_Model_Preference::GetDefaultFadeOut();
		
		if (isset($info["cuein"])) {
			$item->setCuein($info["cuein"]);
		}
		if (isset($info["cueout"])) {
			$item->setCueout($info["cueout"]);
		}
		
		$fadeIn = (isset($info["fadein"])) ? $info["fadein"] : $defaultFadeIn;
		$item->setFadein($fadeIn);

		$fadeOut = (isset($info["fadeout"])) ? $info["fadeout"] : $defaultFadeOut;
		$item->setFadeout($fadeOut);

		$item->generateCliplength();
		
		//need trackoffset to be zero for the first item.
		if ($position !== 0) {
			$item->setTrackOffset($defaultCrossfade);
		}
		
		$item->setMediaId($info["id"]);
		$item->setPosition($position);
		
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

				$mediaContent = $this->buildContentItem($info, $position);
				$mediaContent->setPlaylist($playlist);
				$mediaContent->save($con);
				
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
			
			$playlist->getMediaContents(null, $con)->delete($con);
			
			$playlist->setName($data["name"]);
			$playlist->setDescription($data["description"]);
			
			$contents = isset($data["contents"]) ? $data["contents"] : array();
			$position = 0;
			$m = array();
			foreach ($contents as $item) {
				
				$mediaContent = $this->buildContentItem($item, $position);
				$mediaContent->setPlaylist($playlist);
				
				$res = $mediaContent->validate();
				if ($res === true) {
					$m[] = $mediaContent;
				}
				else {
					Logging::info($res);
					throw new Exception("invalid media content");
				}
				
				$position++;
				
				//save each content item in the transaction
				//first so that Playlist preSave can calculate
				//the new playlist length properly.
				$mediaContent->save($con);
			}
			
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
			MediaContentQuery::create(null, $con)
				->filterByPlaylist($playlist)
				->delete($con);
			
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
	
	public function createContextMenu($playlist) {
	
		$id = $playlist->getId();
		
		$menu = array();
	
		$menu["preview"] = array(
			"name" => _("Preview"),
			"icon" => "play",
			"id" => $id,
			"callback" => "previewItem"
		);
		
		$menu["edit"] = array(
			"name"=> _("Edit"),
			"icon" => "edit",
			"id" => $id,
			"callback" => "openPlaylist"
		);
	
		$menu["delete"] = array(
			"name" => _("Delete"),
			"icon" => "delete",
			"id" => $id,
			"callback" => "deleteItem"
		);
	
		return $menu;
	}
}