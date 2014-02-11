<?php

use Airtime\CcSubjsPeer;
use Airtime\MediaItem\WebstreamPeer;
use Airtime\MediaItem\PlaylistPeer;
use Airtime\MediaItem\AudioFilePeer;

use Airtime\MediaItem\AudioFileQuery;
use Airtime\MediaItem\WebstreamQuery;
use Airtime\MediaItem\PlaylistQuery;
use Airtime\MediaItemQuery;

class Application_Service_MediaService
{
	//used for creating media search.
	//can't seem to call a class dynamically even though
	//http://www.php.net/manual/en/language.namespaces.importing.php
	//seems to say it's possible.
	private $_ns = array(
		"AudioFilePeer" => "Airtime\MediaItem\AudioFilePeer",
		"PlaylistPeer" => "Airtime\MediaItem\PlaylistPeer",
		"WebstreamPeer" => "Airtime\MediaItem\WebstreamPeer",
		"CcSubjsPeer" => "Airtime\CcSubjsPeer",
	);

	private function enhanceDatatablesColumns(&$datatablesColumns) {

		$checkbox = array(
			"sTitle" =>	"",
			"mDataProp" => "Checkbox",
			"bSortable" => false,
			"bSearchable" => false,
			"bVisible" => true,
			"sWidth" => "25px",
			"sClass" => "library_checkbox",
		);

		//add the checkbox to the beginning.
		array_unshift($datatablesColumns, $checkbox);
	}

	/*
	 * add display only columns such as checkboxs to the datatables response.
	 * these should not be columns that could be calculated in the DB query.
	 */
	private function enhanceDatatablesOutput(&$output) {

		//add in data for the display columns.
		foreach ($output as &$row) {
			$row["Checkbox"] = '<input type="checkbox">';
		}
	}

