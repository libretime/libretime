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
				"isColumn" => false
			),
			"IsScheduled" => array(
				"isColumn" => true,
				"title" => _("Scheduled"),
				"width" => "90px",
				"class" => "library_is_scheduled",
				"searchable" => false,
			),
			"IsPlaylist" => array(
				"isColumn" => true,
				"title" => _("Playlist"),
				"width" => "90px",
				"class" => "library_is_playlist",
				"searchable" => false,
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
			"BitRate" => array(
				"isColumn" => true,
				"title" => _("Bit Rate"),
				"width" => "80px",
				"class" => "library_bitrate",
				"visible" => false,
				"searchable" => false,
			),
			"Bpm" => array(
				"isColumn" => true,
				"title" => _("BPM"),
				"width" => "50px",
				"class" => "library_bpm",
				"visible" => false,
				"searchable" => false,
			),
			"Composer" => array(
				"isColumn" => true,
				"title" => _("Composer"),
				"width" => "150px",
				"class" => "library_composer",
				"visible" => false,
			),
			"Conductor" => array(
				"isColumn" => true,
				"title" => _("Conductor"),
				"width" => "125px",
				"class" => "library_conductor",
				"visible" => false,
			),
			"Copyright" => array(
				"isColumn" => true,
				"title" => _("Copyright"),
				"width" => "125px",
				"class" => "library_copyright",
				"visible" => false,
			),
			"Cuein" => array(
				"isColumn" => true,
				"title" => _("Cue In"),
				"width" => "80px",
				"class" => "library_length",
				"visible" => false,
				"searchable" => false,
			),
			"Cueout" => array(
				"isColumn" => true,
				"title" => _("Cue Out"),
				"width" => "80px",
				"class" => "library_length",
				"visible" => false,
				"searchable" => false,
			),
			"EncodedBy" => array(
				"isColumn" => true,
				"title" => _("Encoded By"),
				"width" => "150px",
				"class" => "library_encoded",
				"visible" => false,
			),
			"Genre" => array(
				"isColumn" => true,
				"title" => _("Genre"),
				"width" => "100px",
				"class" => "library_genre",
				"visible" => false,
			),
			"IsrcNumber" => array(
				"isColumn" => true,
				"title" => _("ISRC"),
				"width" => "150px",
				"class" => "library_isrc",
				"visible" => false,
			),
			"Label" => array(
				"isColumn" => true,
				"title" => _("Label"),
				"width" => "125px",
				"class" => "library_label",
				"visible" => false,
			),
			"Language" => array(
				"isColumn" => true,
				"title" => _("Language"),
				"width" => "125px",
				"class" => "library_language",
				"visible" => false,
			),
			"UpdatedAt" => array(
				"isColumn" => true,
				"title" => _("Last Modified"),
				"width" => "125px",
				"class" => "library_modified_time",
				"visible" => false,
				"searchable" => false,
			),
			"LastPlayedTime" => array(
				"isColumn" => true,
				"title" => _("Last Played"),
				"width" => "125px",
				"class" => "library_modified_time",
				"visible" => false,
				"searchable" => false,
			),
			"CueLength" => array(
				"isColumn" => true,
				"title" => _("Length"),
				"width" => "80px",
				"class" => "library_length",
				"searchable" => false,
			),
			"Mime" => array(
				"isColumn" => true,
				"title" => _("Mime"),
				"width" => "80px",
				"class" => "library_mime",
				"visible" => false,
			),
			"Mood" => array(
				"isColumn" => true,
				"title" => _("Mood"),
				"width" => "70px",
				"class" => "library_mood",
				"visible" => false,
			),	
			"CcSubjs.DbLogin" => array(
				"isColumn" => true,
				"title" => _("Owner"),
				"width" => "125px",
				"class" => "library_owner",
				"visible" => false
			),
			"ReplayGain" => array(
				"isColumn" => true,
				"title" => _("Replay Gain"),
				"width" => "80px",
				"class" => "library_replay_gain",
				"visible" => false,
				"searchable" => false,
			),
			"SampleRate" => array(
				"isColumn" => true,
				"title" => _("Sample Rate"),
				"width" => "80px",
				"class" => "library_sr",
				"visible" => false,
				"searchable" => false,
			),
			"TrackNumber" => array(
				"isColumn" => true,
				"title" => _("Track number"),
				"width" => "65px",
				"class" => "library_track",
				"visible" => false,
				"searchable" => false,
			),
			"CreatedAt" => array(
				"isColumn" => true,
				"title" => _("Uploaded"),
				"width" => "125px",
				"class" => "library_upload_time",
				"visible" => false,
				"searchable" => false,
			),
			"InfoUrl" => array(
				"isColumn" => true,
				"title" => _("Website"),
				"width" => "150px",
				"class" => "library_url",
				"visible" => false,
			),
			"Year" => array(
				"isColumn" => true,
				"title" => _("Year"),
				"width" => "60px",
				"class" => "library_year",
				"visible" => false,
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
				"class" => "library_title"
			),
			"Mime" => array(
				"isColumn" => true,
				"title" => _("Mime"),
				"width" => "80px",
				"class" => "library_mime",
			),
			"Url" => array(
				"isColumn" => true,
				"title" => _("Url"),
				"width" => "150px",
				"class" => "library_url",
			),
			"CreatedAt" => array(
				"isColumn" => true,
				"title" => _("Created"),
				"width" => "125px",
				"class" => "library_upload_time",
				"visible" => false,
				"searchable" => false,
			),
			"UpdatedAt" => array(
				"isColumn" => true,
				"title" => _("Last Modified"),
				"width" => "125px",
				"class" => "library_modified_time",
				"visible" => false,
				"searchable" => false,
			),
			"CcSubjs.DbLogin" => array(
				"isColumn" => true,
				"title" => _("Owner"),
				"width" => "160px",
				"class" => "library_owner"
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
				"class" => "library_type"
			),
			"Name" => array(
				"isColumn" => true,
				"title" => _("Title"),
				"width" => "170px",
				"class" => "library_title"
			),
			"Description" => array(
				"isColumn" => true,
				"title" => _("Description"),
				"width" => "200px",
				"class" => "library_description"
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
				"searchable" => false,
			),
			"UpdatedAt" => array(
				"isColumn" => true,
				"title" => _("Last Modified"),
				"width" => "125px",
				"class" => "library_modified_time",
				"visible" => false,
				"searchable" => false,
			),
			"CcSubjs.DbLogin" => array(
				"isColumn" => true,
				"title" => _("Owner"),
				"width" => "160px",
				"class" => "library_owner"
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
			);
		}
		
		self::enhanceDatatablesColumns($datatablesColumns);
		
		return $datatablesColumns;
	}
	
	private function buildQuery($query, $params, $dataColumns, $aliasedColumns) {
		
		$len = intval($params["iColumns"]);
		for ($i = 0; $i < $len; $i++) {
			
			$prop = $params["mDataProp_{$i}"];
			
			//if it's not in this array then it's a display only column.
			if (in_array($prop, $dataColumns)) {
				$selectColumns[] = $prop;
			}		
		}
		
		$query->setFormatter('PropelOnDemandFormatter');
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
		
		//LIMIT OFFSET statements
		$limit = intval($params["iDisplayLength"]);
		$offset = intval($params["iDisplayStart"]);
		
		$query
			->limit($limit)
			->offset($offset);
		
		//Logging::info($query->toString());
		
		return $query;
	}
	
	private function columnMapCallback($class) {
		
		$func = function ($column) use ($class) {
		
			return $class::translateFieldName($column, BasePeer::TYPE_COLNAME, BasePeer::TYPE_PHPNAME);
		};
		
		return $func;
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
		
		$columns = array_keys(self::getAudioFileColumnDetails());
		$aliases = self::getAudioFileColumnAliases();
		
		$q = AudioFileQuery::create();
		
		$m = $q->getModelName();
		$q->withColumn("({$m}.Cueout - {$m}.Cuein)", "cuelength");
		
		$q = self::buildQuery($q, $params, $columns, $aliases);
		$coll = $q->find();
		
		return self::createOutput($coll, $columns);
	}
	
	public function getDatatablesWebstreams($params) {
		
		$columns = array_keys(self::getWebstreamColumnDetails());
		$aliases = self::getWebstreamColumnAliases();
	
		$q = WebstreamQuery::create();
		$q = self::buildQuery($q, $params, $columns, $aliases);
		$coll = $q->find();
	
		return self::createOutput($coll, $columns);
	}
	
	public function getDatatablesPlaylists($params) {
	
		$columns = array_keys(self::getPlaylistColumnDetails());
		$aliases = self::getPlaylistColumnAliases();
		
		$q = PlaylistQuery::create();
		$q = self::buildQuery($q, $params, $columns, $aliases);
		$coll = $q->find();
	
		return self::createOutput($coll, $columns);
	}
	
	public function getMediaViewScript() {
		
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
		$script = "localStorage.setItem( 'datatables-audiofile-aoColumns', JSON.stringify($columns) ); ";
		
		//set webstream columns for display of data.
		$columns = json_encode(self::makeDatatablesColumns('Webstream'));
		$script .= "localStorage.setItem( 'datatables-webstream-aoColumns', JSON.stringify($columns) ); ";
		
		//set playlist columns for display of data.
		$columns = json_encode(self::makeDatatablesColumns('Playlist'));
		$script .= "localStorage.setItem( 'datatables-playlist-aoColumns', JSON.stringify($columns) ); ";
		
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
}