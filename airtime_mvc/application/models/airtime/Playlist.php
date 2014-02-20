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
class Playlist extends BasePlaylist
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

	/*
	 * returns a list of media contents.
	 */
	public function getContents(PropelPDO $con = null) {

	    Logging::enablePropelLogging();

		$q = MediaContentQuery::create();
		$m = $q->getModelName();

		//use a window function to calculate offsets for the playlist.
		return $q
		    ->withColumn("SUM({$m}.Cliplength)  OVER(ORDER BY {$m}.Position) - 
		    	SUM({$m}.TrackOffset) OVER(ORDER BY {$m}.Position)", "offset")
		    ->filterByPlaylist($this)
		    ->joinWith('MediaItem', Criteria::LEFT_JOIN)
		    ->joinWith("MediaItem.AudioFile", Criteria::LEFT_JOIN)
    		->joinWith("MediaItem.Webstream", Criteria::LEFT_JOIN)
		    ->find($con);

		Logging::disablePropelLogging();
	}

	/**
	 * Computes the value of the aggregate column length *
	 * @param PropelPDO $con A connection object
	 *
	 * @return mixed The scalar result from the aggregate query
	 */
	public function computeLength(PropelPDO $con)
	{
		//have to subtract the track offsets (crossfade times)
		$stmt = $con->prepare('SELECT SUM(cliplength) - SUM(trackoffset) 
				FROM "media_content" WHERE media_content.playlist_id = :p1');
		$stmt->bindValue(':p1', $this->getId());
		$stmt->execute();

		return $stmt->fetchColumn();
	}

	/**
	 * Updates the aggregate column length *
	 * @param PropelPDO $con A connection object
	 */
	public function updateLength(PropelPDO $con)
	{
		$length = $this->computeLength($con);

		//update both tables (inheritance) for this playlist
		$stmt = $con->prepare('UPDATE media_playlist SET length = :p1 WHERE media_playlist.id = :p2');
		$stmt->bindValue(':p1', $length);
		$stmt->bindValue(':p2', $this->getId());
		$stmt->execute();

		$stmt = $con->prepare('UPDATE media_item SET length = :p1 WHERE media_item.id = :p2');
		$stmt->bindValue(':p1', $length);
		$stmt->bindValue(':p2', $this->getId());
		$stmt->execute();

		//need to make the object aware of the change.
		//for last modified
		if ($this->length != $length) {
			$this->modifiedColumns[] = PlaylistPeer::LENGTH;
		}
		$this->length = $length;
	}

	public function getLength()
	{
		if (is_null($this->length)) {
			$this->length = "00:00:00";
		}

		return $this->length;
	}

	//* if this returns false when creating a new object it seems ONLY a media item row is created
	//and nothing in the playlist table. seems like a bug...
	public function preSave(PropelPDO $con = null)
	{
		//run through positions to close gaps if any.

		$this->updateLength($con);

		return true;
	}

	public function postSave(PropelPDO $con = null)
    {
    	//$this->updateLength($con);
    }

    public function getScheduledContent() {

    	$contents = $this->getMediaContents();
    	$items = array();

    	foreach ($contents as $content) {
    		$data = array();
    		$data["id"] = $content->getMediaId();
    		$data["cliplength"] = $content->getCliplength();
    		$data["cuein"] = $content->getCuein();
    		$data["cueout"] = $content->getCueout();
    		$data["fadein"] = $content->getFadein();
    		$data["fadeout"] = $content->getFadeout();

    		$items[] = $data;
    	}

    	return $items;
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
