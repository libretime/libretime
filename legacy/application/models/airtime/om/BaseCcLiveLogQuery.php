<?php


/**
 * Base class that represents a query for the 'cc_live_log' table.
 *
 *
 *
 * @method CcLiveLogQuery orderByDbId($order = Criteria::ASC) Order by the id column
 * @method CcLiveLogQuery orderByDbState($order = Criteria::ASC) Order by the state column
 * @method CcLiveLogQuery orderByDbStartTime($order = Criteria::ASC) Order by the start_time column
 * @method CcLiveLogQuery orderByDbEndTime($order = Criteria::ASC) Order by the end_time column
 *
 * @method CcLiveLogQuery groupByDbId() Group by the id column
 * @method CcLiveLogQuery groupByDbState() Group by the state column
 * @method CcLiveLogQuery groupByDbStartTime() Group by the start_time column
 * @method CcLiveLogQuery groupByDbEndTime() Group by the end_time column
 *
 * @method CcLiveLogQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method CcLiveLogQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method CcLiveLogQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method CcLiveLog findOne(PropelPDO $con = null) Return the first CcLiveLog matching the query
 * @method CcLiveLog findOneOrCreate(PropelPDO $con = null) Return the first CcLiveLog matching the query, or a new CcLiveLog object populated from the query conditions when no match is found
 *
 * @method CcLiveLog findOneByDbState(string $state) Return the first CcLiveLog filtered by the state column
 * @method CcLiveLog findOneByDbStartTime(string $start_time) Return the first CcLiveLog filtered by the start_time column
 * @method CcLiveLog findOneByDbEndTime(string $end_time) Return the first CcLiveLog filtered by the end_time column
 *
 * @method array findByDbId(int $id) Return CcLiveLog objects filtered by the id column
 * @method array findByDbState(string $state) Return CcLiveLog objects filtered by the state column
 * @method array findByDbStartTime(string $start_time) Return CcLiveLog objects filtered by the start_time column
 * @method array findByDbEndTime(string $end_time) Return CcLiveLog objects filtered by the end_time column
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
    public function __construct($dbName = null, $modelName = null, $modelAlias = null)
    {
        if (null === $dbName) {
            $dbName = 'airtime';
        }
        if (null === $modelName) {
            $modelName = 'CcLiveLog';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new CcLiveLogQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   CcLiveLogQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return CcLiveLogQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof CcLiveLogQuery) {
            return $criteria;
        }
        $query = new CcLiveLogQuery(null, null, $modelAlias);

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
     * @return   CcLiveLog|CcLiveLog[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = CcLiveLogPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(CcLiveLogPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 CcLiveLog A model object, or null if the key is not found
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
     * @return                 CcLiveLog A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT "id", "state", "start_time", "end_time" FROM "cc_live_log" WHERE "id" = :p0';
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
            $obj = new CcLiveLog();
            $obj->hydrate($row);
            CcLiveLogPeer::addInstanceToPool($obj, (string) $key);
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
     * @return CcLiveLog|CcLiveLog[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|CcLiveLog[]|mixed the list of results, formatted by the current formatter
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
     * @return CcLiveLogQuery The current query, for fluid interface
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
     * @return CcLiveLogQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(CcLiveLogPeer::ID, $keys, Criteria::IN);
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
     * @return CcLiveLogQuery The current query, for fluid interface
     */
    public function filterByDbId($dbId = null, $comparison = null)
    {
        if (is_array($dbId)) {
            $useMinMax = false;
            if (isset($dbId['min'])) {
                $this->addUsingAlias(CcLiveLogPeer::ID, $dbId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dbId['max'])) {
                $this->addUsingAlias(CcLiveLogPeer::ID, $dbId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CcLiveLogPeer::ID, $dbId, $comparison);
    }

    /**
     * Filter the query on the state column
     *
     * Example usage:
     * <code>
     * $query->filterByDbState('fooValue');   // WHERE state = 'fooValue'
     * $query->filterByDbState('%fooValue%'); // WHERE state LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbState The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcLiveLogQuery The current query, for fluid interface
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
     * @return CcLiveLogQuery The current query, for fluid interface
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
     * Example usage:
     * <code>
     * $query->filterByDbEndTime('2011-03-14'); // WHERE end_time = '2011-03-14'
     * $query->filterByDbEndTime('now'); // WHERE end_time = '2011-03-14'
     * $query->filterByDbEndTime(array('max' => 'yesterday')); // WHERE end_time < '2011-03-13'
     * </code>
     *
     * @param     mixed $dbEndTime The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcLiveLogQuery The current query, for fluid interface
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
     * @param   CcLiveLog $ccLiveLog Object to remove from the list of results
     *
     * @return CcLiveLogQuery The current query, for fluid interface
     */
    public function prune($ccLiveLog = null)
    {
        if ($ccLiveLog) {
            $this->addUsingAlias(CcLiveLogPeer::ID, $ccLiveLog->getDbId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}
