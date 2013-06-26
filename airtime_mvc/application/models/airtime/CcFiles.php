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

    public function getDbLength($format = "H:i:s.u")
    {
        return parent::getDbLength($format);
    }

    public function setDbLength($v)
    {
        //we are using DateTime instead of DateInterval because the latter doesn't
        //support subseconds :(
        if ($v instanceof DateTime) {
            $dt = $v;
        }
        else {

            try {

                $dt = new DateTime($v);

            } catch (Exception $x) {
                throw new PropelException('Error parsing date/time value: ' .
                    var_export($v, true), $x);
            }
        }

        $this->length = $dt->format('H:i:s.u');
        $this->modifiedColumns[] = CcFilesPeer::LENGTH;

        return $this;
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
