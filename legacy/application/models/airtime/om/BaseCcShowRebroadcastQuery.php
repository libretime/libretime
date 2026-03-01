<?php


/**
 * Base class that represents a query for the 'cc_show_rebroadcast' table.
 *
 *
 *
 * @method CcShowRebroadcastQuery orderByDbId($order = Criteria::ASC) Order by the id column
 * @method CcShowRebroadcastQuery orderByDbDayOffset($order = Criteria::ASC) Order by the day_offset column
 * @method CcShowRebroadcastQuery orderByDbStartTime($order = Criteria::ASC) Order by the start_time column
 * @method CcShowRebroadcastQuery orderByDbShowId($order = Criteria::ASC) Order by the show_id column
 *
 * @method CcShowRebroadcastQuery groupByDbId() Group by the id column
 * @method CcShowRebroadcastQuery groupByDbDayOffset() Group by the day_offset column
 * @method CcShowRebroadcastQuery groupByDbStartTime() Group by the start_time column
 * @method CcShowRebroadcastQuery groupByDbShowId() Group by the show_id column
 *
 * @method CcShowRebroadcastQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method CcShowRebroadcastQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method CcShowRebroadcastQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method CcShowRebroadcastQuery leftJoinCcShow($relationAlias = null) Adds a LEFT JOIN clause to the query using the CcShow relation
 * @method CcShowRebroadcastQuery rightJoinCcShow($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CcShow relation
 * @method CcShowRebroadcastQuery innerJoinCcShow($relationAlias = null) Adds a INNER JOIN clause to the query using the CcShow relation
 *
 * @method CcShowRebroadcast findOne(PropelPDO $con = null) Return the first CcShowRebroadcast matching the query
 * @method CcShowRebroadcast findOneOrCreate(PropelPDO $con = null) Return the first CcShowRebroadcast matching the query, or a new CcShowRebroadcast object populated from the query conditions when no match is found
 *
 * @method CcShowRebroadcast findOneByDbDayOffset(string $day_offset) Return the first CcShowRebroadcast filtered by the day_offset column
 * @method CcShowRebroadcast findOneByDbStartTime(string $start_time) Return the first CcShowRebroadcast filtered by the start_time column
 * @method CcShowRebroadcast findOneByDbShowId(int $show_id) Return the first CcShowRebroadcast filtered by the show_id column
 *
 * @method array findByDbId(int $id) Return CcShowRebroadcast objects filtered by the id column
 * @method array findByDbDayOffset(string $day_offset) Return CcShowRebroadcast objects filtered by the day_offset column
 * @method array findByDbStartTime(string $start_time) Return CcShowRebroadcast objects filtered by the start_time column
 * @method array findByDbShowId(int $show_id) Return CcShowRebroadcast objects filtered by the show_id column
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcShowRebroadcastQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseCcShowRebroadcastQuery object.
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
            $modelName = 'CcShowRebroadcast';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new CcShowRebroadcastQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   CcShowRebroadcastQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return CcShowRebroadcastQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof CcShowRebroadcastQuery) {
            return $criteria;
        }
        $query = new CcShowRebroadcastQuery(null, null, $modelAlias);

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
     * @return   CcShowRebroadcast|CcShowRebroadcast[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = CcShowRebroadcastPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(CcShowRebroadcastPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 CcShowRebroadcast A model object, or null if the key is not found
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
     * @return                 CcShowRebroadcast A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT "id", "day_offset", "start_time", "show_id" FROM "cc_show_rebroadcast" WHERE "id" = :p0';
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
            $obj = new CcShowRebroadcast();
            $obj->hydrate($row);
            CcShowRebroadcastPeer::addInstanceToPool($obj, (string) $key);
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
     * @return CcShowRebroadcast|CcShowRebroadcast[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|CcShowRebroadcast[]|mixed the list of results, formatted by the current formatter
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
     * @return CcShowRebroadcastQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(CcShowRebroadcastPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return CcShowRebroadcastQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(CcShowRebroadcastPeer::ID, $keys, Criteria::IN);
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
     * @return CcShowRebroadcastQuery The current query, for fluid interface
     */
    public function filterByDbId($dbId = null, $comparison = null)
    {
        if (is_array($dbId)) {
            $useMinMax = false;
            if (isset($dbId['min'])) {
                $this->addUsingAlias(CcShowRebroadcastPeer::ID, $dbId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dbId['max'])) {
                $this->addUsingAlias(CcShowRebroadcastPeer::ID, $dbId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CcShowRebroadcastPeer::ID, $dbId, $comparison);
    }

    /**
     * Filter the query on the day_offset column
     *
     * Example usage:
     * <code>
     * $query->filterByDbDayOffset('fooValue');   // WHERE day_offset = 'fooValue'
     * $query->filterByDbDayOffset('%fooValue%'); // WHERE day_offset LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbDayOffset The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcShowRebroadcastQuery The current query, for fluid interface
     */
    public function filterByDbDayOffset($dbDayOffset = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($dbDayOffset)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $dbDayOffset)) {
                $dbDayOffset = str_replace('*', '%', $dbDayOffset);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CcShowRebroadcastPeer::DAY_OFFSET, $dbDayOffset, $comparison);
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
     * @return CcShowRebroadcastQuery The current query, for fluid interface
     */
    public function filterByDbStartTime($dbStartTime = null, $comparison = null)
    {
        if (is_array($dbStartTime)) {
            $useMinMax = false;
            if (isset($dbStartTime['min'])) {
                $this->addUsingAlias(CcShowRebroadcastPeer::START_TIME, $dbStartTime['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dbStartTime['max'])) {
                $this->addUsingAlias(CcShowRebroadcastPeer::START_TIME, $dbStartTime['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CcShowRebroadcastPeer::START_TIME, $dbStartTime, $comparison);
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
     * @return CcShowRebroadcastQuery The current query, for fluid interface
     */
    public function filterByDbShowId($dbShowId = null, $comparison = null)
    {
        if (is_array($dbShowId)) {
            $useMinMax = false;
            if (isset($dbShowId['min'])) {
                $this->addUsingAlias(CcShowRebroadcastPeer::SHOW_ID, $dbShowId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dbShowId['max'])) {
                $this->addUsingAlias(CcShowRebroadcastPeer::SHOW_ID, $dbShowId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CcShowRebroadcastPeer::SHOW_ID, $dbShowId, $comparison);
    }

    /**
     * Filter the query by a related CcShow object
     *
     * @param   CcShow|PropelObjectCollection $ccShow The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 CcShowRebroadcastQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCcShow($ccShow, $comparison = null)
    {
        if ($ccShow instanceof CcShow) {
            return $this
                ->addUsingAlias(CcShowRebroadcastPeer::SHOW_ID, $ccShow->getDbId(), $comparison);
        } elseif ($ccShow instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(CcShowRebroadcastPeer::SHOW_ID, $ccShow->toKeyValue('PrimaryKey', 'DbId'), $comparison);
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
     * @return CcShowRebroadcastQuery The current query, for fluid interface
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
     * @param   CcShowRebroadcast $ccShowRebroadcast Object to remove from the list of results
     *
     * @return CcShowRebroadcastQuery The current query, for fluid interface
     */
    public function prune($ccShowRebroadcast = null)
    {
        if ($ccShowRebroadcast) {
            $this->addUsingAlias(CcShowRebroadcastPeer::ID, $ccShowRebroadcast->getDbId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}
