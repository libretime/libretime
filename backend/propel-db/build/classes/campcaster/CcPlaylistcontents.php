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
		return Common::getTimeInSub($this, 'FADEIN');
	}

	public function setDbFadein($time)
        {
                return Common::setTimeInSub($this, 'FADEIN', $time);
        }

	public function getDbFadeout()
        {
                return Common::getTimeInSub($this, 'FADEOUT');
        }

	public function setDbFadeout($time)
        {
                return Common::setTimeInSub($this, 'FADEOUT', $time);
        }

	public function getDbCuein()
        {
                return Common::getTimeInSub($this, 'CUEIN');
        }

	public function setDbCuein($time)
        {
                return Common::setTimeInSub($this, 'CUEIN', $time);
        }

	public function getDbCueout()
        {
                return Common::getTimeInSub($this, 'CUEOUT');
        }

        public function setDbCueout($time)
        {
                return Common::setTimeInSub($this, 'CUEOUT', $time);
        }

	public function getDbCliplength()
        {
                return Common::getTimeInSub($this, 'CLIPLENGTH');
        }

        public function setDbCliplength($time)
        {
                return Common::setTimeInSub($this, 'CLIPLENGTH', $time);
        }




} // CcPlaylistcontents
