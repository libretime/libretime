<?php

use Airtime\MediaItem\PlaylistPeer;

use Airtime\MediaItemQuery;

use Airtime\MediaItem\MediaContent;

class Application_Service_PlaylistService
{
	/*return array (
			"id" => $obj->getId(),
			"cuein" => $obj->getSchedulingCueIn(),
			"cueout" => $obj->getSchedulingCueOut(),
			"fadein" => $obj->getSchedulingFadeIn(),
			"fadeout" => $obj->getSchedulingFadeOut(),
			"length" => $obj->getSchedulingLength(),
			"crossfadeDuration" => 0
		);
	*/
	private function buildContentItem($info) {
		$item = new MediaContent();
		
		$item->setCuein($info["cuein"]);
		$item->setCueout($info["cueout"]);
		$item->setFadein($info["fadein"]);
		$item->setFadeout($info["fadeout"]);
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
			
			$contents = $data["contents"];
			$position = 0;
			foreach ($contents as $item) {
				$mediaContent = $this->buildContentItem($item);
				$mediaContent->setPosition($position);
				$playlist->addMediaContent($mediaContent);
				
				$position++;
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
}