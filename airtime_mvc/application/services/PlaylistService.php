<?php

use Airtime\MediaItem\MediaContentQuery;
use Airtime\MediaItem\MediaContent;
use Airtime\MediaItem\PlaylistPeer;
use Airtime\MediaItem\Playlist;
use Airtime\MediaItemQuery;

class Application_Service_PlaylistService
{
	public function createContextMenu($playlist) {
	
		$id = $playlist->getId();
		
		$menu = array();
	
		if ($playlist->isStatic()) {
			$menu["preview"] = array(
				"name" => _("Preview"),
				"icon" => "play",
				"id" => $id,
				"callback" => "previewItem"
			);
		}

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
	
	public function savePlaylist($playlist, $info, $con) {
		
		$con->beginTransaction();
		 
		try {
			if (isset($info["name"])) {
				$playlist->setName($info["name"]);
			}
			
			if (isset($info["description"])) {
				$playlist->setDescription($info["description"]);
			}
			
			if (isset($info["rules"])) {
				
				$rules = $info["rules"];
				
				$form = new Application_Form_PlaylistRules();
				
				if (isset($info["rules"]["criteria"])) {
					$form->buildCriteriaOptions($info["rules"]["criteria"]);
				}
				
				$criteriaFields = $form->getPopulateHelp();
				
				$playlistRules = array(
					"pl_repeat_tracks" => $rules[Playlist::RULE_REPEAT_TRACKS],
					"pl_my_tracks" => $rules[Playlist::RULE_USERS_TRACKS_ONLY],
					"pl_order_column" => $rules[Playlist::RULE_ORDER][Playlist::RULE_ORDER_COLUMN],
					"pl_order_direction" => $rules[Playlist::RULE_ORDER][Playlist::RULE_ORDER_DIRECTION],
					"pl_limit_value" => $rules["limit"]["value"],
					"pl_limit_options" => $rules["limit"]["unit"]
				);
				
				$data = array_merge($criteriaFields, $playlistRules);
				
				if ($form->isValid($data)) {
					Logging::info("playlist rules are valid");
					Logging::info($form->getValues());
					$playlist->setRules($info["rules"]);
				}
				else {
					Logging::info("invalid playlist rules");
					Logging::info($form->getMessages());
				}
			}
			
			//only save content for static playlists
			if ($playlist->isStatic()) {
				$content = isset($info["content"]) ? $info["content"] : array();
				$playlist->savePlaylistContent($con, $content, true);
			}
			
			$playlist->save($con); 
			$con->commit();
		}
		catch (Exception $e) {
			$con->rollBack();
			throw $e;
		}	
	}
}