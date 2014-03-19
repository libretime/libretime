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
	
	public function isStatic() {
		return $this->getClassKey() === intval(PlaylistPeer::CLASSKEY_0);
	}
	
	protected function getCriteriaRules(&$query) {
		
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
    
    //takes seconds, returns in interval format.
    private function formatLimitValue($secs) {
    	 
    	$hour = floor($secs / 3600);
    	 
    	$leftover = $secs - $hour*3600;
    	$min = floor($leftover / 60);
    	 
    	$leftover = $leftover - $min*60;
    	$sec = floor($leftover);
    	 
    	$duration = "{$hour}:{$min}:{$sec}";
    	 
    	return $duration;
    }
    
    public function generateContent(PropelPDO $con) {
    
    	try {
    		
    		$ruleSet = $this->getRules();
    		$criteria = isset($ruleSet["criteria"]) ? $ruleSet["criteria"] : array();
    		Logging::info($criteria);
    
    		$query = AudioFileQuery::create();
    
    		//filter to this user's tracks only.
    		if ($ruleSet[self::RULE_USERS_TRACKS_ONLY]) {
    			$userService = new \Application_Service_UserService();
    			$user = $userService->getCurrentUser();
    			$query->filterByCcSubjs($user);
    		}
    
    		$m = $query->getModelName();
    		$query->withColumn("({$m}.Cueout - {$m}.Cuein)", "cuelength");
    
    		$criteriaRules = self::getCriteriaRules($query);
    
    		$conditionAnd = array();
    		$conNum = 0;
    		foreach ($criteria as $andBlock) {
    			$conditionOr = array();
    			 
    			foreach ($andBlock as $orBlock) {
    				$rule = $criteriaRules[$orBlock["modifier"]];
    
    				$column = $orBlock["criteria"];
    				$input1 = $orBlock["input1"];
    				$input2 = isset($orBlock["input2"]) ? $orBlock["input2"] : null;
    
    				$condition = $rule($column, $input1, $input2);
    
    				$conditionOr[] = $condition;
    			}
    			 
    			$query->combine($conditionOr, 'or', $conNum);
    			$conditionAnd[] = $conNum;
    			$conNum++;
    		}
    
    		$query->where($conditionAnd, 'and');
    
    		//order by a chosen column or by random.
    		$order = $ruleSet["order"];
    		if ($order["column"] != "") {
    			$query->orderBy($order["column"], $order["direction"]);
    		}
    		else {
    			$query->addAscendingOrderByColumn('random()');
    		}
    
    		//filter length using the subquery to take advantage of window columns
    		//added for length selection purposes.
    		//using window function on subquery so that the ORDER BY statement can be run
    		//(need this incase it's order by random())
    		$windowedQuery = AudioFileQuery::create()
    		->addSelectQuery($query, "mainfilter")
    		->withColumn("SUM(mainfilter.cuelength) OVER (rows between unbounded preceding and current row)", "agglength");
    
    		//lag it to pick the item that just goes over the limit to not have empty space.
    		$laggedQuery = AudioFileQuery::create()
    		->addSelectQuery($windowedQuery, "windowedfilter")
    		->withColumn("windowedfilter.agglength", "subtotal")
    		->withColumn("LAG(windowedfilter.agglength) OVER (rows between unbounded preceding and current row)", "laglength");
    
    		//limit the query based on length or #items
    		$limitedQuery = AudioFileQuery::create()
    		->addSelectQuery($laggedQuery, "laggedfilter")
    		->withColumn("laggedfilter.subtotal", "subtotal");
    
    		$limitValue = $ruleSet["limit"]["value"];
    		$limitUnit = $ruleSet["limit"]["unit"];
    
    		if ($limitUnit === "items") {
    			$limitedQuery->limit($limitValue);
    		}
    		else {
    			$duration = "1:00:00";
    				
    			switch($limitUnit) {
    
    				case "minutes":
    					if (isset($limitValue)) {
    
    						$secs = $limitValue * 60;
    						$duration = $this->formatLimitValue($secs);
    					}
    					break;
    				case "hours":
    					if (isset($limitValue)) {
    
    						$secs = $limitValue * 3600;
    						$duration = $this->formatLimitValue($secs);
    					}
    					break;
    			}
    
    			$limitedQuery
    			->where("laggedfilter.laglength < '$duration'")
    			->_or()
    			//the IS NULL item is the first in the list.
    			->where("laggedfilter.laglength IS NULL");
    		}
    
    		$files = $limitedQuery->find();
    
    		$ids = array();
    		foreach ($files as $file) {
    			$ids[] = $file->getId();
    		}
    
    		//check if we've hit our time with the selected files, or if we must repeat tracks.
    		if ($ruleSet[self::RULE_REPEAT_TRACKS]) {
    			 
    			switch($limitUnit) {
    					
    				case "items":
    					while ($limitValue > count($ids)) {
    						$index = mt_rand(0, count($files)-1);
    						$file = $files[$index];
    						$ids[] = $file->getId();
    					}
    						
    					break;
    				case "minutes":
    				case "hours":
    					$playlistLength = $files[count($files)-1]->getSubtotal();
    					$playlistLengthSecs = \Application_Common_DateHelper::playlistTimeToSeconds($playlistLength);
    					$durationSecs = \Application_Common_DateHelper::playlistTimeToSeconds($duration);
    						
    					while ($durationSecs > $playlistLengthSecs) {
    						$index = mt_rand(0, count($files)-1);
    						$file = $files[$index];
    						$ids[] = $file->getId();
    
    						$filelength = $file->getCueLength();
    						$filelengthSecs = \Application_Common_DateHelper::playlistTimeToSeconds($filelength);
    						$playlistLengthSecs += $filelengthSecs;
    					}
    					break;
    			}
    		}
    
    		return $ids;
    
    		$con->commit();
    	}
    	catch (Exception $e) {
    		$con->rollBack();
    		Logging::error($e->getFile().$e->getLine());
    		Logging::error($e->getMessage());
    		throw $e;
    	}
    }
}
