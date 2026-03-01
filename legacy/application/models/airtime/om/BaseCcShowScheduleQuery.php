<?php


/**
 * Base class that represents a query for the 'cc_show_schedule' table.
 *
 *
 *
 * @method     CcShowScheduleQuery orderByDbId($order = Criteria::ASC) Order by the id column
 * @method     CcShowScheduleQuery orderByDbInstanceId($order = Criteria::ASC) Order by the instance_id column
 * @method     CcShowScheduleQuery orderByDbPosition($order = Criteria::ASC) Order by the position column
 * @method     CcShowScheduleQuery orderByDbGroupId($order = Criteria::ASC) Order by the group_id column
 *
 * @method     CcShowScheduleQuery groupByDbId() Group by the id column
 * @method     CcShowScheduleQuery groupByDbInstanceId() Group by the instance_id column
 * @method     CcShowScheduleQuery groupByDbPosition() Group by the position column
 * @method     CcShowScheduleQuery groupByDbGroupId() Group by the group_id column
 *
 * @method     CcShowScheduleQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     CcShowScheduleQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     CcShowScheduleQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     CcShowScheduleQuery leftJoinCcShowInstances($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcShowInstances relation
 * @method     CcShowScheduleQuery rightJoinCcShowInstances($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcShowInstances relation
 * @method     CcShowScheduleQuery innerJoinCcShowInstances($relationAlias = '') Adds a INNER JOIN clause to the query using the CcShowInstances relation
 *
 * @method     CcShowSchedule findOne(PropelPDO $con = null) Return the first CcShowSchedule matching the query
 * @method     CcShowSchedule findOneOrCreate(PropelPDO $con = null) Return the first CcShowSchedule matching the query, or a new CcShowSchedule object populated from the query conditions when no match is found
 *
 * @method     CcShowSchedule findOneByDbId(int $id) Return the first CcShowSchedule filtered by the id column
 * @method     CcShowSchedule findOneByDbInstanceId(int $instance_id) Return the first CcShowSchedule filtered by the instance_id column
 * @method     CcShowSchedule findOneByDbPosition(int $position) Return the first CcShowSchedule filtered by the position column
 * @method     CcShowSchedule findOneByDbGroupId(int $group_id) Return the first CcShowSchedule filtered by the group_id column
 *
 * @method     array findByDbId(int $id) Return CcShowSchedule objects filtered by the id column
 * @method     array findByDbInstanceId(int $instance_id) Return CcShowSchedule objects filtered by the instance_id column
 * @method     array findByDbPosition(int $position) Return CcShowSchedule objects filtered by the position column
 * @method     array findByDbGroupId(int $group_id) Return CcShowSchedule objects filtered by the group_id column
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcShowScheduleQuery extends ModelCriteria
{

	/**
	 * Initializes internal state of BaseCcShowScheduleQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'airtime', $modelName = 'CcShowSchedule', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new CcShowScheduleQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    CcShowScheduleQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof CcShowScheduleQuery) {
			return $criteria;
		}
		$query = new CcShowScheduleQuery();
		if (null !== $modelAlias) {
			$query->setModelAlias($modelAlias);
		}
		if ($criteria instanceof Criteria) {
			$query->mergeWith($criteria);
		}
		return $query;
	}

	/**
	 * Find object by primary key
	 * Use instance pooling to avoid a database query if the object exists
	 * <code>
	 * $obj  = $c->findPk(12, $con);
	 * </code>
	 * @param     mixed $key Primary key to use for the query
	 * @param     PropelPDO $con an optional connection object
	 *
	 * @return    CcShowSchedule|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = CcShowSchedulePeer::getInstanceFromPool((string) $key))) && $this->getFormatter()->isObjectFormatter()) {
			// the object is alredy in the instance pool
			return $obj;
		} else {
			// the object has not been requested yet, or the formatter is not an object formatter
			$criteria = $this->isKeepQuery() ? clone $this : $this;
			$stmt = $criteria
				->filterByPrimaryKey($key)
				->getSelectStatement($con);
			return $criteria->getFormatter()->init($criteria)->formatOne($stmt);
		}
	}

	/**
	 * Find objects by primary key
	 * <code>
	 * $objs = $c->findPks(array(12, 56, 832), $con);
	 * </code>
	 * @param     array $keys Primary keys to use for the query
	 * @param     PropelPDO $con an optional connection object
	 *
	 * @return    PropelObjectCollection|array|mixed the list of results, formatted by the current formatter
	 */
	public function findPks($keys, $con = null)
	{
		$criteria = $this->isKeepQuery() ? clone $this : $this;
		return $this
			->filterByPrimaryKeys($keys)
			->find($con);
	}

	/**
	 * Filter the query by primary key
	 *
	 * @param     mixed $key Primary key to use for the query
	 *
	 * @return    CcShowScheduleQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(CcShowSchedulePeer::ID, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    CcShowScheduleQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(CcShowSchedulePeer::ID, $keys, Criteria::IN);
	}

	/**
	 * Filter the query on the id column
	 *
	 * @param     int|array $dbId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcShowScheduleQuery The current query, for fluid interface
	 */
	public function filterByDbId($dbId = null, $comparison = null)
	{
		if (is_array($dbId) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(CcShowSchedulePeer::ID, $dbId, $comparison);
	}

	/**
	 * Filter the query on the instance_id column
	 *
	 * @param     int|array $dbInstanceId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcShowScheduleQuery The current query, for fluid interface
	 */
	public function filterByDbInstanceId($dbInstanceId = null, $comparison = null)
	{
		if (is_array($dbInstanceId)) {
			$useMinMax = false;
			if (isset($dbInstanceId['min'])) {
				$this->addUsingAlias(CcShowSchedulePeer::INSTANCE_ID, $dbInstanceId['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbInstanceId['max'])) {
				$this->addUsingAlias(CcShowSchedulePeer::INSTANCE_ID, $dbInstanceId['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcShowSchedulePeer::INSTANCE_ID, $dbInstanceId, $comparison);
	}

	/**
	 * Filter the query on the position column
	 *
	 * @param     int|array $dbPosition The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcShowScheduleQuery The current query, for fluid interface
	 */
	public function filterByDbPosition($dbPosition = null, $comparison = null)
	{
		if (is_array($dbPosition)) {
			$useMinMax = false;
			if (isset($dbPosition['min'])) {
				$this->addUsingAlias(CcShowSchedulePeer::POSITION, $dbPosition['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbPosition['max'])) {
				$this->addUsingAlias(CcShowSchedulePeer::POSITION, $dbPosition['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcShowSchedulePeer::POSITION, $dbPosition, $comparison);
	}

	/**
	 * Filter the query on the group_id column
	 *
	 * @param     int|array $dbGroupId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcShowScheduleQuery The current query, for fluid interface
	 */
	public function filterByDbGroupId($dbGroupId = null, $comparison = null)
	{
		if (is_array($dbGroupId)) {
			$useMinMax = false;
			if (isset($dbGroupId['min'])) {
				$this->addUsingAlias(CcShowSchedulePeer::GROUP_ID, $dbGroupId['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbGroupId['max'])) {
				$this->addUsingAlias(CcShowSchedulePeer::GROUP_ID, $dbGroupId['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcShowSchedulePeer::GROUP_ID, $dbGroupId, $comparison);
	}

	/**
	 * Filter the query by a related CcShowInstances object
	 *
	 * @param     CcShowInstances $ccShowInstances  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcShowScheduleQuery The current query, for fluid interface
	 */
	public function filterByCcShowInstances($ccShowInstances, $comparison = null)
	{
		return $this
			->addUsingAlias(CcShowSchedulePeer::INSTANCE_ID, $ccShowInstances->getDbId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcShowInstances relation
	 *
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcShowScheduleQuery The current query, for fluid interface
	 */
	public function joinCcShowInstances($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('CcShowInstances');

		// create a ModelJoin object for this join
		$join = new ModelJoin();
		$join->setJoinType($joinType);
		$join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
		if ($previousJoin = $this->getPreviousJoin()) {
			$join->setPreviousJoin($previousJoin);
		}

		// add the ModelJoin to the current object
		if($relationAlias) {
			$this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
			$this->addJoinObject($join, $relationAlias);
		} else {
			$this->addJoinObject($join, 'CcShowInstances');
		}

		return $this;
	}

	/**
	 * Use the CcShowInstances relation CcShowInstances object
	 *
	 * @see       useQuery()
	 *
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcShowInstancesQuery A secondary query class using the current class as primary query
	 */
	public function useCcShowInstancesQuery($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinCcShowInstances($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CcShowInstances', 'CcShowInstancesQuery');
	}

	/**
	 * Exclude object from result
	 *
	 * @param     CcShowSchedule $ccShowSchedule Object to remove from the list of results
	 *
	 * @return    CcShowScheduleQuery The current query, for fluid interface
	 */
	public function prune($ccShowSchedule = null)
	{
		if ($ccShowSchedule) {
			$this->addUsingAlias(CcShowSchedulePeer::ID, $ccShowSchedule->getDbId(), Criteria::NOT_EQUAL);
	  }

		return $this;
	}

} // BaseCcShowScheduleQuery
