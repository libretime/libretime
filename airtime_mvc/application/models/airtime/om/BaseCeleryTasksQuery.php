<?php


/**
 * Base class that represents a query for the 'celery_tasks' table.
 *
 *
 *
 * @method CeleryTasksQuery orderByDbId($order = Criteria::ASC) Order by the id column
 * @method CeleryTasksQuery orderByDbTaskId($order = Criteria::ASC) Order by the task_id column
 * @method CeleryTasksQuery orderByDbTrackReference($order = Criteria::ASC) Order by the track_reference column
 * @method CeleryTasksQuery orderByDbName($order = Criteria::ASC) Order by the name column
 * @method CeleryTasksQuery orderByDbDispatchTime($order = Criteria::ASC) Order by the dispatch_time column
 * @method CeleryTasksQuery orderByDbStatus($order = Criteria::ASC) Order by the status column
 *
 * @method CeleryTasksQuery groupByDbId() Group by the id column
 * @method CeleryTasksQuery groupByDbTaskId() Group by the task_id column
 * @method CeleryTasksQuery groupByDbTrackReference() Group by the track_reference column
 * @method CeleryTasksQuery groupByDbName() Group by the name column
 * @method CeleryTasksQuery groupByDbDispatchTime() Group by the dispatch_time column
 * @method CeleryTasksQuery groupByDbStatus() Group by the status column
 *
 * @method CeleryTasksQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method CeleryTasksQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method CeleryTasksQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method CeleryTasksQuery leftJoinThirdPartyTrackReferences($relationAlias = null) Adds a LEFT JOIN clause to the query using the ThirdPartyTrackReferences relation
 * @method CeleryTasksQuery rightJoinThirdPartyTrackReferences($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ThirdPartyTrackReferences relation
 * @method CeleryTasksQuery innerJoinThirdPartyTrackReferences($relationAlias = null) Adds a INNER JOIN clause to the query using the ThirdPartyTrackReferences relation
 *
 * @method CeleryTasks findOne(PropelPDO $con = null) Return the first CeleryTasks matching the query
 * @method CeleryTasks findOneOrCreate(PropelPDO $con = null) Return the first CeleryTasks matching the query, or a new CeleryTasks object populated from the query conditions when no match is found
 *
 * @method CeleryTasks findOneByDbTaskId(string $task_id) Return the first CeleryTasks filtered by the task_id column
 * @method CeleryTasks findOneByDbTrackReference(int $track_reference) Return the first CeleryTasks filtered by the track_reference column
 * @method CeleryTasks findOneByDbName(string $name) Return the first CeleryTasks filtered by the name column
 * @method CeleryTasks findOneByDbDispatchTime(string $dispatch_time) Return the first CeleryTasks filtered by the dispatch_time column
 * @method CeleryTasks findOneByDbStatus(string $status) Return the first CeleryTasks filtered by the status column
 *
 * @method array findByDbId(int $id) Return CeleryTasks objects filtered by the id column
 * @method array findByDbTaskId(string $task_id) Return CeleryTasks objects filtered by the task_id column
 * @method array findByDbTrackReference(int $track_reference) Return CeleryTasks objects filtered by the track_reference column
 * @method array findByDbName(string $name) Return CeleryTasks objects filtered by the name column
 * @method array findByDbDispatchTime(string $dispatch_time) Return CeleryTasks objects filtered by the dispatch_time column
 * @method array findByDbStatus(string $status) Return CeleryTasks objects filtered by the status column
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCeleryTasksQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseCeleryTasksQuery object.
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
            $modelName = 'CeleryTasks';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new CeleryTasksQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   CeleryTasksQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return CeleryTasksQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof CeleryTasksQuery) {
            return $criteria;
        }
        $query = new CeleryTasksQuery(null, null, $modelAlias);

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
     * @return   CeleryTasks|CeleryTasks[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = CeleryTasksPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(CeleryTasksPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 CeleryTasks A model object, or null if the key is not found
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
     * @return                 CeleryTasks A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT "id", "task_id", "track_reference", "name", "dispatch_time", "status" FROM "celery_tasks" WHERE "id" = :p0';
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
            $obj = new CeleryTasks();
            $obj->hydrate($row);
            CeleryTasksPeer::addInstanceToPool($obj, (string) $key);
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
     * @return CeleryTasks|CeleryTasks[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|CeleryTasks[]|mixed the list of results, formatted by the current formatter
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
     * @return CeleryTasksQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(CeleryTasksPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return CeleryTasksQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(CeleryTasksPeer::ID, $keys, Criteria::IN);
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
     * @return CeleryTasksQuery The current query, for fluid interface
     */
    public function filterByDbId($dbId = null, $comparison = null)
    {
        if (is_array($dbId)) {
            $useMinMax = false;
            if (isset($dbId['min'])) {
                $this->addUsingAlias(CeleryTasksPeer::ID, $dbId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dbId['max'])) {
                $this->addUsingAlias(CeleryTasksPeer::ID, $dbId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CeleryTasksPeer::ID, $dbId, $comparison);
    }

    /**
     * Filter the query on the task_id column
     *
     * Example usage:
     * <code>
     * $query->filterByDbTaskId('fooValue');   // WHERE task_id = 'fooValue'
     * $query->filterByDbTaskId('%fooValue%'); // WHERE task_id LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbTaskId The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CeleryTasksQuery The current query, for fluid interface
     */
    public function filterByDbTaskId($dbTaskId = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($dbTaskId)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $dbTaskId)) {
                $dbTaskId = str_replace('*', '%', $dbTaskId);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CeleryTasksPeer::TASK_ID, $dbTaskId, $comparison);
    }

    /**
     * Filter the query on the track_reference column
     *
     * Example usage:
     * <code>
     * $query->filterByDbTrackReference(1234); // WHERE track_reference = 1234
     * $query->filterByDbTrackReference(array(12, 34)); // WHERE track_reference IN (12, 34)
     * $query->filterByDbTrackReference(array('min' => 12)); // WHERE track_reference >= 12
     * $query->filterByDbTrackReference(array('max' => 12)); // WHERE track_reference <= 12
     * </code>
     *
     * @see       filterByThirdPartyTrackReferences()
     *
     * @param     mixed $dbTrackReference The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CeleryTasksQuery The current query, for fluid interface
     */
    public function filterByDbTrackReference($dbTrackReference = null, $comparison = null)
    {
        if (is_array($dbTrackReference)) {
            $useMinMax = false;
            if (isset($dbTrackReference['min'])) {
                $this->addUsingAlias(CeleryTasksPeer::TRACK_REFERENCE, $dbTrackReference['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dbTrackReference['max'])) {
                $this->addUsingAlias(CeleryTasksPeer::TRACK_REFERENCE, $dbTrackReference['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CeleryTasksPeer::TRACK_REFERENCE, $dbTrackReference, $comparison);
    }

    /**
     * Filter the query on the name column
     *
     * Example usage:
     * <code>
     * $query->filterByDbName('fooValue');   // WHERE name = 'fooValue'
     * $query->filterByDbName('%fooValue%'); // WHERE name LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbName The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CeleryTasksQuery The current query, for fluid interface
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

        return $this->addUsingAlias(CeleryTasksPeer::NAME, $dbName, $comparison);
    }

    /**
     * Filter the query on the dispatch_time column
     *
     * Example usage:
     * <code>
     * $query->filterByDbDispatchTime('2011-03-14'); // WHERE dispatch_time = '2011-03-14'
     * $query->filterByDbDispatchTime('now'); // WHERE dispatch_time = '2011-03-14'
     * $query->filterByDbDispatchTime(array('max' => 'yesterday')); // WHERE dispatch_time < '2011-03-13'
     * </code>
     *
     * @param     mixed $dbDispatchTime The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CeleryTasksQuery The current query, for fluid interface
     */
    public function filterByDbDispatchTime($dbDispatchTime = null, $comparison = null)
    {
        if (is_array($dbDispatchTime)) {
            $useMinMax = false;
            if (isset($dbDispatchTime['min'])) {
                $this->addUsingAlias(CeleryTasksPeer::DISPATCH_TIME, $dbDispatchTime['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dbDispatchTime['max'])) {
                $this->addUsingAlias(CeleryTasksPeer::DISPATCH_TIME, $dbDispatchTime['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CeleryTasksPeer::DISPATCH_TIME, $dbDispatchTime, $comparison);
    }

    /**
     * Filter the query on the status column
     *
     * Example usage:
     * <code>
     * $query->filterByDbStatus('fooValue');   // WHERE status = 'fooValue'
     * $query->filterByDbStatus('%fooValue%'); // WHERE status LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbStatus The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CeleryTasksQuery The current query, for fluid interface
     */
    public function filterByDbStatus($dbStatus = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($dbStatus)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $dbStatus)) {
                $dbStatus = str_replace('*', '%', $dbStatus);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CeleryTasksPeer::STATUS, $dbStatus, $comparison);
    }

    /**
     * Filter the query by a related ThirdPartyTrackReferences object
     *
     * @param   ThirdPartyTrackReferences|PropelObjectCollection $thirdPartyTrackReferences The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 CeleryTasksQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByThirdPartyTrackReferences($thirdPartyTrackReferences, $comparison = null)
    {
        if ($thirdPartyTrackReferences instanceof ThirdPartyTrackReferences) {
            return $this
                ->addUsingAlias(CeleryTasksPeer::TRACK_REFERENCE, $thirdPartyTrackReferences->getDbId(), $comparison);
        } elseif ($thirdPartyTrackReferences instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(CeleryTasksPeer::TRACK_REFERENCE, $thirdPartyTrackReferences->toKeyValue('PrimaryKey', 'DbId'), $comparison);
        } else {
            throw new PropelException('filterByThirdPartyTrackReferences() only accepts arguments of type ThirdPartyTrackReferences or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the ThirdPartyTrackReferences relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return CeleryTasksQuery The current query, for fluid interface
     */
    public function joinThirdPartyTrackReferences($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('ThirdPartyTrackReferences');

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
            $this->addJoinObject($join, 'ThirdPartyTrackReferences');
        }

        return $this;
    }

    /**
     * Use the ThirdPartyTrackReferences relation ThirdPartyTrackReferences object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   ThirdPartyTrackReferencesQuery A secondary query class using the current class as primary query
     */
    public function useThirdPartyTrackReferencesQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinThirdPartyTrackReferences($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'ThirdPartyTrackReferences', 'ThirdPartyTrackReferencesQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   CeleryTasks $celeryTasks Object to remove from the list of results
     *
     * @return CeleryTasksQuery The current query, for fluid interface
     */
    public function prune($celeryTasks = null)
    {
        if ($celeryTasks) {
            $this->addUsingAlias(CeleryTasksPeer::ID, $celeryTasks->getDbId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}
