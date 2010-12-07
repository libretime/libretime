<?php


/**
 * Base class that represents a query for the 'cc_show' table.
 *
 * 
 *
 * @method     CcShowQuery orderByDbId($order = Criteria::ASC) Order by the id column
 * @method     CcShowQuery orderByDbName($order = Criteria::ASC) Order by the name column
 * @method     CcShowQuery orderByDbFirstShow($order = Criteria::ASC) Order by the first_show column
 * @method     CcShowQuery orderByDbLastShow($order = Criteria::ASC) Order by the last_show column
 * @method     CcShowQuery orderByDbStartTime($order = Criteria::ASC) Order by the start_time column
 * @method     CcShowQuery orderByDbEndTime($order = Criteria::ASC) Order by the end_time column
 * @method     CcShowQuery orderByDbRepeats($order = Criteria::ASC) Order by the repeats column
 * @method     CcShowQuery orderByDbDay($order = Criteria::ASC) Order by the day column
 * @method     CcShowQuery orderByDbDescription($order = Criteria::ASC) Order by the description column
 * @method     CcShowQuery orderByDbShowId($order = Criteria::ASC) Order by the show_id column
 *
 * @method     CcShowQuery groupByDbId() Group by the id column
 * @method     CcShowQuery groupByDbName() Group by the name column
 * @method     CcShowQuery groupByDbFirstShow() Group by the first_show column
 * @method     CcShowQuery groupByDbLastShow() Group by the last_show column
 * @method     CcShowQuery groupByDbStartTime() Group by the start_time column
 * @method     CcShowQuery groupByDbEndTime() Group by the end_time column
 * @method     CcShowQuery groupByDbRepeats() Group by the repeats column
 * @method     CcShowQuery groupByDbDay() Group by the day column
 * @method     CcShowQuery groupByDbDescription() Group by the description column
 * @method     CcShowQuery groupByDbShowId() Group by the show_id column
 *
 * @method     CcShowQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     CcShowQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     CcShowQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     CcShow findOne(PropelPDO $con = null) Return the first CcShow matching the query
 * @method     CcShow findOneOrCreate(PropelPDO $con = null) Return the first CcShow matching the query, or a new CcShow object populated from the query conditions when no match is found
 *
 * @method     CcShow findOneByDbId(int $id) Return the first CcShow filtered by the id column
 * @method     CcShow findOneByDbName(string $name) Return the first CcShow filtered by the name column
 * @method     CcShow findOneByDbFirstShow(string $first_show) Return the first CcShow filtered by the first_show column
 * @method     CcShow findOneByDbLastShow(string $last_show) Return the first CcShow filtered by the last_show column
 * @method     CcShow findOneByDbStartTime(string $start_time) Return the first CcShow filtered by the start_time column
 * @method     CcShow findOneByDbEndTime(string $end_time) Return the first CcShow filtered by the end_time column
 * @method     CcShow findOneByDbRepeats(int $repeats) Return the first CcShow filtered by the repeats column
 * @method     CcShow findOneByDbDay(int $day) Return the first CcShow filtered by the day column
 * @method     CcShow findOneByDbDescription(string $description) Return the first CcShow filtered by the description column
 * @method     CcShow findOneByDbShowId(int $show_id) Return the first CcShow filtered by the show_id column
 *
 * @method     array findByDbId(int $id) Return CcShow objects filtered by the id column
 * @method     array findByDbName(string $name) Return CcShow objects filtered by the name column
 * @method     array findByDbFirstShow(string $first_show) Return CcShow objects filtered by the first_show column
 * @method     array findByDbLastShow(string $last_show) Return CcShow objects filtered by the last_show column
 * @method     array findByDbStartTime(string $start_time) Return CcShow objects filtered by the start_time column
 * @method     array findByDbEndTime(string $end_time) Return CcShow objects filtered by the end_time column
 * @method     array findByDbRepeats(int $repeats) Return CcShow objects filtered by the repeats column
 * @method     array findByDbDay(int $day) Return CcShow objects filtered by the day column
 * @method     array findByDbDescription(string $description) Return CcShow objects filtered by the description column
 * @method     array findByDbShowId(int $show_id) Return CcShow objects filtered by the show_id column
 *
 * @package    propel.generator.campcaster.om
 */
abstract class BaseCcShowQuery extends ModelCriteria
{

