<?php

use Airtime\PlayoutHistory\CcPlayoutHistory;
use Airtime\PlayoutHistory\CcPlayoutHistoryQuery;
use Airtime\PlayoutHistory\CcPlayoutHistoryMetaData;
use Airtime\CcScheduleQuery;

class Strategy_WebstreamHistoryItem implements Strategy_HistoryItem
{
	public function insertHistoryItem($schedId, $con, $opts) {
	
		$con->beginTransaction();
	
		try {
			
			//notifyMediaItemStartPlayAction will also be called with webstream data, but nothing should be logged here,
			//notifyWebstreamDataAction should take care of it.
			if (isset($opts[HISTORY_ITEM_STARTS])) {
				$start = $opts[HISTORY_ITEM_STARTS];
			}
			else {
				throw new Exception("Must give a history item start time.");
			}
				
			$item = CcScheduleQuery::create()
				->filterByPrimaryKey($schedId)
				->joinWith("MediaItem", Criteria::LEFT_JOIN)
				->findOne($con);
	
			$mediaItem = $item->getMediaItem($con);
				
			$metadata = array();
			$metadata[MDATA_KEY_TITLE] = $opts[MDATA_KEY_TITLE];
			$metadata[MDATA_KEY_CREATOR] = $mediaItem->getName();
			
			$end = isset($opts[HISTORY_ITEM_ENDS]) ? $opts[HISTORY_ITEM_ENDS] : null;
				
			$history = new CcPlayoutHistory();
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
		catch (Exception $e) {
			$con->rollback();
			throw $e;
		}
	}
}