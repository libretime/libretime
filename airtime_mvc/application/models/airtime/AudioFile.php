<?php

namespace Airtime\MediaItem;

use \Config;
use \Exception;
use \PropelException;
use Airtime\MediaItem\om\BaseAudioFile;
use Airtime\CcMusicDirsQuery;


/**
 * Skeleton subclass for representing a row from the 'audio_file' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.airtime
 */
class AudioFile extends BaseAudioFile
{
	// Metadata Keys for files
	// user editable metadata
	private $_userEditableMd = array (
		'MDATA_KEY_TITLE' => "TrackTitle",
		'MDATA_KEY_CREATOR' => "ArtistName",
		'MDATA_KEY_SOURCE' => "AlbumTitle",
		'MDATA_KEY_URL' => "InfoUrl",
		'MDATA_KEY_GENRE' => "Genre",
		'MDATA_KEY_MOOD' => "Mood",
		'MDATA_KEY_LABEL' => "Label",
		'MDATA_KEY_COMPOSER' => "Composer",
		'MDATA_KEY_ISRC' => "IsrcNumber",
		'MDATA_KEY_COPYRIGHT' => "Copyright",
		'MDATA_KEY_YEAR' => "Year",
		'MDATA_KEY_BPM' => "Bpm",
		'MDATA_KEY_TRACKNUMBER' => "TrackNumber",
		'MDATA_KEY_CONDUCTOR' => "Conductor",
		'MDATA_KEY_LANGUAGE' => "Language",
	);

	public function getName() {
		return $this->getTrackTitle();
	}

	public function getCreator() {
		return $this->getArtistName();
	}

	public function getRealFileExtension() {

		$path = $this->getFilepath();
		$path_elements = explode('.', $path);

		if (count($path_elements) < 2) {
			return "";
		} else {
			return $path_elements[count($path_elements) - 1];
		}
	}

	/**
	 * Return suitable extension.
	 *
	 * @return string
	 *         file extension without a dot
	 */
	public function getFileExtension() {

		$possible_ext = $this->getRealFileExtension();
		if ($possible_ext !== "") {
			return $possible_ext;
		}

		// We fallback to guessing the extension from the mimetype if we
		// cannot extract it from the file name

		$mime = $this->getMime();

		if ($mime == "audio/ogg" || $mime == "application/ogg") {
			return "ogg";
		}
		elseif ($mime == "audio/mp3" || $mime == "audio/mpeg") {
			return "mp3";
		}
		elseif ($mime == "audio/x-flac") {
			return "flac";
		}
		elseif ($mime == "audio/mp4") {
			return "mp4";
		}
		else {
			throw new Exception("Unknown $mime");
		}
	}

	public function getURI() {
		return self::getFileUrl();
	}

	/**
	 * Get the URL to access this file
	 */
	public function getFileUrl()
	{
		$CC_CONFIG = Config::getConfig();

		$protocol = empty($_SERVER['HTTPS']) ? "http" : "https";

		$serverName = $_SERVER['SERVER_NAME'];
		$serverPort = $_SERVER['SERVER_PORT'];
		$subDir = $CC_CONFIG['baseDir'];

		if ($subDir[0] === "/") {
			$subDir = substr($subDir, 1, strlen($subDir) - 1);
		}

		$baseUrl = "{$protocol}://{$serverName}:{$serverPort}/{$subDir}";

		return $this->getRelativeFileUrl($baseUrl);
	}

	/**
	 * Sometimes we want a relative URL and not a full URL. See bug
	 * http://dev.sourcefabric.org/browse/CC-2403
	 */
	public function getRelativeFileUrl($baseUrl)
	{
		return $baseUrl."api/get-media/file/".$this->getId().".".$this->getFileExtension();
	}

	/*
	 * @param string $key MDATA_KEY_TITLE
	 * @param mixed $value 'test_title'
	 *
	 */
	public function setMetadataValue($key, $value) {

		if (isset($this->_userEditableMd[$key])) {
			$propelColumn = $this->_userEditableMd[$key];
			$method = "set$propelColumn";

			$this->$method($value);
		}

		return $this;
	}

