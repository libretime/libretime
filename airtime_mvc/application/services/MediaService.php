<?php

use Airtime\CcSubjsPeer;
use Airtime\MediaItem\WebstreamPeer;
use Airtime\MediaItem\PlaylistPeer;
use Airtime\MediaItem\AudioFilePeer;

use Airtime\MediaItem\AudioFileQuery;
use Airtime\MediaItem\WebstreamQuery;
use Airtime\MediaItem\PlaylistQuery;

class Application_Service_MediaService
{
	private function getAudioFileColumnDetails() {
		
		return array(
			"Id" => array(
				"isColumn" => false
			),
			"TrackTitle" => array(
				"isColumn" => true,
				"title" => _("Title"),
				"width" => "170px",
				"class" => "library_title"
			),
			"ArtistName" => array(
				"isColumn" => true,
				"title" => _("Creator"),
				"width" => "160px",
				"class" => "library_creator"
			),
			"AlbumTitle" => array(
				"isColumn" => true,
				"title" => _("Album"),
				"width" => "150px",
				"class" => "library_album"
			),
		);
	}
	
	private function getWebstreamColumnDetails() {
	
		return array(
			"Id" => array(
				"isColumn" => false
			),
			"Name" => array(
				"isColumn" => true,
				"title" => _("Title"),
				"width" => "170px",
				"class" => "library_title"
			),
			"CcSubjs.DbLogin" => array(
				"isColumn" => true,
				"title" => _("Owner"),
				"width" => "160px",
				"class" => "library_owner"
			)
		);
	}
	
	private function getAudioFileDatatableColumnOrder() {

		return array (
			"TrackTitle",
			"ArtistName",
			"AlbumTitle",
		);
	}
	
	private function getWebstreamDatatableColumnOrder() {
	
		return array (
			"Name",
			"CcSubjs.DbLogin",
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
				//replacing the dots because datatables will expect a nested array for joined tables
				//and propel is giving us a single dimension array.
				"mDataProp" => $columnOrder[$i],
				"bSortable" => isset($data["sortable"]) ? $data["sortable"] : true,
				"bSearchable" => isset($data["searchable"]) ? $data["searchable"] : true,
				"bVisible" => isset($data["visible"]) ? $data["visible"] : true,
				"sWidth" => $data["width"],
				"sClass" => $data["class"],
			);
		}
		
		return $datatablesColumns;
	}
	
	private function buildQuery($query, $params) {
		
		$alias = "media";
		
		$selectColumns = array();
		
		$len = intval($params["iColumns"]);
		for ($i = 0; $i < $len; $i++) {
			$selectColumns[] = $params["mDataProp_{$i}"];	
		}
		
		//$query->select($selectColumns);
		
		//$query->setFormatter('PropelArrayFormatter');
		$query->setFormatter('PropelOnDemandFormatter');
		
		//all media join this table for the "Owner" column;
		//removing the "." access since PropelSimpleArrayFormatter returns a flat array
		//Datatables is expecting a nested object if there is a "." in the name.
		//would be nice to extend class PropelSimpleArrayFormatter if possible to include
		//nested associative arrays in the output.
		$query->joinWith("CcSubjs");
		
		//take care of WHERE clause
		$search = $params["sSearch"];
		$searchTerms = $search == "" ? array() : explode(" ", $search);
		$andConditions = array();
		$orConditions = array();
		
		//namespacing seems to cause a problem in the WHERE clause 
		//if we don't prefix the PHP name with the model or alias.
		$modelName = $query->getModelName();
		foreach ($searchTerms as $term) {
			
			$orConditions = array();
			
			$len = intval($params["iColumns"]);
			for ($i = 0; $i < $len; $i++) {
				
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

		//ORDER BY statements
		$len = intval($params["iSortingCols"]);
		for ($i = 0; $i < $len; $i++) {
			
			$colNum = $params["iSortCol_{$i}"];
			$colName = $params["mDataProp_{$colNum}"];
			$colDir = $params["sSortDir_{$i}"] === "asc" ? Criteria::ASC : Criteria::DESC;
			
			$query->orderBy($colName, $colDir);
		}
		
		//LIMIT OFFSET statements
		$limit = intval($params["iDisplayLength"]);
		$offset = intval($params["iDisplayStart"]);
		
		$query
			->limit($limit)
			->offset($offset);
		
		Logging::info($query->toString());
		
		return $query;
	}
	
	private function columnMapCallback($class) {
		
		$func = function ($column) use ($class) {
		
			return $class::translateFieldName($column, BasePeer::TYPE_COLNAME, BasePeer::TYPE_PHPNAME);
		};
		
		return $func;
	}
	
	/*
	 * @param $coll PropelCollection formatted on demand.
	 * 
	 * @return $output, an array of data with the columns needed for datatables.
	 */
	private function createOutput($coll, $columns) {
		
		$output = array();
		$item;
		
		foreach ($coll as $media) {
				
			$item = array();
				
			foreach ($columns as $column) {
		
				$x = $media;
				$a = $item;
				$getters = explode(".", $column);
		
				foreach ($getters as $attr) {
					
					$k = $attr;
					$method = "get{$attr}";
					$x = $x->$method();
				}
		
				$item[$column] = $x;
			}
				
			$output[] = $item;
		}
		
		return $output;
	}
	
	public function getDatatablesAudioFiles($params) {
		
		$columns = self::getAudioFileDatatableColumnOrder();
		
		$q = AudioFileQuery::create();
		$q = self::buildQuery($q, $params);
		$coll = $q->find();
		
		return self::createOutput($coll, $columns);
	}
	
	public function getDatatablesWebstreams($params) {
		
		$columns = self::getWebstreamDatatableColumnOrder();
	
		$q = WebstreamQuery::create();
		$q = self::buildQuery($q, $params);
		$coll = $q->find();
	
		return self::createOutput($coll, $columns);
	}
	
	public function getDatatablesPlaylists($params) {
	
		$columns = self::getPlaylistDatatableColumnOrder();
		
		$q = PlaylistQuery::create();
		$q = self::buildQuery($q, $params);
		$coll = $q->find();
	
		return self::createOutput($coll, $columns);
	}
}