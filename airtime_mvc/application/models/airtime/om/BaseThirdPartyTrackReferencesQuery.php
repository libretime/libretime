<?php


/**
 * Base class that represents a query for the 'third_party_track_references' table.
 *
 *
 *
 * @method ThirdPartyTrackReferencesQuery orderByDbId($order = Criteria::ASC) Order by the id column
 * @method ThirdPartyTrackReferencesQuery orderByDbService($order = Criteria::ASC) Order by the service column
 * @method ThirdPartyTrackReferencesQuery orderByDbForeignId($order = Criteria::ASC) Order by the foreign_id column
 * @method ThirdPartyTrackReferencesQuery orderByDbBrokerTaskId($order = Criteria::ASC) Order by the broker_task_id column
 * @method ThirdPartyTrackReferencesQuery orderByDbBrokerTaskName($order = Criteria::ASC) Order by the broker_task_name column
 * @method ThirdPartyTrackReferencesQuery orderByDbBrokerTaskDispatchTime($order = Criteria::ASC) Order by the broker_task_dispatch_time column
 * @method ThirdPartyTrackReferencesQuery orderByDbFileId($order = Criteria::ASC) Order by the file_id column
 * @method ThirdPartyTrackReferencesQuery orderByDbStatus($order = Criteria::ASC) Order by the status column
 *
 * @method ThirdPartyTrackReferencesQuery groupByDbId() Group by the id column
 * @method ThirdPartyTrackReferencesQuery groupByDbService() Group by the service column
 * @method ThirdPartyTrackReferencesQuery groupByDbForeignId() Group by the foreign_id column
 * @method ThirdPartyTrackReferencesQuery groupByDbBrokerTaskId() Group by the broker_task_id column
 * @method ThirdPartyTrackReferencesQuery groupByDbBrokerTaskName() Group by the broker_task_name column
 * @method ThirdPartyTrackReferencesQuery groupByDbBrokerTaskDispatchTime() Group by the broker_task_dispatch_time column
 * @method ThirdPartyTrackReferencesQuery groupByDbFileId() Group by the file_id column
 * @method ThirdPartyTrackReferencesQuery groupByDbStatus() Group by the status column
 *
 * @method ThirdPartyTrackReferencesQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method ThirdPartyTrackReferencesQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method ThirdPartyTrackReferencesQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method ThirdPartyTrackReferencesQuery leftJoinCcPlayoutHistoryTemplate($relationAlias = null) Adds a LEFT JOIN clause to the query using the CcPlayoutHistoryTemplate relation
 * @method ThirdPartyTrackReferencesQuery rightJoinCcPlayoutHistoryTemplate($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CcPlayoutHistoryTemplate relation
 * @method ThirdPartyTrackReferencesQuery innerJoinCcPlayoutHistoryTemplate($relationAlias = null) Adds a INNER JOIN clause to the query using the CcPlayoutHistoryTemplate relation
 *
 * @method ThirdPartyTrackReferences findOne(PropelPDO $con = null) Return the first ThirdPartyTrackReferences matching the query
 * @method ThirdPartyTrackReferences findOneOrCreate(PropelPDO $con = null) Return the first ThirdPartyTrackReferences matching the query, or a new ThirdPartyTrackReferences object populated from the query conditions when no match is found
 *
 * @method ThirdPartyTrackReferences findOneByDbService(string $service) Return the first ThirdPartyTrackReferences filtered by the service column
 * @method ThirdPartyTrackReferences findOneByDbForeignId(string $foreign_id) Return the first ThirdPartyTrackReferences filtered by the foreign_id column
 * @method ThirdPartyTrackReferences findOneByDbBrokerTaskId(string $broker_task_id) Return the first ThirdPartyTrackReferences filtered by the broker_task_id column
 * @method ThirdPartyTrackReferences findOneByDbBrokerTaskName(string $broker_task_name) Return the first ThirdPartyTrackReferences filtered by the broker_task_name column
 * @method ThirdPartyTrackReferences findOneByDbBrokerTaskDispatchTime(string $broker_task_dispatch_time) Return the first ThirdPartyTrackReferences filtered by the broker_task_dispatch_time column
 * @method ThirdPartyTrackReferences findOneByDbFileId(int $file_id) Return the first ThirdPartyTrackReferences filtered by the file_id column
 * @method ThirdPartyTrackReferences findOneByDbStatus(string $status) Return the first ThirdPartyTrackReferences filtered by the status column
 *
 * @method array findByDbId(int $id) Return ThirdPartyTrackReferences objects filtered by the id column
 * @method array findByDbService(string $service) Return ThirdPartyTrackReferences objects filtered by the service column
 * @method array findByDbForeignId(string $foreign_id) Return ThirdPartyTrackReferences objects filtered by the foreign_id column
 * @method array findByDbBrokerTaskId(string $broker_task_id) Return ThirdPartyTrackReferences objects filtered by the broker_task_id column
 * @method array findByDbBrokerTaskName(string $broker_task_name) Return ThirdPartyTrackReferences objects filtered by the broker_task_name column
 * @method array findByDbBrokerTaskDispatchTime(string $broker_task_dispatch_time) Return ThirdPartyTrackReferences objects filtered by the broker_task_dispatch_time column
 * @method array findByDbFileId(int $file_id) Return ThirdPartyTrackReferences objects filtered by the file_id column
 * @method array findByDbStatus(string $status) Return ThirdPartyTrackReferences objects filtered by the status column
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseThirdPartyTrackReferencesQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseThirdPartyTrackReferencesQuery object.
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
            $modelName = 'ThirdPartyTrackReferences';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ThirdPartyTrackReferencesQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   ThirdPartyTrackReferencesQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return ThirdPartyTrackReferencesQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof ThirdPartyTrackReferencesQuery) {
            return $criteria;
        }
        $query = new ThirdPartyTrackReferencesQuery(null, null, $modelAlias);

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
     * @return   ThirdPartyTrackReferences|ThirdPartyTrackReferences[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = ThirdPartyTrackReferencesPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(ThirdPartyTrackReferencesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 ThirdPartyTrackReferences A model object, or null if the key is not found
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
     * @return                 ThirdPartyTrackReferences A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT "id", "service", "foreign_id", "broker_task_id", "broker_task_name", "broker_task_dispatch_time", "file_id", "status" FROM "third_party_track_references" WHERE "id" = :p0';
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
            $obj = new ThirdPartyTrackReferences();
            $obj->hydrate($row);
            ThirdPartyTrackReferencesPeer::addInstanceToPool($obj, (string) $key);
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
     * @return ThirdPartyTrackReferences|ThirdPartyTrackReferences[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|ThirdPartyTrackReferences[]|mixed the list of results, formatted by the current formatter
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
     * @return ThirdPartyTrackReferencesQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(ThirdPartyTrackReferencesPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ThirdPartyTrackReferencesQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(ThirdPartyTrackReferencesPeer::ID, $keys, Criteria::IN);
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
     * @return ThirdPartyTrackReferencesQuery The current query, for fluid interface
     */
    public function filterByDbId($dbId = null, $comparison = null)
    {
        if (is_array($dbId)) {
            $useMinMax = false;
            if (isset($dbId['min'])) {
                $this->addUsingAlias(ThirdPartyTrackReferencesPeer::ID, $dbId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dbId['max'])) {
                $this->addUsingAlias(ThirdPartyTrackReferencesPeer::ID, $dbId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ThirdPartyTrackReferencesPeer::ID, $dbId, $comparison);
    }

    /**
     * Filter the query on the service column
     *
     * Example usage:
     * <code>
     * $query->filterByDbService('fooValue');   // WHERE service = 'fooValue'
     * $query->filterByDbService('%fooValue%'); // WHERE service LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbService The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ThirdPartyTrackReferencesQuery The current query, for fluid interface
     */
    public function filterByDbService($dbService = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($dbService)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $dbService)) {
                $dbService = str_replace('*', '%', $dbService);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ThirdPartyTrackReferencesPeer::SERVICE, $dbService, $comparison);
    }

    /**
     * Filter the query on the foreign_id column
     *
     * Example usage:
     * <code>
     * $query->filterByDbForeignId('fooValue');   // WHERE foreign_id = 'fooValue'
     * $query->filterByDbForeignId('%fooValue%'); // WHERE foreign_id LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbForeignId The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ThirdPartyTrackReferencesQuery The current query, for fluid interface
     */
    public function filterByDbForeignId($dbForeignId = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($dbForeignId)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $dbForeignId)) {
                $dbForeignId = str_replace('*', '%', $dbForeignId);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ThirdPartyTrackReferencesPeer::FOREIGN_ID, $dbForeignId, $comparison);
    }

    /**
     * Filter the query on the broker_task_id column
     *
     * Example usage:
     * <code>
     * $query->filterByDbBrokerTaskId('fooValue');   // WHERE broker_task_id = 'fooValue'
     * $query->filterByDbBrokerTaskId('%fooValue%'); // WHERE broker_task_id LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbBrokerTaskId The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ThirdPartyTrackReferencesQuery The current query, for fluid interface
     */
    public function filterByDbBrokerTaskId($dbBrokerTaskId = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($dbBrokerTaskId)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $dbBrokerTaskId)) {
                $dbBrokerTaskId = str_replace('*', '%', $dbBrokerTaskId);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ThirdPartyTrackReferencesPeer::BROKER_TASK_ID, $dbBrokerTaskId, $comparison);
    }

    /**
     * Filter the query on the broker_task_name column
     *
     * Example usage:
     * <code>
     * $query->filterByDbBrokerTaskName('fooValue');   // WHERE broker_task_name = 'fooValue'
     * $query->filterByDbBrokerTaskName('%fooValue%'); // WHERE broker_task_name LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbBrokerTaskName The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ThirdPartyTrackReferencesQuery The current query, for fluid interface
     */
    public function filterByDbBrokerTaskName($dbBrokerTaskName = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($dbBrokerTaskName)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $dbBrokerTaskName)) {
                $dbBrokerTaskName = str_replace('*', '%', $dbBrokerTaskName);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ThirdPartyTrackReferencesPeer::BROKER_TASK_NAME, $dbBrokerTaskName, $comparison);
    }

    /**
     * Filter the query on the broker_task_dispatch_time column
     *
     * Example usage:
     * <code>
     * $query->filterByDbBrokerTaskDispatchTime('2011-03-14'); // WHERE broker_task_dispatch_time = '2011-03-14'
     * $query->filterByDbBrokerTaskDispatchTime('now'); // WHERE broker_task_dispatch_time = '2011-03-14'
     * $query->filterByDbBrokerTaskDispatchTime(array('max' => 'yesterday')); // WHERE broker_task_dispatch_time < '2011-03-13'
     * </code>
     *
     * @param     mixed $dbBrokerTaskDispatchTime The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ThirdPartyTrackReferencesQuery The current query, for fluid interface
     */
    public function filterByDbBrokerTaskDispatchTime($dbBrokerTaskDispatchTime = null, $comparison = null)
    {
        if (is_array($dbBrokerTaskDispatchTime)) {
            $useMinMax = false;
            if (isset($dbBrokerTaskDispatchTime['min'])) {
                $this->addUsingAlias(ThirdPartyTrackReferencesPeer::BROKER_TASK_DISPATCH_TIME, $dbBrokerTaskDispatchTime['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dbBrokerTaskDispatchTime['max'])) {
                $this->addUsingAlias(ThirdPartyTrackReferencesPeer::BROKER_TASK_DISPATCH_TIME, $dbBrokerTaskDispatchTime['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ThirdPartyTrackReferencesPeer::BROKER_TASK_DISPATCH_TIME, $dbBrokerTaskDispatchTime, $comparison);
    }

    /**
     * Filter the query on the file_id column
     *
     * Example usage:
     * <code>
     * $query->filterByDbFileId(1234); // WHERE file_id = 1234
     * $query->filterByDbFileId(array(12, 34)); // WHERE file_id IN (12, 34)
     * $query->filterByDbFileId(array('min' => 12)); // WHERE file_id >= 12
     * $query->filterByDbFileId(array('max' => 12)); // WHERE file_id <= 12
     * </code>
     *
     * @see       filterByCcPlayoutHistoryTemplate()
     *
     * @param     mixed $dbFileId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ThirdPartyTrackReferencesQuery The current query, for fluid interface
     */
    public function filterByDbFileId($dbFileId = null, $comparison = null)
    {
        if (is_array($dbFileId)) {
            $useMinMax = false;
            if (isset($dbFileId['min'])) {
                $this->addUsingAlias(ThirdPartyTrackReferencesPeer::FILE_ID, $dbFileId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dbFileId['max'])) {
                $this->addUsingAlias(ThirdPartyTrackReferencesPeer::FILE_ID, $dbFileId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ThirdPartyTrackReferencesPeer::FILE_ID, $dbFileId, $comparison);
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
     * @return ThirdPartyTrackReferencesQuery The current query, for fluid interface
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

        return $this->addUsingAlias(ThirdPartyTrackReferencesPeer::STATUS, $dbStatus, $comparison);
    }

    /**
     * Filter the query by a related CcPlayoutHistoryTemplate object
     *
     * @param   CcPlayoutHistoryTemplate|PropelObjectCollection $ccPlayoutHistoryTemplate The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 ThirdPartyTrackReferencesQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCcPlayoutHistoryTemplate($ccPlayoutHistoryTemplate, $comparison = null)
    {
        if ($ccPlayoutHistoryTemplate instanceof CcPlayoutHistoryTemplate) {
            return $this
                ->addUsingAlias(ThirdPartyTrackReferencesPeer::FILE_ID, $ccPlayoutHistoryTemplate->getDbId(), $comparison);
        } elseif ($ccPlayoutHistoryTemplate instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(ThirdPartyTrackReferencesPeer::FILE_ID, $ccPlayoutHistoryTemplate->toKeyValue('PrimaryKey', 'DbId'), $comparison);
        } else {
            throw new PropelException('filterByCcPlayoutHistoryTemplate() only accepts arguments of type CcPlayoutHistoryTemplate or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CcPlayoutHistoryTemplate relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ThirdPartyTrackReferencesQuery The current query, for fluid interface
     */
    public function joinCcPlayoutHistoryTemplate($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CcPlayoutHistoryTemplate');

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
            $this->addJoinObject($join, 'CcPlayoutHistoryTemplate');
        }

        return $this;
    }

    /**
     * Use the CcPlayoutHistoryTemplate relation CcPlayoutHistoryTemplate object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   CcPlayoutHistoryTemplateQuery A secondary query class using the current class as primary query
     */
    public function useCcPlayoutHistoryTemplateQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinCcPlayoutHistoryTemplate($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CcPlayoutHistoryTemplate', 'CcPlayoutHistoryTemplateQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ThirdPartyTrackReferences $thirdPartyTrackReferences Object to remove from the list of results
     *
     * @return ThirdPartyTrackReferencesQuery The current query, for fluid interface
     */
    public function prune($thirdPartyTrackReferences = null)
    {
        if ($thirdPartyTrackReferences) {
            $this->addUsingAlias(ThirdPartyTrackReferencesPeer::ID, $thirdPartyTrackReferences->getDbId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}
