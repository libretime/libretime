<?php


/**
 * Base class that represents a query for the 'cc_listener_count' table.
 *
 * 
 *
 * @method     CcListenerCountQuery orderByDbId($order = Criteria::ASC) Order by the id column
 * @method     CcListenerCountQuery orderByDbTimestampId($order = Criteria::ASC) Order by the timestamp_id column
 * @method     CcListenerCountQuery orderByDbMountNameId($order = Criteria::ASC) Order by the mount_name_id column
 * @method     CcListenerCountQuery orderByDbListenerCount($order = Criteria::ASC) Order by the listener_count column
 *
 * @method     CcListenerCountQuery groupByDbId() Group by the id column
 * @method     CcListenerCountQuery groupByDbTimestampId() Group by the timestamp_id column
 * @method     CcListenerCountQuery groupByDbMountNameId() Group by the mount_name_id column
 * @method     CcListenerCountQuery groupByDbListenerCount() Group by the listener_count column
 *
 * @method     CcListenerCountQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     CcListenerCountQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     CcListenerCountQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     CcListenerCountQuery leftJoinCcTimestamp($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcTimestamp relation
 * @method     CcListenerCountQuery rightJoinCcTimestamp($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcTimestamp relation
 * @method     CcListenerCountQuery innerJoinCcTimestamp($relationAlias = '') Adds a INNER JOIN clause to the query using the CcTimestamp relation
 *
 * @method     CcListenerCountQuery leftJoinCcMountName($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcMountName relation
 * @method     CcListenerCountQuery rightJoinCcMountName($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcMountName relation
 * @method     CcListenerCountQuery innerJoinCcMountName($relationAlias = '') Adds a INNER JOIN clause to the query using the CcMountName relation
 *
 * @method     CcListenerCount findOne(PropelPDO $con = null) Return the first CcListenerCount matching the query
 * @method     CcListenerCount findOneOrCreate(PropelPDO $con = null) Return the first CcListenerCount matching the query, or a new CcListenerCount object populated from the query conditions when no match is found
 *
 * @method     CcListenerCount findOneByDbId(int $id) Return the first CcListenerCount filtered by the id column
 * @method     CcListenerCount findOneByDbTimestampId(int $timestamp_id) Return the first CcListenerCount filtered by the timestamp_id column
 * @method     CcListenerCount findOneByDbMountNameId(int $mount_name_id) Return the first CcListenerCount filtered by the mount_name_id column
 * @method     CcListenerCount findOneByDbListenerCount(int $listener_count) Return the first CcListenerCount filtered by the listener_count column
 *
 * @method     array findByDbId(int $id) Return CcListenerCount objects filtered by the id column
 * @method     array findByDbTimestampId(int $timestamp_id) Return CcListenerCount objects filtered by the timestamp_id column
 * @method     array findByDbMountNameId(int $mount_name_id) Return CcListenerCount objects filtered by the mount_name_id column
 * @method     array findByDbListenerCount(int $listener_count) Return CcListenerCount objects filtered by the listener_count column
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcListenerCountQuery extends ModelCriteria
{

	/**
	 * Initializes internal state of BaseCcListenerCountQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'airtime', $modelName = 'CcListenerCount', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new CcListenerCountQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    CcListenerCountQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof CcListenerCountQuery) {
			return $criteria;
		}
		$query = new CcListenerCountQuery();
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
	 * @return    CcListenerCount|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = CcListenerCountPeer::getInstanceFromPool((string) $key))) && $this->getFormatter()->isObjectFormatter()) {
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
	 * @return    CcListenerCountQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(CcListenerCountPeer::ID, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    CcListenerCountQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(CcListenerCountPeer::ID, $keys, Criteria::IN);
	}

	/**
	 * Filter the query on the id column
	 * 
	 * @param     int|array $dbId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcListenerCountQuery The current query, for fluid interface
	 */
	public function filterByDbId($dbId = null, $comparison = null)
	{
		if (is_array($dbId) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(CcListenerCountPeer::ID, $dbId, $comparison);
	}

	/**
	 * Filter the query on the timestamp_id column
	 * 
	 * @param     int|array $dbTimestampId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcListenerCountQuery The current query, for fluid interface
	 */
	public function filterByDbTimestampId($dbTimestampId = null, $comparison = null)
	{
		if (is_array($dbTimestampId)) {
			$useMinMax = false;
			if (isset($dbTimestampId['min'])) {
				$this->addUsingAlias(CcListenerCountPeer::TIMESTAMP_ID, $dbTimestampId['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbTimestampId['max'])) {
				$this->addUsingAlias(CcListenerCountPeer::TIMESTAMP_ID, $dbTimestampId['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcListenerCountPeer::TIMESTAMP_ID, $dbTimestampId, $comparison);
	}

	/**
	 * Filter the query on the mount_name_id column
	 * 
	 * @param     int|array $dbMountNameId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcListenerCountQuery The current query, for fluid interface
	 */
	public function filterByDbMountNameId($dbMountNameId = null, $comparison = null)
	{
		if (is_array($dbMountNameId)) {
			$useMinMax = false;
			if (isset($dbMountNameId['min'])) {
				$this->addUsingAlias(CcListenerCountPeer::MOUNT_NAME_ID, $dbMountNameId['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbMountNameId['max'])) {
				$this->addUsingAlias(CcListenerCountPeer::MOUNT_NAME_ID, $dbMountNameId['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcListenerCountPeer::MOUNT_NAME_ID, $dbMountNameId, $comparison);
	}

	/**
	 * Filter the query on the listener_count column
	 * 
	 * @param     int|array $dbListenerCount The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcListenerCountQuery The current query, for fluid interface
	 */
	public function filterByDbListenerCount($dbListenerCount = null, $comparison = null)
	{
		if (is_array($dbListenerCount)) {
			$useMinMax = false;
			if (isset($dbListenerCount['min'])) {
				$this->addUsingAlias(CcListenerCountPeer::LISTENER_COUNT, $dbListenerCount['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbListenerCount['max'])) {
				$this->addUsingAlias(CcListenerCountPeer::LISTENER_COUNT, $dbListenerCount['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcListenerCountPeer::LISTENER_COUNT, $dbListenerCount, $comparison);
	}

	/**
	 * Filter the query by a related CcTimestamp object
	 *
	 * @param     CcTimestamp $ccTimestamp  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcListenerCountQuery The current query, for fluid interface
	 */
	public function filterByCcTimestamp($ccTimestamp, $comparison = null)
	{
		return $this
			->addUsingAlias(CcListenerCountPeer::TIMESTAMP_ID, $ccTimestamp->getDbId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcTimestamp relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcListenerCountQuery The current query, for fluid interface
	 */
	public function joinCcTimestamp($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('CcTimestamp');
		
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
			$this->addJoinObject($join, 'CcTimestamp');
		}
		
		return $this;
	}

	/**
	 * Use the CcTimestamp relation CcTimestamp object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcTimestampQuery A secondary query class using the current class as primary query
	 */
	public function useCcTimestampQuery($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinCcTimestamp($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CcTimestamp', 'CcTimestampQuery');
	}

	/**
	 * Filter the query by a related CcMountName object
	 *
	 * @param     CcMountName $ccMountName  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcListenerCountQuery The current query, for fluid interface
	 */
	public function filterByCcMountName($ccMountName, $comparison = null)
	{
		return $this
			->addUsingAlias(CcListenerCountPeer::MOUNT_NAME_ID, $ccMountName->getDbId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcMountName relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcListenerCountQuery The current query, for fluid interface
	 */
	public function joinCcMountName($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('CcMountName');
		
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
			$this->addJoinObject($join, 'CcMountName');
		}
		
		return $this;
	}

	/**
	 * Use the CcMountName relation CcMountName object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcMountNameQuery A secondary query class using the current class as primary query
	 */
	public function useCcMountNameQuery($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinCcMountName($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CcMountName', 'CcMountNameQuery');
	}

	/**
	 * Exclude object from result
	 *
	 * @param     CcListenerCount $ccListenerCount Object to remove from the list of results
	 *
	 * @return    CcListenerCountQuery The current query, for fluid interface
	 */
	public function prune($ccListenerCount = null)
	{
		if ($ccListenerCount) {
			$this->addUsingAlias(CcListenerCountPeer::ID, $ccListenerCount->getDbId(), Criteria::NOT_EQUAL);
	  }
	  
		return $this;
	}

} // BaseCcListenerCountQuery
