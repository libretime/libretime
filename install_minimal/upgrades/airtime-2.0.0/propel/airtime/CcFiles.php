<?php

require_once('Common.php');

/**
 * Skeleton subclass for representing a row from the 'cc_files' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.campcaster
 */
class CcFiles extends BaseCcFiles {

	public function getDbLength()
    {
        return $this->length;
    }

    public function setDbLength($time)
    {
		$this->length = $time;
		//$this->modifiedColumns[] = CcPlaylistcontentsPeer::LENGTH;
        return Common::setTimeInSub($this, 'LENGTH', $time);
    }


} // CcFiles
