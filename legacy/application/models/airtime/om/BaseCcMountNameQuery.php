<?php


/**
 * Base class that represents a query for the 'cc_mount_name' table.
 *
 *
 *
 * @method CcMountNameQuery orderByDbId($order = Criteria::ASC) Order by the id column
 * @method CcMountNameQuery orderByDbMountName($order = Criteria::ASC) Order by the mount_name column
 *
 * @method CcMountNameQuery groupByDbId() Group by the id column
 * @method CcMountNameQuery groupByDbMountName() Group by the mount_name column
 *
 * @method CcMountNameQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method CcMountNameQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method CcMountNameQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method CcMountNameQuery leftJoinCcListenerCount($relationAlias = null) Adds a LEFT JOIN clause to the query using the CcListenerCount relation
 * @method CcMountNameQuery rightJoinCcListenerCount($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CcListenerCount relation
 * @method CcMountNameQuery innerJoinCcListenerCount($relationAlias = null) Adds a INNER JOIN clause to the query using the CcListenerCount relation
 *
 * @method CcMountName findOne(PropelPDO $con = null) Return the first CcMountName matching the query
 * @method CcMountName findOneOrCreate(PropelPDO $con = null) Return the first CcMountName matching the query, or a new CcMountName object populated from the query conditions when no match is found
 *
 * @method CcMountName findOneByDbMountName(string $mount_name) Return the first CcMountName filtered by the mount_name column
 *
 * @method array findByDbId(int $id) Return CcMountName objects filtered by the id column
 * @method array findByDbMountName(string $mount_name) Return CcMountName objects filtered by the mount_name column
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcMountNameQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseCcMountNameQuery object.
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
            $modelName = 'CcMountName';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new CcMountNameQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   CcMountNameQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return CcMountNameQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof CcMountNameQuery) {
            return $criteria;
        }
        $query = new CcMountNameQuery(null, null, $modelAlias);

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
     * @return   CcMountName|CcMountName[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = CcMountNamePeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(CcMountNamePeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 CcMountName A model object, or null if the key is not found
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
     * @return                 CcMountName A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT "id", "mount_name" FROM "cc_mount_name" WHERE "id" = :p0';
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
            $obj = new CcMountName();
            $obj->hydrate($row);
            CcMountNamePeer::addInstanceToPool($obj, (string) $key);
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
     * @return CcMountName|CcMountName[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|CcMountName[]|mixed the list of results, formatted by the current formatter
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
     * @return CcMountNameQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(CcMountNamePeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return CcMountNameQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(CcMountNamePeer::ID, $keys, Criteria::IN);
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
     * @return CcMountNameQuery The current query, for fluid interface
     */
    public function filterByDbId($dbId = null, $comparison = null)
    {
        if (is_array($dbId)) {
            $useMinMax = false;
            if (isset($dbId['min'])) {
                $this->addUsingAlias(CcMountNamePeer::ID, $dbId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dbId['max'])) {
                $this->addUsingAlias(CcMountNamePeer::ID, $dbId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CcMountNamePeer::ID, $dbId, $comparison);
    }

    /**
     * Filter the query on the mount_name column
     *
     * Example usage:
     * <code>
     * $query->filterByDbMountName('fooValue');   // WHERE mount_name = 'fooValue'
     * $query->filterByDbMountName('%fooValue%'); // WHERE mount_name LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbMountName The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcMountNameQuery The current query, for fluid interface
     */
    public function filterByDbMountName($dbMountName = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($dbMountName)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $dbMountName)) {
                $dbMountName = str_replace('*', '%', $dbMountName);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CcMountNamePeer::MOUNT_NAME, $dbMountName, $comparison);
    }

    /**
     * Filter the query by a related CcListenerCount object
     *
     * @param   CcListenerCount|PropelObjectCollection $ccListenerCount  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 CcMountNameQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCcListenerCount($ccListenerCount, $comparison = null)
    {
        if ($ccListenerCount instanceof CcListenerCount) {
            return $this
                ->addUsingAlias(CcMountNamePeer::ID, $ccListenerCount->getDbMountNameId(), $comparison);
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
     * @return CcMountNameQuery The current query, for fluid interface
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
     * @param   CcMountName $ccMountName Object to remove from the list of results
     *
     * @return CcMountNameQuery The current query, for fluid interface
     */
    public function prune($ccMountName = null)
    {
        if ($ccMountName) {
            $this->addUsingAlias(CcMountNamePeer::ID, $ccMountName->getDbId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}