	/**
	 * Initializes internal state of BaseCcShowQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'campcaster', $modelName = 'CcShow', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new CcShowQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    CcShowQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof CcShowQuery) {
			return $criteria;
		}
		$query = new CcShowQuery();
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
	 * @return    CcShow|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = CcShowPeer::getInstanceFromPool((string) $key))) && $this->getFormatter()->isObjectFormatter()) {
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
	 * @return    CcShowQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(CcShowPeer::ID, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    CcShowQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(CcShowPeer::ID, $keys, Criteria::IN);
	}

	/**
	 * Filter the query on the id column
	 * 
	 * @param     int|array $dbId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcShowQuery The current query, for fluid interface
	 */
	public function filterByDbId($dbId = null, $comparison = null)
	{
		if (is_array($dbId) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(CcShowPeer::ID, $dbId, $comparison);
	}

	/**
	 * Filter the query on the name column
	 * 
	 * @param     string $dbName The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcShowQuery The current query, for fluid interface
	 */
	public function filterByDbName($dbName = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbName)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbName)) {
				$dbName = str_replace('*', '%', $dbName);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcShowPeer::NAME, $dbName, $comparison);
	}

	/**
	 * Filter the query on the first_show column
	 * 
	 * @param     string|array $dbFirstShow The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcShowQuery The current query, for fluid interface
	 */
	public function filterByDbFirstShow($dbFirstShow = null, $comparison = null)
	{
		if (is_array($dbFirstShow)) {
			$useMinMax = false;
			if (isset($dbFirstShow['min'])) {
				$this->addUsingAlias(CcShowPeer::FIRST_SHOW, $dbFirstShow['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbFirstShow['max'])) {
				$this->addUsingAlias(CcShowPeer::FIRST_SHOW, $dbFirstShow['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcShowPeer::FIRST_SHOW, $dbFirstShow, $comparison);
	}

	/**
	 * Filter the query on the last_show column
	 * 
	 * @param     string|array $dbLastShow The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcShowQuery The current query, for fluid interface
	 */
	public function filterByDbLastShow($dbLastShow = null, $comparison = null)
	{
		if (is_array($dbLastShow)) {
			$useMinMax = false;
			if (isset($dbLastShow['min'])) {
				$this->addUsingAlias(CcShowPeer::LAST_SHOW, $dbLastShow['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbLastShow['max'])) {
				$this->addUsingAlias(CcShowPeer::LAST_SHOW, $dbLastShow['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcShowPeer::LAST_SHOW, $dbLastShow, $comparison);
	}

	/**
	 * Filter the query on the start_time column
	 * 
	 * @param     string|array $dbStartTime The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcShowQuery The current query, for fluid interface
	 */
	public function filterByDbStartTime($dbStartTime = null, $comparison = null)
	{
		if (is_array($dbStartTime)) {
			$useMinMax = false;
			if (isset($dbStartTime['min'])) {
				$this->addUsingAlias(CcShowPeer::START_TIME, $dbStartTime['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbStartTime['max'])) {
				$this->addUsingAlias(CcShowPeer::START_TIME, $dbStartTime['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcShowPeer::START_TIME, $dbStartTime, $comparison);
	}

	/**
	 * Filter the query on the end_time column
	 * 
	 * @param     string|array $dbEndTime The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcShowQuery The current query, for fluid interface
	 */
	public function filterByDbEndTime($dbEndTime = null, $comparison = null)
	{
		if (is_array($dbEndTime)) {
			$useMinMax = false;
			if (isset($dbEndTime['min'])) {
				$this->addUsingAlias(CcShowPeer::END_TIME, $dbEndTime['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbEndTime['max'])) {
				$this->addUsingAlias(CcShowPeer::END_TIME, $dbEndTime['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcShowPeer::END_TIME, $dbEndTime, $comparison);
	}

	/**
	 * Filter the query on the repeats column
	 * 
	 * @param     int|array $dbRepeats The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcShowQuery The current query, for fluid interface
	 */
	public function filterByDbRepeats($dbRepeats = null, $comparison = null)
	{
		if (is_array($dbRepeats)) {
			$useMinMax = false;
			if (isset($dbRepeats['min'])) {
				$this->addUsingAlias(CcShowPeer::REPEATS, $dbRepeats['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbRepeats['max'])) {
				$this->addUsingAlias(CcShowPeer::REPEATS, $dbRepeats['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcShowPeer::REPEATS, $dbRepeats, $comparison);
	}

	/**
	 * Filter the query on the day column
	 * 
	 * @param     int|array $dbDay The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcShowQuery The current query, for fluid interface
	 */
	public function filterByDbDay($dbDay = null, $comparison = null)
	{
		if (is_array($dbDay)) {
			$useMinMax = false;
			if (isset($dbDay['min'])) {
				$this->addUsingAlias(CcShowPeer::DAY, $dbDay['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbDay['max'])) {
				$this->addUsingAlias(CcShowPeer::DAY, $dbDay['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcShowPeer::DAY, $dbDay, $comparison);
	}

	/**
	 * Filter the query on the description column
	 * 
	 * @param     string $dbDescription The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcShowQuery The current query, for fluid interface
	 */
	public function filterByDbDescription($dbDescription = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbDescription)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbDescription)) {
				$dbDescription = str_replace('*', '%', $dbDescription);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcShowPeer::DESCRIPTION, $dbDescription, $comparison);
	}

	/**
	 * Filter the query on the show_id column
	 * 
	 * @param     int|array $dbShowId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcShowQuery The current query, for fluid interface
	 */
	public function filterByDbShowId($dbShowId = null, $comparison = null)
	{
		if (is_array($dbShowId)) {
			$useMinMax = false;
			if (isset($dbShowId['min'])) {
				$this->addUsingAlias(CcShowPeer::SHOW_ID, $dbShowId['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbShowId['max'])) {
				$this->addUsingAlias(CcShowPeer::SHOW_ID, $dbShowId['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcShowPeer::SHOW_ID, $dbShowId, $comparison);
	}

	/**
	 * Exclude object from result
	 *
	 * @param     CcShow $ccShow Object to remove from the list of results
	 *
	 * @return    CcShowQuery The current query, for fluid interface
	 */
	public function prune($ccShow = null)
	{
		if ($ccShow) {
			$this->addUsingAlias(CcShowPeer::ID, $ccShow->getDbId(), Criteria::NOT_EQUAL);
	  }
	  
		return $this;
	}

} // BaseCcShowQuery
