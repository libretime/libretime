<?php


/**
 * Base class that represents a query for the 'cc_playout_history_metadata' table.
 *
 * 
 *
 * @method     CcPlayoutHistoryMetaDataQuery orderByDbId($order = Criteria::ASC) Order by the id column
 * @method     CcPlayoutHistoryMetaDataQuery orderByDbHistoryId($order = Criteria::ASC) Order by the history_id column
 * @method     CcPlayoutHistoryMetaDataQuery orderByDbKey($order = Criteria::ASC) Order by the key column
 * @method     CcPlayoutHistoryMetaDataQuery orderByDbValue($order = Criteria::ASC) Order by the value column
 *
 * @method     CcPlayoutHistoryMetaDataQuery groupByDbId() Group by the id column
 * @method     CcPlayoutHistoryMetaDataQuery groupByDbHistoryId() Group by the history_id column
 * @method     CcPlayoutHistoryMetaDataQuery groupByDbKey() Group by the key column
 * @method     CcPlayoutHistoryMetaDataQuery groupByDbValue() Group by the value column
 *
 * @method     CcPlayoutHistoryMetaDataQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     CcPlayoutHistoryMetaDataQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     CcPlayoutHistoryMetaDataQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     CcPlayoutHistoryMetaDataQuery leftJoinCcPlayoutHistory($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcPlayoutHistory relation
 * @method     CcPlayoutHistoryMetaDataQuery rightJoinCcPlayoutHistory($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcPlayoutHistory relation
 * @method     CcPlayoutHistoryMetaDataQuery innerJoinCcPlayoutHistory($relationAlias = '') Adds a INNER JOIN clause to the query using the CcPlayoutHistory relation
 *
 * @method     CcPlayoutHistoryMetaData findOne(PropelPDO $con = null) Return the first CcPlayoutHistoryMetaData matching the query
 * @method     CcPlayoutHistoryMetaData findOneOrCreate(PropelPDO $con = null) Return the first CcPlayoutHistoryMetaData matching the query, or a new CcPlayoutHistoryMetaData object populated from the query conditions when no match is found
 *
 * @method     CcPlayoutHistoryMetaData findOneByDbId(int $id) Return the first CcPlayoutHistoryMetaData filtered by the id column
 * @method     CcPlayoutHistoryMetaData findOneByDbHistoryId(int $history_id) Return the first CcPlayoutHistoryMetaData filtered by the history_id column
 * @method     CcPlayoutHistoryMetaData findOneByDbKey(string $key) Return the first CcPlayoutHistoryMetaData filtered by the key column
 * @method     CcPlayoutHistoryMetaData findOneByDbValue(string $value) Return the first CcPlayoutHistoryMetaData filtered by the value column
 *
 * @method     array findByDbId(int $id) Return CcPlayoutHistoryMetaData objects filtered by the id column
 * @method     array findByDbHistoryId(int $history_id) Return CcPlayoutHistoryMetaData objects filtered by the history_id column
 * @method     array findByDbKey(string $key) Return CcPlayoutHistoryMetaData objects filtered by the key column
 * @method     array findByDbValue(string $value) Return CcPlayoutHistoryMetaData objects filtered by the value column
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcPlayoutHistoryMetaDataQuery extends ModelCriteria
{

	/**
	 * Initializes internal state of BaseCcPlayoutHistoryMetaDataQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'airtime', $modelName = 'CcPlayoutHistoryMetaData', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new CcPlayoutHistoryMetaDataQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    CcPlayoutHistoryMetaDataQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof CcPlayoutHistoryMetaDataQuery) {
			return $criteria;
		}
		$query = new CcPlayoutHistoryMetaDataQuery();
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
	 * @return    CcPlayoutHistoryMetaData|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = CcPlayoutHistoryMetaDataPeer::getInstanceFromPool((string) $key))) && $this->getFormatter()->isObjectFormatter()) {
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
	 * @return    CcPlayoutHistoryMetaDataQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(CcPlayoutHistoryMetaDataPeer::ID, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    CcPlayoutHistoryMetaDataQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(CcPlayoutHistoryMetaDataPeer::ID, $keys, Criteria::IN);
	}

	/**
	 * Filter the query on the id column
	 * 
	 * @param     int|array $dbId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcPlayoutHistoryMetaDataQuery The current query, for fluid interface
	 */
	public function filterByDbId($dbId = null, $comparison = null)
	{
		if (is_array($dbId) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(CcPlayoutHistoryMetaDataPeer::ID, $dbId, $comparison);
	}

	/**
	 * Filter the query on the history_id column
	 * 
	 * @param     int|array $dbHistoryId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcPlayoutHistoryMetaDataQuery The current query, for fluid interface
	 */
	public function filterByDbHistoryId($dbHistoryId = null, $comparison = null)
	{
		if (is_array($dbHistoryId)) {
			$useMinMax = false;
			if (isset($dbHistoryId['min'])) {
				$this->addUsingAlias(CcPlayoutHistoryMetaDataPeer::HISTORY_ID, $dbHistoryId['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbHistoryId['max'])) {
				$this->addUsingAlias(CcPlayoutHistoryMetaDataPeer::HISTORY_ID, $dbHistoryId['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcPlayoutHistoryMetaDataPeer::HISTORY_ID, $dbHistoryId, $comparison);
	}

	/**
	 * Filter the query on the key column
	 * 
	 * @param     string $dbKey The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcPlayoutHistoryMetaDataQuery The current query, for fluid interface
	 */
	public function filterByDbKey($dbKey = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbKey)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbKey)) {
				$dbKey = str_replace('*', '%', $dbKey);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcPlayoutHistoryMetaDataPeer::KEY, $dbKey, $comparison);
	}

	/**
	 * Filter the query on the value column
	 * 
	 * @param     string $dbValue The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcPlayoutHistoryMetaDataQuery The current query, for fluid interface
	 */
	public function filterByDbValue($dbValue = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbValue)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbValue)) {
				$dbValue = str_replace('*', '%', $dbValue);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcPlayoutHistoryMetaDataPeer::VALUE, $dbValue, $comparison);
	}

	/**
	 * Filter the query by a related CcPlayoutHistory object
	 *
	 * @param     CcPlayoutHistory $ccPlayoutHistory  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcPlayoutHistoryMetaDataQuery The current query, for fluid interface
	 */
	public function filterByCcPlayoutHistory($ccPlayoutHistory, $comparison = null)
	{
		return $this
			->addUsingAlias(CcPlayoutHistoryMetaDataPeer::HISTORY_ID, $ccPlayoutHistory->getDbId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcPlayoutHistory relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcPlayoutHistoryMetaDataQuery The current query, for fluid interface
	 */
	public function joinCcPlayoutHistory($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('CcPlayoutHistory');
		
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
			$this->addJoinObject($join, 'CcPlayoutHistory');
		}
		
		return $this;
	}

	/**
	 * Use the CcPlayoutHistory relation CcPlayoutHistory object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcPlayoutHistoryQuery A secondary query class using the current class as primary query
	 */
	public function useCcPlayoutHistoryQuery($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinCcPlayoutHistory($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CcPlayoutHistory', 'CcPlayoutHistoryQuery');
	}

	/**
	 * Exclude object from result
	 *
	 * @param     CcPlayoutHistoryMetaData $ccPlayoutHistoryMetaData Object to remove from the list of results
	 *
	 * @return    CcPlayoutHistoryMetaDataQuery The current query, for fluid interface
	 */
	public function prune($ccPlayoutHistoryMetaData = null)
	{
		if ($ccPlayoutHistoryMetaData) {
			$this->addUsingAlias(CcPlayoutHistoryMetaDataPeer::ID, $ccPlayoutHistoryMetaData->getDbId(), Criteria::NOT_EQUAL);
	  }
	  
		return $this;
	}

} // BaseCcPlayoutHistoryMetaDataQuery
