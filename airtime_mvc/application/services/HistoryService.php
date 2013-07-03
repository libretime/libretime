<?php

class Application_Service_HistoryService
{
	private $con;
	private $timezone;
	
	public function __construct()
	{
		$this->con = isset($con) ? $con : Propel::getConnection(CcPlayoutHistoryPeer::DATABASE_NAME);
		$this->timezone    = date_default_timezone_get();
	}
	
	public function insertPlayedItem($schedId) {
		
		$this->con->beginTransaction();
		
		try {
			
			$item = CcScheduleQuery::create()->findPK($schedId, $this->con);
			
			//TODO figure out how to combine these all into 1 query.
			$showInstance = $item->getCcShowInstances($this->con);
			$show = $showInstance->getCcShow($this->con);
			
			$fileId = $item->getDbFileId();
			
			//don't add webstreams
			if (isset($fileId)) {
				
				//$starts = $item->getDbStarts(null);
				//$ends = $item->getDbEnds(null);
				
				$metadata = array();
				//$metadata["date"] = $starts->format('Y-m-d');
				//$metadata["start"] = $starts->format('H:i:s');
				//$metadata["end"] = $ends->format('H:i:s');
				$metadata["showname"] = $show->getDbName();
				
				$history = new CcPlayoutHistory();
				$history->setDbFileId($fileId);
				$history->setDbStarts($item->getDbStarts(null));
				$history->setDbEnds($item->getDbEnds(null));
				
				foreach ($metadata as $key => $val) {
					$meta = new CcPlayoutHistoryMetaData();
					$meta->setDbKey($key);
					$meta->setDbValue($val);
					
					$history->addCcPlayoutHistoryMetaData($meta);
				}
					
				$history->save($this->con);
			}
			
			$this->con->commit();
		} 
		catch (Exception $e) {
			$this->con->rollback();
			throw $e;
		}
	}

}