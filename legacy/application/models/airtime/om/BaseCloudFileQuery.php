<?php


/**
 * Base class that represents a query for the 'cloud_file' table.
 *
 *
 *
 * @method CloudFileQuery orderByDbId($order = Criteria::ASC) Order by the id column
 * @method CloudFileQuery orderByStorageBackend($order = Criteria::ASC) Order by the storage_backend column
 * @method CloudFileQuery orderByResourceId($order = Criteria::ASC) Order by the resource_id column
 * @method CloudFileQuery orderByCcFileId($order = Criteria::ASC) Order by the cc_file_id column
 *
 * @method CloudFileQuery groupByDbId() Group by the id column
 * @method CloudFileQuery groupByStorageBackend() Group by the storage_backend column
 * @method CloudFileQuery groupByResourceId() Group by the resource_id column
 * @method CloudFileQuery groupByCcFileId() Group by the cc_file_id column
 *
 * @method CloudFileQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method CloudFileQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method CloudFileQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method CloudFileQuery leftJoinCcFiles($relationAlias = null) Adds a LEFT JOIN clause to the query using the CcFiles relation
 * @method CloudFileQuery rightJoinCcFiles($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CcFiles relation
 * @method CloudFileQuery innerJoinCcFiles($relationAlias = null) Adds a INNER JOIN clause to the query using the CcFiles relation
 *
 * @method CloudFile findOne(PropelPDO $con = null) Return the first CloudFile matching the query
 * @method CloudFile findOneOrCreate(PropelPDO $con = null) Return the first CloudFile matching the query, or a new CloudFile object populated from the query conditions when no match is found
 *
 * @method CloudFile findOneByStorageBackend(string $storage_backend) Return the first CloudFile filtered by the storage_backend column
 * @method CloudFile findOneByResourceId(string $resource_id) Return the first CloudFile filtered by the resource_id column
 * @method CloudFile findOneByCcFileId(int $cc_file_id) Return the first CloudFile filtered by the cc_file_id column
 *
 * @method array findByDbId(int $id) Return CloudFile objects filtered by the id column
 * @method array findByStorageBackend(string $storage_backend) Return CloudFile objects filtered by the storage_backend column
 * @method array findByResourceId(string $resource_id) Return CloudFile objects filtered by the resource_id column
 * @method array findByCcFileId(int $cc_file_id) Return CloudFile objects filtered by the cc_file_id column
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCloudFileQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseCloudFileQuery object.
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
            $modelName = 'CloudFile';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new CloudFileQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   CloudFileQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return CloudFileQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof CloudFileQuery) {
            return $criteria;
        }
        $query = new CloudFileQuery(null, null, $modelAlias);

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
     * @return   CloudFile|CloudFile[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = CloudFilePeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(CloudFilePeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 CloudFile A model object, or null if the key is not found
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
     * @return                 CloudFile A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT "id", "storage_backend", "resource_id", "cc_file_id" FROM "cloud_file" WHERE "id" = :p0';
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
            $obj = new CloudFile();
            $obj->hydrate($row);
            CloudFilePeer::addInstanceToPool($obj, (string) $key);
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
     * @return CloudFile|CloudFile[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|CloudFile[]|mixed the list of results, formatted by the current formatter
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
     * @return CloudFileQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(CloudFilePeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return CloudFileQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(CloudFilePeer::ID, $keys, Criteria::IN);
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
     * @return CloudFileQuery The current query, for fluid interface
     */
    public function filterByDbId($dbId = null, $comparison = null)
    {
        if (is_array($dbId)) {
            $useMinMax = false;
            if (isset($dbId['min'])) {
                $this->addUsingAlias(CloudFilePeer::ID, $dbId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dbId['max'])) {
                $this->addUsingAlias(CloudFilePeer::ID, $dbId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CloudFilePeer::ID, $dbId, $comparison);
    }

    /**
     * Filter the query on the storage_backend column
     *
     * Example usage:
     * <code>
     * $query->filterByStorageBackend('fooValue');   // WHERE storage_backend = 'fooValue'
     * $query->filterByStorageBackend('%fooValue%'); // WHERE storage_backend LIKE '%fooValue%'
     * </code>
     *
     * @param     string $storageBackend The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CloudFileQuery The current query, for fluid interface
     */
    public function filterByStorageBackend($storageBackend = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($storageBackend)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $storageBackend)) {
                $storageBackend = str_replace('*', '%', $storageBackend);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CloudFilePeer::STORAGE_BACKEND, $storageBackend, $comparison);
    }

    /**
     * Filter the query on the resource_id column
     *
     * Example usage:
     * <code>
     * $query->filterByResourceId('fooValue');   // WHERE resource_id = 'fooValue'
     * $query->filterByResourceId('%fooValue%'); // WHERE resource_id LIKE '%fooValue%'
     * </code>
     *
     * @param     string $resourceId The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CloudFileQuery The current query, for fluid interface
     */
    public function filterByResourceId($resourceId = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($resourceId)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $resourceId)) {
                $resourceId = str_replace('*', '%', $resourceId);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CloudFilePeer::RESOURCE_ID, $resourceId, $comparison);
    }

    /**
     * Filter the query on the cc_file_id column
     *
     * Example usage:
     * <code>
     * $query->filterByCcFileId(1234); // WHERE cc_file_id = 1234
     * $query->filterByCcFileId(array(12, 34)); // WHERE cc_file_id IN (12, 34)
     * $query->filterByCcFileId(array('min' => 12)); // WHERE cc_file_id >= 12
     * $query->filterByCcFileId(array('max' => 12)); // WHERE cc_file_id <= 12
     * </code>
     *
     * @see       filterByCcFiles()
     *
     * @param     mixed $ccFileId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CloudFileQuery The current query, for fluid interface
     */
    public function filterByCcFileId($ccFileId = null, $comparison = null)
    {
        if (is_array($ccFileId)) {
            $useMinMax = false;
            if (isset($ccFileId['min'])) {
                $this->addUsingAlias(CloudFilePeer::CC_FILE_ID, $ccFileId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($ccFileId['max'])) {
                $this->addUsingAlias(CloudFilePeer::CC_FILE_ID, $ccFileId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CloudFilePeer::CC_FILE_ID, $ccFileId, $comparison);
    }

    /**
     * Filter the query by a related CcFiles object
     *
     * @param   CcFiles|PropelObjectCollection $ccFiles The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 CloudFileQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCcFiles($ccFiles, $comparison = null)
    {
        if ($ccFiles instanceof CcFiles) {
            return $this
                ->addUsingAlias(CloudFilePeer::CC_FILE_ID, $ccFiles->getDbId(), $comparison);
        } elseif ($ccFiles instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(CloudFilePeer::CC_FILE_ID, $ccFiles->toKeyValue('PrimaryKey', 'DbId'), $comparison);
        } else {
            throw new PropelException('filterByCcFiles() only accepts arguments of type CcFiles or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CcFiles relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return CloudFileQuery The current query, for fluid interface
     */
    public function joinCcFiles($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CcFiles');

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
            $this->addJoinObject($join, 'CcFiles');
        }

        return $this;
    }

    /**
     * Use the CcFiles relation CcFiles object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   CcFilesQuery A secondary query class using the current class as primary query
     */
    public function useCcFilesQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinCcFiles($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CcFiles', 'CcFilesQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   CloudFile $cloudFile Object to remove from the list of results
     *
     * @return CloudFileQuery The current query, for fluid interface
     */
    public function prune($cloudFile = null)
    {
        if ($cloudFile) {
            $this->addUsingAlias(CloudFilePeer::ID, $cloudFile->getDbId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}
