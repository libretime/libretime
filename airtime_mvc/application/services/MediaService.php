<?php

use Airtime\MediaItem\AudioFileQuery;

use Airtime\MediaItem\AudioFilePeer;

class Application_Service_MediaService
{
	private function getAudioColumnDetails() {
	
		return array(
			AudioFilePeer::ID => array(
				"isColumn" => false
			),
			AudioFilePeer::TRACK_TITLE => array(
				"isColumn" => true,
				"title" => _("Title"),
				"width" => "170px",
				"class" => "library_title"
			),
			AudioFilePeer::ARTIST_NAME => array(
				"isColumn" => true,
				"title" => _("Creator"),
				"width" => "160px",
				"class" => "library_creator"
			),
			AudioFilePeer::ALBUM_TITLE => array(
				"isColumn" => true,
				"title" => _("Album"),
				"width" => "150px",
				"class" => "library_album"
			),
		);
	}
	
	private function getAudioDatatableColumnOrder() {
	
		return array (
			AudioFilePeer::TRACK_TITLE,
			AudioFilePeer::ARTIST_NAME,
			AudioFilePeer::ALBUM_TITLE,
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
				"mDataProp" => AudioFilePeer::translateFieldName($columnOrder[$i], BasePeer::TYPE_COLNAME, BasePeer::TYPE_PHPNAME),
				"bSortable" => isset($data["sortable"]) ? $data["sortable"] : true,
				"bSearchable" => isset($data["searchable"]) ? $data["searchable"] : true,
				"bVisible" => isset($data["visible"]) ? $data["visible"] : true,
				"sWidth" => $data["width"],
				"sClass" => $data["class"],
			);
		}
		
		return $datatablesColumns;
	}
	
	public function getDatatablesAudioFiles($params) {
		
		$func = function ($column) {
		
			return AudioFilePeer::translateFieldName($column, BasePeer::TYPE_COLNAME, BasePeer::TYPE_PHPNAME);
		};

		$columns = array_keys(self::getAudioColumnDetails());
		$selectColumns = array_map($func, $columns);
		
		$q = AudioFileQuery::create();
		$q->select($selectColumns);
		$coll = $q->find();
		
		return $coll->toArray();
	}
}