<?php


/**
 * Base class that represents a query for the 'cc_listener_count' table.
 *
 *
 *
 * @method CcListenerCountQuery orderByDbId($order = Criteria::ASC) Order by the id column
 * @method CcListenerCountQuery orderByDbTimestampId($order = Criteria::ASC) Order by the timestamp_id column
 * @method CcListenerCountQuery orderByDbMountNameId($order = Criteria::ASC) Order by the mount_name_id column
 * @method CcListenerCountQuery orderByDbListenerCount($order = Criteria::ASC) Order by the listener_count column
 *
 * @method CcListenerCountQuery groupByDbId() Group by the id column
 * @method CcListenerCountQuery groupByDbTimestampId() Group by the timestamp_id column
 * @method CcListenerCountQuery groupByDbMountNameId() Group by the mount_name_id column
 * @method CcListenerCountQuery groupByDbListenerCount() Group by the listener_count column
 *
 * @method CcListenerCountQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method CcListenerCountQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method CcListenerCountQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method CcListenerCountQuery leftJoinCcTimestamp($relationAlias = null) Adds a LEFT JOIN clause to the query using the CcTimestamp relation
 * @method CcListenerCountQuery rightJoinCcTimestamp($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CcTimestamp relation
 * @method CcListenerCountQuery innerJoinCcTimestamp($relationAlias = null) Adds a INNER JOIN clause to the query using the CcTimestamp relation
 *
 * @method CcListenerCountQuery leftJoinCcMountName($relationAlias = null) Adds a LEFT JOIN clause to the query using the CcMountName relation
 * @method CcListenerCountQuery rightJoinCcMountName($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CcMountName relation
 * @method CcListenerCountQuery innerJoinCcMountName($relationAlias = null) Adds a INNER JOIN clause to the query using the CcMountName relation
 *
 * @method CcListenerCount findOne(PropelPDO $con = null) Return the first CcListenerCount matching the query
 * @method CcListenerCount findOneOrCreate(PropelPDO $con = null) Return the first CcListenerCount matching the query, or a new CcListenerCount object populated from the query conditions when no match is found
 *
 * @method CcListenerCount findOneByDbTimestampId(int $timestamp_id) Return the first CcListenerCount filtered by the timestamp_id column
 * @method CcListenerCount findOneByDbMountNameId(int $mount_name_id) Return the first CcListenerCount filtered by the mount_name_id column
 * @method CcListenerCount findOneByDbListenerCount(int $listener_count) Return the first CcListenerCount filtered by the listener_count column
 *
 * @method array findByDbId(int $id) Return CcListenerCount objects filtered by the id column
 * @method array findByDbTimestampId(int $timestamp_id) Return CcListenerCount objects filtered by the timestamp_id column
 * @method array findByDbMountNameId(int $mount_name_id) Return CcListenerCount objects filtered by the mount_name_id column
 * @method array findByDbListenerCount(int $listener_count) Return CcListenerCount objects filtered by the listener_count column
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcListenerCountQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseCcListenerCountQuery object.
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
            $modelName = 'CcListenerCount';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new CcListenerCountQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   CcListenerCountQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return CcListenerCountQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof CcListenerCountQuery) {
            return $criteria;
        }
        $query = new CcListenerCountQuery(null, null, $modelAlias);

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
     * @return   CcListenerCount|CcListenerCount[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = CcListenerCountPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(CcListenerCountPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 CcListenerCount A model object, or null if the key is not found
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
     * @return                 CcListenerCount A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT "id", "timestamp_id", "mount_name_id", "listener_count" FROM "cc_listener_count" WHERE "id" = :p0';
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
            $obj = new CcListenerCount();
            $obj->hydrate($row);
            CcListenerCountPeer::addInstanceToPool($obj, (string) $key);
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
     * @return CcListenerCount|CcListenerCount[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|CcListenerCount[]|mixed the list of results, formatted by the current formatter
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
     * @return CcListenerCountQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(CcListenerCountPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return CcListenerCountQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(CcListenerCountPeer::ID, $keys, Criteria::IN);
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
     * @return CcListenerCountQuery The current query, for fluid interface
     */
    public function filterByDbId($dbId = null, $comparison = null)
    {
        if (is_array($dbId)) {
            $useMinMax = false;
            if (isset($dbId['min'])) {
                $this->addUsingAlias(CcListenerCountPeer::ID, $dbId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dbId['max'])) {
                $this->addUsingAlias(CcListenerCountPeer::ID, $dbId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CcListenerCountPeer::ID, $dbId, $comparison);
    }

    /**
     * Filter the query on the timestamp_id column
     *
     * Example usage:
     * <code>
     * $query->filterByDbTimestampId(1234); // WHERE timestamp_id = 1234
     * $query->filterByDbTimestampId(array(12, 34)); // WHERE timestamp_id IN (12, 34)
     * $query->filterByDbTimestampId(array('min' => 12)); // WHERE timestamp_id >= 12
     * $query->filterByDbTimestampId(array('max' => 12)); // WHERE timestamp_id <= 12
     * </code>
     *
     * @see       filterByCcTimestamp()
     *
     * @param     mixed $dbTimestampId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcListenerCountQuery The current query, for fluid interface
     */
    public function filterByDbTimestampId($dbTimestampId = null, $comparison = null)
    {
        if (is_array($dbTimestampId)) {
            $useMinMax = false;
            if (isset($dbTimestampId['min'])) {
                $this->addUsingAlias(CcListenerCountPeer::TIMESTAMP_ID, $dbTimestampId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dbTimestampId['max'])) {
                $this->addUsingAlias(CcListenerCountPeer::TIMESTAMP_ID, $dbTimestampId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CcListenerCountPeer::TIMESTAMP_ID, $dbTimestampId, $comparison);
    }

    /**
     * Filter the query on the mount_name_id column
     *
     * Example usage:
     * <code>
     * $query->filterByDbMountNameId(1234); // WHERE mount_name_id = 1234
     * $query->filterByDbMountNameId(array(12, 34)); // WHERE mount_name_id IN (12, 34)
     * $query->filterByDbMountNameId(array('min' => 12)); // WHERE mount_name_id >= 12
     * $query->filterByDbMountNameId(array('max' => 12)); // WHERE mount_name_id <= 12
     * </code>
     *
     * @see       filterByCcMountName()
     *
     * @param     mixed $dbMountNameId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcListenerCountQuery The current query, for fluid interface
     */
    public function filterByDbMountNameId($dbMountNameId = null, $comparison = null)
    {
        if (is_array($dbMountNameId)) {
            $useMinMax = false;
            if (isset($dbMountNameId['min'])) {
                $this->addUsingAlias(CcListenerCountPeer::MOUNT_NAME_ID, $dbMountNameId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dbMountNameId['max'])) {
                $this->addUsingAlias(CcListenerCountPeer::MOUNT_NAME_ID, $dbMountNameId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CcListenerCountPeer::MOUNT_NAME_ID, $dbMountNameId, $comparison);
    }

    /**
     * Filter the query on the listener_count column
     *
     * Example usage:
     * <code>
     * $query->filterByDbListenerCount(1234); // WHERE listener_count = 1234
     * $query->filterByDbListenerCount(array(12, 34)); // WHERE listener_count IN (12, 34)
     * $query->filterByDbListenerCount(array('min' => 12)); // WHERE listener_count >= 12
     * $query->filterByDbListenerCount(array('max' => 12)); // WHERE listener_count <= 12
     * </code>
     *
     * @param     mixed $dbListenerCount The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcListenerCountQuery The current query, for fluid interface
     */
    public function filterByDbListenerCount($dbListenerCount = null, $comparison = null)
    {
        if (is_array($dbListenerCount)) {
            $useMinMax = false;
            if (isset($dbListenerCount['min'])) {
                $this->addUsingAlias(CcListenerCountPeer::LISTENER_COUNT, $dbListenerCount['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dbListenerCount['max'])) {
                $this->addUsingAlias(CcListenerCountPeer::LISTENER_COUNT, $dbListenerCount['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CcListenerCountPeer::LISTENER_COUNT, $dbListenerCount, $comparison);
    }

    /**
     * Filter the query by a related CcTimestamp object
     *
     * @param   CcTimestamp|PropelObjectCollection $ccTimestamp The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 CcListenerCountQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCcTimestamp($ccTimestamp, $comparison = null)
    {
        if ($ccTimestamp instanceof CcTimestamp) {
            return $this
                ->addUsingAlias(CcListenerCountPeer::TIMESTAMP_ID, $ccTimestamp->getDbId(), $comparison);
        } elseif ($ccTimestamp instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(CcListenerCountPeer::TIMESTAMP_ID, $ccTimestamp->toKeyValue('PrimaryKey', 'DbId'), $comparison);
        } else {
            throw new PropelException('filterByCcTimestamp() only accepts arguments of type CcTimestamp or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CcTimestamp relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return CcListenerCountQuery The current query, for fluid interface
     */
    public function joinCcTimestamp($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CcTimestamp');

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
            $this->addJoinObject($join, 'CcTimestamp');
        }

        return $this;
    }

    /**
     * Use the CcTimestamp relation CcTimestamp object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   CcTimestampQuery A secondary query class using the current class as primary query
     */
    public function useCcTimestampQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinCcTimestamp($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CcTimestamp', 'CcTimestampQuery');
    }

    /**
     * Filter the query by a related CcMountName object
     *
     * @param   CcMountName|PropelObjectCollection $ccMountName The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 CcListenerCountQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCcMountName($ccMountName, $comparison = null)
    {
        if ($ccMountName instanceof CcMountName) {
            return $this
                ->addUsingAlias(CcListenerCountPeer::MOUNT_NAME_ID, $ccMountName->getDbId(), $comparison);
        } elseif ($ccMountName instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(CcListenerCountPeer::MOUNT_NAME_ID, $ccMountName->toKeyValue('PrimaryKey', 'DbId'), $comparison);
        } else {
            throw new PropelException('filterByCcMountName() only accepts arguments of type CcMountName or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CcMountName relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return CcListenerCountQuery The current query, for fluid interface
     */
    public function joinCcMountName($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CcMountName');

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
            $this->addJoinObject($join, 'CcMountName');
        }

        return $this;
    }

    /**
     * Use the CcMountName relation CcMountName object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   CcMountNameQuery A secondary query class using the current class as primary query
     */
    public function useCcMountNameQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinCcMountName($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CcMountName', 'CcMountNameQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   CcListenerCount $ccListenerCount Object to remove from the list of results
     *
     * @return CcListenerCountQuery The current query, for fluid interface
     */
    public function prune($ccListenerCount = null)
    {
        if ($ccListenerCount) {
            $this->addUsingAlias(CcListenerCountPeer::ID, $ccListenerCount->getDbId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}
