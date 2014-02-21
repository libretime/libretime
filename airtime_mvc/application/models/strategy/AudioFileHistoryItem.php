<?php

use Airtime\PlayoutHistory\CcPlayoutHistory;
use Airtime\PlayoutHistory\CcPlayoutHistoryQuery;
use Airtime\PlayoutHistory\CcPlayoutHistoryMetaData;
use Airtime\CcScheduleQuery;

class Strategy_AudioFileHistoryItem implements Strategy_HistoryItem
{
	// Metadata Keys for files
	// user editable metadata
	private $_historyMD = array (
		'MDATA_KEY_TITLE' => "TrackTitle",
		'MDATA_KEY_CREATOR' => "ArtistName",
		'MDATA_KEY_SOURCE' => "AlbumTitle",
		'MDATA_KEY_URL' => "InfoUrl",
		'MDATA_KEY_GENRE' => "Genre",
		'MDATA_KEY_MOOD' => "Mood",
		'MDATA_KEY_LABEL' => "Label",
		'MDATA_KEY_COMPOSER' => "Composer",
		'MDATA_KEY_ISRC' => "IsrcNumber",
		'MDATA_KEY_COPYRIGHT' => "Copyright",
		'MDATA_KEY_YEAR' => "Year",
		'MDATA_KEY_TRACKNUMBER' => "TrackNumber",
		'MDATA_KEY_CONDUCTOR' => "Conductor",
		'MDATA_KEY_LANGUAGE' => "Language",
		'MDATA_KEY_LENGTH' => "Length",
		'MDATA_KEY_ISRC' => "IsrcNumber",
	);
	
	public function insertHistoryItem($schedId, $con, $opts) {
	
		$con->beginTransaction();
	
		try {
	
			$item = CcScheduleQuery::create()
				->filterByPrimaryKey($schedId)
				->joinWith("CcShowInstances", Criteria::LEFT_JOIN)
				->joinWith("MediaItem", Criteria::LEFT_JOIN)
				->joinWith("MediaItem.AudioFile", Criteria::LEFT_JOIN)
				->findOne($con);
	
			$showInstance = $item->getCcShowInstances($con);
			$mediaItem = $item->getMediaItem($con);
			$audiofile = $mediaItem->getChildObject();
	
			$mediaId = $mediaItem->getId();
			$metadata = $audiofile->getMetadata();
			
			$start = isset($opts[HISTORY_ITEM_STARTS]) ? $opts[HISTORY_ITEM_STARTS] : $item->getDbStarts(null);
			
			if (isset($opts[HISTORY_ITEM_ENDS])) {
				$end = $opts[HISTORY_ITEM_ENDS];
			}
			else {
				$instanceEnd = $showInstance->getDbEnds(null);
				$itemEnd = $item->getDbEnds(null);
				$end = ($instanceEnd < $itemEnd) ? $instanceEnd : $itemEnd;
			}

			//first check if this is a duplicate
			// (caused by restarting liquidsoap)

			$prevRecord = CcPlayoutHistoryQuery::create()
				->filterByDbStarts($start)
				->filterByDbEnds($end)
				->filterByDbMediaId($mediaId)
				->findOne($con);

			if (empty($prevRecord)) {
					
				$history = new CcPlayoutHistory();
				$history->setDbMediaId($mediaId);
				$history->setDbStarts($start);
				$history->setDbEnds($end);
				$history->setDbInstanceId($item->getDbInstanceId());
					
				foreach ($metadata as $key => $val) {
					$meta = new CcPlayoutHistoryMetaData();
					$meta->setDbKey($key);
					$meta->setDbValue($val);
						
					$history->addCcPlayoutHistoryMetaData($meta);
				}
					
				$history->save($con);
				$con->commit();
			}
		}
		catch (Exception $e) {
			$con->rollback();
			throw $e;
		}
	}
}