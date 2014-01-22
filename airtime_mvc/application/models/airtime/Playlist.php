<?php

namespace Airtime\MediaItem;

use Airtime\MediaItem\om\BasePlaylist;
use \Criteria;
use \PropelPDO;
use \Exception;


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
	}
	
	/*
	 * returns a list of media contents.
	 */
	public function getContents($criteria = NULL, PropelPDO $con = NULL) {
		
		if (is_null($criteria)) {
			$criteria = new Criteria();
			$criteria->addAscendingOrderByColumn(MediaContentPeer::POSITION);
		}
		
		return parent::getMediaContentsJoinMediaItem($criteria, $con);
	}
	
	/**
	 * Computes the value of the aggregate column length *
	 * @param PropelPDO $con A connection object
	 *
	 * @return mixed The scalar result from the aggregate query
	 */
	public function computeLength(PropelPDO $con)
	{
		$stmt = $con->prepare('SELECT SUM(cliplength) FROM "media_content" WHERE media_content.playlist_id = :p1');
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
		$this->length = $length;
	}
	
	public function getLength()
	{
		if (is_null($this->length)) {
			$this->length = "00:00:00";
		}
		
		return $this->length;
	}
	
	public function postSave(PropelPDO $con = null)
    {
    	$this->updateLength($con);
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
}
