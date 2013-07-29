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
		$this->timezone = Application_Model_Preference::GetTimezone();
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

	    $timezoneUTC = new DateTimeZone("UTC");
	    $timezoneLocal = new DateTimeZone($this->timezone);

	    //need to display the results in the station's timezone.
	    foreach ($results["history"] as $index => &$result) {

	    	$dateTime = new DateTime($result["starts"], $timezoneUTC);
	    	$dateTime->setTimezone($timezoneLocal);
	    	$result["starts"] = $dateTime->format("Y-m-d H:i:s");

	    	$dateTime = new DateTime($result["ends"], $timezoneUTC);
	    	$dateTime->setTimezone($timezoneLocal);
	    	$result["ends"] = $dateTime->format("Y-m-d H:i:s");
	    }

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

				/*
				foreach ($metadata as $key => $val) {
					$meta = new CcPlayoutHistoryMetaData();
					$meta->setDbKey($key);
					$meta->setDbValue($val);

					$history->addCcPlayoutHistoryMetaData($meta);
				}
				*/

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

		try {
			$form = new Application_Form_EditHistoryItem();
			$template = $this->getItemTemplate();

			$form->createFromTemplate($template);

			return $form;
		}
		catch (Exception $e) {
			Logging::info($e);
			throw $e;
		}
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
	        throw $e;
	    }
	}

	public function populateTemplateItem($values) {

		$this->con->beginTransaction();

		try {
		    $template = $this->getItemTemplate();
		    $prefix = Application_Form_EditHistoryItem::ID_PREFIX;
		    $historyRecord = new CcPlayoutHistory();

		    $timezoneUTC = new DateTimeZone("UTC");
		    $timezoneLocal = new DateTimeZone($this->timezone);

	    	$dateTime = new DateTime($values[$prefix."starts"], $timezoneLocal);
	    	$dateTime->setTimezone($timezoneUTC);
	    	$historyRecord->setDbStarts($dateTime->format("Y-m-d H:i:s"));

	    	$dateTime = new DateTime($values[$prefix."ends"], $timezoneLocal);
	    	$dateTime->setTimezone($timezoneUTC);
	    	$historyRecord->setDbEnds($dateTime->format("Y-m-d H:i:s"));

	    	$templateValues = $values[$prefix."template"];

	    	$file = $historyRecord->getCcFiles();

	    	$metadata = array();

	    	for ($i = 0, $len = count($template); $i < $len; $i++) {

	    	    $item = $template[$i];
	    	    $key = $item["name"];
	    	    $isFileMd = $item["isFileMd"];

	    	    $entry = $templateValues[$prefix.$key];

	    	    if (!$isFileMd) {
	    	        Logging::info("adding metadata");
	    	    }
	    	    else if ($isFileMd && isset($file)) {
	    	        Logging::info("adding file metadata associated to a file");
	    	    }
	    	    else if ($isFileMd && empty($file)) {
                    Logging::info("adding file metadata NOT associated to a file");
                    $metadata[$key] = $entry;
	    	    }
	    	    else {
	    	        Logging::info("doing something else");
	    	    }


	    	}

	    	if (count($metadata) > 0) {
	    	    $meta = new CcPlayoutHistoryMetaData();
	    	}

	    	foreach ($metadata as $key => $val) {

    	    	$meta->setDbKey($key);
    	    	$meta->setDbValue($val);

    	    	$historyRecord->addCcPlayoutHistoryMetaData($meta);
	    	}

	    	$historyRecord->save($this->con);

	    	$this->con->commit();
    	}
    	catch (Exception $e) {
    		$this->con->rollback();
    		Logging::info($e);
    		throw $e;
    	}

	}

	public function createPlayedItem($data) {

		try {
			$form = $this->makeHistoryItemForm(null);
			$history_id = $form->getElement("his_item_id");

	        if ($form->isValid($data)) {
	        	$history_id->setIgnore(true);
	        	$values = $form->getValues();

	        	Logging::info("created list item");
	        	Logging::info($values);

	        	$this->populateTemplateItem($values);
	        }
	        else {
	        	Logging::info("created list item NOT VALID");
	        }

	        Logging::info($form->getMessages());

	        //return $json;
		}
		catch (Exception $e) {
			Logging::info($e);
		}
	}

	/* id is an id in cc_playout_history */
	public function editPlayedItem($data) {

		try {
			$id = $data["his_item_id"];
			$form = $this->makeHistoryItemForm($id);
			$history_id = $form->getElement("his_item_id");
			$history_id->setRequired(true);

			if ($form->isValid($data)) {
			    $history_id->setIgnore(true);
	        	$values = $form->getValues();

	        	Logging::info("edited list item");
	        	Logging::info($values);
	        }
	        else {
	        	Logging::info("edited list item NOT VALID");
	        }

	        Logging::info($form->getMessages());
		}
		catch (Exception $e) {
			Logging::info($e);
		}
	}

	/* id is an id in cc_files */
	public function editPlayedFile($data) {

		$this->con->beginTransaction();

		try {
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

	        $this->con->commit();
		}
		catch (Exception $e) {
			$this->con->rollback();
			Logging::info($e);
			throw $e;
		}

        return $json;
	}

	/* id is an id in cc_playout_history */
	public function deletePlayedItem($id) {

		$this->con->beginTransaction();

		try {

			$record = CcPlayoutHistoryQuery::create()->findPk($id, $this->con);
			$record->delete($this->con);

			$this->con->commit();
		}
		catch (Exception $e) {
			$this->con->rollback();
			Logging::info($e);
			throw $e;
		}
	}


	//---------------- Following code is for History Templates --------------------------//

	public function getFieldTypes() {

	    $fields = array(
    	    TEMPLATE_DATE,
    	    TEMPLATE_TIME,
    	    TEMPLATE_DATETIME,
    	    TEMPLATE_STRING,
    	    TEMPLATE_BOOLEAN,
    	    TEMPLATE_INT,
    	    TEMPLATE_FLOAT,
	    );

	    return $fields;
	}
	
	public function getFileMetadataTypes() {
		
		$fileMD = array(
			array("name"=> MDATA_KEY_TITLE, "type"=> TEMPLATE_STRING),
			array("name"=> MDATA_KEY_CREATOR, "type"=> TEMPLATE_STRING),
			array("name"=> MDATA_KEY_SOURCE, "type"=> TEMPLATE_STRING),
			array("name"=> MDATA_KEY_DURATION, "type"=> TEMPLATE_STRING),
			array("name"=> MDATA_KEY_GENRE, "type"=> TEMPLATE_STRING),
			array("name"=> MDATA_KEY_MOOD, "type"=> TEMPLATE_STRING),
			array("name"=> MDATA_KEY_LABEL, "type"=> TEMPLATE_STRING),
			array("name"=> MDATA_KEY_COMPOSER, "type"=> TEMPLATE_STRING),
			array("name"=> MDATA_KEY_ISRC, "type"=> TEMPLATE_STRING),
			array("name"=> MDATA_KEY_COPYRIGHT, "type"=> TEMPLATE_STRING),
			array("name"=> MDATA_KEY_YEAR, "type"=> TEMPLATE_INT),
			array("name"=> MDATA_KEY_TRACKNUMBER, "type"=> TEMPLATE_INT),
			array("name"=> MDATA_KEY_CONDUCTOR, "type"=> TEMPLATE_STRING),
			array("name"=> MDATA_KEY_LANGUAGE, "type"=> TEMPLATE_STRING),
		);
		
		return $fileMD;
	}

	public function mandatoryItemTemplate() {

	    $fields = array();

	    $fields[] = array("name" => "starts", "type" => TEMPLATE_DATETIME, "isFileMd" => false);
	    $fields[] = array("name" => "ends", "type" => TEMPLATE_DATETIME, "isFileMd" => false);

	    return $fields;
	}

	private function defaultItemTemplate() {

		$fields = array();

		$fields[] = array("name" => "starts", "type" => TEMPLATE_DATETIME, "isFileMd" => false);
		$fields[] = array("name" => "ends", "type" => TEMPLATE_DATETIME, "isFileMd" => false);
		$fields[] = array("name" => MDATA_KEY_TITLE, "type" => TEMPLATE_STRING, "isFileMd" => true); //these fields can be populated from an associated file.
		$fields[] = array("name" => MDATA_KEY_CREATOR, "type" => TEMPLATE_STRING, "isFileMd" => true);

		return $fields;
	}

	private function getItemTemplate() {

		$template_id = Application_Model_Preference::GetHistoryItemTemplate();

		if (is_numeric($template_id)) {
			Logging::info("template id is: $template_id");
		}
		else {
			Logging::info("Using default template");
			$template = $this->defaultItemTemplate();
		}

		return $template;
	}
	
	public function createItemTemplate($fields) {
		
		$this->con->beginTransaction();
		
		try {
		
			$template = new CcPlayoutHistoryTemplate();
			
			$this->con->commit();
		}
		catch (Exception $e) {
			$this->con->rollback();
			Logging::info($e);
			throw $e;
		}
	}

}