<?php

use Airtime\CcSubjsPeer;
use Airtime\MediaItem\WebstreamPeer;
use Airtime\MediaItem\WebstreamQuery;
use Airtime\MediaItemQuery;

class Application_Service_DatatableWebstreamService extends Application_Service_DatatableService
{
	protected $columns;
	
	protected $order = array (
		"Name",
		"Mime",
		"Url",
		"CcSubjs.DbLogin",
		"CreatedAt",
		"UpdatedAt",
		"Length",
	);
	
	protected $aliases = array(
	);
	
	public function __construct() {
	
		parent::__construct();
	}
	
	protected function getSettings() {
		return Application_Model_Preference::getWebstreamTableSetting();
	}
	
	protected function getColumns() {

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
	
	public function getDatatables($params) {
	
		Logging::enablePropelLogging();
	
		$q = WebstreamQuery::create();
		$q->joinWith("CcSubjs");
		$results = self::buildQuery($q, $params);
	
		Logging::disablePropelLogging();
	
		return array(
			"count" => $results["count"],
			"totalCount" => $results["totalCount"],
			"records" => $this->createOutput($results["media"], $this->columnKeys)
		);
	}
}