	private function getAudioFileColumnDetails() {

		return array(
			"Id" => array(
				"isColumn" => false,
				"advancedSearch" => array(
					"type" => null
				)
			),
			"IsScheduled" => array(
				"isColumn" => true,
				"title" => _("Scheduled"),
				"width" => "90px",
				"class" => "library_is_scheduled",
				"searchable" => false,
				"advancedSearch" => array(
					"type" => "checkbox"
				)
			),
			"IsPlaylist" => array(
				"isColumn" => true,
				"title" => _("Playlist"),
				"width" => "90px",
				"class" => "library_is_playlist",
				"searchable" => false,
				"advancedSearch" => array(
					"type" => "checkbox"
				)
			),
			"TrackTitle" => array(
				"isColumn" => true,
				"title" => _("Title"),
				"width" => "170px",
				"class" => "library_title",
				"advancedSearch" => array(
					"type" => "text"
				)
			),
			"ArtistName" => array(
				"isColumn" => true,
				"title" => _("Creator"),
				"width" => "160px",
				"class" => "library_creator",
				"advancedSearch" => array(
					"type" => "text"
				)
			),
			"AlbumTitle" => array(
				"isColumn" => true,
				"title" => _("Album"),
				"width" => "150px",
				"class" => "library_album",
				"advancedSearch" => array(
					"type" => "text"
				)
			),
			"BitRate" => array(
				"isColumn" => true,
				"title" => _("Bit Rate"),
				"width" => "80px",
				"class" => "library_bitrate",
				"visible" => false,
				"advancedSearch" => array(
					"type" => "number-range"
				)
			),
			"Bpm" => array(
				"isColumn" => true,
				"title" => _("BPM"),
				"width" => "50px",
				"class" => "library_bpm",
				"visible" => false,
				"advancedSearch" => array(
					"type" => "number-range"
				)
			),
			"Composer" => array(
				"isColumn" => true,
				"title" => _("Composer"),
				"width" => "150px",
				"class" => "library_composer",
				"visible" => false,
				"advancedSearch" => array(
					"type" => "text"
				)
			),
			"Conductor" => array(
				"isColumn" => true,
				"title" => _("Conductor"),
				"width" => "125px",
				"class" => "library_conductor",
				"visible" => false,
				"advancedSearch" => array(
					"type" => "text"
				)
			),
			"Copyright" => array(
				"isColumn" => true,
				"title" => _("Copyright"),
				"width" => "125px",
				"class" => "library_copyright",
				"visible" => false,
				"advancedSearch" => array(
					"type" => "text"
				)
			),
			"Cuein" => array(
				"isColumn" => true,
				"title" => _("Cue In"),
				"width" => "80px",
				"class" => "library_length",
				"visible" => false,
				"searchable" => false,
				"advancedSearch" => array(
					"type" => null
				)
			),
			"Cueout" => array(
				"isColumn" => true,
				"title" => _("Cue Out"),
				"width" => "80px",
				"class" => "library_length",
				"visible" => false,
				"searchable" => false,
				"advancedSearch" => array(
					"type" => null
				)
			),
			"EncodedBy" => array(
				"isColumn" => true,
				"title" => _("Encoded By"),
				"width" => "150px",
				"class" => "library_encoded",
				"visible" => false,
				"advancedSearch" => array(
					"type" => "text"
				)
			),
			"Genre" => array(
				"isColumn" => true,
				"title" => _("Genre"),
				"width" => "100px",
				"class" => "library_genre",
				"visible" => false,
				"advancedSearch" => array(
					"type" => "text"
				)
			),
			"IsrcNumber" => array(
				"isColumn" => true,
				"title" => _("ISRC"),
				"width" => "150px",
				"class" => "library_isrc",
				"visible" => false,
				"advancedSearch" => array(
					"type" => "text"
				)
			),
			"Label" => array(
				"isColumn" => true,
				"title" => _("Label"),
				"width" => "125px",
				"class" => "library_label",
				"visible" => false,
				"advancedSearch" => array(
					"type" => "text"
				)
			),
			"Language" => array(
				"isColumn" => true,
				"title" => _("Language"),
				"width" => "125px",
				"class" => "library_language",
				"visible" => false,
				"advancedSearch" => array(
					"type" => "text"
				)
			),
			"UpdatedAt" => array(
				"isColumn" => true,
				"title" => _("Last Modified"),
				"width" => "125px",
				"class" => "library_modified_time",
				"visible" => false,
				"advancedSearch" => array(
					"type" => "date-range"
				)
			),
			"LastPlayedTime" => array(
				"isColumn" => true,
				"title" => _("Last Played"),
				"width" => "125px",
				"class" => "library_modified_time",
				"visible" => false,
				"advancedSearch" => array(
					"type" => "date-range"
				)
			),
			"CueLength" => array(
				"isColumn" => true,
				"title" => _("Length"),
				"width" => "80px",
				"class" => "library_length",
				"searchable" => false,
				"advancedSearch" => array(
					"type" => null
				)
			),
			"Mime" => array(
				"isColumn" => true,
				"title" => _("Mime"),
				"width" => "80px",
				"class" => "library_mime",
				"visible" => false,
				"advancedSearch" => array(
					"type" => "text"
				)
			),
			"Mood" => array(
				"isColumn" => true,
				"title" => _("Mood"),
				"width" => "70px",
				"class" => "library_mood",
				"visible" => false,
				"advancedSearch" => array(
					"type" => "text"
				)
			),
			"CcSubjs.DbLogin" => array(
				"isColumn" => true,
				"title" => _("Owner"),
				"width" => "125px",
				"class" => "library_owner",
				"visible" => false,
				"advancedSearch" => array(
					"type" => "text"
				)
			),
			"ReplayGain" => array(
				"isColumn" => true,
				"title" => _("Replay Gain"),
				"width" => "80px",
				"class" => "library_replay_gain",
				"visible" => false,
				"advancedSearch" => array(
					"type" => "number-range"
				)
			),
			"SampleRate" => array(
				"isColumn" => true,
				"title" => _("Sample Rate"),
				"width" => "80px",
				"class" => "library_sr",
				"visible" => false,
				"advancedSearch" => array(
					"type" => "number-range"
				)
			),
			"TrackNumber" => array(
				"isColumn" => true,
				"title" => _("Track number"),
				"width" => "65px",
				"class" => "library_track",
				"visible" => false,
				"advancedSearch" => array(
					"type" => "number-range"
				)
			),
			"CreatedAt" => array(
				"isColumn" => true,
				"title" => _("Uploaded"),
				"width" => "125px",
				"class" => "library_upload_time",
				"visible" => false,
				"advancedSearch" => array(
					"type" => "date-range"
				)
			),
			"InfoUrl" => array(
				"isColumn" => true,
				"title" => _("Website"),
				"width" => "150px",
				"class" => "library_url",
				"visible" => false,
				"advancedSearch" => array(
					"type" => "text"
				)

			),
			"Year" => array(
				"isColumn" => true,
				"title" => _("Year"),
				"width" => "60px",
				"class" => "library_year",
				"visible" => false,
				"advancedSearch" => array(
					"type" => "number-range"
				)
			),
		);
	}

