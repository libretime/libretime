<?php

namespace Airtime;

use Airtime\om\BaseCcMusicDirsQuery;
use Airtime\CcMusicDirsPeer;


/**
 * Skeleton subclass for performing query and update operations on the 'cc_music_dirs' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.airtime
 */
class CcMusicDirsQuery extends BaseCcMusicDirsQuery
{
	
	public function filterByFullFilepath($filepath) {
	
		$directory = CcMusicDirsPeer::DIRECTORY;
		
		$escapedPath = pg_escape_string($filepath);
	
		return $this
			->filterByType(array("watched", "stor"))
			->filterByExists(true)
			->filterByWatched(true)
			->where($this->getModelAliasOrName().".Directory = substring('$escapedPath' from 0 for char_length($directory)+1)")
			;
	}
}
