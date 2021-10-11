<?php


/**
 * Base class that represents a query for the 'cc_show_hosts' table.
 *
 *
 *
 * @method CcShowHostsQuery orderByDbId($order = Criteria::ASC) Order by the id column
 * @method CcShowHostsQuery orderByDbShow($order = Criteria::ASC) Order by the show_id column
 * @method CcShowHostsQuery orderByDbHost($order = Criteria::ASC) Order by the subjs_id column
 *
 * @method CcShowHostsQuery groupByDbId() Group by the id column
 * @method CcShowHostsQuery groupByDbShow() Group by the show_id column
 * @method CcShowHostsQuery groupByDbHost() Group by the subjs_id column
 *
 * @method CcShowHostsQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method CcShowHostsQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method CcShowHostsQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method CcShowHostsQuery leftJoinCcShow($relationAlias = null) Adds a LEFT JOIN clause to the query using the CcShow relation
 * @method CcShowHostsQuery rightJoinCcShow($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CcShow relation
 * @method CcShowHostsQuery innerJoinCcShow($relationAlias = null) Adds a INNER JOIN clause to the query using the CcShow relation
 *
 * @method CcShowHostsQuery leftJoinCcSubjs($relationAlias = null) Adds a LEFT JOIN clause to the query using the CcSubjs relation
 * @method CcShowHostsQuery rightJoinCcSubjs($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CcSubjs relation
 * @method CcShowHostsQuery innerJoinCcSubjs($relationAlias = null) Adds a INNER JOIN clause to the query using the CcSubjs relation
 *
 * @method CcShowHosts findOne(PropelPDO $con = null) Return the first CcShowHosts matching the query
 * @method CcShowHosts findOneOrCreate(PropelPDO $con = null) Return the first CcShowHosts matching the query, or a new CcShowHosts object populated from the query conditions when no match is found
 *
 * @method CcShowHosts findOneByDbShow(int $show_id) Return the first CcShowHosts filtered by the show_id column
 * @method CcShowHosts findOneByDbHost(int $subjs_id) Return the first CcShowHosts filtered by the subjs_id column
 *
 * @method array findByDbId(int $id) Return CcShowHosts objects filtered by the id column
 * @method array findByDbShow(int $show_id) Return CcShowHosts objects filtered by the show_id column
 * @method array findByDbHost(int $subjs_id) Return CcShowHosts objects filtered by the subjs_id column
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcShowHostsQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseCcShowHostsQuery object.
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
            $modelName = 'CcShowHosts';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new CcShowHostsQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   CcShowHostsQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return CcShowHostsQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof CcShowHostsQuery) {
            return $criteria;
        }
        $query = new CcShowHostsQuery(null, null, $modelAlias);

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
     * @return   CcShowHosts|CcShowHosts[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = CcShowHostsPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(CcShowHostsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 CcShowHosts A model object, or null if the key is not found
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
     * @return                 CcShowHosts A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT "id", "show_id", "subjs_id" FROM "cc_show_hosts" WHERE "id" = :p0';
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
            $obj = new CcShowHosts();
            $obj->hydrate($row);
            CcShowHostsPeer::addInstanceToPool($obj, (string) $key);
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
     * @return CcShowHosts|CcShowHosts[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|CcShowHosts[]|mixed the list of results, formatted by the current formatter
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
     * @return CcShowHostsQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(CcShowHostsPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return CcShowHostsQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(CcShowHostsPeer::ID, $keys, Criteria::IN);
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
     * @return CcShowHostsQuery The current query, for fluid interface
     */
    public function filterByDbId($dbId = null, $comparison = null)
    {
        if (is_array($dbId)) {
            $useMinMax = false;
            if (isset($dbId['min'])) {
                $this->addUsingAlias(CcShowHostsPeer::ID, $dbId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dbId['max'])) {
                $this->addUsingAlias(CcShowHostsPeer::ID, $dbId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CcShowHostsPeer::ID, $dbId, $comparison);
    }

    /**
     * Filter the query on the show_id column
     *
     * Example usage:
     * <code>
     * $query->filterByDbShow(1234); // WHERE show_id = 1234
     * $query->filterByDbShow(array(12, 34)); // WHERE show_id IN (12, 34)
     * $query->filterByDbShow(array('min' => 12)); // WHERE show_id >= 12
     * $query->filterByDbShow(array('max' => 12)); // WHERE show_id <= 12
     * </code>
     *
     * @see       filterByCcShow()
     *
     * @param     mixed $dbShow The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcShowHostsQuery The current query, for fluid interface
     */
    public function filterByDbShow($dbShow = null, $comparison = null)
    {
        if (is_array($dbShow)) {
            $useMinMax = false;
            if (isset($dbShow['min'])) {
                $this->addUsingAlias(CcShowHostsPeer::SHOW_ID, $dbShow['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dbShow['max'])) {
                $this->addUsingAlias(CcShowHostsPeer::SHOW_ID, $dbShow['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CcShowHostsPeer::SHOW_ID, $dbShow, $comparison);
    }

    /**
     * Filter the query on the subjs_id column
     *
     * Example usage:
     * <code>
     * $query->filterByDbHost(1234); // WHERE subjs_id = 1234
     * $query->filterByDbHost(array(12, 34)); // WHERE subjs_id IN (12, 34)
     * $query->filterByDbHost(array('min' => 12)); // WHERE subjs_id >= 12
     * $query->filterByDbHost(array('max' => 12)); // WHERE subjs_id <= 12
     * </code>
     *
     * @see       filterByCcSubjs()
     *
     * @param     mixed $dbHost The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcShowHostsQuery The current query, for fluid interface
     */
    public function filterByDbHost($dbHost = null, $comparison = null)
    {
        if (is_array($dbHost)) {
            $useMinMax = false;
            if (isset($dbHost['min'])) {
                $this->addUsingAlias(CcShowHostsPeer::SUBJS_ID, $dbHost['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dbHost['max'])) {
                $this->addUsingAlias(CcShowHostsPeer::SUBJS_ID, $dbHost['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CcShowHostsPeer::SUBJS_ID, $dbHost, $comparison);
    }

    /**
     * Filter the query by a related CcShow object
     *
     * @param   CcShow|PropelObjectCollection $ccShow The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 CcShowHostsQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCcShow($ccShow, $comparison = null)
    {
        if ($ccShow instanceof CcShow) {
            return $this
                ->addUsingAlias(CcShowHostsPeer::SHOW_ID, $ccShow->getDbId(), $comparison);
        } elseif ($ccShow instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(CcShowHostsPeer::SHOW_ID, $ccShow->toKeyValue('PrimaryKey', 'DbId'), $comparison);
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
     * @return CcShowHostsQuery The current query, for fluid interface
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
     * Filter the query by a related CcSubjs object
     *
     * @param   CcSubjs|PropelObjectCollection $ccSubjs The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 CcShowHostsQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCcSubjs($ccSubjs, $comparison = null)
    {
        if ($ccSubjs instanceof CcSubjs) {
            return $this
                ->addUsingAlias(CcShowHostsPeer::SUBJS_ID, $ccSubjs->getDbId(), $comparison);
        } elseif ($ccSubjs instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(CcShowHostsPeer::SUBJS_ID, $ccSubjs->toKeyValue('PrimaryKey', 'DbId'), $comparison);
        } else {
            throw new PropelException('filterByCcSubjs() only accepts arguments of type CcSubjs or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CcSubjs relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return CcShowHostsQuery The current query, for fluid interface
     */
    public function joinCcSubjs($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CcSubjs');

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
            $this->addJoinObject($join, 'CcSubjs');
        }

        return $this;
    }

    /**
     * Use the CcSubjs relation CcSubjs object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   CcSubjsQuery A secondary query class using the current class as primary query
     */
    public function useCcSubjsQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinCcSubjs($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CcSubjs', 'CcSubjsQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   CcShowHosts $ccShowHosts Object to remove from the list of results
     *
     * @return CcShowHostsQuery The current query, for fluid interface
     */
    public function prune($ccShowHosts = null)
    {
        if ($ccShowHosts) {
            $this->addUsingAlias(CcShowHostsPeer::ID, $ccShowHosts->getDbId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}