	private function getAudioFileDatatableColumnOrder() {

		return array (
			"IsScheduled",
			"IsPlaylist",
			"TrackTitle",
			"ArtistName",
			"AlbumTitle",
			"BitRate",
			"Bpm",
			"Composer",
			"Conductor",
			"Copyright",
			"Cuein",
			"Cueout",
			"EncodedBy",
			"Genre",
			"IsrcNumber",
			"Label",
			"Language",
			"UpdatedAt",
			"LastPlayedTime",
			"CueLength", //this is a custom function in AudioFile
			"Mime",
			"Mood",
			"CcSubjs.DbLogin",
			"ReplayGain",
			"SampleRate",
			"TrackNumber",
			"CreatedAt",
			"InfoUrl",
			"Year",
		);
	}

	private function getAudioFileColumnAliases() {

		return array(
			"CueLength",
		);
	}

	private function getWebstreamColumnDetails() {

		return array(
			"Id" => array(
				"isColumn" => false
			),
			"Name" => array(
				"isColumn" => true,
				"title" => _("Name"),
				"width" => "170px",
				"class" => "library_title",
		        "advancedSearch" => array(
	                "type" => "text"
		        )
			),
			"Mime" => array(
				"isColumn" => true,
				"title" => _("Mime"),
				"width" => "80px",
				"class" => "library_mime",
		        "advancedSearch" => array(
	                "type" => "text"
		        )
			),
			"Url" => array(
				"isColumn" => true,
				"title" => _("Url"),
				"width" => "150px",
				"class" => "library_url",
		        "advancedSearch" => array(
	                "type" => "text"
		        )
			),
			"CreatedAt" => array(
				"isColumn" => true,
				"title" => _("Created"),
				"width" => "125px",
				"class" => "library_upload_time",
				"visible" => false,
		        "advancedSearch" => array(
	                "type" => "date-range"
		        )
			),
			"UpdatedAt" => array(
				"isColumn" => true,
				"title" => _("Last Modified"),
				"width" => "125px",
				"class" => "library_modified_time",
				"visible" => false,
		        "advancedSearch" => array(
	                "type" => "date-range"
		        )
			),
			"CcSubjs.DbLogin" => array(
				"isColumn" => true,
				"title" => _("Owner"),
				"width" => "160px",
				"class" => "library_owner",
		        "advancedSearch" => array(
	                "type" => "text"
		        )
			),
			"Length" => array(
				"isColumn" => true,
				"title" => _("Default Length"),
				"width" => "80px",
				"class" => "library_length",
				"searchable" => false,
				"visible" => false,
			),
		);
	}

	private function getWebstreamDatatableColumnOrder() {

		return array (
			"Name",
			"Mime",
			"Url",
			"CcSubjs.DbLogin",
			"CreatedAt",
			"UpdatedAt",
			"Length",
		);
	}

	private function getWebstreamColumnAliases() {

		return array(
		);
	}

