<?php

namespace Airtime\MediaItem;

use Airtime\MediaItem\om\BasePlaylist;
use \Criteria;
use \PropelPDO;
use \Exception;
use \Logging;

const RULE_REPEAT_TRACKS = "repeat-tracks";
const RULE_USERS_TRACKS_ONLY = "owners-tracks";

/**
 * Skeleton subclass for representing a row from the 'playlist' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.airtime
 */
abstract class Playlist extends BasePlaylist implements \Interface_Playlistable
{
	public function applyDefaultValues() {
		parent::applyDefaultValues();

		$this->name = _('Untitled Playlist');
		$this->modifiedColumns[] = PlaylistPeer::NAME;
		
		$defaultRules = array(
			RULE_REPEAT_TRACKS => true,
			RULE_USERS_TRACKS_ONLY => false
		);
		
		$this->setRules($defaultRules);
	}
    
    /**
     * Get the [rules] column value.
     *
     * @return array
     */
    public function getRules()
    {
    	$rules = parent::getRules();
    
    	return json_decode($rules);
    }
    
    /**
     * Set the value of [rules] column.
     *
     * @param  array $v new value
     * @return PlaylistRule The current object (for fluent API support)
     */
    public function setRules($v)
    {
    	$rules = json_encode($v);
    
    	if ($rules === false) {
    		throw new PropelException("Cannot parse rules JSON");
    	}
    	parent::setRules($rules);
    
    	return $this;
    } // setRules()
}
