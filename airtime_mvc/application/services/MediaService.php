<?php

use Airtime\CcSubjsPeer;
use Airtime\MediaItem\WebstreamPeer;
use Airtime\MediaItem\PlaylistPeer;
use Airtime\MediaItem\AudioFilePeer;

use Airtime\MediaItem\AudioFileQuery;
use Airtime\MediaItem\WebstreamQuery;
use Airtime\MediaItem\PlaylistQuery;
use Airtime\MediaItemQuery;

class Application_Service_MediaService
{
	public function setSessionMediaObject($obj) {

		$obj_sess = new Zend_Session_Namespace(UI_PLAYLISTCONTROLLER_OBJ_SESSNAME);

		if (is_null($obj)) {
			unset($obj_sess->id);
		}
		else {
			$obj_sess->id = $obj->getId();
		}
	}

	public function getSessionMediaObject() {

		$obj_sess = new Zend_Session_Namespace(UI_PLAYLISTCONTROLLER_OBJ_SESSNAME);
		//some type of media is in the session
		if (isset($obj_sess->id)) {
			$obj = MediaItemQuery::create()->findPk($obj_sess->id);
			
			if (isset($obj) && $obj->getType() === "Playlist") {
				return $obj->getChildObject();
			}
			else {
				$obj_sess->id = null;
			}
		}
	}

	public function createLibraryColumnsJavascript() {

		//set audio columns for display of data.
		$datatablesService = new Application_Service_DatatableAudioFileService();
		$columns = json_encode($datatablesService->makeDatatablesColumns());
		$script = "localStorage.setItem( 'datatables-audio-aoColumns', JSON.stringify($columns) ); ";

		//set webstream columns for display of data.
		$datatablesService = new Application_Service_DatatableWebstreamService();
		$columns = json_encode($datatablesService->makeDatatablesColumns());
		$script .= "localStorage.setItem( 'datatables-webstream-aoColumns', JSON.stringify($columns) ); ";

		//set playlist columns for display of data.
		$datatablesService = new Application_Service_DatatablePlaylistService();
		$columns = json_encode($datatablesService->makeDatatablesColumns());
		$script .= "localStorage.setItem( 'datatables-playlist-aoColumns', JSON.stringify($columns) ); ";

		return $script;
	}

	public function createLibraryColumnSettingsJavascript() {

		$script = "";

		$settings = Application_Model_Preference::getAudioTableSetting();
        if (!is_null($settings)) {
            $data = json_encode($settings);
            $script .= "localStorage.setItem( 'datatables-audio', JSON.stringify($data) ); ";
        }
        else {
        	$script .= "localStorage.setItem( 'datatables-audio', null ); ";
        }

        $settings = Application_Model_Preference::getWebstreamTableSetting();
        if (!is_null($settings)) {
        	$data = json_encode($settings);
        	$script .= "localStorage.setItem( 'datatables-webstream', JSON.stringify($data) ); ";
        }
        else {
        	$script .= "localStorage.setItem( 'datatables-webstream', null ); ";
        }

        $settings = Application_Model_Preference::getPlaylistTableSetting();
        if (!is_null($settings)) {
        	$data = json_encode($settings);
        	$script .= "localStorage.setItem( 'datatables-playlist', JSON.stringify($data) ); ";
        }
        else {
        	$script .= "localStorage.setItem( 'datatables-playlist', null ); ";
        }

		return $script;
	}

	/*
	 * @param $obj MediaItem object.
	 * @return $service proper service for this item type.
	 */
	public function locateServiceType($obj) {

		$class = $obj->getDescendantClass();
		$class = explode("\\", $class);
		$type = array_pop($class);

		$serviceClass = "Application_Service_{$type}Service";
		return new $serviceClass();
	}

	public function createContextMenu($mediaId) {

		$mediaItem = MediaItemQuery::create()->findPK($mediaId);
		$obj = $mediaItem->getChildObject();

		$service = self::locateServiceType($mediaItem);

		return $service->createContextMenu($obj);
	}

	public function getJPlayerPreviewPlaylist($mediaId) {

		$mediaItem = MediaItemQuery::create()->findPK($mediaId);

		$type = $mediaItem->getType();

		$class = "Presentation_JPlayerItem{$type}";

		$jPlayerPlaylist = new $class($mediaItem);

		return $jPlayerPlaylist;
	}
}