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
	
	//opts is from datatables.
	public function getPlayedItemData($startDT, $endDT, $opts)
	{
		$mainSqlQuery = "";
		$paramMap = array();
		$sqlTypes = $this->getSqlTypes();
		
		$start = $startDT->format("Y-m-d H:i:s");
		$end = $endDT->format("Y-m-d H:i:s");
		$paramMap["starts"] = $start;
		$paramMap["ends"] = $end;
		
		$template = $this->getConfiguredItemTemplate();
		$fields = $template["fields"];
		$required = $this->mandatoryItemFields();		
		
		$fields_filemd = array();
		$filemd_keys = array();
		$fields_general = array();
		$general_keys = array();
		
		foreach ($fields as $index=>$field) {
			
			if (in_array($field["name"], $required)) {
				continue;
			}
			
			if ($field["isFileMd"]) {
				$fields_filemd[] = $field;
				$filemd_keys[] = $field["name"];
			}
			else {
				$fields_general[] = $field;
				$general_keys[] = $field["name"];
			}
		}
		
		$historyRange = "(".
		"SELECT history.starts, history.ends, history.id AS history_id".
		" FROM cc_playout_history as history".
		" WHERE history.starts >= :starts and history.starts < :ends".
		") AS history_range";
				
		$manualMeta = "(".
		"SELECT %KEY%.value AS %KEY%, %KEY%.history_id".
		" FROM (".
		" SELECT * from cc_playout_history_metadata AS phm WHERE phm.key = :meta_%KEY%".
		" ) AS %KEY%".
		" ) AS %KEY%_filter";
		
		$mainSelect = array("history_range.starts", "history_range.ends", "history_range.history_id");
		$mdFilters = array();
		
		$numFileMdFields = count($fields_filemd);
		
		if ($numFileMdFields > 0) {
		
			//these 3 selects are only needed if $fields_filemd has some fields.
			$fileSelect = array("history_file.history_id");
			$nonNullFileSelect = array("file.id as file_id");
			$nullFileSelect = array("null_file.history_id");
			
			$fileMdFilters = array();
			
			//populate the different dynamic selects with file info.
			for ($i = 0; $i < $numFileMdFields; $i++) {
				
				$field = $fields_filemd[$i];
				$key = $field["name"];
				$type = $sqlTypes[$field["type"]];
				
				$fileSelect[] = "file_md.{$key}::{$type}";
				$nonNullFileSelect[] = "file.{$key}::{$type}";
				$nullFileSelect[] = "{$key}_filter.{$key}::{$type}";
				$mainSelect[] = "file_info.{$key}::{$type}";
				
				$fileMdFilters[] = str_replace("%KEY%", $key, $manualMeta);
				$paramMap["meta_{$key}"] = $key;
			}
			
			//the files associated with scheduled playback in Airtime.
			$historyFile = "(".
			"SELECT history.id AS history_id, history.file_id".
			" FROM cc_playout_history AS history".
			" WHERE history.file_id IS NOT NULL".
			") AS history_file";
			
			$fileMd = "(".
			"SELECT %NON_NULL_FILE_SELECT%".
			" FROM cc_files AS file".
			") AS file_md";
			
			$fileMd = str_replace("%NON_NULL_FILE_SELECT%", join(", ", $nonNullFileSelect), $fileMd);
			
			//null files are from manually added data (filling in webstream info etc)
			$nullFile = "(".
			"SELECT history.id AS history_id".
			" FROM cc_playout_history AS history".
			" WHERE history.file_id IS NULL".
			") AS null_file";
			
			
			//----------------------------------
			//building the file inner query
			
			$fileSqlQuery = 
			"SELECT ".join(", ", $fileSelect).
			" FROM {$historyFile}".
			" LEFT JOIN {$fileMd} USING (file_id)".
			" UNION".
			" SELECT ".join(", ", $nullFileSelect).
			" FROM {$nullFile}";
			
			foreach ($fileMdFilters as $filter) {
				
				$fileSqlQuery.=
				" LEFT JOIN {$filter} USING(history_id)";
			}
				
		}
		
		for ($i = 0, $len = count($fields_general); $i < $len; $i++) {
			
			$field = $fields_general[$i];
			$key = $field["name"];
			$type = $sqlTypes[$field["type"]];
			
			$mdFilters[] = str_replace("%KEY%", $key, $manualMeta);
			$paramMap["meta_{$key}"] = $key;
			$mainSelect[] = "{$key}_filter.{$key}::{$type}";
		}
		
		$mainSqlQuery.=
		"SELECT ".join(", ", $mainSelect).
		" FROM {$historyRange}";
		
		if (isset($fileSqlQuery)) {
			
			$mainSqlQuery.=
			" LEFT JOIN ( {$fileSqlQuery} ) as file_info USING(history_id)";
		}
		
		foreach ($mdFilters as $filter) {
		
			$mainSqlQuery.=
			" LEFT JOIN {$filter} USING(history_id)";
		}
		
		//------------------------------------------------------------------------
		//Using Datatables parameters to sort the data.
		
		$numOrderColumns = $opts["iSortingCols"];
		$orderBys = array();
		
		for ($i = 0; $i < $numOrderColumns; $i++) {
			
			$colNum = $opts["iSortCol_".$i];
			$key = $opts["mDataProp_".$colNum];
			$sortDir = $opts["sSortDir_".$i];
			
			if (in_array($key, $required)) {

				$orderBys[] = "history_range.{$key} {$sortDir}";
			}
			else if (in_array($key, $filemd_keys)) {
			
				$orderBys[] = "file_info.{$key} {$sortDir}";
			}
			else if (in_array($key, $general_keys)) {

				$orderBys[] = "{$key}_filter.{$key} {$sortDir}";
			}
			else {
				throw new Exception("Error: $key is not part of the template.");
			}
		}
		
		if ($numOrderColumns > 0) {
			
			$orders = join(", ", $orderBys);
			
			$mainSqlQuery.=
			" ORDER BY {$orders}";
		}
			
		$stmt = $this->con->prepare($mainSqlQuery);
		foreach ($paramMap as $param => $v) {
			$stmt->bindValue($param, $v);
		}
		
		$rows = array();
		if ($stmt->execute()) {
			$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		}
		else {
			$msg = implode(',', $stmt->errorInfo());
			Logging::info($msg);
			throw new Exception("Error: $msg");
		}
		
		$totalRows = count($rows);
		
		//-----------------------------------------------------------------------
		//processing results.
		
		$timezoneUTC = new DateTimeZone("UTC");
		$timezoneLocal = new DateTimeZone($this->timezone);
		
		//need to display the results in the station's timezone.
		foreach ($rows as $index => &$result) {
		
			$dateTime = new DateTime($result["starts"], $timezoneUTC);
			$dateTime->setTimezone($timezoneLocal);
			$result["starts"] = $dateTime->format("Y-m-d H:i:s");
		
			$dateTime = new DateTime($result["ends"], $timezoneUTC);
			$dateTime->setTimezone($timezoneLocal);
			$result["ends"] = $dateTime->format("Y-m-d H:i:s");
		}
		
		return array(
			"sEcho" => intval($opts["sEcho"]),
			//"iTotalDisplayRecords" => intval($totalDisplayRows),
			"iTotalDisplayRecords" => intval($totalRows),
			"iTotalRecords" => intval($totalRows),
			"history" => $rows
		);
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

				$metadata = array();
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
	
	private function getSqlTypes() {
	
		$fields = array(
			TEMPLATE_DATE => "date",
			TEMPLATE_TIME => "time",
			TEMPLATE_DATETIME => "datetime",
			TEMPLATE_STRING => "text",
			TEMPLATE_BOOLEAN => "boolean",
			TEMPLATE_INT => "integer",
			TEMPLATE_FLOAT => "float",
		);
	
		return $fields;
	}
	
	public function getFileMetadataTypes() {
		
		$fileMD = array(
			array("name"=> MDATA_KEY_TITLE, "type"=> TEMPLATE_STRING, "sql"),
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

		$template["name"] = "Template".date("Y-m-d H:i:s");
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
	
	public function getDatatablesPlayedItemColumns() {
		
		try {
			$template = $this->getConfiguredItemTemplate();
			
			$columns = array();
			
			foreach ($template["fields"] as $index=>$field) {
				
				$key = $field["name"];
				
				$columns[] = array(
					"sTitle"=> $key,
					"mDataProp"=> $key,
					"sClass"=> "his_{$key}"
				);
			}
			
			return $columns;
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
	
	public function getConfiguredTemplateIds() {
		
		try {
			$id = Application_Model_Preference::GetHistoryItemTemplate();
			
			return array($id);
		}
		catch (Exception $e) {
			throw $e;
		}
	}
	
	public function createItemTemplate($config) {
		
		$this->con->beginTransaction();
		
		try {
			
			$default = $this->defaultItemTemplate();
			
			$name = isset($config["name"]) ? $config["name"] : $default["name"];
			$fields = isset($config["fields"]) ? $config["fields"] : $default["fields"];
			
			$doSetDefault = isset($config['setDefault']) ? $config['setDefault'] : false;
		
			$template = new CcPlayoutHistoryTemplate();
			$template->setDbName($name);
			$template->setDbType(self::TEMPLATE_TYPE_ITEM);

			foreach ($fields as $index=>$field) {
				
				$isMd = ($field["isFileMd"] == 'true') ? true : false;
				
				$templateField = new CcPlayoutHistoryTemplateField();
				$templateField->setDbName($field["name"]);
				$templateField->setDbType($field["type"]);
				$templateField->setDbIsFileMD($isMd);
				$templateField->setDbPosition($index);
				
				$template->addCcPlayoutHistoryTemplateField($templateField);
			}
			
			$template->save($this->con);
						
			if ($doSetDefault) {
				$this->setConfiguredItemTemplate($template->getDbid());
			}
			
			$this->con->commit();
			
			return $template->getDbid();
		}
		catch (Exception $e) {
			$this->con->rollback();
			throw $e;
		}		
	}
	
	public function updateItemTemplate($id, $name, $fields, $doSetDefault=false) {
	
		$this->con->beginTransaction();
	
		try {
				
			$template = CcPlayoutHistoryTemplateQuery::create()->findPk($id, $this->con);
			$template->setDbName($name);
			
			if (count($fields) === 0) {
				$t = $this->defaultItemTemplate();
				$fields = $t["fields"]; 
			}
			
			$template->getCcPlayoutHistoryTemplateFields()->delete($this->con);
			
			foreach ($fields as $index=>$field) {
	
				$isMd = ($field["isFileMd"] == 'true') ? true : false;
	
				$templateField = new CcPlayoutHistoryTemplateField();
				$templateField->setDbName($field["name"]);
				$templateField->setDbType($field["type"]);
				$templateField->setDbIsFileMD($isMd);
				$templateField->setDbPosition($index);
	
				$template->addCcPlayoutHistoryTemplateField($templateField);
			}
				
			$template->save($this->con);
	
			if ($doSetDefault) {
				$this->setConfiguredItemTemplate($template->getDbid());
			}
				
			$this->con->commit();
		}
		catch (Exception $e) {
			$this->con->rollback();
			throw $e;
		}
	}
	
	public function deleteTemplate($id) {
		
		$this->con->beginTransaction();
		
		try {
			
			$template = CcPlayoutHistoryTemplateQuery::create()->findPk($id, $this->con);
			$template->delete($this->con);
			
		    $this->con->commit();
		}
		catch (Exception $e) {
			$this->con->rollback();
			throw $e;
		}
	}
}