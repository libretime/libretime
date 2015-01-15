<?php


/**
 * Base class that represents a query for the 'cc_timestamp' table.
 *
 * 
 *
 * @method     CcTimestampQuery orderByDbId($order = Criteria::ASC) Order by the id column
 * @method     CcTimestampQuery orderByDbTimestamp($order = Criteria::ASC) Order by the timestamp column
 *
 * @method     CcTimestampQuery groupByDbId() Group by the id column
 * @method     CcTimestampQuery groupByDbTimestamp() Group by the timestamp column
 *
 * @method     CcTimestampQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     CcTimestampQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     CcTimestampQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     CcTimestampQuery leftJoinCcListenerCount($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcListenerCount relation
 * @method     CcTimestampQuery rightJoinCcListenerCount($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcListenerCount relation
 * @method     CcTimestampQuery innerJoinCcListenerCount($relationAlias = '') Adds a INNER JOIN clause to the query using the CcListenerCount relation
 *
 * @method     CcTimestamp findOne(PropelPDO $con = null) Return the first CcTimestamp matching the query
 * @method     CcTimestamp findOneOrCreate(PropelPDO $con = null) Return the first CcTimestamp matching the query, or a new CcTimestamp object populated from the query conditions when no match is found
 *
 * @method     CcTimestamp findOneByDbId(int $id) Return the first CcTimestamp filtered by the id column
 * @method     CcTimestamp findOneByDbTimestamp(string $timestamp) Return the first CcTimestamp filtered by the timestamp column
 *
 * @method     array findByDbId(int $id) Return CcTimestamp objects filtered by the id column
 * @method     array findByDbTimestamp(string $timestamp) Return CcTimestamp objects filtered by the timestamp column
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcTimestampQuery extends ModelCriteria
{

	/**
	 * Initializes internal state of BaseCcTimestampQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'airtime', $modelName = 'CcTimestamp', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new CcTimestampQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    CcTimestampQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof CcTimestampQuery) {
			return $criteria;
		}
		$query = new CcTimestampQuery();
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
	 * @return    CcTimestamp|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = CcTimestampPeer::getInstanceFromPool((string) $key))) && $this->getFormatter()->isObjectFormatter()) {
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
	 * @return    CcTimestampQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(CcTimestampPeer::ID, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    CcTimestampQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(CcTimestampPeer::ID, $keys, Criteria::IN);
	}

	/**
	 * Filter the query on the id column
	 * 
	 * @param     int|array $dbId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcTimestampQuery The current query, for fluid interface
	 */
	public function filterByDbId($dbId = null, $comparison = null)
	{
		if (is_array($dbId) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(CcTimestampPeer::ID, $dbId, $comparison);
	}

	/**
	 * Filter the query on the timestamp column
	 * 
	 * @param     string|array $dbTimestamp The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcTimestampQuery The current query, for fluid interface
	 */
	public function filterByDbTimestamp($dbTimestamp = null, $comparison = null)
	{
		if (is_array($dbTimestamp)) {
			$useMinMax = false;
			if (isset($dbTimestamp['min'])) {
				$this->addUsingAlias(CcTimestampPeer::TIMESTAMP, $dbTimestamp['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbTimestamp['max'])) {
				$this->addUsingAlias(CcTimestampPeer::TIMESTAMP, $dbTimestamp['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcTimestampPeer::TIMESTAMP, $dbTimestamp, $comparison);
	}

	/**
	 * Filter the query by a related CcListenerCount object
	 *
	 * @param     CcListenerCount $ccListenerCount  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcTimestampQuery The current query, for fluid interface
	 */
	public function filterByCcListenerCount($ccListenerCount, $comparison = null)
	{
		return $this
			->addUsingAlias(CcTimestampPeer::ID, $ccListenerCount->getDbTimestampId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcListenerCount relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcTimestampQuery The current query, for fluid interface
	 */
	public function joinCcListenerCount($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('CcListenerCount');
		
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
			$this->addJoinObject($join, 'CcListenerCount');
		}
		
		return $this;
	}

	/**
	 * Use the CcListenerCount relation CcListenerCount object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcListenerCountQuery A secondary query class using the current class as primary query
	 */
	public function useCcListenerCountQuery($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinCcListenerCount($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CcListenerCount', 'CcListenerCountQuery');
	}

	/**
	 * Exclude object from result
	 *
	 * @param     CcTimestamp $ccTimestamp Object to remove from the list of results
	 *
	 * @return    CcTimestampQuery The current query, for fluid interface
	 */
	public function prune($ccTimestamp = null)
	{
		if ($ccTimestamp) {
			$this->addUsingAlias(CcTimestampPeer::ID, $ccTimestamp->getDbId(), Criteria::NOT_EQUAL);
	  }
	  
		return $this;
	}

} // BaseCcTimestampQuery
