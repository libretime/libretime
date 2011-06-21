<?php

require_once('Common.php');

/**
 * Skeleton subclass for representing a row from the 'cc_playlistcontents' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.campcaster
 */
class CcPlaylistcontents extends BaseCcPlaylistcontents {

	public function getDbFadein()
	{
		return $this->fadein;
	}

	public function setDbFadein($time)
    {
		$this->fadein = $time;
		//$this->modifiedColumns[] = CcPlaylistcontentsPeer::FADEIN;
        Common::setTimeInSub($this, 'FADEIN', $time);
    }

	public function getDbFadeout()
    {
        return $this->fadeout;
    }

	public function setDbFadeout($time)
    {
		$this->fadeout = $time;
		//$this->modifiedColumns[] = CcPlaylistcontentsPeer::FADEOUT;
        Common::setTimeInSub($this, 'FADEOUT', $time);
    }

	public function getDbCuein()
    {
        return $this->cuein;
    }

	public function setDbCuein($time)
    {
		$this->cuein = $time;
		//$this->modifiedColumns[] = CcPlaylistcontentsPeer::CUEIN;
        Common::setTimeInSub($this, 'CUEIN', $time);
    }

	public function getDbCueout()
    {
        return $this->cueout;
    }

    public function setDbCueout($time)
    {
		$this->cueout = $time;
		//$this->modifiedColumns[] = CcPlaylistcontentsPeer::CUEOUT;
        Common::setTimeInSub($this, 'CUEOUT', $time);
    }

	public function getDbCliplength()
    {
        return $this->cliplength;
    }

    public function setDbCliplength($time)
    {
        $this->cliplength = $time;
		//$this->modifiedColumns[] = CcPlaylistcontentsPeer::CLIPLENGTH;
        Common::setTimeInSub($this, 'CLIPLENGTH', $time);
    }




} // CcPlaylistcontents
