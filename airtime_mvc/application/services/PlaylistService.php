<?php

use Airtime\MediaItem\MediaContentQuery;

use Airtime\MediaItem\PlaylistPeer;

use Airtime\MediaItemQuery;

use Airtime\MediaItem\MediaContent;

class Application_Service_PlaylistService
{

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
	
	public function createPlaylist($type) {
		
		switch($type) {
			case PlaylistPeer::CLASSKEY_0:
				$class = PlaylistPeer::CLASSNAME_0;
				return new $class();
				break;
			case PlaylistPeer::CLASSKEY_1:
			default:
				$class = PlaylistPeer::CLASSNAME_1;
				return new $class();
				break;
				
		}
	}
}