	public function setMetadata($md) {

		foreach ($md as $index => $value) {
			$this->setMetadataValue($index, $value);
		}

		return $this;
	}

	/**
	 * Get metadata as array.
	 *
	 * @return array
	 */
	public function getMetadata()
	{
		$md = array();
		foreach ($this->_userEditableMd as $mdColumn => $propelColumn) {
			$method = "get$propelColumn";
			$md[$mdColumn] = $this->$method();
		}

		return $md;
	}

	public function reactivateFile($md) {
		// If the file already exists we will update and make sure that
		// it's marked as 'exists'.
		$this->setFileExists(true);
		$this->setFileHidden(false);
		$this->setMetadata($md);

		return $this;
	}

	public function setFilepath($filepath) {

		$dir = CcMusicDirsQuery::create()
			->filterByFullFilepath($filepath)
			->findOne();

		if (isset($dir)) {
			$this->setCcMusicDirs($dir);
			parent::setFilepath($filepath);
		}
		else {
			throw new PropelException("Directory unknown for filepath $filepath");
		}

		return $this;
	}

	public function setTrackNumber($v) {

		if (!is_numeric($v)) {
			$v = null;
		}

		parent::setTrackNumber($v);

		return $this;
	}

	public function setBitRate($v) {

		if (!is_numeric($v)) {
			$v = null;
		}

		parent::setBitRate($v);

		return $this;
	}

	public function setSampleRate($v) {

		if (!is_numeric($v)) {
			$v = null;
		}

		parent::setSampleRate($v);

		return $this;
	}

	public function setYear($v) {

		// We need to make sure to clean this value before
		// inserting into database. If value is outside of range
		// [-2^31, 2^31-1] then postgresl will throw error when
		// trying to retrieve this value. We could make sure
		// number is within these bounds, but simplest is to do
		// substring to 4 digits (both values are garbage, but
		// at least our new garbage value won't cause errors).
		// If the value is 2012-01-01, then substring to first 4
		// digits is an OK result. CC-3771


		if (strlen($v) > 4) {
			$v = substr($v, 0, 4);
		}
		if (!is_numeric($v)) {
			$v = null;
		}

		parent::setYear($v);

		return $this;
	}

	public function getCueLength()
	{
		$cuein = $this->getCuein();
		$cueout = $this->getCueout();

		$cueinSec = \Application_Common_DateHelper::playlistTimeToSeconds($cuein);
		$cueoutSec = \Application_Common_DateHelper::playlistTimeToSeconds($cueout);
		$lengthSec = bcsub($cueoutSec, $cueinSec, 6);

		$length = \Application_Common_DateHelper::secondsToPlaylistTime($lengthSec);

		return $length;
	}

	// returns true if the file exists and is not hidden
	public function isVisible() {
		return $this->getFileExists() && !$this->getFileHidden();
	}

	public function isSchedulable() {
		return $this->isVisible();
	}

	public function getSchedulingLength() {
		return $this->getCueLength();
	}

	public function getSchedulingCueIn() {
		return $this->getCuein();
	}

	public function getSchedulingCueOut() {
		return $this->getCueout();
	}

	public function getSchedulingFadeIn() {
		return \Application_Model_Preference::GetDefaultFadeIn();
	}

	public function getSchedulingFadeOut() {
		return \Application_Model_Preference::GetDefaultFadeOut();
	}

	public function getScheduledContent() {

		return array (
			array (
				"id" => $this->getId(),
				"cliplength" => $this->getCueLength(),
				"cuein" => $this->getCuein(),
				"cueout" => $this->getCueout(),
				"fadein" => \Application_Model_Preference::GetDefaultFadeIn(),
				"fadeout" => \Application_Model_Preference::GetDefaultFadeOut(),
			)
		);
	}
}
