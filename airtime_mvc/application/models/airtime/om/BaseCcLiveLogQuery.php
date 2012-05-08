<?php


/**
 * Base class that represents a query for the 'cc_live_log' table.
 *
 * 
 *
 * @method     CcLiveLogQuery orderByDbId($order = Criteria::ASC) Order by the id column
 * @method     CcLiveLogQuery orderByDbState($order = Criteria::ASC) Order by the state column
 * @method     CcLiveLogQuery orderByDbStartTime($order = Criteria::ASC) Order by the start_time column
 * @method     CcLiveLogQuery orderByDbEndTime($order = Criteria::ASC) Order by the end_time column
 *
 * @method     CcLiveLogQuery groupByDbId() Group by the id column
 * @method     CcLiveLogQuery groupByDbState() Group by the state column
 * @method     CcLiveLogQuery groupByDbStartTime() Group by the start_time column
 * @method     CcLiveLogQuery groupByDbEndTime() Group by the end_time column
 *
 * @method     CcLiveLogQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     CcLiveLogQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     CcLiveLogQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     CcLiveLog findOne(PropelPDO $con = null) Return the first CcLiveLog matching the query
 * @method     CcLiveLog findOneOrCreate(PropelPDO $con = null) Return the first CcLiveLog matching the query, or a new CcLiveLog object populated from the query conditions when no match is found
 *
 * @method     CcLiveLog findOneByDbId(int $id) Return the first CcLiveLog filtered by the id column
 * @method     CcLiveLog findOneByDbState(string $state) Return the first CcLiveLog filtered by the state column
 * @method     CcLiveLog findOneByDbStartTime(string $start_time) Return the first CcLiveLog filtered by the start_time column
 * @method     CcLiveLog findOneByDbEndTime(string $end_time) Return the first CcLiveLog filtered by the end_time column
 *
 * @method     array findByDbId(int $id) Return CcLiveLog objects filtered by the id column
 * @method     array findByDbState(string $state) Return CcLiveLog objects filtered by the state column
 * @method     array findByDbStartTime(string $start_time) Return CcLiveLog objects filtered by the start_time column
 * @method     array findByDbEndTime(string $end_time) Return CcLiveLog objects filtered by the end_time column
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcLiveLogQuery extends ModelCriteria
{

	/**
	 * Initializes internal state of BaseCcLiveLogQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'airtime', $modelName = 'CcLiveLog', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new CcLiveLogQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    CcLiveLogQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof CcLiveLogQuery) {
			return $criteria;
		}
		$query = new CcLiveLogQuery();
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
	 * @return    CcLiveLog|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = CcLiveLogPeer::getInstanceFromPool((string) $key))) && $this->getFormatter()->isObjectFormatter()) {
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
	 * @return    CcLiveLogQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(CcLiveLogPeer::ID, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    CcLiveLogQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(CcLiveLogPeer::ID, $keys, Criteria::IN);
	}

	/**
	 * Filter the query on the id column
	 * 
	 * @param     int|array $dbId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcLiveLogQuery The current query, for fluid interface
	 */
	public function filterByDbId($dbId = null, $comparison = null)
	{
		if (is_array($dbId) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(CcLiveLogPeer::ID, $dbId, $comparison);
	}

	/**
	 * Filter the query on the state column
	 * 
	 * @param     string $dbState The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcLiveLogQuery The current query, for fluid interface
	 */
	public function filterByDbState($dbState = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbState)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbState)) {
				$dbState = str_replace('*', '%', $dbState);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcLiveLogPeer::STATE, $dbState, $comparison);
	}

	/**
	 * Filter the query on the start_time column
	 * 
	 * @param     string|array $dbStartTime The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcLiveLogQuery The current query, for fluid interface
	 */
	public function filterByDbStartTime($dbStartTime = null, $comparison = null)
	{
		if (is_array($dbStartTime)) {
			$useMinMax = false;
			if (isset($dbStartTime['min'])) {
				$this->addUsingAlias(CcLiveLogPeer::START_TIME, $dbStartTime['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbStartTime['max'])) {
				$this->addUsingAlias(CcLiveLogPeer::START_TIME, $dbStartTime['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcLiveLogPeer::START_TIME, $dbStartTime, $comparison);
	}

	/**
	 * Filter the query on the end_time column
	 * 
	 * @param     string|array $dbEndTime The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcLiveLogQuery The current query, for fluid interface
	 */
	public function filterByDbEndTime($dbEndTime = null, $comparison = null)
	{
		if (is_array($dbEndTime)) {
			$useMinMax = false;
			if (isset($dbEndTime['min'])) {
				$this->addUsingAlias(CcLiveLogPeer::END_TIME, $dbEndTime['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbEndTime['max'])) {
				$this->addUsingAlias(CcLiveLogPeer::END_TIME, $dbEndTime['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcLiveLogPeer::END_TIME, $dbEndTime, $comparison);
	}

	/**
	 * Exclude object from result
	 *
	 * @param     CcLiveLog $ccLiveLog Object to remove from the list of results
	 *
	 * @return    CcLiveLogQuery The current query, for fluid interface
	 */
	public function prune($ccLiveLog = null)
	{
		if ($ccLiveLog) {
			$this->addUsingAlias(CcLiveLogPeer::ID, $ccLiveLog->getDbId(), Criteria::NOT_EQUAL);
	  }
	  
		return $this;
	}

} // BaseCcLiveLogQuery