	private function getPlaylistColumnDetails() {

		return array(
			"Id" => array(
				"isColumn" => false
			),
			"Type" => array(
				"isColumn" => true,
				"title" => _("Type"),
				"width" => "25px",
				"class" => "library_type",
		        "advancedSearch" => array(
	                "type" => "select",
		            "values" => array("standard", "static", "dynamic")
		        )
			),
			"Name" => array(
				"isColumn" => true,
				"title" => _("Title"),
				"width" => "170px",
				"class" => "library_title",
		        "advancedSearch" => array(
	                "type" => "text"
		        )
			),
			"Description" => array(
				"isColumn" => true,
				"title" => _("Description"),
				"width" => "200px",
				"class" => "library_description",
		        "advancedSearch" => array(
	                "type" => "text"
		        )
			),
			"Length" => array(
				"isColumn" => true,
				"title" => _("Length"),
				"width" => "80px",
				"class" => "library_length",
				"searchable" => false,
			),
			"CreatedAt" => array(
				"isColumn" => true,
				"title" => _("Created"),
				"width" => "125px",
				"class" => "library_upload_time",
				"visible" => false,
		        "advancedSearch" => array(
	                "type" => "date-range"
		        )
			),
			"UpdatedAt" => array(
				"isColumn" => true,
				"title" => _("Last Modified"),
				"width" => "125px",
				"class" => "library_modified_time",
				"visible" => false,
		        "advancedSearch" => array(
	                "type" => "date-range"
		        )
			),
			"CcSubjs.DbLogin" => array(
				"isColumn" => true,
				"title" => _("Owner"),
				"width" => "160px",
				"class" => "library_owner",
		        "advancedSearch" => array(
	                "type" => "text"
		        )
			),
		);
	}

	private function getPlaylistDatatableColumnOrder() {

		return array (
			"Type",
			"Name",
			"Description",
			"CreatedAt",
			"UpdatedAt",
			"CcSubjs.DbLogin",
			"Length",
		);
	}

	private function getPlaylistColumnAliases() {

		return array(
		);
	}

	/*
	 * @param $type string
	 * 		which media datatable to create columns for.
	 */
	public function makeDatatablesColumns($type) {

		$orderMethod = "get{$type}DatatableColumnOrder";
		$infoMethod = "get{$type}ColumnDetails";

		$datatablesColumns = array();

		$columnOrder = self::$orderMethod();
		$columnInfo = self::$infoMethod();

		for ($i = 0; $i < count($columnOrder); $i++) {

			$data = $columnInfo[$columnOrder[$i]];

			$datatablesColumns[] = array(
				"sTitle" =>	$data["title"],
				"mDataProp" => $columnOrder[$i],
				"bSortable" => isset($data["sortable"]) ? $data["sortable"] : true,
				"bSearchable" => isset($data["searchable"]) ? $data["searchable"] : true,
				"bVisible" => isset($data["visible"]) ? $data["visible"] : true,
				"sWidth" => $data["width"],
				"sClass" => $data["class"],
				"search" => isset($data["advancedSearch"]) ? $data["advancedSearch"] : null
			);
		}

		self::enhanceDatatablesColumns($datatablesColumns);

		return $datatablesColumns;
	}

	private function getColumnType($base, $column) {
		$b = explode("\\", $base);
		$b = array_pop($b);
		$class = $this->_ns["{$b}Peer"];

		$field = $class::translateFieldName($column, BasePeer::TYPE_PHPNAME, BasePeer::TYPE_FIELDNAME);
		Logging::info($field);

		$propelMap = $class::getTableMap();
		$col = $propelMap->getColumn($field);
		$type = $col->getType();
		Logging::info($type);

		return $type;
	}

