<?php

require_once 'formatters/LengthFormatter.php';

class Application_Service_HistoryService
{
	private $con;
	private $timezone;
	
	const TEMPLATE_TYPE_ITEM = "item";
	const TEMPLATE_TYPE_AGGREGATE = "aggregate";

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
		
/*

select * from (

select history.id as history_id
from cc_playout_history as history
where history.file_id IS NULL
) as null_files

LEFT JOIN

(
select track_title.value as track_title, track_title.history_id 
from (select * from cc_playout_history_metadata as phm 
where key = 'track_title') 
as track_title

) as track_filter

USING (history_id)

LEFT JOIN

(
select artist_name.value as artist_name, artist_name.history_id 
from (select * from cc_playout_history_metadata as phm 
where key = 'artist_name') 
as artist_name

) as artist_filter

USING (history_id)

LEFT JOIN

(
select album_title.value as album_title, album_title.history_id 
from (select * from cc_playout_history_metadata as phm 
where key = 'album_title') 
as album_title

) as album_filter

USING (history_id)
 
 */
		
		
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
			and history.file_id is not NULL
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
			$template = $this->getConfiguredItemTemplate();
			$required = $this->mandatoryItemFields();
			$form->createFromTemplate($template["fields"], $required);

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
		    $template = $this->getConfiguredItemTemplate();
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

	    	$md = array();
	    	$metadata = array();
	    	$fields = $template["fields"];
	    	$required = $this->mandatoryItemFields();

	    	for ($i = 0, $len = count($fields); $i < $len; $i++) {
	    		
	    	    $field = $fields[$i];
	    	    $key = $field["name"];
	    	    
	    	    //required is delt with before this loop.
	    	    if (in_array($key, $required)) {
	    	    	continue;
	    	    }
	    	    
	    	    $isFileMd = $field["isFileMd"];
	    	    $entry = $templateValues[$prefix.$key];
	    	    
	    	    if ($isFileMd && isset($file)) {
	    	        Logging::info("adding metadata associated to a file for {$key}");
	    	        $md[$key] = $entry;
	    	    }
	    	    else {
	    	    	Logging::info("adding metadata for {$key}");
                    $metadata[$key] = $entry;
	    	    }
	    	}
	    	
	    	if (count($md) > 0) {
	    		$f = Application_Model_StoredFile::createWithFile($file, $this->con);
	    		$f->setDbColMetadata($md);
	    	}

	    	foreach ($metadata as $key => $val) {

	    		$meta = new CcPlayoutHistoryMetaData();
    	    	$meta->setDbKey($key);
    	    	$meta->setDbValue($val);

    	    	$historyRecord->addCcPlayoutHistoryMetaData($meta);
	    	}

	    	$historyRecord->save($this->con);
	    	$this->con->commit();
    	}
    	catch (Exception $e) {
    		$this->con->rollback();
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
	        	Logging::info($form->getMessages());
	        }
		}
		catch (Exception $e) {
			throw $e;
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

	public function mandatoryItemFields() {

	    $fields = array("starts", "ends");

	    return $fields;
	}

	private function defaultItemTemplate() {

		$template = array();
		$fields = array();

		$fields[] = array("name" => "starts", "type" => TEMPLATE_DATETIME, "isFileMd" => false);
		$fields[] = array("name" => "ends", "type" => TEMPLATE_DATETIME, "isFileMd" => false);
		$fields[] = array("name" => MDATA_KEY_TITLE, "type" => TEMPLATE_STRING, "isFileMd" => true); //these fields can be populated from an associated file.
		$fields[] = array("name" => MDATA_KEY_CREATOR, "type" => TEMPLATE_STRING, "isFileMd" => true);

		$template["name"] = "";
		$template["fields"] = $fields;
		
		return $template;
	}
	
	private function loadTemplate($id) {

		try {
			$template = CcPlayoutHistoryTemplateQuery::create()->findPk($id, $this->con);
			
			$c = new Criteria();
			$c->addAscendingOrderByColumn(CcPlayoutHistoryTemplateFieldPeer::POSITION);
			$config = $template->getCcPlayoutHistoryTemplateFields($c, $this->con);
			$fields = array();
			
			foreach ($config as $item) {
				
				$fields[] = array(
					"name" => $item->getDbName(), 
					"type" => $item->getDbType(),
					"isFileMd" => $item->getDbIsFileMD(),
					"id" => $item->getDbId()
				);
			}
			
			$data = array();
			$data["name"] = $template->getDbName();
			$data["fields"] = $fields;
			
			return $data;
		}
		catch (Exception $e) {
			throw $e;
		}
	}

	public function getItemTemplate($id) {

		if (is_numeric($id)) {
			Logging::info("template id is: $id");
			$template = $this->loadTemplate($id);
		}
		else {
			Logging::info("Using default template");
			$template = $this->defaultItemTemplate();
		}

		return $template;
	}
	
	public function getListItemTemplates() {
		
		$list = array();
		
		try {
			
			$templates = CcPlayoutHistoryTemplateQuery::create()
				->setFormatter(ModelCriteria::FORMAT_ON_DEMAND)
				->findByDbType(self::TEMPLATE_TYPE_ITEM);
			
			foreach ($templates as $template) {
				$list[$template->getDbId()] = $template->getDbName();
			}
			
			return $list;
		}
		catch (Exception $e) {
			throw $e;
		}
	}
	
	public function getConfiguredItemTemplate() {
		try {
			$id = Application_Model_Preference::GetHistoryItemTemplate();
			return $this->getItemTemplate($id);
		}
		catch (Exception $e) {
			throw $e;
		}
	}
	
	public function setConfiguredItemTemplate($id) {
		try {
			Application_Model_Preference::SetHistoryItemTemplate($id);
		}
		catch (Exception $e) {
			throw $e;
		}
	}
	
	public function createItemTemplate($config) {
		
		$this->con->beginTransaction();
		
		try {
		
			$template = new CcPlayoutHistoryTemplate();
			$template->setDbName($config["name"]);
			$template->setDbType(self::TEMPLATE_TYPE_ITEM);
			
			$fields = $config["fields"];
			
			foreach ($fields as $index=>$field) {
				
				$isMd = ($field["filemd"] == 'true') ? true : false;
				
				$templateField = new CcPlayoutHistoryTemplateField();
				$templateField->setDbName($field["name"]);
				$templateField->setDbType($field["type"]);
				$templateField->setDbIsFileMD($isMd);
				$templateField->setDbPosition($index);
				
				$template->addCcPlayoutHistoryTemplateField($templateField);
			}
			
			$template->save($this->con);
			
			$doSetDefault = $config['setDefault'];
			if (isset($doSetDefault) && $doSetDefault) {
				$this->setConfiguredItemTemplate($template->getDbid());
			}
			
			$this->con->commit();
		}
		catch (Exception $e) {
			$this->con->rollback();
			throw $e;
		}		
	}

}