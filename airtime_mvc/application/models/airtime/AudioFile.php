<?php

namespace Airtime\MediaItem;

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
	
	public function setMetadata($md) {
	
		foreach ($md as $index => $value) {
	
			if (isset($this->_userEditableMd[$index])) {
				$propelColumn = $this->_userEditableMd[$index];
				$method = "set$propelColumn";
					
				$this->$method($value);
			}
		}
		
		return $this;
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
		
		if ($v === "") {
			$v = null;
		}
		
		parent::setTrackNumber($v);
		
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
}
