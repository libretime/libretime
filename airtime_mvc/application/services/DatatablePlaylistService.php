<?php

use Airtime\CcSubjsPeer;
use Airtime\MediaItem\PlaylistPeer;
use Airtime\MediaItem\PlaylistQuery;
use Airtime\MediaItemQuery;

class Application_Service_DatatablePlaylistService extends Application_Service_DatatableService
{
	protected $columns;
	
	protected $order = array (
		"ClassKey",
		"Name",
		"Description",
		"CreatedAt",
		"UpdatedAt",
		"CcSubjs.DbLogin",
		"Length",
	);
	
	protected $aliases = array(
	);
	
	//format classes used to manipulate data for presentation.
	protected $_formatters = array(
		"Length" => "Format_PlaylistLength"
	);
	
	public function __construct() {
	
		parent::__construct();
	}
	
	protected function getSettings() {
		return Application_Model_Preference::getPlaylistTableSetting();
	}
	
	protected function getColumns() {
	
		return array(
			"Id" => array(
				"isColumn" => false
			),
			"ClassKey" => array(
				"isColumn" => true,
				"title" => _("Type"),
				"width" => "25px",
				"class" => "library_type",
				"advancedSearch" => array(
					"type" => "select",
					"values" => array(
						"0" => "static", 
						"1" => "dynamic"
					)
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

			$row['CreatedAt'] = $this->enhanceRowDate($row['CreatedAt']);
			$row['UpdatedAt'] = $this->enhanceRowDate($row['UpdatedAt']);
		}
	}
	
	public function getDatatables($params) {
	
		Logging::enablePropelLogging();

		$q = PlaylistQuery::create();
		$results = self::buildQuery($q, $params);
	
		Logging::disablePropelLogging();
	
		return array(
			"count" => $results["count"],
			"totalCount" => $results["totalCount"],
			"records" => self::createOutput($results["media"], $this->columnKeys)
		);
	}
}