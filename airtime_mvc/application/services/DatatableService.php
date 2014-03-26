<?php

use Airtime\CcSubjsPeer;
use Airtime\MediaItem\WebstreamPeer;
use Airtime\MediaItem\PlaylistPeer;
use Airtime\MediaItem\AudioFilePeer;

use Airtime\MediaItem\AudioFileQuery;
use Airtime\MediaItem\WebstreamQuery;
use Airtime\MediaItem\PlaylistQuery;
use Airtime\MediaItemQuery;

abstract class Application_Service_DatatableService
{
	//format classes used to manipulate data for presentation.
	protected $_formatters = array(

	);
	
	public function __construct() {
		
		$this->columns = $this->getColumns();
		
		//can't rely on ordering with these settings as it can be one action behind.
		$this->settings = $this->getSettings();
		
		$this->columnKeys = array_keys($this->columns);
		
		$this->displayTimezone = new DateTimeZone(Application_Model_Preference::GetUserTimezone());
		$this->utcTimezone = new DateTimeZone("UTC");
	}
	
	//used for creating media search.
	//can't seem to call a class dynamically even though
	//http://www.php.net/manual/en/language.namespaces.importing.php
	//seems to say it's possible.
	protected $_ns = array(
		"AudioFilePeer" => "Airtime\MediaItem\AudioFilePeer",
		"PlaylistPeer" => "Airtime\MediaItem\PlaylistPeer",
		"WebstreamPeer" => "Airtime\MediaItem\WebstreamPeer",
		"CcSubjsPeer" => "Airtime\CcSubjsPeer",
	);
	
	protected $_propelTextTypes = array(
		PropelColumnTypes::VARCHAR,
		PropelColumnTypes::LONGVARCHAR,
		PropelColumnTypes::CHAR
	);
	
	public function makeDatatablesColumns() {
	
		$datatablesColumns = array();
	
		$columnOrder = $this->order;
		$columnInfo = $this->columns;
	
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
	
		$this->enhanceDatatablesColumns($datatablesColumns);
	
		return $datatablesColumns;
	}
	
	private function getColumnType($prop, $modelName) {
	
		if (strrpos($prop, ".") === false) {
			$base = $modelName;
			$column = $prop;
		}
		else {
			list($base, $column) = explode(".", $prop);
		}
	
		$b = explode("\\", $base);
		$b = array_pop($b);
		$class = $this->_ns["{$b}Peer"];
	
		$field = $class::translateFieldName($column, BasePeer::TYPE_PHPNAME, BasePeer::TYPE_FIELDNAME);
		
		$propelMap = $class::getTableMap();
		$col = $propelMap->getColumn($field);
		$type = $col->getType();
		$searchType = $this->columns[$prop]["advancedSearch"]["type"];
		
		return array(
			"type" => $type,
			"column" => "{$base}.{$column}",
			"searchType" => $searchType
		);
	}
	
	private function generateRandomString($length = 15)
	{
	    return substr(sha1(rand()), 0, $length);
	}
	
	private function searchNumberRange($query, $col, $from, $to) {
		$prefix = self::generateRandomString(5);
		$num = 0;
	
		if (isset($from) && is_numeric($from)) {
			$name = "{$prefix}_{$col}_from";
			$cond = "{$col} >= ?";
			$query->condition($name, $cond, $from);
			$num++;
		}
	
		if (isset($to) && is_numeric($to)) {
			$name = "{$prefix}_{$col}_to";
			$cond = "{$col} <= ?";
			$query->condition($name, $cond, $to);
			$num++;
		}
	
		if ($num > 1) {
			$name = "{$prefix}_{$col}_from_to";
			$query->combine(array("{$prefix}_{$col}_from", "{$prefix}_{$col}_to"), 'and', $name);
		}
	
		//returns the final query condition to combine with other columns.
		return $name;
	}
	
	private function searchNumber($query, $col, $value) {
		$prefix = self::generateRandomString(5);
		
		$name = "{$prefix}_{$col}";
		$cond = "{$col} = ?";
		$query->condition($name, $cond, $value);
		
		//returns the final query condition to combine with other columns.
		return $name;
	}
	
	//need to return name of condition so that
	//all advanced search fields can be combined into an AND.
	private function searchString($query, $col, $value) {
		$prefix = self::generateRandomString(5);
		
		$name = "{$prefix}_{$col}";
		$cond = "{$col} iLIKE ?";
		$param = "%{$value}%";
		$query->condition($name, $cond, $param);
	
		return $name;
	}
	
	private function searchDateRange($query, $col, $from, $to) {
		$num = 0;
		$prefix = self::generateRandomString(5);
	
		if (isset($from) && preg_match_all('/(\d{4}-\d{2}-\d{2})/', $from)) {
			$name = "{$prefix}_{$col}_from";
			$cond = "{$col} >= ?";
	
			$query->condition($name, $cond, $from);
			$num++;
		}
	
		if (isset($to) && preg_match_all('/(\d{4}-\d{2}-\d{2})/', $to)) {
			$name = "{$prefix}_{$col}_to";
			$cond = "{$col} <= ?";

			$query->condition($name, $cond, $to);
			$num++;
		}
	
		if ($num > 1) {
			$name = "{$prefix}_{$col}_from_to";
			$query->combine(array("{$prefix}_{$col}_from", "{$prefix}_{$col}_to"), 'and', $name);
		}
	
		//returns the final query condition to combine with other columns.
		return $name;
	}
	
