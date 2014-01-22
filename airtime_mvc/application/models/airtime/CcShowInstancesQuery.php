<?php

namespace Airtime;

use Airtime\om\BaseCcShowInstancesQuery;


/**
 * Skeleton subclass for performing query and update operations on the 'cc_show_instances' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.airtime
 */
class CcShowInstancesQuery extends BaseCcShowInstancesQuery
{
	/*
	 * @param $start DateTime in UTC
	 * @param $end DateTime in UTC
	 * 
	 * finds show instances that are playing out at any point during $start and $end.
	 * 
	 */
	public function between($start, $end) {

		$alias = $this->getModelAliasOrName();
		$startTime = $start->format('Y-m-d H:i:s');
		$endTime = $end->format('Y-m-d H:i:s');
		
		return $this
			->condition("between1", $alias.".DbStarts >= ?", $startTime)
			->condition("between2", $alias.".DbStarts < ?", $endTime)
			->combine(array('between1', 'between2'), 'and', 'between12')
			->condition("between3", $alias.".DbEnds > ?", $startTime)
			->condition("between4", $alias.".DbEnds <= ?", $endTime)
			->combine(array('between3', 'between4'), 'and', 'between34')
			->condition("between5", $alias.".DbStarts <= ?", $startTime)
			->condition("between6", $alias.".DbEnds >= ?", $endTime)
			->combine(array('between5', 'between6'), 'and', 'between56')
			->where(array('between12', 'between34', 'between56'), 'or')
			;
	}
}
