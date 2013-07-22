<?php

require_once 'formatters/LengthFormatter.php';

class Application_Service_HistoryService
{
	private $con;
	private $timezone;

	private $mDataPropMap = array (
		"artist" => "artist_name",
		"title" => "track_title",
		"played" => "played",
		"length" => "length",
		"composer" => "composer",
		"copyright" => "copyright",
	    "starts" => "starts",
	    "ends" => "ends"
	);

	public function __construct()
	{
		$this->con = isset($con) ? $con : Propel::getConnection(CcPlayoutHistoryPeer::DATABASE_NAME);
		$this->timezone = date_default_timezone_get();
	}

	/*
	 * map front end mDataProp labels to proper column names for searching etc.
	*/
	private function translateColumns($opts)
	{
		for ($i = 0; $i < $opts["iColumns"]; $i++) {

			if ($opts["bSearchable_{$i}"] === "true") {
				$opts["mDataProp_{$i}"] = $this->mDataPropMap[$opts["mDataProp_{$i}"]];
			}
		}
	}

	public function getListView($startDT, $endDT, $opts)
	{
	    $this->translateColumns($opts);

	    $select = array (
	        "file.track_title as title",
	        "file.artist_name as artist",
	        "playout.starts",
	        "playout.ends",
	        "playout.history_id"
	    );

	    $start = $startDT->format("Y-m-d H:i:s");
	    $end = $endDT->format("Y-m-d H:i:s");

	    $historyTable = "(
	    select history.starts as starts, history.ends as ends,
	    history.id as history_id, history.file_id as file_id
	    from cc_playout_history as history
	    where history.starts >= '{$start}' and history.starts < '{$end}'
	    ) AS playout
	    left join cc_files as file on (file.id = playout.file_id)";

	    $results = Application_Model_Datatables::findEntries($this->con, $select, $historyTable, $opts, "history");

	    return $results;
	}

	public function getAggregateView($startDT, $endDT, $opts)
	{
		$this->translateColumns($opts);

		$select = array (
			"file.track_title as title",
			"file.artist_name as artist",
			"playout.played as played",
			"playout.file_id",
			"file.composer as composer",
			"file.copyright as copyright",
			"file.length as length"
		);

		$start = $startDT->format("Y-m-d H:i:s");
		$end = $endDT->format("Y-m-d H:i:s");

		$historyTable = "(
			select count(history.file_id) as played, history.file_id as file_id
			from cc_playout_history as history
			where history.starts >= '{$start}' and history.starts < '{$end}'
			group by history.file_id
		) AS playout
		left join cc_files as file on (file.id = playout.file_id)";

		$results = Application_Model_Datatables::findEntries($this->con, $select, $historyTable, $opts, "history");

		foreach ($results["history"] as &$row) {
			$formatter = new LengthFormatter($row['length']);
			$row['length'] = $formatter->format();
		}

		return $results;
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

	/* id is an id in cc_playout_history */
	public function makeHistoryItemForm($id) {

	}

	/* id is an id in cc_files */
	public function makeHistoryFileForm($id) {

	    try {
		    $form = new Application_Form_EditHistoryFile();

		    $file = Application_Model_StoredFile::RecallById($id, $this->con);
		    $md = $file->getDbColMetadata();

		    $form->populate(array(
		        'his_file_id' => $id,
		        'his_file_title' => $md[MDATA_KEY_TITLE],
		        'his_file_creator' => $md[MDATA_KEY_CREATOR],
		        'his_file_composer' => $md[MDATA_KEY_COMPOSER],
		        'his_file_copyright' => $md[MDATA_KEY_COPYRIGHT]
		    ));

		    return $form;
	    }
	    catch (Exception $e) {
	        Logging::info($e);
	    }
	}

	public function createPlayedItem() {

	}

	/* id is an id in cc_playout_history */
	public function editPlayedItem($id) {

	}

	/* id is an id in cc_files */
	public function editPlayedFile($data) {

        $form = new Application_Form_EditHistoryFile();

        $json = $form->processAjax($data);
        Logging::info($json);

        if ($form->isValid($data)) {

            $id = $data["his_file_id"];
            $file = Application_Model_StoredFile::RecallById($id, $this->con);

            $md = array(
                MDATA_KEY_TITLE => $data['his_file_title'],
                MDATA_KEY_CREATOR => $data['his_file_creator'],
                MDATA_KEY_COMPOSER => $data['his_file_composer'],
                MDATA_KEY_COPYRIGHT => $data['his_file_copyright']
            );

            $file->setDbColMetadata($md);
        }

        return $json;
	}

}