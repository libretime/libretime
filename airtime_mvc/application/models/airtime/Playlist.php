<?php

namespace Airtime\MediaItem;

use Airtime\MediaItemQuery;

use Airtime\MediaItem\om\BasePlaylist;
use \Criteria;
use \PropelPDO;
use \Exception;
use \Logging;
use \Propel;


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
	const RULE_REPEAT_TRACKS = 0;
	const RULE_USERS_TRACKS_ONLY = 1;
	
	public function applyDefaultValues() {
		parent::applyDefaultValues();

		$this->name = _('Untitled Playlist');
		$this->modifiedColumns[] = PlaylistPeer::NAME;
		
		$defaultRules = array(
			self::RULE_REPEAT_TRACKS => true,
			self::RULE_USERS_TRACKS_ONLY => false
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
    
    	return json_decode($rules, true);
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
    
    
    public function savePlaylistContent($content)
    {
    	$con = Propel::getConnection(PlaylistPeer::DATABASE_NAME);
		$con->beginTransaction();
		
		try {
			
			$position = 0;
			$m = array();
			$this->getMediaContents(null, $con)->delete($con);
			
			foreach ($content as $item) {
				
				$mediaId = $item["id"];
				$cuein = isset($item["cuein"]) ? $item["cuein"] : null;
				$cueout = isset($item["cueout"]) ? $item["cueout"] : null;
				$fadein = isset($item["fadein"]) ? $item["fadein"] : null;
				$fadeout = isset($item["fadeout"]) ? $item["fadeout"] : null;
				
				$mediaItem = MediaItemQuery::create()->findPK($mediaId, $con);
				$mediaContent = $this->buildContentItem($mediaItem, $position, $cuein, $cueout, $fadein, $fadeout);
				$mediaContent->setPlaylist($this);
				
				$res = $mediaContent->validate();
				if ($res === true) {
					$m[] = $mediaContent;
				}
				else {
					Logging::info($res);
					throw new Exception("invalid media content");
				}
				
				$position++;
				
				//save each content item in the transaction
				//first so that Playlist preSave can calculate
				//the new playlist length properly.
				$mediaContent->save($con);
			}
			
			$this->save($con);
			
			$con->commit();
		}
		catch (Exception $e) {
			$con->rollBack();
			Logging::error($e->getMessage());
			throw $e;
		}
    }
}