	private function searchNumber($query, $col, $from, $to) {
		$num = 0;

		if (isset($from) && is_numeric($from)) {
			$name = "adv_{$col}_from";
			$cond = "{$col} >= ?";
			$query->condition($name, $cond, $from);
			$num++;
		}

		if (isset($to) && is_numeric($to)) {
			$name = "adv_{$col}_to";
			$cond = "{$col} <= ?";
			$query->condition($name, $cond, $to);
			$num++;
		}

		if ($num > 1) {
			$name = "adv_{$col}_from_to";
			$query->combine(array("adv_{$col}_from", "adv_{$col}_to"), 'and', $name);
		}

		//returns the final query condition to combine with other columns.
		return $name;
	}

	//need to return name of condition so that
	//all advanced search fields can be combined into an AND.
	private function searchString($query, $col, $value) {

		$name = "adv_{$col}";
		$cond = "{$col} iLIKE ?";
		$param = "%{$value}%";
		$query->condition($name, $cond, $param);

		return $name;
	}

	private function searchDate($query, $col, $from, $to) {
		$num = 0;

		if (isset($from) && preg_match_all('/(\d{4}-\d{2}-\d{2})/', $from)) {
			$name = "adv_{$col}_from";
			$cond = "{$col} >= ?";

			$date = Application_Common_DateHelper::UserTimezoneStringToUTCString($from);
			$query->condition($name, $cond, $date);
			$num++;
		}

		if (isset($to) && preg_match_all('/(\d{4}-\d{2}-\d{2})/', $to)) {
			$name = "adv_{$col}_to";
			$cond = "{$col} <= ?";

			$date = Application_Common_DateHelper::UserTimezoneStringToUTCString($to);
			$query->condition($name, $cond, $date);
			$num++;
		}

		if ($num > 1) {
			$name = "adv_{$col}_from_to";
			$query->combine(array("adv_{$col}_from", "adv_{$col}_to"), 'and', $name);
		}

		//returns the final query condition to combine with other columns.
		return $name;
	}

	private function buildQuery($query, $params, $dataColumns, $aliasedColumns) {
		//namespacing seems to cause a problem in the WHERE clause
		//if we don't prefix the PHP name with the model or alias.
		$modelName = $query->getModelName();

		$query->setFormatter('PropelOnDemandFormatter');
		$query->joinWith("CcSubjs");

		$totalCount = $query->count();

		//add advanced search terms to query.
		$len = intval($params["iColumns"]);
		$advConds = array();
		for ($i = 0; $i < $len; $i++) {

			$prop = $params["mDataProp_{$i}"];

			if ($params["bSearchable_{$i}"] === "true"
				&& $params["sSearch_{$i}"] != ""
				&& in_array($prop, $dataColumns)
				&& !in_array($prop, $aliasedColumns)) {

				if (strrpos($prop, ".") === false) {
					$b = $modelName;
					$c = $prop;
				}
				else {
					list($b, $c) = explode(".", $prop);
				}

				$type = self::getColumnType($b, $c);
				$searchCol = "{$b}.{$c}";
				$value = $params["sSearch_{$i}"];
				$separator = $params["sRangeSeparator"];

				switch($type) {
					case PropelColumnTypes::DATE:
      				case PropelColumnTypes::TIMESTAMP:
      					list($from, $to) = explode($separator, $value);
      					$advConds[] = self::searchDate($query, $searchCol, $from, $to);
      					break;
      				case PropelColumnTypes::NUMERIC:
      				case PropelColumnTypes::INTEGER:
      					list($from, $to) = explode($separator, $value);
      					$advConds[] = self::searchNumber($query, $searchCol, $from, $to);
      					break;
      				default:
      					$advConds[] = self::searchString($query, $searchCol, $value);
      					break;
				}
			}
		}
		if (count($advConds) > 0) {
			$query->where($advConds, 'and');
		}

		//take care of WHERE clause
		/*
		$search = $params["sSearch"];
		$searchTerms = $search == "" ? array() : explode(" ", $search);
		$andConditions = array();
		$orConditions = array();

		foreach ($searchTerms as $term) {

			$orConditions = array();

			$len = intval($params["iColumns"]);
			for ($i = 0; $i < $len; $i++) {

				if ($params["bSearchable_{$i}"] === "true") {

					$whereTerm = $params["mDataProp_{$i}"];
					if (strrpos($whereTerm, ".") === false) {
						$whereTerm = $modelName.".".$whereTerm;
					}

					$name = "{$term}{$i}";
					$cond = "{$whereTerm} iLIKE ?";
					$param = "{$term}%";

					$query->condition($name, $cond, $param);

					$orConditions[] = $name;
				}
			}

			if (count($searchTerms) > 1) {
				$query->combine($orConditions, 'or', $term);
				$andConditions[] = $term;
			}
			else {
				$query->where($orConditions, 'or');
			}
		}
		if (count($andConditions) > 1) {
			$query->where($andConditions, 'and');
		}
		*/

		//ORDER BY statements
		$len = intval($params["iSortingCols"]);
		for ($i = 0; $i < $len; $i++) {

			$colNum = $params["iSortCol_{$i}"];

			if ($params["bSortable_{$colNum}"] == "true") {
				$colName = $params["mDataProp_{$colNum}"];
				$colDir = $params["sSortDir_{$i}"] === "asc" ? Criteria::ASC : Criteria::DESC;

				//need to lowercase the column name for the syntax generated by propel
				//to work properly in postgresql.
				if (in_array($colName, $aliasedColumns)) {
					$colName = strtolower($colName);
				}

				$query->orderBy($colName, $colDir);
			}
		}

		$filteredCount = $query->count();

		//LIMIT OFFSET statements
		$limit = intval($params["iDisplayLength"]);
		$offset = intval($params["iDisplayStart"]);

		$query
			->limit($limit)
			->offset($offset);

		$records = $query->find();

		return array (
			"totalCount" => $totalCount,
	    	"count" => $filteredCount,
	    	"media" => $records
		);
	}

