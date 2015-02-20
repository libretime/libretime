<?php


/**
 * Base class that represents a query for the 'cc_playout_history_metadata' table.
 *
 *
 *
 * @method CcPlayoutHistoryMetaDataQuery orderByDbId($order = Criteria::ASC) Order by the id column
 * @method CcPlayoutHistoryMetaDataQuery orderByDbHistoryId($order = Criteria::ASC) Order by the history_id column
 * @method CcPlayoutHistoryMetaDataQuery orderByDbKey($order = Criteria::ASC) Order by the key column
 * @method CcPlayoutHistoryMetaDataQuery orderByDbValue($order = Criteria::ASC) Order by the value column
 *
 * @method CcPlayoutHistoryMetaDataQuery groupByDbId() Group by the id column
 * @method CcPlayoutHistoryMetaDataQuery groupByDbHistoryId() Group by the history_id column
 * @method CcPlayoutHistoryMetaDataQuery groupByDbKey() Group by the key column
 * @method CcPlayoutHistoryMetaDataQuery groupByDbValue() Group by the value column
 *
 * @method CcPlayoutHistoryMetaDataQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method CcPlayoutHistoryMetaDataQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method CcPlayoutHistoryMetaDataQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method CcPlayoutHistoryMetaDataQuery leftJoinCcPlayoutHistory($relationAlias = null) Adds a LEFT JOIN clause to the query using the CcPlayoutHistory relation
 * @method CcPlayoutHistoryMetaDataQuery rightJoinCcPlayoutHistory($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CcPlayoutHistory relation
 * @method CcPlayoutHistoryMetaDataQuery innerJoinCcPlayoutHistory($relationAlias = null) Adds a INNER JOIN clause to the query using the CcPlayoutHistory relation
 *
 * @method CcPlayoutHistoryMetaData findOne(PropelPDO $con = null) Return the first CcPlayoutHistoryMetaData matching the query
 * @method CcPlayoutHistoryMetaData findOneOrCreate(PropelPDO $con = null) Return the first CcPlayoutHistoryMetaData matching the query, or a new CcPlayoutHistoryMetaData object populated from the query conditions when no match is found
 *
 * @method CcPlayoutHistoryMetaData findOneByDbHistoryId(int $history_id) Return the first CcPlayoutHistoryMetaData filtered by the history_id column
 * @method CcPlayoutHistoryMetaData findOneByDbKey(string $key) Return the first CcPlayoutHistoryMetaData filtered by the key column
 * @method CcPlayoutHistoryMetaData findOneByDbValue(string $value) Return the first CcPlayoutHistoryMetaData filtered by the value column
 *
 * @method array findByDbId(int $id) Return CcPlayoutHistoryMetaData objects filtered by the id column
 * @method array findByDbHistoryId(int $history_id) Return CcPlayoutHistoryMetaData objects filtered by the history_id column
 * @method array findByDbKey(string $key) Return CcPlayoutHistoryMetaData objects filtered by the key column
 * @method array findByDbValue(string $value) Return CcPlayoutHistoryMetaData objects filtered by the value column
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
    public function __construct($dbName = null, $modelName = null, $modelAlias = null)
    {
        if (null === $dbName) {
            $dbName = 'airtime';
        }
        if (null === $modelName) {
            $modelName = 'CcPlayoutHistoryMetaData';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new CcPlayoutHistoryMetaDataQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   CcPlayoutHistoryMetaDataQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return CcPlayoutHistoryMetaDataQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof CcPlayoutHistoryMetaDataQuery) {
            return $criteria;
        }
        $query = new CcPlayoutHistoryMetaDataQuery(null, null, $modelAlias);

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
     * @return   CcPlayoutHistoryMetaData|CcPlayoutHistoryMetaData[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = CcPlayoutHistoryMetaDataPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(CcPlayoutHistoryMetaDataPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 CcPlayoutHistoryMetaData A model object, or null if the key is not found
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
     * @return                 CcPlayoutHistoryMetaData A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT "id", "history_id", "key", "value" FROM "cc_playout_history_metadata" WHERE "id" = :p0';
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
            $obj = new CcPlayoutHistoryMetaData();
            $obj->hydrate($row);
            CcPlayoutHistoryMetaDataPeer::addInstanceToPool($obj, (string) $key);
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
     * @return CcPlayoutHistoryMetaData|CcPlayoutHistoryMetaData[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|CcPlayoutHistoryMetaData[]|mixed the list of results, formatted by the current formatter
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
     * @return CcPlayoutHistoryMetaDataQuery The current query, for fluid interface
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
     * @return CcPlayoutHistoryMetaDataQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(CcPlayoutHistoryMetaDataPeer::ID, $keys, Criteria::IN);
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
     * @return CcPlayoutHistoryMetaDataQuery The current query, for fluid interface
     */
    public function filterByDbId($dbId = null, $comparison = null)
    {
        if (is_array($dbId)) {
            $useMinMax = false;
            if (isset($dbId['min'])) {
                $this->addUsingAlias(CcPlayoutHistoryMetaDataPeer::ID, $dbId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dbId['max'])) {
                $this->addUsingAlias(CcPlayoutHistoryMetaDataPeer::ID, $dbId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CcPlayoutHistoryMetaDataPeer::ID, $dbId, $comparison);
    }

    /**
     * Filter the query on the history_id column
     *
     * Example usage:
     * <code>
     * $query->filterByDbHistoryId(1234); // WHERE history_id = 1234
     * $query->filterByDbHistoryId(array(12, 34)); // WHERE history_id IN (12, 34)
     * $query->filterByDbHistoryId(array('min' => 12)); // WHERE history_id >= 12
     * $query->filterByDbHistoryId(array('max' => 12)); // WHERE history_id <= 12
     * </code>
     *
     * @see       filterByCcPlayoutHistory()
     *
     * @param     mixed $dbHistoryId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcPlayoutHistoryMetaDataQuery The current query, for fluid interface
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
     * Example usage:
     * <code>
     * $query->filterByDbKey('fooValue');   // WHERE key = 'fooValue'
     * $query->filterByDbKey('%fooValue%'); // WHERE key LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbKey The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcPlayoutHistoryMetaDataQuery The current query, for fluid interface
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
     * Example usage:
     * <code>
     * $query->filterByDbValue('fooValue');   // WHERE value = 'fooValue'
     * $query->filterByDbValue('%fooValue%'); // WHERE value LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbValue The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcPlayoutHistoryMetaDataQuery The current query, for fluid interface
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
     * @param   CcPlayoutHistory|PropelObjectCollection $ccPlayoutHistory The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 CcPlayoutHistoryMetaDataQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCcPlayoutHistory($ccPlayoutHistory, $comparison = null)
    {
        if ($ccPlayoutHistory instanceof CcPlayoutHistory) {
            return $this
                ->addUsingAlias(CcPlayoutHistoryMetaDataPeer::HISTORY_ID, $ccPlayoutHistory->getDbId(), $comparison);
        } elseif ($ccPlayoutHistory instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(CcPlayoutHistoryMetaDataPeer::HISTORY_ID, $ccPlayoutHistory->toKeyValue('PrimaryKey', 'DbId'), $comparison);
        } else {
            throw new PropelException('filterByCcPlayoutHistory() only accepts arguments of type CcPlayoutHistory or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CcPlayoutHistory relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return CcPlayoutHistoryMetaDataQuery The current query, for fluid interface
     */
    public function joinCcPlayoutHistory($relationAlias = null, $joinType = Criteria::INNER_JOIN)
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
        if ($relationAlias) {
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
     * @return   CcPlayoutHistoryQuery A secondary query class using the current class as primary query
     */
    public function useCcPlayoutHistoryQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinCcPlayoutHistory($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CcPlayoutHistory', 'CcPlayoutHistoryQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   CcPlayoutHistoryMetaData $ccPlayoutHistoryMetaData Object to remove from the list of results
     *
     * @return CcPlayoutHistoryMetaDataQuery The current query, for fluid interface
     */
    public function prune($ccPlayoutHistoryMetaData = null)
    {
        if ($ccPlayoutHistoryMetaData) {
            $this->addUsingAlias(CcPlayoutHistoryMetaDataPeer::ID, $ccPlayoutHistoryMetaData->getDbId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}