	protected function isVisible($prop) {
	
		$origPropOrder = array_flip($this->order);
		$origIndex = $origPropOrder[$prop];
		
		$origIndex++; //hacky, but used because of added checkbox column for now.
		$currIndex = $this->settings["ColReorder"][$origIndex];
		
		$vis = $this->settings["abVisCols"][$currIndex];
		
		return ($vis === "true" ? true : false);
	}
	
	protected function buildQuery($query, $params) {
		//namespacing seems to cause a problem in the WHERE clause
		//if we don't prefix the PHP name with the model or alias.
		$modelName = $query->getModelName();
	
		$query->setFormatter('PropelOnDemandFormatter');
	
		$totalCount = $query->count();
	
		//add advanced search terms to query.
		$len = intval($params["iColumns"]);
		
		//regular search terms
		$search = $params["sSearch"];
		$searchTerms = $search == "" ? array() : explode(" ", $search);
		$searchTermCount = count($searchTerms);
		
		//from advanced search
		$advConds = array();
		//general search if column is visible and is varchar/text
		$regularConds = array();
		//combined conditions
		$searchConds = array();
		
		for ($i = 0; $i < $len; $i++) {
	
			$prop = $params["mDataProp_{$i}"];
	
			if ($params["bSearchable_{$i}"] === "true"
				&& in_array($prop, $this->columnKeys)
				&& !in_array($prop, $this->aliases)) {
	
				$info = self::getColumnType($prop, $modelName);
				$searchCol = $info["column"];
				$type = $info["type"];
				$searchType = $info["searchType"];
				
				if ($params["sSearch_{$i}"] != "") {
					$value = $params["sSearch_{$i}"];
					
					switch($type) {
						case PropelColumnTypes::DATE:
						case PropelColumnTypes::TIMESTAMP:
							$advConds[] = self::searchDateRange($query, $searchCol, $value["from"], $value["to"]);
							break;
						case PropelColumnTypes::NUMERIC:
						case PropelColumnTypes::INTEGER:
							if ($searchType == "number-range") {
								$advConds[] = self::searchNumberRange($query, $searchCol, $value["from"], $value["to"]);
							}
							else {
								$advConds[] = self::searchNumber($query, $searchCol, $value);
							}
							break;
						default:
							$advConds[] = self::searchString($query, $searchCol, $value);
							break;
					}
				}
				
				if (self::isVisible($prop) && in_array($type, $this->_propelTextTypes)) {
					
					for ($s = 0; $s < $searchTermCount; $s++) {
						
						$regularConds[$s][] = self::searchString($query, $searchCol, $searchTerms[$s]);
					}	
				}
			}
		}
		
		if ($searchTermCount > 0) {
			
			for ($s = 0; $s < $searchTermCount; $s++) {
			
				$name = "regular_search_{$s}";
				$query->combine($regularConds[$s], 'or', $name);
				$advConds[] = $name;
			}
		}
		
		if (count($advConds) > 0) {
			$query->where($advConds, 'and');
		}
	
		//ORDER BY statements
		$len = intval($params["iSortingCols"]);
		for ($i = 0; $i < $len; $i++) {
	
			$colNum = $params["iSortCol_{$i}"];
	
			if ($params["bSortable_{$colNum}"] == "true") {
				$colName = $params["mDataProp_{$colNum}"];
				$colDir = $params["sSortDir_{$i}"] === "asc" ? Criteria::ASC : Criteria::DESC;
	
				//need to lowercase the column name for the syntax generated by propel
				//to work properly in postgresql.
				if (in_array($colName, $this->aliases)) {
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
	
	protected function makeArray(&$array, &$getters, $obj, $formatter=null) {
	
		$key = array_shift($getters);
		$method = "get{$key}";
	
		if (count($getters) == 0) {
			
			if (is_null($formatter)) {
				$array[$key] = $obj->$method();
			}
			else {
				$class = new $formatter($obj);
				$array[$key] = $class->$method();
			}

			return;
		}
	
		if (empty($array[$key])) {
			$array[$key] = array();
		}
		$a =& $array[$key];
		$nextObj = $obj->$method();
	
		return self::makeArray($a, $getters, $nextObj, $formatter);
	}
	
	/*
	 * @param $coll PropelCollection formatted on demand.
	*
	* @return $output, an array of data with the columns needed for datatables.
	*/
	protected function createOutput($coll, $columns) {
	
		$output = array();
		foreach ($coll as $media) {
	
			$item = array();
			foreach ($columns as $column) {
	
				$formatter = null;
				if (isset($this->_formatters[$column])) {
					$formatter = $this->_formatters[$column];
				}
				$getters = explode(".", $column);
				self::makeArray($item, $getters, $media, $formatter);
			}
	
			$output[] = $item;
		}
	
		$this->enhanceDatatablesOutput($output);
	
		return $output;
	}
	
	protected function enhanceRowDate($utcDateTimeString) {
		
		$date = new DateTime($utcDateTimeString, $this->utcTimezone);
		$date->setTimeZone($this->displayTimezone);
		return $date->format('Y-m-d H:i:s');
	}
	
	//return null or a formatted datetime string in UTC
	protected function filterDate($userDateTimeString) {
	
		$filtered = null;
	
		if (isset($userDateTimeString)) {
			$date = new DateTime($userDateTimeString, $this->displayTimezone);
			$date->setTimeZone($this->utcTimezone);
			$filtered = $date->format('Y-m-d H:i:s');
		}
	
		return $filtered;
	}
	
	protected abstract function getColumns();
	protected abstract function getSettings();
}