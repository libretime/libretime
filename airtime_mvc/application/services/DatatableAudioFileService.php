<?php

use Airtime\CcSubjsPeer;
use Airtime\MediaItem\WebstreamPeer;
use Airtime\MediaItem\PlaylistPeer;
use Airtime\MediaItem\AudioFilePeer;

use Airtime\MediaItem\AudioFileQuery;
use Airtime\MediaItem\WebstreamQuery;
use Airtime\MediaItem\PlaylistQuery;
use Airtime\MediaItemQuery;

class Application_Service_DatatableAudioFileService extends Application_Service_DatatableService
{
	protected $columns; 
	
	protected $order = array (
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
		"CueLength",
		"Mime",
		"Mood",
		"CcSubjs.DbLogin",
		"ReplayGain",
		"SampleRate",
		"TrackNumber",
		"CreatedAt",
		"InfoUrl",
		"Year",
		"PlayCount"
	);
	
	protected $aliases = array(
		"CueLength",
	);
	
	//format classes used to manipulate data for presentation.
	protected $_formatters = array(
		"BitRate" => "Format_AudioFileBitRate"	
	);
	
	public function __construct() {
		
		parent::__construct();
	}
	
	protected function getSettings() {
		return Application_Model_Preference::getAudioTableSetting();
	}
	
	protected function getColumns() {

		return array(
			"Id" => array(
				"isColumn" => false,
				"advancedSearch" => array(
					"type" => null
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
				"title" => _("Track"),
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
			"PlayCount" => array(
				"isColumn" => true,
				"title" => _("Play Count"),
				"width" => "60px",
				"class" => "library_playcount",
				"visible" => false,
				"advancedSearch" => array(
					"type" => "number-range"
				)
			),
		);
	}
	
	/*
	 * add display only columns such as checkboxs to the datatables response.
	* these should not be columns that could be calculated in the DB query.
	*/
	protected function enhanceDatatablesColumns(&$datatablesColumns) {
	
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
	
	protected function enhanceDatatablesOutput(&$output) {
		
		//add in data for the display columns.
		foreach ($output as &$row) {
			$row["Checkbox"] = '<input type="checkbox">';

			$formatter = new Format_HHMMSSULength($row["Cuein"]);
			$row["Cuein"] = $formatter->format();
			
			$formatter = new Format_HHMMSSULength($row["Cueout"]);
			$row["Cueout"] = $formatter->format();
			
			$formatter = new Format_HHMMSSULength($row["CueLength"]);
			$row["CueLength"] = $formatter->format();
			
			$formatter = new Format_Samplerate($row['SampleRate']);
			$row['SampleRate'] = $formatter->format();
			
			$row['CreatedAt'] = $this->enhanceRowDate($row['CreatedAt']);
			$row['UpdatedAt'] = $this->enhanceRowDate($row['UpdatedAt']);
			
			if (isset($row["LastPlayedTime"])) {
				$row['LastPlayedTime'] = $this->enhanceRowDate($row['LastPlayedTime']);
			}
		}
		
	}
	
	//params is given from datatables
	//need to format data correctly for search,
	//datetimes should be in UTC etc.
	//pretty much undoing things that have been done in function enhanceDatatablesOutput
	protected function filterSearchParams(&$params) {

		//advanced search has not been initialized yet.
		if (empty($params["sRangeSeparator"])) {
			return;
		}
		
		$len = intval($params["iColumns"]);
		$separator = $params["sRangeSeparator"];
		
		for ($i = 0; $i < $len; $i++) {
		
			$prop = $params["mDataProp_{$i}"];
			$search = $params["sSearch_{$i}"];
			
			if ($search === "") {
				continue;
			}
			
			switch ($prop) {
				
				case "Bpm":
				case "ReplayGain":
				case "Year":
				case "PlayCount":
					$filtered = array();
					$range = explode($separator, $search);
					$filtered["from"] = isset($range[0]) && $range[0] !== "" ? intval($range[0]) : null;
					$filtered["to"] = isset($range[1]) && $range[1] !== "" ? intval($range[1]) : null;
					$params["sSearch_{$i}"] = $filtered;
					break;
				case "BitRate":
				case "SampleRate":
					$filtered = array();
					$range = explode($separator, $search);
					$filtered["from"] = isset($range[0]) && $range[0] !== "" ? floatval($range[0])* 1000 : null;
					$filtered["to"] = isset($range[1]) && $range[1] !== "" ? floatval($range[1])* 1000 : null;
					$params["sSearch_{$i}"] = $filtered;
					break;
				case "CreatedAt":
				case "UpdatedAt":
				case "LastPlayedTime":
					$filtered = array();
					$range = explode($separator, $search);
					Logging::info($range);
					$filtered["from"] = isset($range[0]) && $range[0] !== "" ? $this->filterDate($range[0]) : null;
					$filtered["to"] = isset($range[1]) && $range[1] !== "" ? $this->filterDate($range[1]) : null;
					$params["sSearch_{$i}"] = $filtered;
					break;
			}
		}
	}
	
	public function getDatatables($params) {
	
		Logging::enablePropelLogging();
	
		$q = AudioFileQuery::create();
	
		$m = $q->getModelName();
		$q->withColumn("({$m}.Cueout - {$m}.Cuein)", "cuelength");
		$q->joinWith("CcSubjs");
		
		$this->filterSearchParams($params);
	
		$results = self::buildQuery($q, $params);
	
		Logging::disablePropelLogging();
	
		return array(
			"count" => $results["count"],
			"totalCount" => $results["totalCount"],
			"records" => $this->createOutput($results["media"], $this->columnKeys)
		);
	}
}