	private function makeArray(&$array, &$getters, $obj) {

		$key = array_shift($getters);
		$method = "get{$key}";

		if (count($getters) == 0) {
			$array[$key] = $obj->$method();
			return;
		}

		if (empty($array[$key])) {
			$array[$key] = array();
		}
		$a =& $array[$key];
		$nextObj = $obj->$method();

		return self::makeArray($a, $getters, $nextObj);
	}

	/*
	 * @param $coll PropelCollection formatted on demand.
	 *
	 * @return $output, an array of data with the columns needed for datatables.
	 */
	private function createOutput($coll, $columns) {

		$output = array();
		foreach ($coll as $media) {

			$item = array();
			foreach ($columns as $column) {

				$getters = explode(".", $column);
				self::makeArray($item, $getters, $media);
			}

			$output[] = $item;
		}

		self::enhanceDatatablesOutput($output);

		return $output;
	}

	public function getDatatablesAudioFiles($params) {

		Logging::enablePropelLogging();

		$columns = array_keys(self::getAudioFileColumnDetails());
		$aliases = self::getAudioFileColumnAliases();

		$q = AudioFileQuery::create();

		$m = $q->getModelName();
		$q->withColumn("({$m}.Cueout - {$m}.Cuein)", "cuelength");

		$results = self::buildQuery($q, $params, $columns, $aliases);

		Logging::disablePropelLogging();

		return array(
			"count" => $results["count"],
			"totalCount" => $results["totalCount"],
			"records" => self::createOutput($results["media"], $columns)
		);
	}

	public function getDatatablesWebstreams($params) {

		Logging::enablePropelLogging();

		$columns = array_keys(self::getWebstreamColumnDetails());
		$aliases = self::getWebstreamColumnAliases();

		$q = WebstreamQuery::create();
		$results = self::buildQuery($q, $params, $columns, $aliases);

		Logging::disablePropelLogging();

		return array(
			"count" => $results["count"],
			"totalCount" => $results["totalCount"],
			"records" => self::createOutput($results["media"], $columns)
		);
	}

