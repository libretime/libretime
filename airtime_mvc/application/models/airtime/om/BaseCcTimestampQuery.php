<?php


/**
 * Base class that represents a query for the 'cc_timestamp' table.
 *
 *
 *
 * @method CcTimestampQuery orderByDbId($order = Criteria::ASC) Order by the id column
 * @method CcTimestampQuery orderByDbTimestamp($order = Criteria::ASC) Order by the timestamp column
 *
 * @method CcTimestampQuery groupByDbId() Group by the id column
 * @method CcTimestampQuery groupByDbTimestamp() Group by the timestamp column
 *
 * @method CcTimestampQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method CcTimestampQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method CcTimestampQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method CcTimestampQuery leftJoinCcListenerCount($relationAlias = null) Adds a LEFT JOIN clause to the query using the CcListenerCount relation
 * @method CcTimestampQuery rightJoinCcListenerCount($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CcListenerCount relation
 * @method CcTimestampQuery innerJoinCcListenerCount($relationAlias = null) Adds a INNER JOIN clause to the query using the CcListenerCount relation
 *
 * @method CcTimestamp findOne(PropelPDO $con = null) Return the first CcTimestamp matching the query
 * @method CcTimestamp findOneOrCreate(PropelPDO $con = null) Return the first CcTimestamp matching the query, or a new CcTimestamp object populated from the query conditions when no match is found
 *
 * @method CcTimestamp findOneByDbTimestamp(string $timestamp) Return the first CcTimestamp filtered by the timestamp column
 *
 * @method array findByDbId(int $id) Return CcTimestamp objects filtered by the id column
 * @method array findByDbTimestamp(string $timestamp) Return CcTimestamp objects filtered by the timestamp column
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcTimestampQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseCcTimestampQuery object.
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
            $modelName = 'CcTimestamp';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new CcTimestampQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   CcTimestampQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return CcTimestampQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof CcTimestampQuery) {
            return $criteria;
        }
        $query = new CcTimestampQuery(null, null, $modelAlias);

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
     * @return   CcTimestamp|CcTimestamp[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = CcTimestampPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(CcTimestampPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 CcTimestamp A model object, or null if the key is not found
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
     * @return                 CcTimestamp A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT "id", "timestamp" FROM "cc_timestamp" WHERE "id" = :p0';
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
            $obj = new CcTimestamp();
            $obj->hydrate($row);
            CcTimestampPeer::addInstanceToPool($obj, (string) $key);
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
     * @return CcTimestamp|CcTimestamp[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|CcTimestamp[]|mixed the list of results, formatted by the current formatter
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
     * @return CcTimestampQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(CcTimestampPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return CcTimestampQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(CcTimestampPeer::ID, $keys, Criteria::IN);
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
     * @return CcTimestampQuery The current query, for fluid interface
     */
    public function filterByDbId($dbId = null, $comparison = null)
    {
        if (is_array($dbId)) {
            $useMinMax = false;
            if (isset($dbId['min'])) {
                $this->addUsingAlias(CcTimestampPeer::ID, $dbId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dbId['max'])) {
                $this->addUsingAlias(CcTimestampPeer::ID, $dbId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CcTimestampPeer::ID, $dbId, $comparison);
    }

    /**
     * Filter the query on the timestamp column
     *
     * Example usage:
     * <code>
     * $query->filterByDbTimestamp('2011-03-14'); // WHERE timestamp = '2011-03-14'
     * $query->filterByDbTimestamp('now'); // WHERE timestamp = '2011-03-14'
     * $query->filterByDbTimestamp(array('max' => 'yesterday')); // WHERE timestamp < '2011-03-13'
     * </code>
     *
     * @param     mixed $dbTimestamp The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcTimestampQuery The current query, for fluid interface
     */
    public function filterByDbTimestamp($dbTimestamp = null, $comparison = null)
    {
        if (is_array($dbTimestamp)) {
            $useMinMax = false;
            if (isset($dbTimestamp['min'])) {
                $this->addUsingAlias(CcTimestampPeer::TIMESTAMP, $dbTimestamp['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dbTimestamp['max'])) {
                $this->addUsingAlias(CcTimestampPeer::TIMESTAMP, $dbTimestamp['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CcTimestampPeer::TIMESTAMP, $dbTimestamp, $comparison);
    }

    /**
     * Filter the query by a related CcListenerCount object
     *
     * @param   CcListenerCount|PropelObjectCollection $ccListenerCount  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 CcTimestampQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCcListenerCount($ccListenerCount, $comparison = null)
    {
        if ($ccListenerCount instanceof CcListenerCount) {
            return $this
                ->addUsingAlias(CcTimestampPeer::ID, $ccListenerCount->getDbTimestampId(), $comparison);
        } elseif ($ccListenerCount instanceof PropelObjectCollection) {
            return $this
                ->useCcListenerCountQuery()
                ->filterByPrimaryKeys($ccListenerCount->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByCcListenerCount() only accepts arguments of type CcListenerCount or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CcListenerCount relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return CcTimestampQuery The current query, for fluid interface
     */
    public function joinCcListenerCount($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CcListenerCount');

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
            $this->addJoinObject($join, 'CcListenerCount');
        }

        return $this;
    }

    /**
     * Use the CcListenerCount relation CcListenerCount object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   CcListenerCountQuery A secondary query class using the current class as primary query
     */
    public function useCcListenerCountQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinCcListenerCount($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CcListenerCount', 'CcListenerCountQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   CcTimestamp $ccTimestamp Object to remove from the list of results
     *
     * @return CcTimestampQuery The current query, for fluid interface
     */
    public function prune($ccTimestamp = null)
    {
        if ($ccTimestamp) {
            $this->addUsingAlias(CcTimestampPeer::ID, $ccTimestamp->getDbId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}
