<?php


/**
 * Base class that represents a query for the 'cc_show_days' table.
 *
 *
 *
 * @method CcShowDaysQuery orderByDbId($order = Criteria::ASC) Order by the id column
 * @method CcShowDaysQuery orderByDbFirstShow($order = Criteria::ASC) Order by the first_show column
 * @method CcShowDaysQuery orderByDbLastShow($order = Criteria::ASC) Order by the last_show column
 * @method CcShowDaysQuery orderByDbStartTime($order = Criteria::ASC) Order by the start_time column
 * @method CcShowDaysQuery orderByDbTimezone($order = Criteria::ASC) Order by the timezone column
 * @method CcShowDaysQuery orderByDbDuration($order = Criteria::ASC) Order by the duration column
 * @method CcShowDaysQuery orderByDbDay($order = Criteria::ASC) Order by the day column
 * @method CcShowDaysQuery orderByDbRepeatType($order = Criteria::ASC) Order by the repeat_type column
 * @method CcShowDaysQuery orderByDbNextPopDate($order = Criteria::ASC) Order by the next_pop_date column
 * @method CcShowDaysQuery orderByDbShowId($order = Criteria::ASC) Order by the show_id column
 * @method CcShowDaysQuery orderByDbRecord($order = Criteria::ASC) Order by the record column
 *
 * @method CcShowDaysQuery groupByDbId() Group by the id column
 * @method CcShowDaysQuery groupByDbFirstShow() Group by the first_show column
 * @method CcShowDaysQuery groupByDbLastShow() Group by the last_show column
 * @method CcShowDaysQuery groupByDbStartTime() Group by the start_time column
 * @method CcShowDaysQuery groupByDbTimezone() Group by the timezone column
 * @method CcShowDaysQuery groupByDbDuration() Group by the duration column
 * @method CcShowDaysQuery groupByDbDay() Group by the day column
 * @method CcShowDaysQuery groupByDbRepeatType() Group by the repeat_type column
 * @method CcShowDaysQuery groupByDbNextPopDate() Group by the next_pop_date column
 * @method CcShowDaysQuery groupByDbShowId() Group by the show_id column
 * @method CcShowDaysQuery groupByDbRecord() Group by the record column
 *
 * @method CcShowDaysQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method CcShowDaysQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method CcShowDaysQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method CcShowDaysQuery leftJoinCcShow($relationAlias = null) Adds a LEFT JOIN clause to the query using the CcShow relation
 * @method CcShowDaysQuery rightJoinCcShow($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CcShow relation
 * @method CcShowDaysQuery innerJoinCcShow($relationAlias = null) Adds a INNER JOIN clause to the query using the CcShow relation
 *
 * @method CcShowDays findOne(PropelPDO $con = null) Return the first CcShowDays matching the query
 * @method CcShowDays findOneOrCreate(PropelPDO $con = null) Return the first CcShowDays matching the query, or a new CcShowDays object populated from the query conditions when no match is found
 *
 * @method CcShowDays findOneByDbFirstShow(string $first_show) Return the first CcShowDays filtered by the first_show column
 * @method CcShowDays findOneByDbLastShow(string $last_show) Return the first CcShowDays filtered by the last_show column
 * @method CcShowDays findOneByDbStartTime(string $start_time) Return the first CcShowDays filtered by the start_time column
 * @method CcShowDays findOneByDbTimezone(string $timezone) Return the first CcShowDays filtered by the timezone column
 * @method CcShowDays findOneByDbDuration(string $duration) Return the first CcShowDays filtered by the duration column
 * @method CcShowDays findOneByDbDay(int $day) Return the first CcShowDays filtered by the day column
 * @method CcShowDays findOneByDbRepeatType(int $repeat_type) Return the first CcShowDays filtered by the repeat_type column
 * @method CcShowDays findOneByDbNextPopDate(string $next_pop_date) Return the first CcShowDays filtered by the next_pop_date column
 * @method CcShowDays findOneByDbShowId(int $show_id) Return the first CcShowDays filtered by the show_id column
 * @method CcShowDays findOneByDbRecord(int $record) Return the first CcShowDays filtered by the record column
 *
 * @method array findByDbId(int $id) Return CcShowDays objects filtered by the id column
 * @method array findByDbFirstShow(string $first_show) Return CcShowDays objects filtered by the first_show column
 * @method array findByDbLastShow(string $last_show) Return CcShowDays objects filtered by the last_show column
 * @method array findByDbStartTime(string $start_time) Return CcShowDays objects filtered by the start_time column
 * @method array findByDbTimezone(string $timezone) Return CcShowDays objects filtered by the timezone column
 * @method array findByDbDuration(string $duration) Return CcShowDays objects filtered by the duration column
 * @method array findByDbDay(int $day) Return CcShowDays objects filtered by the day column
 * @method array findByDbRepeatType(int $repeat_type) Return CcShowDays objects filtered by the repeat_type column
 * @method array findByDbNextPopDate(string $next_pop_date) Return CcShowDays objects filtered by the next_pop_date column
 * @method array findByDbShowId(int $show_id) Return CcShowDays objects filtered by the show_id column
 * @method array findByDbRecord(int $record) Return CcShowDays objects filtered by the record column
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
    public function __construct($dbName = null, $modelName = null, $modelAlias = null)
    {
        if (null === $dbName) {
            $dbName = 'airtime';
        }
        if (null === $modelName) {
            $modelName = 'CcShowDays';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new CcShowDaysQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   CcShowDaysQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return CcShowDaysQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof CcShowDaysQuery) {
            return $criteria;
        }
        $query = new CcShowDaysQuery(null, null, $modelAlias);

        if ($criteria instanceof Criteria) {
            $query->mergeWith($criteria);
        }

        return $query;
    }

    /**
     * Find object by primary key.
     * Propel uses the instance pool to skip the database if the object exists.
     * Go fast if the query is untouched.
     *
     * <code>
     * $obj  = $c->findPk(12, $con);
     * </code>
     *
     * @param mixed $key Primary key to use for the query
     * @param     PropelPDO $con an optional connection object
     *
     * @return   CcShowDays|CcShowDays[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = CcShowDaysPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(CcShowDaysPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }
        $this->basePreSelect($con);
        if ($this->formatter || $this->modelAlias || $this->with || $this->select
         || $this->selectColumns || $this->asColumns || $this->selectModifiers
         || $this->map || $this->having || $this->joins) {
            return $this->findPkComplex($key, $con);
        } else {
            return $this->findPkSimple($key, $con);
        }
    }

    /**
     * Alias of findPk to use instance pooling
     *
     * @param     mixed $key Primary key to use for the query
     * @param     PropelPDO $con A connection object
     *
     * @return                 CcShowDays A model object, or null if the key is not found
     * @throws PropelException
     */
     public function findOneByDbId($key, $con = null)
     {
        return $this->findPk($key, $con);
     }

    /**
     * Find object by primary key using raw SQL to go fast.
     * Bypass doSelect() and the object formatter by using generated code.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     PropelPDO $con A connection object
     *
     * @return                 CcShowDays A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT "id", "first_show", "last_show", "start_time", "timezone", "duration", "day", "repeat_type", "next_pop_date", "show_id", "record" FROM "cc_show_days" WHERE "id" = :p0';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key, PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $obj = new CcShowDays();
            $obj->hydrate($row);
            CcShowDaysPeer::addInstanceToPool($obj, (string) $key);
        }
        $stmt->closeCursor();

        return $obj;
    }

    /**
     * Find object by primary key.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     PropelPDO $con A connection object
     *
     * @return CcShowDays|CcShowDays[]|mixed the result, formatted by the current formatter
     */
    protected function findPkComplex($key, $con)
    {
        // As the query uses a PK condition, no limit(1) is necessary.
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $stmt = $criteria
            ->filterByPrimaryKey($key)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->formatOne($stmt);
    }

    /**
     * Find objects by primary key
     * <code>
     * $objs = $c->findPks(array(12, 56, 832), $con);
     * </code>
     * @param     array $keys Primary keys to use for the query
     * @param     PropelPDO $con an optional connection object
     *
     * @return PropelObjectCollection|CcShowDays[]|mixed the list of results, formatted by the current formatter
     */
    public function findPks($keys, $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection($this->getDbName(), Propel::CONNECTION_READ);
        }
        $this->basePreSelect($con);
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $stmt = $criteria
            ->filterByPrimaryKeys($keys)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->format($stmt);
    }

    /**
     * Filter the query by primary key
     *
     * @param     mixed $key Primary key to use for the query
     *
     * @return CcShowDaysQuery The current query, for fluid interface
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
     * @return CcShowDaysQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(CcShowDaysPeer::ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the id column
     *
     * Example usage:
     * <code>
     * $query->filterByDbId(1234); // WHERE id = 1234
     * $query->filterByDbId(array(12, 34)); // WHERE id IN (12, 34)
     * $query->filterByDbId(array('min' => 12)); // WHERE id >= 12
     * $query->filterByDbId(array('max' => 12)); // WHERE id <= 12
     * </code>
     *
     * @param     mixed $dbId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcShowDaysQuery The current query, for fluid interface
     */
    public function filterByDbId($dbId = null, $comparison = null)
    {
        if (is_array($dbId)) {
            $useMinMax = false;
            if (isset($dbId['min'])) {
                $this->addUsingAlias(CcShowDaysPeer::ID, $dbId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dbId['max'])) {
                $this->addUsingAlias(CcShowDaysPeer::ID, $dbId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CcShowDaysPeer::ID, $dbId, $comparison);
    }

    /**
     * Filter the query on the first_show column
     *
     * Example usage:
     * <code>
     * $query->filterByDbFirstShow('2011-03-14'); // WHERE first_show = '2011-03-14'
     * $query->filterByDbFirstShow('now'); // WHERE first_show = '2011-03-14'
     * $query->filterByDbFirstShow(array('max' => 'yesterday')); // WHERE first_show < '2011-03-13'
     * </code>
     *
     * @param     mixed $dbFirstShow The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcShowDaysQuery The current query, for fluid interface
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
     * Example usage:
     * <code>
     * $query->filterByDbLastShow('2011-03-14'); // WHERE last_show = '2011-03-14'
     * $query->filterByDbLastShow('now'); // WHERE last_show = '2011-03-14'
     * $query->filterByDbLastShow(array('max' => 'yesterday')); // WHERE last_show < '2011-03-13'
     * </code>
     *
     * @param     mixed $dbLastShow The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcShowDaysQuery The current query, for fluid interface
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
     * Example usage:
     * <code>
     * $query->filterByDbStartTime('2011-03-14'); // WHERE start_time = '2011-03-14'
     * $query->filterByDbStartTime('now'); // WHERE start_time = '2011-03-14'
     * $query->filterByDbStartTime(array('max' => 'yesterday')); // WHERE start_time < '2011-03-13'
     * </code>
     *
     * @param     mixed $dbStartTime The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcShowDaysQuery The current query, for fluid interface
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
     * Example usage:
     * <code>
     * $query->filterByDbTimezone('fooValue');   // WHERE timezone = 'fooValue'
     * $query->filterByDbTimezone('%fooValue%'); // WHERE timezone LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbTimezone The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcShowDaysQuery The current query, for fluid interface
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
     * Example usage:
     * <code>
     * $query->filterByDbDuration('fooValue');   // WHERE duration = 'fooValue'
     * $query->filterByDbDuration('%fooValue%'); // WHERE duration LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbDuration The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcShowDaysQuery The current query, for fluid interface
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
     * Example usage:
     * <code>
     * $query->filterByDbDay(1234); // WHERE day = 1234
     * $query->filterByDbDay(array(12, 34)); // WHERE day IN (12, 34)
     * $query->filterByDbDay(array('min' => 12)); // WHERE day >= 12
     * $query->filterByDbDay(array('max' => 12)); // WHERE day <= 12
     * </code>
     *
     * @param     mixed $dbDay The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcShowDaysQuery The current query, for fluid interface
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
     * Example usage:
     * <code>
     * $query->filterByDbRepeatType(1234); // WHERE repeat_type = 1234
     * $query->filterByDbRepeatType(array(12, 34)); // WHERE repeat_type IN (12, 34)
     * $query->filterByDbRepeatType(array('min' => 12)); // WHERE repeat_type >= 12
     * $query->filterByDbRepeatType(array('max' => 12)); // WHERE repeat_type <= 12
     * </code>
     *
     * @param     mixed $dbRepeatType The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcShowDaysQuery The current query, for fluid interface
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
     * Example usage:
     * <code>
     * $query->filterByDbNextPopDate('2011-03-14'); // WHERE next_pop_date = '2011-03-14'
     * $query->filterByDbNextPopDate('now'); // WHERE next_pop_date = '2011-03-14'
     * $query->filterByDbNextPopDate(array('max' => 'yesterday')); // WHERE next_pop_date < '2011-03-13'
     * </code>
     *
     * @param     mixed $dbNextPopDate The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcShowDaysQuery The current query, for fluid interface
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
     * Example usage:
     * <code>
     * $query->filterByDbShowId(1234); // WHERE show_id = 1234
     * $query->filterByDbShowId(array(12, 34)); // WHERE show_id IN (12, 34)
     * $query->filterByDbShowId(array('min' => 12)); // WHERE show_id >= 12
     * $query->filterByDbShowId(array('max' => 12)); // WHERE show_id <= 12
     * </code>
     *
     * @see       filterByCcShow()
     *
     * @param     mixed $dbShowId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcShowDaysQuery The current query, for fluid interface
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
     * Example usage:
     * <code>
     * $query->filterByDbRecord(1234); // WHERE record = 1234
     * $query->filterByDbRecord(array(12, 34)); // WHERE record IN (12, 34)
     * $query->filterByDbRecord(array('min' => 12)); // WHERE record >= 12
     * $query->filterByDbRecord(array('max' => 12)); // WHERE record <= 12
     * </code>
     *
     * @param     mixed $dbRecord The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcShowDaysQuery The current query, for fluid interface
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
     * @param   CcShow|PropelObjectCollection $ccShow The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 CcShowDaysQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCcShow($ccShow, $comparison = null)
    {
        if ($ccShow instanceof CcShow) {
            return $this
                ->addUsingAlias(CcShowDaysPeer::SHOW_ID, $ccShow->getDbId(), $comparison);
        } elseif ($ccShow instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(CcShowDaysPeer::SHOW_ID, $ccShow->toKeyValue('PrimaryKey', 'DbId'), $comparison);
        } else {
            throw new PropelException('filterByCcShow() only accepts arguments of type CcShow or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CcShow relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return CcShowDaysQuery The current query, for fluid interface
     */
    public function joinCcShow($relationAlias = null, $joinType = Criteria::INNER_JOIN)
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
        if ($relationAlias) {
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
     * @return   CcShowQuery A secondary query class using the current class as primary query
     */
    public function useCcShowQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinCcShow($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CcShow', 'CcShowQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   CcShowDays $ccShowDays Object to remove from the list of results
     *
     * @return CcShowDaysQuery The current query, for fluid interface
     */
    public function prune($ccShowDays = null)
    {
        if ($ccShowDays) {
            $this->addUsingAlias(CcShowDaysPeer::ID, $ccShowDays->getDbId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}
