<?php


/**
 * Base class that represents a query for the 'cc_webstream_metadata' table.
 *
 *
 *
 * @method CcWebstreamMetadataQuery orderByDbId($order = Criteria::ASC) Order by the id column
 * @method CcWebstreamMetadataQuery orderByDbInstanceId($order = Criteria::ASC) Order by the instance_id column
 * @method CcWebstreamMetadataQuery orderByDbStartTime($order = Criteria::ASC) Order by the start_time column
 * @method CcWebstreamMetadataQuery orderByDbLiquidsoapData($order = Criteria::ASC) Order by the liquidsoap_data column
 *
 * @method CcWebstreamMetadataQuery groupByDbId() Group by the id column
 * @method CcWebstreamMetadataQuery groupByDbInstanceId() Group by the instance_id column
 * @method CcWebstreamMetadataQuery groupByDbStartTime() Group by the start_time column
 * @method CcWebstreamMetadataQuery groupByDbLiquidsoapData() Group by the liquidsoap_data column
 *
 * @method CcWebstreamMetadataQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method CcWebstreamMetadataQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method CcWebstreamMetadataQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method CcWebstreamMetadataQuery leftJoinCcSchedule($relationAlias = null) Adds a LEFT JOIN clause to the query using the CcSchedule relation
 * @method CcWebstreamMetadataQuery rightJoinCcSchedule($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CcSchedule relation
 * @method CcWebstreamMetadataQuery innerJoinCcSchedule($relationAlias = null) Adds a INNER JOIN clause to the query using the CcSchedule relation
 *
 * @method CcWebstreamMetadata findOne(PropelPDO $con = null) Return the first CcWebstreamMetadata matching the query
 * @method CcWebstreamMetadata findOneOrCreate(PropelPDO $con = null) Return the first CcWebstreamMetadata matching the query, or a new CcWebstreamMetadata object populated from the query conditions when no match is found
 *
 * @method CcWebstreamMetadata findOneByDbInstanceId(int $instance_id) Return the first CcWebstreamMetadata filtered by the instance_id column
 * @method CcWebstreamMetadata findOneByDbStartTime(string $start_time) Return the first CcWebstreamMetadata filtered by the start_time column
 * @method CcWebstreamMetadata findOneByDbLiquidsoapData(string $liquidsoap_data) Return the first CcWebstreamMetadata filtered by the liquidsoap_data column
 *
 * @method array findByDbId(int $id) Return CcWebstreamMetadata objects filtered by the id column
 * @method array findByDbInstanceId(int $instance_id) Return CcWebstreamMetadata objects filtered by the instance_id column
 * @method array findByDbStartTime(string $start_time) Return CcWebstreamMetadata objects filtered by the start_time column
 * @method array findByDbLiquidsoapData(string $liquidsoap_data) Return CcWebstreamMetadata objects filtered by the liquidsoap_data column
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
    public function __construct($dbName = null, $modelName = null, $modelAlias = null)
    {
        if (null === $dbName) {
            $dbName = 'airtime';
        }
        if (null === $modelName) {
            $modelName = 'CcWebstreamMetadata';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new CcWebstreamMetadataQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   CcWebstreamMetadataQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return CcWebstreamMetadataQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof CcWebstreamMetadataQuery) {
            return $criteria;
        }
        $query = new CcWebstreamMetadataQuery(null, null, $modelAlias);

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
     * @return   CcWebstreamMetadata|CcWebstreamMetadata[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = CcWebstreamMetadataPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(CcWebstreamMetadataPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 CcWebstreamMetadata A model object, or null if the key is not found
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
     * @return                 CcWebstreamMetadata A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT "id", "instance_id", "start_time", "liquidsoap_data" FROM "cc_webstream_metadata" WHERE "id" = :p0';
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
            $obj = new CcWebstreamMetadata();
            $obj->hydrate($row);
            CcWebstreamMetadataPeer::addInstanceToPool($obj, (string) $key);
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
     * @return CcWebstreamMetadata|CcWebstreamMetadata[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|CcWebstreamMetadata[]|mixed the list of results, formatted by the current formatter
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
     * @return CcWebstreamMetadataQuery The current query, for fluid interface
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
     * @return CcWebstreamMetadataQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(CcWebstreamMetadataPeer::ID, $keys, Criteria::IN);
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
     * @return CcWebstreamMetadataQuery The current query, for fluid interface
     */
    public function filterByDbId($dbId = null, $comparison = null)
    {
        if (is_array($dbId)) {
            $useMinMax = false;
            if (isset($dbId['min'])) {
                $this->addUsingAlias(CcWebstreamMetadataPeer::ID, $dbId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dbId['max'])) {
                $this->addUsingAlias(CcWebstreamMetadataPeer::ID, $dbId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CcWebstreamMetadataPeer::ID, $dbId, $comparison);
    }

    /**
     * Filter the query on the instance_id column
     *
     * Example usage:
     * <code>
     * $query->filterByDbInstanceId(1234); // WHERE instance_id = 1234
     * $query->filterByDbInstanceId(array(12, 34)); // WHERE instance_id IN (12, 34)
     * $query->filterByDbInstanceId(array('min' => 12)); // WHERE instance_id >= 12
     * $query->filterByDbInstanceId(array('max' => 12)); // WHERE instance_id <= 12
     * </code>
     *
     * @see       filterByCcSchedule()
     *
     * @param     mixed $dbInstanceId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcWebstreamMetadataQuery The current query, for fluid interface
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
     * @return CcWebstreamMetadataQuery The current query, for fluid interface
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
     * Example usage:
     * <code>
     * $query->filterByDbLiquidsoapData('fooValue');   // WHERE liquidsoap_data = 'fooValue'
     * $query->filterByDbLiquidsoapData('%fooValue%'); // WHERE liquidsoap_data LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbLiquidsoapData The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcWebstreamMetadataQuery The current query, for fluid interface
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
     * @param   CcSchedule|PropelObjectCollection $ccSchedule The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 CcWebstreamMetadataQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCcSchedule($ccSchedule, $comparison = null)
    {
        if ($ccSchedule instanceof CcSchedule) {
            return $this
                ->addUsingAlias(CcWebstreamMetadataPeer::INSTANCE_ID, $ccSchedule->getDbId(), $comparison);
        } elseif ($ccSchedule instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(CcWebstreamMetadataPeer::INSTANCE_ID, $ccSchedule->toKeyValue('PrimaryKey', 'DbId'), $comparison);
        } else {
            throw new PropelException('filterByCcSchedule() only accepts arguments of type CcSchedule or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CcSchedule relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return CcWebstreamMetadataQuery The current query, for fluid interface
     */
    public function joinCcSchedule($relationAlias = null, $joinType = Criteria::INNER_JOIN)
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
        if ($relationAlias) {
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
     * @return   CcScheduleQuery A secondary query class using the current class as primary query
     */
    public function useCcScheduleQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinCcSchedule($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CcSchedule', 'CcScheduleQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   CcWebstreamMetadata $ccWebstreamMetadata Object to remove from the list of results
     *
     * @return CcWebstreamMetadataQuery The current query, for fluid interface
     */
    public function prune($ccWebstreamMetadata = null)
    {
        if ($ccWebstreamMetadata) {
            $this->addUsingAlias(CcWebstreamMetadataPeer::ID, $ccWebstreamMetadata->getDbId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}
