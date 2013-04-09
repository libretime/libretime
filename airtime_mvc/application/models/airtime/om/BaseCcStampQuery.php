<?php


/**
 * Base class that represents a query for the 'cc_stamp' table.
 *
 * 
 *
 * @method     CcStampQuery orderByDbId($order = Criteria::ASC) Order by the id column
 * @method     CcStampQuery orderByDbShowId($order = Criteria::ASC) Order by the show_id column
 * @method     CcStampQuery orderByDbInstanceId($order = Criteria::ASC) Order by the instance_id column
 * @method     CcStampQuery orderByDbLinked($order = Criteria::ASC) Order by the linked column
 *
 * @method     CcStampQuery groupByDbId() Group by the id column
 * @method     CcStampQuery groupByDbShowId() Group by the show_id column
 * @method     CcStampQuery groupByDbInstanceId() Group by the instance_id column
 * @method     CcStampQuery groupByDbLinked() Group by the linked column
 *
 * @method     CcStampQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     CcStampQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     CcStampQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     CcStampQuery leftJoinCcShow($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcShow relation
 * @method     CcStampQuery rightJoinCcShow($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcShow relation
 * @method     CcStampQuery innerJoinCcShow($relationAlias = '') Adds a INNER JOIN clause to the query using the CcShow relation
 *
 * @method     CcStampQuery leftJoinCcShowInstances($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcShowInstances relation
 * @method     CcStampQuery rightJoinCcShowInstances($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcShowInstances relation
 * @method     CcStampQuery innerJoinCcShowInstances($relationAlias = '') Adds a INNER JOIN clause to the query using the CcShowInstances relation
 *
 * @method     CcStampQuery leftJoinCcStampContents($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcStampContents relation
 * @method     CcStampQuery rightJoinCcStampContents($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcStampContents relation
 * @method     CcStampQuery innerJoinCcStampContents($relationAlias = '') Adds a INNER JOIN clause to the query using the CcStampContents relation
 *
 * @method     CcStamp findOne(PropelPDO $con = null) Return the first CcStamp matching the query
 * @method     CcStamp findOneOrCreate(PropelPDO $con = null) Return the first CcStamp matching the query, or a new CcStamp object populated from the query conditions when no match is found
 *
 * @method     CcStamp findOneByDbId(int $id) Return the first CcStamp filtered by the id column
 * @method     CcStamp findOneByDbShowId(int $show_id) Return the first CcStamp filtered by the show_id column
 * @method     CcStamp findOneByDbInstanceId(int $instance_id) Return the first CcStamp filtered by the instance_id column
 * @method     CcStamp findOneByDbLinked(boolean $linked) Return the first CcStamp filtered by the linked column
 *
 * @method     array findByDbId(int $id) Return CcStamp objects filtered by the id column
 * @method     array findByDbShowId(int $show_id) Return CcStamp objects filtered by the show_id column
 * @method     array findByDbInstanceId(int $instance_id) Return CcStamp objects filtered by the instance_id column
 * @method     array findByDbLinked(boolean $linked) Return CcStamp objects filtered by the linked column
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcStampQuery extends ModelCriteria
{

	/**
	 * Initializes internal state of BaseCcStampQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'airtime', $modelName = 'CcStamp', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new CcStampQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    CcStampQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof CcStampQuery) {
			return $criteria;
		}
		$query = new CcStampQuery();
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
	 * @return    CcStamp|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = CcStampPeer::getInstanceFromPool((string) $key))) && $this->getFormatter()->isObjectFormatter()) {
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
	 * @return    CcStampQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(CcStampPeer::ID, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    CcStampQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(CcStampPeer::ID, $keys, Criteria::IN);
	}

	/**
	 * Filter the query on the id column
	 * 
	 * @param     int|array $dbId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcStampQuery The current query, for fluid interface
	 */
	public function filterByDbId($dbId = null, $comparison = null)
	{
		if (is_array($dbId) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(CcStampPeer::ID, $dbId, $comparison);
	}

	/**
	 * Filter the query on the show_id column
	 * 
	 * @param     int|array $dbShowId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcStampQuery The current query, for fluid interface
	 */
	public function filterByDbShowId($dbShowId = null, $comparison = null)
	{
		if (is_array($dbShowId)) {
			$useMinMax = false;
			if (isset($dbShowId['min'])) {
				$this->addUsingAlias(CcStampPeer::SHOW_ID, $dbShowId['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbShowId['max'])) {
				$this->addUsingAlias(CcStampPeer::SHOW_ID, $dbShowId['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcStampPeer::SHOW_ID, $dbShowId, $comparison);
	}

	/**
	 * Filter the query on the instance_id column
	 * 
	 * @param     int|array $dbInstanceId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcStampQuery The current query, for fluid interface
	 */
	public function filterByDbInstanceId($dbInstanceId = null, $comparison = null)
	{
		if (is_array($dbInstanceId)) {
			$useMinMax = false;
			if (isset($dbInstanceId['min'])) {
				$this->addUsingAlias(CcStampPeer::INSTANCE_ID, $dbInstanceId['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbInstanceId['max'])) {
				$this->addUsingAlias(CcStampPeer::INSTANCE_ID, $dbInstanceId['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcStampPeer::INSTANCE_ID, $dbInstanceId, $comparison);
	}

	/**
	 * Filter the query on the linked column
	 * 
	 * @param     boolean|string $dbLinked The value to use as filter.
	 *            Accepts strings ('false', 'off', '-', 'no', 'n', and '0' are false, the rest is true)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcStampQuery The current query, for fluid interface
	 */
	public function filterByDbLinked($dbLinked = null, $comparison = null)
	{
		if (is_string($dbLinked)) {
			$linked = in_array(strtolower($dbLinked), array('false', 'off', '-', 'no', 'n', '0')) ? false : true;
		}
		return $this->addUsingAlias(CcStampPeer::LINKED, $dbLinked, $comparison);
	}

	/**
	 * Filter the query by a related CcShow object
	 *
	 * @param     CcShow $ccShow  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcStampQuery The current query, for fluid interface
	 */
	public function filterByCcShow($ccShow, $comparison = null)
	{
		return $this
			->addUsingAlias(CcStampPeer::SHOW_ID, $ccShow->getDbId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcShow relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcStampQuery The current query, for fluid interface
	 */
	public function joinCcShow($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('CcShow');
		
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
			$this->addJoinObject($join, 'CcShow');
		}
		
		return $this;
	}

	/**
	 * Use the CcShow relation CcShow object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcShowQuery A secondary query class using the current class as primary query
	 */
	public function useCcShowQuery($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinCcShow($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CcShow', 'CcShowQuery');
	}

	/**
	 * Filter the query by a related CcShowInstances object
	 *
	 * @param     CcShowInstances $ccShowInstances  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcStampQuery The current query, for fluid interface
	 */
	public function filterByCcShowInstances($ccShowInstances, $comparison = null)
	{
		return $this
			->addUsingAlias(CcStampPeer::INSTANCE_ID, $ccShowInstances->getDbId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcShowInstances relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcStampQuery The current query, for fluid interface
	 */
	public function joinCcShowInstances($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
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
	public function useCcShowInstancesQuery($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinCcShowInstances($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CcShowInstances', 'CcShowInstancesQuery');
	}

	/**
	 * Filter the query by a related CcStampContents object
	 *
	 * @param     CcStampContents $ccStampContents  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcStampQuery The current query, for fluid interface
	 */
	public function filterByCcStampContents($ccStampContents, $comparison = null)
	{
		return $this
			->addUsingAlias(CcStampPeer::ID, $ccStampContents->getDbStampId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcStampContents relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcStampQuery The current query, for fluid interface
	 */
	public function joinCcStampContents($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('CcStampContents');
		
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
			$this->addJoinObject($join, 'CcStampContents');
		}
		
		return $this;
	}

	/**
	 * Use the CcStampContents relation CcStampContents object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcStampContentsQuery A secondary query class using the current class as primary query
	 */
	public function useCcStampContentsQuery($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinCcStampContents($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CcStampContents', 'CcStampContentsQuery');
	}

	/**
	 * Exclude object from result
	 *
	 * @param     CcStamp $ccStamp Object to remove from the list of results
	 *
	 * @return    CcStampQuery The current query, for fluid interface
	 */
	public function prune($ccStamp = null)
	{
		if ($ccStamp) {
			$this->addUsingAlias(CcStampPeer::ID, $ccStamp->getDbId(), Criteria::NOT_EQUAL);
	  }
	  
		return $this;
	}

} // BaseCcStampQuery
