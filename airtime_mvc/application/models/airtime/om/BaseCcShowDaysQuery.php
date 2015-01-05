<?php


/**
 * Base class that represents a query for the 'cc_show_days' table.
 *
 * 
 *
 * @method     CcShowDaysQuery orderByDbId($order = Criteria::ASC) Order by the id column
 * @method     CcShowDaysQuery orderByDbFirstShow($order = Criteria::ASC) Order by the first_show column
 * @method     CcShowDaysQuery orderByDbLastShow($order = Criteria::ASC) Order by the last_show column
 * @method     CcShowDaysQuery orderByDbStartTime($order = Criteria::ASC) Order by the start_time column
 * @method     CcShowDaysQuery orderByDbTimezone($order = Criteria::ASC) Order by the timezone column
 * @method     CcShowDaysQuery orderByDbDuration($order = Criteria::ASC) Order by the duration column
 * @method     CcShowDaysQuery orderByDbDay($order = Criteria::ASC) Order by the day column
 * @method     CcShowDaysQuery orderByDbRepeatType($order = Criteria::ASC) Order by the repeat_type column
 * @method     CcShowDaysQuery orderByDbNextPopDate($order = Criteria::ASC) Order by the next_pop_date column
 * @method     CcShowDaysQuery orderByDbShowId($order = Criteria::ASC) Order by the show_id column
 * @method     CcShowDaysQuery orderByDbRecord($order = Criteria::ASC) Order by the record column
 *
 * @method     CcShowDaysQuery groupByDbId() Group by the id column
 * @method     CcShowDaysQuery groupByDbFirstShow() Group by the first_show column
 * @method     CcShowDaysQuery groupByDbLastShow() Group by the last_show column
 * @method     CcShowDaysQuery groupByDbStartTime() Group by the start_time column
 * @method     CcShowDaysQuery groupByDbTimezone() Group by the timezone column
 * @method     CcShowDaysQuery groupByDbDuration() Group by the duration column
 * @method     CcShowDaysQuery groupByDbDay() Group by the day column
 * @method     CcShowDaysQuery groupByDbRepeatType() Group by the repeat_type column
 * @method     CcShowDaysQuery groupByDbNextPopDate() Group by the next_pop_date column
 * @method     CcShowDaysQuery groupByDbShowId() Group by the show_id column
 * @method     CcShowDaysQuery groupByDbRecord() Group by the record column
 *
 * @method     CcShowDaysQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     CcShowDaysQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     CcShowDaysQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     CcShowDaysQuery leftJoinCcShow($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcShow relation
 * @method     CcShowDaysQuery rightJoinCcShow($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcShow relation
 * @method     CcShowDaysQuery innerJoinCcShow($relationAlias = '') Adds a INNER JOIN clause to the query using the CcShow relation
 *
 * @method     CcShowDays findOne(PropelPDO $con = null) Return the first CcShowDays matching the query
 * @method     CcShowDays findOneOrCreate(PropelPDO $con = null) Return the first CcShowDays matching the query, or a new CcShowDays object populated from the query conditions when no match is found
 *
 * @method     CcShowDays findOneByDbId(int $id) Return the first CcShowDays filtered by the id column
 * @method     CcShowDays findOneByDbFirstShow(string $first_show) Return the first CcShowDays filtered by the first_show column
 * @method     CcShowDays findOneByDbLastShow(string $last_show) Return the first CcShowDays filtered by the last_show column
 * @method     CcShowDays findOneByDbStartTime(string $start_time) Return the first CcShowDays filtered by the start_time column
 * @method     CcShowDays findOneByDbTimezone(string $timezone) Return the first CcShowDays filtered by the timezone column
 * @method     CcShowDays findOneByDbDuration(string $duration) Return the first CcShowDays filtered by the duration column
 * @method     CcShowDays findOneByDbDay(int $day) Return the first CcShowDays filtered by the day column
 * @method     CcShowDays findOneByDbRepeatType(int $repeat_type) Return the first CcShowDays filtered by the repeat_type column
 * @method     CcShowDays findOneByDbNextPopDate(string $next_pop_date) Return the first CcShowDays filtered by the next_pop_date column
 * @method     CcShowDays findOneByDbShowId(int $show_id) Return the first CcShowDays filtered by the show_id column
 * @method     CcShowDays findOneByDbRecord(int $record) Return the first CcShowDays filtered by the record column
 *
 * @method     array findByDbId(int $id) Return CcShowDays objects filtered by the id column
 * @method     array findByDbFirstShow(string $first_show) Return CcShowDays objects filtered by the first_show column
 * @method     array findByDbLastShow(string $last_show) Return CcShowDays objects filtered by the last_show column
 * @method     array findByDbStartTime(string $start_time) Return CcShowDays objects filtered by the start_time column
 * @method     array findByDbTimezone(string $timezone) Return CcShowDays objects filtered by the timezone column
 * @method     array findByDbDuration(string $duration) Return CcShowDays objects filtered by the duration column
 * @method     array findByDbDay(int $day) Return CcShowDays objects filtered by the day column
 * @method     array findByDbRepeatType(int $repeat_type) Return CcShowDays objects filtered by the repeat_type column
 * @method     array findByDbNextPopDate(string $next_pop_date) Return CcShowDays objects filtered by the next_pop_date column
 * @method     array findByDbShowId(int $show_id) Return CcShowDays objects filtered by the show_id column
 * @method     array findByDbRecord(int $record) Return CcShowDays objects filtered by the record column
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcShowDaysQuery extends ModelCriteria
{

	/**
	 * Initializes internal state of BaseCcShowDaysQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'airtime', $modelName = 'CcShowDays', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new CcShowDaysQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    CcShowDaysQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof CcShowDaysQuery) {
			return $criteria;
		}
		$query = new CcShowDaysQuery();
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
	 * @return    CcShowDays|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = CcShowDaysPeer::getInstanceFromPool((string) $key))) && $this->getFormatter()->isObjectFormatter()) {
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
	 * @return    CcShowDaysQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(CcShowDaysPeer::ID, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    CcShowDaysQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(CcShowDaysPeer::ID, $keys, Criteria::IN);
	}

	/**
	 * Filter the query on the id column
	 * 
	 * @param     int|array $dbId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcShowDaysQuery The current query, for fluid interface
	 */
	public function filterByDbId($dbId = null, $comparison = null)
	{
		if (is_array($dbId) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(CcShowDaysPeer::ID, $dbId, $comparison);
	}

	/**
	 * Filter the query on the first_show column
	 * 
	 * @param     string|array $dbFirstShow The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcShowDaysQuery The current query, for fluid interface
	 */
	public function filterByDbFirstShow($dbFirstShow = null, $comparison = null)
	{
		if (is_array($dbFirstShow)) {
			$useMinMax = false;
			if (isset($dbFirstShow['min'])) {
				$this->addUsingAlias(CcShowDaysPeer::FIRST_SHOW, $dbFirstShow['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbFirstShow['max'])) {
				$this->addUsingAlias(CcShowDaysPeer::FIRST_SHOW, $dbFirstShow['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcShowDaysPeer::FIRST_SHOW, $dbFirstShow, $comparison);
	}

	/**
	 * Filter the query on the last_show column
	 * 
	 * @param     string|array $dbLastShow The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcShowDaysQuery The current query, for fluid interface
	 */
	public function filterByDbLastShow($dbLastShow = null, $comparison = null)
	{
		if (is_array($dbLastShow)) {
			$useMinMax = false;
			if (isset($dbLastShow['min'])) {
				$this->addUsingAlias(CcShowDaysPeer::LAST_SHOW, $dbLastShow['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbLastShow['max'])) {
				$this->addUsingAlias(CcShowDaysPeer::LAST_SHOW, $dbLastShow['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcShowDaysPeer::LAST_SHOW, $dbLastShow, $comparison);
	}

	/**
	 * Filter the query on the start_time column
	 * 
	 * @param     string|array $dbStartTime The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcShowDaysQuery The current query, for fluid interface
	 */
	public function filterByDbStartTime($dbStartTime = null, $comparison = null)
	{
		if (is_array($dbStartTime)) {
			$useMinMax = false;
			if (isset($dbStartTime['min'])) {
				$this->addUsingAlias(CcShowDaysPeer::START_TIME, $dbStartTime['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbStartTime['max'])) {
				$this->addUsingAlias(CcShowDaysPeer::START_TIME, $dbStartTime['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcShowDaysPeer::START_TIME, $dbStartTime, $comparison);
	}

	/**
	 * Filter the query on the timezone column
	 * 
	 * @param     string $dbTimezone The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcShowDaysQuery The current query, for fluid interface
	 */
	public function filterByDbTimezone($dbTimezone = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbTimezone)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbTimezone)) {
				$dbTimezone = str_replace('*', '%', $dbTimezone);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcShowDaysPeer::TIMEZONE, $dbTimezone, $comparison);
	}

	/**
	 * Filter the query on the duration column
	 * 
	 * @param     string $dbDuration The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcShowDaysQuery The current query, for fluid interface
	 */
	public function filterByDbDuration($dbDuration = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbDuration)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbDuration)) {
				$dbDuration = str_replace('*', '%', $dbDuration);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcShowDaysPeer::DURATION, $dbDuration, $comparison);
	}

	/**
	 * Filter the query on the day column
	 * 
	 * @param     int|array $dbDay The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcShowDaysQuery The current query, for fluid interface
	 */
	public function filterByDbDay($dbDay = null, $comparison = null)
	{
		if (is_array($dbDay)) {
			$useMinMax = false;
			if (isset($dbDay['min'])) {
				$this->addUsingAlias(CcShowDaysPeer::DAY, $dbDay['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbDay['max'])) {
				$this->addUsingAlias(CcShowDaysPeer::DAY, $dbDay['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcShowDaysPeer::DAY, $dbDay, $comparison);
	}

	/**
	 * Filter the query on the repeat_type column
	 * 
	 * @param     int|array $dbRepeatType The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcShowDaysQuery The current query, for fluid interface
	 */
	public function filterByDbRepeatType($dbRepeatType = null, $comparison = null)
	{
		if (is_array($dbRepeatType)) {
			$useMinMax = false;
			if (isset($dbRepeatType['min'])) {
				$this->addUsingAlias(CcShowDaysPeer::REPEAT_TYPE, $dbRepeatType['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbRepeatType['max'])) {
				$this->addUsingAlias(CcShowDaysPeer::REPEAT_TYPE, $dbRepeatType['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcShowDaysPeer::REPEAT_TYPE, $dbRepeatType, $comparison);
	}

	/**
	 * Filter the query on the next_pop_date column
	 * 
	 * @param     string|array $dbNextPopDate The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcShowDaysQuery The current query, for fluid interface
	 */
	public function filterByDbNextPopDate($dbNextPopDate = null, $comparison = null)
	{
		if (is_array($dbNextPopDate)) {
			$useMinMax = false;
			if (isset($dbNextPopDate['min'])) {
				$this->addUsingAlias(CcShowDaysPeer::NEXT_POP_DATE, $dbNextPopDate['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbNextPopDate['max'])) {
				$this->addUsingAlias(CcShowDaysPeer::NEXT_POP_DATE, $dbNextPopDate['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcShowDaysPeer::NEXT_POP_DATE, $dbNextPopDate, $comparison);
	}

	/**
	 * Filter the query on the show_id column
	 * 
	 * @param     int|array $dbShowId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcShowDaysQuery The current query, for fluid interface
	 */
	public function filterByDbShowId($dbShowId = null, $comparison = null)
	{
		if (is_array($dbShowId)) {
			$useMinMax = false;
			if (isset($dbShowId['min'])) {
				$this->addUsingAlias(CcShowDaysPeer::SHOW_ID, $dbShowId['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbShowId['max'])) {
				$this->addUsingAlias(CcShowDaysPeer::SHOW_ID, $dbShowId['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcShowDaysPeer::SHOW_ID, $dbShowId, $comparison);
	}

	/**
	 * Filter the query on the record column
	 * 
	 * @param     int|array $dbRecord The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcShowDaysQuery The current query, for fluid interface
	 */
	public function filterByDbRecord($dbRecord = null, $comparison = null)
	{
		if (is_array($dbRecord)) {
			$useMinMax = false;
			if (isset($dbRecord['min'])) {
				$this->addUsingAlias(CcShowDaysPeer::RECORD, $dbRecord['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbRecord['max'])) {
				$this->addUsingAlias(CcShowDaysPeer::RECORD, $dbRecord['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcShowDaysPeer::RECORD, $dbRecord, $comparison);
	}

	/**
	 * Filter the query by a related CcShow object
	 *
	 * @param     CcShow $ccShow  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcShowDaysQuery The current query, for fluid interface
	 */
	public function filterByCcShow($ccShow, $comparison = null)
	{
		return $this
			->addUsingAlias(CcShowDaysPeer::SHOW_ID, $ccShow->getDbId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcShow relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcShowDaysQuery The current query, for fluid interface
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
	 * Exclude object from result
	 *
	 * @param     CcShowDays $ccShowDays Object to remove from the list of results
	 *
	 * @return    CcShowDaysQuery The current query, for fluid interface
	 */
	public function prune($ccShowDays = null)
	{
		if ($ccShowDays) {
			$this->addUsingAlias(CcShowDaysPeer::ID, $ccShowDays->getDbId(), Criteria::NOT_EQUAL);
	  }
	  
		return $this;
	}

} // BaseCcShowDaysQuery
