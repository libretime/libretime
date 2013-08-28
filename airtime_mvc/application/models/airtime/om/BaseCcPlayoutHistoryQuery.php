<?php


/**
 * Base class that represents a query for the 'cc_playout_history' table.
 *
 * 
 *
 * @method     CcPlayoutHistoryQuery orderByDbId($order = Criteria::ASC) Order by the id column
 * @method     CcPlayoutHistoryQuery orderByDbFileId($order = Criteria::ASC) Order by the file_id column
 * @method     CcPlayoutHistoryQuery orderByDbStarts($order = Criteria::ASC) Order by the starts column
 * @method     CcPlayoutHistoryQuery orderByDbEnds($order = Criteria::ASC) Order by the ends column
 * @method     CcPlayoutHistoryQuery orderByDbInstanceId($order = Criteria::ASC) Order by the instance_id column
 *
 * @method     CcPlayoutHistoryQuery groupByDbId() Group by the id column
 * @method     CcPlayoutHistoryQuery groupByDbFileId() Group by the file_id column
 * @method     CcPlayoutHistoryQuery groupByDbStarts() Group by the starts column
 * @method     CcPlayoutHistoryQuery groupByDbEnds() Group by the ends column
 * @method     CcPlayoutHistoryQuery groupByDbInstanceId() Group by the instance_id column
 *
 * @method     CcPlayoutHistoryQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     CcPlayoutHistoryQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     CcPlayoutHistoryQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     CcPlayoutHistoryQuery leftJoinCcFiles($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcFiles relation
 * @method     CcPlayoutHistoryQuery rightJoinCcFiles($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcFiles relation
 * @method     CcPlayoutHistoryQuery innerJoinCcFiles($relationAlias = '') Adds a INNER JOIN clause to the query using the CcFiles relation
 *
 * @method     CcPlayoutHistoryQuery leftJoinCcShowInstances($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcShowInstances relation
 * @method     CcPlayoutHistoryQuery rightJoinCcShowInstances($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcShowInstances relation
 * @method     CcPlayoutHistoryQuery innerJoinCcShowInstances($relationAlias = '') Adds a INNER JOIN clause to the query using the CcShowInstances relation
 *
 * @method     CcPlayoutHistoryQuery leftJoinCcPlayoutHistoryMetaData($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcPlayoutHistoryMetaData relation
 * @method     CcPlayoutHistoryQuery rightJoinCcPlayoutHistoryMetaData($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcPlayoutHistoryMetaData relation
 * @method     CcPlayoutHistoryQuery innerJoinCcPlayoutHistoryMetaData($relationAlias = '') Adds a INNER JOIN clause to the query using the CcPlayoutHistoryMetaData relation
 *
 * @method     CcPlayoutHistory findOne(PropelPDO $con = null) Return the first CcPlayoutHistory matching the query
 * @method     CcPlayoutHistory findOneOrCreate(PropelPDO $con = null) Return the first CcPlayoutHistory matching the query, or a new CcPlayoutHistory object populated from the query conditions when no match is found
 *
 * @method     CcPlayoutHistory findOneByDbId(int $id) Return the first CcPlayoutHistory filtered by the id column
 * @method     CcPlayoutHistory findOneByDbFileId(int $file_id) Return the first CcPlayoutHistory filtered by the file_id column
 * @method     CcPlayoutHistory findOneByDbStarts(string $starts) Return the first CcPlayoutHistory filtered by the starts column
 * @method     CcPlayoutHistory findOneByDbEnds(string $ends) Return the first CcPlayoutHistory filtered by the ends column
 * @method     CcPlayoutHistory findOneByDbInstanceId(int $instance_id) Return the first CcPlayoutHistory filtered by the instance_id column
 *
 * @method     array findByDbId(int $id) Return CcPlayoutHistory objects filtered by the id column
 * @method     array findByDbFileId(int $file_id) Return CcPlayoutHistory objects filtered by the file_id column
 * @method     array findByDbStarts(string $starts) Return CcPlayoutHistory objects filtered by the starts column
 * @method     array findByDbEnds(string $ends) Return CcPlayoutHistory objects filtered by the ends column
 * @method     array findByDbInstanceId(int $instance_id) Return CcPlayoutHistory objects filtered by the instance_id column
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcPlayoutHistoryQuery extends ModelCriteria
{

	/**
	 * Initializes internal state of BaseCcPlayoutHistoryQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'airtime', $modelName = 'CcPlayoutHistory', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new CcPlayoutHistoryQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    CcPlayoutHistoryQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof CcPlayoutHistoryQuery) {
			return $criteria;
		}
		$query = new CcPlayoutHistoryQuery();
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
	 * @return    CcPlayoutHistory|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = CcPlayoutHistoryPeer::getInstanceFromPool((string) $key))) && $this->getFormatter()->isObjectFormatter()) {
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
	 * @return    CcPlayoutHistoryQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(CcPlayoutHistoryPeer::ID, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    CcPlayoutHistoryQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(CcPlayoutHistoryPeer::ID, $keys, Criteria::IN);
	}

	/**
	 * Filter the query on the id column
	 * 
	 * @param     int|array $dbId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcPlayoutHistoryQuery The current query, for fluid interface
	 */
	public function filterByDbId($dbId = null, $comparison = null)
	{
		if (is_array($dbId) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(CcPlayoutHistoryPeer::ID, $dbId, $comparison);
	}

	/**
	 * Filter the query on the file_id column
	 * 
	 * @param     int|array $dbFileId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcPlayoutHistoryQuery The current query, for fluid interface
	 */
	public function filterByDbFileId($dbFileId = null, $comparison = null)
	{
		if (is_array($dbFileId)) {
			$useMinMax = false;
			if (isset($dbFileId['min'])) {
				$this->addUsingAlias(CcPlayoutHistoryPeer::FILE_ID, $dbFileId['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbFileId['max'])) {
				$this->addUsingAlias(CcPlayoutHistoryPeer::FILE_ID, $dbFileId['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcPlayoutHistoryPeer::FILE_ID, $dbFileId, $comparison);
	}

	/**
	 * Filter the query on the starts column
	 * 
	 * @param     string|array $dbStarts The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcPlayoutHistoryQuery The current query, for fluid interface
	 */
	public function filterByDbStarts($dbStarts = null, $comparison = null)
	{
		if (is_array($dbStarts)) {
			$useMinMax = false;
			if (isset($dbStarts['min'])) {
				$this->addUsingAlias(CcPlayoutHistoryPeer::STARTS, $dbStarts['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbStarts['max'])) {
				$this->addUsingAlias(CcPlayoutHistoryPeer::STARTS, $dbStarts['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcPlayoutHistoryPeer::STARTS, $dbStarts, $comparison);
	}

	/**
	 * Filter the query on the ends column
	 * 
	 * @param     string|array $dbEnds The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcPlayoutHistoryQuery The current query, for fluid interface
	 */
	public function filterByDbEnds($dbEnds = null, $comparison = null)
	{
		if (is_array($dbEnds)) {
			$useMinMax = false;
			if (isset($dbEnds['min'])) {
				$this->addUsingAlias(CcPlayoutHistoryPeer::ENDS, $dbEnds['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbEnds['max'])) {
				$this->addUsingAlias(CcPlayoutHistoryPeer::ENDS, $dbEnds['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcPlayoutHistoryPeer::ENDS, $dbEnds, $comparison);
	}

	/**
	 * Filter the query on the instance_id column
	 * 
	 * @param     int|array $dbInstanceId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcPlayoutHistoryQuery The current query, for fluid interface
	 */
	public function filterByDbInstanceId($dbInstanceId = null, $comparison = null)
	{
		if (is_array($dbInstanceId)) {
			$useMinMax = false;
			if (isset($dbInstanceId['min'])) {
				$this->addUsingAlias(CcPlayoutHistoryPeer::INSTANCE_ID, $dbInstanceId['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbInstanceId['max'])) {
				$this->addUsingAlias(CcPlayoutHistoryPeer::INSTANCE_ID, $dbInstanceId['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcPlayoutHistoryPeer::INSTANCE_ID, $dbInstanceId, $comparison);
	}

	/**
	 * Filter the query by a related CcFiles object
	 *
	 * @param     CcFiles $ccFiles  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcPlayoutHistoryQuery The current query, for fluid interface
	 */
	public function filterByCcFiles($ccFiles, $comparison = null)
	{
		return $this
			->addUsingAlias(CcPlayoutHistoryPeer::FILE_ID, $ccFiles->getDbId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcFiles relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcPlayoutHistoryQuery The current query, for fluid interface
	 */
	public function joinCcFiles($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('CcFiles');
		
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
			$this->addJoinObject($join, 'CcFiles');
		}
		
		return $this;
	}

	/**
	 * Use the CcFiles relation CcFiles object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcFilesQuery A secondary query class using the current class as primary query
	 */
	public function useCcFilesQuery($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinCcFiles($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CcFiles', 'CcFilesQuery');
	}

	/**
	 * Filter the query by a related CcShowInstances object
	 *
	 * @param     CcShowInstances $ccShowInstances  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcPlayoutHistoryQuery The current query, for fluid interface
	 */
	public function filterByCcShowInstances($ccShowInstances, $comparison = null)
	{
		return $this
			->addUsingAlias(CcPlayoutHistoryPeer::INSTANCE_ID, $ccShowInstances->getDbId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcShowInstances relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcPlayoutHistoryQuery The current query, for fluid interface
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
	 * Filter the query by a related CcPlayoutHistoryMetaData object
	 *
	 * @param     CcPlayoutHistoryMetaData $ccPlayoutHistoryMetaData  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcPlayoutHistoryQuery The current query, for fluid interface
	 */
	public function filterByCcPlayoutHistoryMetaData($ccPlayoutHistoryMetaData, $comparison = null)
	{
		return $this
			->addUsingAlias(CcPlayoutHistoryPeer::ID, $ccPlayoutHistoryMetaData->getDbHistoryId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcPlayoutHistoryMetaData relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcPlayoutHistoryQuery The current query, for fluid interface
	 */
	public function joinCcPlayoutHistoryMetaData($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('CcPlayoutHistoryMetaData');
		
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
			$this->addJoinObject($join, 'CcPlayoutHistoryMetaData');
		}
		
		return $this;
	}

	/**
	 * Use the CcPlayoutHistoryMetaData relation CcPlayoutHistoryMetaData object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcPlayoutHistoryMetaDataQuery A secondary query class using the current class as primary query
	 */
	public function useCcPlayoutHistoryMetaDataQuery($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinCcPlayoutHistoryMetaData($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CcPlayoutHistoryMetaData', 'CcPlayoutHistoryMetaDataQuery');
	}

	/**
	 * Exclude object from result
	 *
	 * @param     CcPlayoutHistory $ccPlayoutHistory Object to remove from the list of results
	 *
	 * @return    CcPlayoutHistoryQuery The current query, for fluid interface
	 */
	public function prune($ccPlayoutHistory = null)
	{
		if ($ccPlayoutHistory) {
			$this->addUsingAlias(CcPlayoutHistoryPeer::ID, $ccPlayoutHistory->getDbId(), Criteria::NOT_EQUAL);
	  }
	  
		return $this;
	}

} // BaseCcPlayoutHistoryQuery
