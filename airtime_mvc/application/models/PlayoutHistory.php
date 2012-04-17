<?php

require_once 'formatters/LengthFormatter.php';

class Application_Model_PlayoutHistory {

	private $con;
	private $timezone;
	
	//in UTC timezone
	private $startDT;
	//in UTC timezone
	private $endDT;
	
	private $epoch_now;
	private $opts;
	
	private $mDataPropMap = array(
		"artist" => "file.artist_name",
		"title" => "file.track_title",
		"played" => "playout.played",
		"length" => "file.length",
		"composer" => "file.composer",
		"copyright" => "file.copyright",		
	);
	
	public function __construct($p_startDT, $p_endDT, $p_opts) {
		
		$this->con = Propel::getConnection(CcSchedulePeer::DATABASE_NAME);
		$this->startDT = $p_startDT;
		$this->endDT = $p_endDT;
		$this->timezone = date_default_timezone_get();
		$this->epoch_now = time();
		$this->opts = $p_opts;
	}
	
	/*
	 * map front end mDataProp labels to proper column names for searching etc.
	 */
	private function translateColumns() {
		
		for ($i = 0; $i < $this->opts["iColumns"]; $i++){
			
			$this->opts["mDataProp_{$i}"] = $this->mDataPropMap[$this->opts["mDataProp_{$i}"]];
		}
	}
	
	public function getItems() {
		
		$this->translateColumns();
		
		$select = array( 
			"file.track_title as title",
			"file.artist_name as artist",
			"playout.played",
			"playout.file_id",
			"file.composer",
			"file.copyright",
			"file.length"
		);
		
		$start = $this->startDT->format("Y-m-d H:i:s");
		$end = $this->endDT->format("Y-m-d H:i:s");
		
		$historyTable = "(
			select count(schedule.file_id) as played, schedule.file_id as file_id
			from cc_schedule as schedule
			where schedule.starts >= '{$start}' and schedule.starts < '{$end}'
			    and schedule.playout_status > 0 and schedule.media_item_played != FALSE
			group by schedule.file_id
			)
			AS playout left join cc_files as file on (file.id = playout.file_id)";

		$results = Application_Model_Datatables::findEntries($this->con, $select, $historyTable, $this->opts, "history");
		
		foreach ($results["history"] as &$row) {
			$formatter = new LengthFormatter($row['length']);
			$row['length'] = $formatter->format();
		}
		
		return $results;
	}	
}