	public function getDatatablesPlaylists($params) {

		Logging::enablePropelLogging();

		$columns = array_keys(self::getPlaylistColumnDetails());
		$aliases = self::getPlaylistColumnAliases();

		$q = PlaylistQuery::create();
		$results = self::buildQuery($q, $params, $columns, $aliases);

		Logging::disablePropelLogging();

		return array(
			"count" => $results["count"],
			"totalCount" => $results["totalCount"],
			"records" => self::createOutput($results["media"], $columns)
		);
	}

	public function setSessionMediaObject($obj) {

		$obj_sess = new Zend_Session_Namespace(UI_PLAYLISTCONTROLLER_OBJ_SESSNAME);

		if (is_null($obj)) {
			unset($obj_sess->id);
		}
		else {
			$obj_sess->id = $obj->getId();
		}
	}

	public function getSessionMediaObject() {

		$obj_sess = new Zend_Session_Namespace(UI_PLAYLISTCONTROLLER_OBJ_SESSNAME);
		//some type of media is in the session
		if (isset($obj_sess->id)) {
			$obj = MediaItemQuery::create()->findPk($obj_sess->id);

			if (isset($obj)) {
				return $obj->getChildObject();
			}
			else {
				$obj_sess->id = null;
			}
		}
	}

	public function createLibraryColumnsJavascript() {

		//set audio columns for display of data.
		$columns = json_encode(self::makeDatatablesColumns('AudioFile'));
		$script = "localStorage.setItem( 'datatables-audio-aoColumns', JSON.stringify($columns) ); ";

		//set webstream columns for display of data.
		$columns = json_encode(self::makeDatatablesColumns('Webstream'));
		$script .= "localStorage.setItem( 'datatables-webstream-aoColumns', JSON.stringify($columns) ); ";

		//set playlist columns for display of data.
		$columns = json_encode(self::makeDatatablesColumns('Playlist'));
		$script .= "localStorage.setItem( 'datatables-playlist-aoColumns', JSON.stringify($columns) ); ";

		return $script;
	}

	public function createLibraryColumnSettingsJavascript() {

		$script = "";

		$settings = Application_Model_Preference::getAudioTableSetting();
        if (!is_null($settings)) {
            $data = json_encode($settings);
            $script .= "localStorage.setItem( 'datatables-audio', JSON.stringify($data) ); ";
        }
        else {
        	$script .= "localStorage.setItem( 'datatables-audio', null ); ";
        }

        $settings = Application_Model_Preference::getWebstreamTableSetting();
        if (!is_null($settings)) {
        	$data = json_encode($settings);
        	$script .= "localStorage.setItem( 'datatables-webstream', JSON.stringify($data) ); ";
        }
        else {
        	$script .= "localStorage.setItem( 'datatables-webstream', null ); ";
        }

        $settings = Application_Model_Preference::getPlaylistTableSetting();
        if (!is_null($settings)) {
        	$data = json_encode($settings);
        	$script .= "localStorage.setItem( 'datatables-playlist', JSON.stringify($data) ); ";
        }
        else {
        	$script .= "localStorage.setItem( 'datatables-playlist', null ); ";
        }

		return $script;
	}

	/*
	 * @param $obj MediaItem object.
	 * @return $service proper service for this item type.
	 */
	public function locateServiceType($obj) {

		$class = $obj->getDescendantClass();
		$class = explode("\\", $class);
		$type = array_pop($class);

		$serviceClass = "Application_Service_{$type}Service";
		return new $serviceClass();
	}

	public function createContextMenu($mediaId) {

		$mediaItem = MediaItemQuery::create()->findPK($mediaId);
		$obj = $mediaItem->getChildObject();

		$service = self::locateServiceType($mediaItem);

		return $service->createContextMenu($obj);
	}

	public function getJPlayerPreviewPlaylist($mediaId) {

		$mediaItem = MediaItemQuery::create()->findPK($mediaId);

		$type = $mediaItem->getType();

		$class = "Presentation_JPlayerItem{$type}";

		$jPlayerPlaylist = new $class($mediaItem);

		return $jPlayerPlaylist;
	}
}