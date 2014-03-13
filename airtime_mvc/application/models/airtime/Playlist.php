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
	const RULE_REPEAT_TRACKS = "repeat-tracks";
	const RULE_USERS_TRACKS_ONLY = "my-tracks";
	const RULE_CRITERIA = "criteria";
	const RULE_ORDER = "order";
	const RULE_ORDER_COLUMN = "column";
	const RULE_ORDER_DIRECTION = "direction";
	
	protected function getCriteriaRules($query) {
		
		//$pattern is like "%VALUE%", or just "VALUE" if % is not needed.
		function createRule(&$query, $comparison, $pattern = "VALUE") {
			return function($col, $value1, $value2 = null) use (&$query, $comparison, $pattern) {
				
				$m = $query->getModelName();
				
				$name = mt_rand(10000, 99999);
				$cond = "{$m}.{$col} {$comparison} ?";
				$param = str_replace("VALUE", $value1, $pattern);
				$query->condition($name, $cond, $param);
					
				return $name;
			};
		}
		
		$range = function ($col, $value1, $value2) use (&$query) {
			
			$name1 = mt_rand(10000, 99999);
			$name2 = mt_rand(10000, 99999);
			$name3 = mt_rand(10000, 99999);
			
			$m = $query->getModelName();
			
			$comparison1 = Criteria::GREATER_EQUAL;
			$comparison2 = Criteria::LESS_EQUAL;
			
			$cond = "{$m}.{$col} {$comparison1} ?";
			$query->condition($name1, $cond, $value1);
			
			$cond = "{$m}.{$col} {$comparison2} ?";
			$query->condition($name2, $cond, $value2);
			
			$query->combine(array($name1, $name2), 'and', $name3);
				
			return $name3;
		};
		
		$contains = createRule($query, Criteria::ILIKE, "%VALUE%");
		$doesntContain = createRule($query, Criteria::NOT_ILIKE, "%VALUE%");
		$is = createRule($query, Criteria::EQUAL);
		$isNot = createRule($query, Criteria::NOT_EQUAL);
		$startsWith = createRule($query, Criteria::ILIKE, "VALUE%");
		$endsWith = createRule($query, Criteria::ILIKE, "%VALUE");
		$isGreaterThan = createRule($query, Criteria::GREATER_THAN);
		$isLessThan = createRule($query, Criteria::LESS_THAN);
		$isGreaterThanEqualTo = createRule($query, Criteria::GREATER_EQUAL);
		$isLessThanEqualTo = createRule($query, Criteria::LESS_EQUAL);
		
		return array(
			null,
			$contains,
			$doesntContain,
			$is,
			$isNot,
			$startsWith,
			$endsWith,
			$isGreaterThan,
			$isLessThan,
			$isGreaterThanEqualTo,
			$isLessThanEqualTo,
			$range
		);
	}
	
	public function applyDefaultValues() {
		parent::applyDefaultValues();

		$this->name = _('Untitled Playlist');
		$this->modifiedColumns[] = PlaylistPeer::NAME;
		
		$defaultRules = array(
			self::RULE_REPEAT_TRACKS => true,
			self::RULE_USERS_TRACKS_ONLY => false,
			"order" => array(
				"column" => "",
				"direction" => "acs"
			),
			"limit" => array(
				"value" => "",
				"unit" => "hours"
			)
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
    	$v[self::RULE_REPEAT_TRACKS] = ($v[self::RULE_REPEAT_TRACKS] === "true") ? true : false;
    	$v[self::RULE_USERS_TRACKS_ONLY] = ($v[self::RULE_USERS_TRACKS_ONLY] === "true") ? true : false;

    	$rules = json_encode($v);
    
    	if ($rules === false) {
    		throw new PropelException("Cannot parse rules JSON");
    	}
    	parent::setRules($rules);
    
    	return $this;
    } // setRules()
    
    
    public function savePlaylistContent($content, $replace=false)
    {
    	$con = Propel::getConnection(PlaylistPeer::DATABASE_NAME);
		$con->beginTransaction();
		
		try {
			
			$m = array();
			$currentContent = $this->getMediaContents(null, $con);
			
			if ($replace) {
				$currentContent->delete($con);
				$position = 0;
			}
			else {
				$position = count($currentContent);
			}
			
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
