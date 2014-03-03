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
	public function __construct() {
		
		$this->columns = $this->getColumns();
		$this->settings = $this->getSettings();
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
	
	/*
	 * add display only columns such as checkboxs to the datatables response.
	* these should not be columns that could be calculated in the DB query.
	*/
	protected function enhanceDatatablesOutput(&$output) {
	
		//add in data for the display columns.
		foreach ($output as &$row) {
			$row["Checkbox"] = '<input type="checkbox">';
		}
	}
	
	public function getColumns();
	public function getSettings();
}