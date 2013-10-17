<?php

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
	
	public function getCueLength()
	{
		$cuein = $this->getDbCuein();
		$cueout = $this->getDbCueout();
		
		$cueinSec = Application_Common_DateHelper::calculateLengthInSeconds($cuein);
		$cueoutSec = Application_Common_DateHelper::calculateLengthInSeconds($cueout);
		$lengthSec = bcsub($cueoutSec, $cueinSec, 6);
		
		$length = Application_Common_DateHelper::secondsToPlaylistTime($lengthSec);
		
		return $length;
	}

    public function setDbTrackNumber($v)
    {
        $max = pow(2, 31)-1;
        $v = ($v > $max) ? $max : $v;

        return parent::setDbTrackNumber($v);
    }

    // returns true if the file exists and is not hidden
    public function visible() {
        return $this->getDbFileExists() && !$this->getDbHidden();
    }

    public function reassignTo($user) 
    {
        $this->setDbOwnerId( $user->getDbId() );
        $this->save();
    }

} // CcFiles
