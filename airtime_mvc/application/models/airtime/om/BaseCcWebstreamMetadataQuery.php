<?php


/**
 * Base class that represents a query for the 'cc_webstream_metadata' table.
 *
 * 
 *
 * @method     CcWebstreamMetadataQuery orderByDbId($order = Criteria::ASC) Order by the id column
 * @method     CcWebstreamMetadataQuery orderByDbInstanceId($order = Criteria::ASC) Order by the instance_id column
 * @method     CcWebstreamMetadataQuery orderByDbStartTime($order = Criteria::ASC) Order by the start_time column
 * @method     CcWebstreamMetadataQuery orderByDbLiquidsoapData($order = Criteria::ASC) Order by the liquidsoap_data column
 *
 * @method     CcWebstreamMetadataQuery groupByDbId() Group by the id column
 * @method     CcWebstreamMetadataQuery groupByDbInstanceId() Group by the instance_id column
 * @method     CcWebstreamMetadataQuery groupByDbStartTime() Group by the start_time column
 * @method     CcWebstreamMetadataQuery groupByDbLiquidsoapData() Group by the liquidsoap_data column
 *
 * @method     CcWebstreamMetadataQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     CcWebstreamMetadataQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     CcWebstreamMetadataQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     CcWebstreamMetadataQuery leftJoinCcSchedule($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcSchedule relation
 * @method     CcWebstreamMetadataQuery rightJoinCcSchedule($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcSchedule relation
 * @method     CcWebstreamMetadataQuery innerJoinCcSchedule($relationAlias = '') Adds a INNER JOIN clause to the query using the CcSchedule relation
 *
 * @method     CcWebstreamMetadata findOne(PropelPDO $con = null) Return the first CcWebstreamMetadata matching the query
 * @method     CcWebstreamMetadata findOneOrCreate(PropelPDO $con = null) Return the first CcWebstreamMetadata matching the query, or a new CcWebstreamMetadata object populated from the query conditions when no match is found
 *
 * @method     CcWebstreamMetadata findOneByDbId(int $id) Return the first CcWebstreamMetadata filtered by the id column
 * @method     CcWebstreamMetadata findOneByDbInstanceId(int $instance_id) Return the first CcWebstreamMetadata filtered by the instance_id column
 * @method     CcWebstreamMetadata findOneByDbStartTime(string $start_time) Return the first CcWebstreamMetadata filtered by the start_time column
 * @method     CcWebstreamMetadata findOneByDbLiquidsoapData(string $liquidsoap_data) Return the first CcWebstreamMetadata filtered by the liquidsoap_data column
 *
 * @method     array findByDbId(int $id) Return CcWebstreamMetadata objects filtered by the id column
 * @method     array findByDbInstanceId(int $instance_id) Return CcWebstreamMetadata objects filtered by the instance_id column
 * @method     array findByDbStartTime(string $start_time) Return CcWebstreamMetadata objects filtered by the start_time column
 * @method     array findByDbLiquidsoapData(string $liquidsoap_data) Return CcWebstreamMetadata objects filtered by the liquidsoap_data column
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcWebstreamMetadataQuery extends ModelCriteria
{

	/**
	 * Initializes internal state of BaseCcWebstreamMetadataQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'airtime', $modelName = 'CcWebstreamMetadata', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new CcWebstreamMetadataQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    CcWebstreamMetadataQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof CcWebstreamMetadataQuery) {
			return $criteria;
		}
		$query = new CcWebstreamMetadataQuery();
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
	 * @return    CcWebstreamMetadata|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = CcWebstreamMetadataPeer::getInstanceFromPool((string) $key))) && $this->getFormatter()->isObjectFormatter()) {
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
	 * @return    CcWebstreamMetadataQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(CcWebstreamMetadataPeer::ID, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    CcWebstreamMetadataQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(CcWebstreamMetadataPeer::ID, $keys, Criteria::IN);
	}

	/**
	 * Filter the query on the id column
	 * 
	 * @param     int|array $dbId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcWebstreamMetadataQuery The current query, for fluid interface
	 */
	public function filterByDbId($dbId = null, $comparison = null)
	{
		if (is_array($dbId) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(CcWebstreamMetadataPeer::ID, $dbId, $comparison);
	}

	/**
	 * Filter the query on the instance_id column
	 * 
	 * @param     int|array $dbInstanceId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcWebstreamMetadataQuery The current query, for fluid interface
	 */
	public function filterByDbInstanceId($dbInstanceId = null, $comparison = null)
	{
		if (is_array($dbInstanceId)) {
			$useMinMax = false;
			if (isset($dbInstanceId['min'])) {
				$this->addUsingAlias(CcWebstreamMetadataPeer::INSTANCE_ID, $dbInstanceId['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbInstanceId['max'])) {
				$this->addUsingAlias(CcWebstreamMetadataPeer::INSTANCE_ID, $dbInstanceId['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcWebstreamMetadataPeer::INSTANCE_ID, $dbInstanceId, $comparison);
	}

	/**
	 * Filter the query on the start_time column
	 * 
	 * @param     string|array $dbStartTime The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcWebstreamMetadataQuery The current query, for fluid interface
	 */
	public function filterByDbStartTime($dbStartTime = null, $comparison = null)
	{
		if (is_array($dbStartTime)) {
			$useMinMax = false;
			if (isset($dbStartTime['min'])) {
				$this->addUsingAlias(CcWebstreamMetadataPeer::START_TIME, $dbStartTime['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbStartTime['max'])) {
				$this->addUsingAlias(CcWebstreamMetadataPeer::START_TIME, $dbStartTime['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcWebstreamMetadataPeer::START_TIME, $dbStartTime, $comparison);
	}

	/**
	 * Filter the query on the liquidsoap_data column
	 * 
	 * @param     string $dbLiquidsoapData The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcWebstreamMetadataQuery The current query, for fluid interface
	 */
	public function filterByDbLiquidsoapData($dbLiquidsoapData = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbLiquidsoapData)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbLiquidsoapData)) {
				$dbLiquidsoapData = str_replace('*', '%', $dbLiquidsoapData);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcWebstreamMetadataPeer::LIQUIDSOAP_DATA, $dbLiquidsoapData, $comparison);
	}

	/**
	 * Filter the query by a related CcSchedule object
	 *
	 * @param     CcSchedule $ccSchedule  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcWebstreamMetadataQuery The current query, for fluid interface
	 */
	public function filterByCcSchedule($ccSchedule, $comparison = null)
	{
		return $this
			->addUsingAlias(CcWebstreamMetadataPeer::INSTANCE_ID, $ccSchedule->getDbId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcSchedule relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcWebstreamMetadataQuery The current query, for fluid interface
	 */
	public function joinCcSchedule($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('CcSchedule');
		
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
			$this->addJoinObject($join, 'CcSchedule');
		}
		
		return $this;
	}

	/**
	 * Use the CcSchedule relation CcSchedule object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcScheduleQuery A secondary query class using the current class as primary query
	 */
	public function useCcScheduleQuery($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinCcSchedule($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CcSchedule', 'CcScheduleQuery');
	}

	/**
	 * Exclude object from result
	 *
	 * @param     CcWebstreamMetadata $ccWebstreamMetadata Object to remove from the list of results
	 *
	 * @return    CcWebstreamMetadataQuery The current query, for fluid interface
	 */
	public function prune($ccWebstreamMetadata = null)
	{
		if ($ccWebstreamMetadata) {
			$this->addUsingAlias(CcWebstreamMetadataPeer::ID, $ccWebstreamMetadata->getDbId(), Criteria::NOT_EQUAL);
	  }
	  
		return $this;
	}

} // BaseCcWebstreamMetadataQuery
