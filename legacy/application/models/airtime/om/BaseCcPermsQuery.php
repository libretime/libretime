<?php


/**
 * Base class that represents a query for the 'cc_perms' table.
 *
 *
 *
 * @method CcPermsQuery orderByPermid($order = Criteria::ASC) Order by the permid column
 * @method CcPermsQuery orderBySubj($order = Criteria::ASC) Order by the subj column
 * @method CcPermsQuery orderByAction($order = Criteria::ASC) Order by the action column
 * @method CcPermsQuery orderByObj($order = Criteria::ASC) Order by the obj column
 * @method CcPermsQuery orderByType($order = Criteria::ASC) Order by the type column
 *
 * @method CcPermsQuery groupByPermid() Group by the permid column
 * @method CcPermsQuery groupBySubj() Group by the subj column
 * @method CcPermsQuery groupByAction() Group by the action column
 * @method CcPermsQuery groupByObj() Group by the obj column
 * @method CcPermsQuery groupByType() Group by the type column
 *
 * @method CcPermsQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method CcPermsQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method CcPermsQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method CcPermsQuery leftJoinCcSubjs($relationAlias = null) Adds a LEFT JOIN clause to the query using the CcSubjs relation
 * @method CcPermsQuery rightJoinCcSubjs($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CcSubjs relation
 * @method CcPermsQuery innerJoinCcSubjs($relationAlias = null) Adds a INNER JOIN clause to the query using the CcSubjs relation
 *
 * @method CcPerms findOne(PropelPDO $con = null) Return the first CcPerms matching the query
 * @method CcPerms findOneOrCreate(PropelPDO $con = null) Return the first CcPerms matching the query, or a new CcPerms object populated from the query conditions when no match is found
 *
 * @method CcPerms findOneBySubj(int $subj) Return the first CcPerms filtered by the subj column
 * @method CcPerms findOneByAction(string $action) Return the first CcPerms filtered by the action column
 * @method CcPerms findOneByObj(int $obj) Return the first CcPerms filtered by the obj column
 * @method CcPerms findOneByType(string $type) Return the first CcPerms filtered by the type column
 *
 * @method array findByPermid(int $permid) Return CcPerms objects filtered by the permid column
 * @method array findBySubj(int $subj) Return CcPerms objects filtered by the subj column
 * @method array findByAction(string $action) Return CcPerms objects filtered by the action column
 * @method array findByObj(int $obj) Return CcPerms objects filtered by the obj column
 * @method array findByType(string $type) Return CcPerms objects filtered by the type column
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcPermsQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseCcPermsQuery object.
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
            $modelName = 'CcPerms';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new CcPermsQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   CcPermsQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return CcPermsQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof CcPermsQuery) {
            return $criteria;
        }
        $query = new CcPermsQuery(null, null, $modelAlias);

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
     * @return   CcPerms|CcPerms[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = CcPermsPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(CcPermsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 CcPerms A model object, or null if the key is not found
     * @throws PropelException
     */
     public function findOneByPermid($key, $con = null)
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
     * @return                 CcPerms A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT "permid", "subj", "action", "obj", "type" FROM "cc_perms" WHERE "permid" = :p0';
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
            $obj = new CcPerms();
            $obj->hydrate($row);
            CcPermsPeer::addInstanceToPool($obj, (string) $key);
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
     * @return CcPerms|CcPerms[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|CcPerms[]|mixed the list of results, formatted by the current formatter
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
     * @return CcPermsQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(CcPermsPeer::PERMID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return CcPermsQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(CcPermsPeer::PERMID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the permid column
     *
     * Example usage:
     * <code>
     * $query->filterByPermid(1234); // WHERE permid = 1234
     * $query->filterByPermid(array(12, 34)); // WHERE permid IN (12, 34)
     * $query->filterByPermid(array('min' => 12)); // WHERE permid >= 12
     * $query->filterByPermid(array('max' => 12)); // WHERE permid <= 12
     * </code>
     *
     * @param     mixed $permid The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcPermsQuery The current query, for fluid interface
     */
    public function filterByPermid($permid = null, $comparison = null)
    {
        if (is_array($permid)) {
            $useMinMax = false;
            if (isset($permid['min'])) {
                $this->addUsingAlias(CcPermsPeer::PERMID, $permid['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($permid['max'])) {
                $this->addUsingAlias(CcPermsPeer::PERMID, $permid['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CcPermsPeer::PERMID, $permid, $comparison);
    }

    /**
     * Filter the query on the subj column
     *
     * Example usage:
     * <code>
     * $query->filterBySubj(1234); // WHERE subj = 1234
     * $query->filterBySubj(array(12, 34)); // WHERE subj IN (12, 34)
     * $query->filterBySubj(array('min' => 12)); // WHERE subj >= 12
     * $query->filterBySubj(array('max' => 12)); // WHERE subj <= 12
     * </code>
     *
     * @see       filterByCcSubjs()
     *
     * @param     mixed $subj The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcPermsQuery The current query, for fluid interface
     */
    public function filterBySubj($subj = null, $comparison = null)
    {
        if (is_array($subj)) {
            $useMinMax = false;
            if (isset($subj['min'])) {
                $this->addUsingAlias(CcPermsPeer::SUBJ, $subj['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($subj['max'])) {
                $this->addUsingAlias(CcPermsPeer::SUBJ, $subj['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CcPermsPeer::SUBJ, $subj, $comparison);
    }

    /**
     * Filter the query on the action column
     *
     * Example usage:
     * <code>
     * $query->filterByAction('fooValue');   // WHERE action = 'fooValue'
     * $query->filterByAction('%fooValue%'); // WHERE action LIKE '%fooValue%'
     * </code>
     *
     * @param     string $action The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcPermsQuery The current query, for fluid interface
     */
    public function filterByAction($action = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($action)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $action)) {
                $action = str_replace('*', '%', $action);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CcPermsPeer::ACTION, $action, $comparison);
    }

    /**
     * Filter the query on the obj column
     *
     * Example usage:
     * <code>
     * $query->filterByObj(1234); // WHERE obj = 1234
     * $query->filterByObj(array(12, 34)); // WHERE obj IN (12, 34)
     * $query->filterByObj(array('min' => 12)); // WHERE obj >= 12
     * $query->filterByObj(array('max' => 12)); // WHERE obj <= 12
     * </code>
     *
     * @param     mixed $obj The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcPermsQuery The current query, for fluid interface
     */
    public function filterByObj($obj = null, $comparison = null)
    {
        if (is_array($obj)) {
            $useMinMax = false;
            if (isset($obj['min'])) {
                $this->addUsingAlias(CcPermsPeer::OBJ, $obj['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($obj['max'])) {
                $this->addUsingAlias(CcPermsPeer::OBJ, $obj['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CcPermsPeer::OBJ, $obj, $comparison);
    }

    /**
     * Filter the query on the type column
     *
     * Example usage:
     * <code>
     * $query->filterByType('fooValue');   // WHERE type = 'fooValue'
     * $query->filterByType('%fooValue%'); // WHERE type LIKE '%fooValue%'
     * </code>
     *
     * @param     string $type The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcPermsQuery The current query, for fluid interface
     */
    public function filterByType($type = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($type)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $type)) {
                $type = str_replace('*', '%', $type);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CcPermsPeer::TYPE, $type, $comparison);
    }

    /**
     * Filter the query by a related CcSubjs object
     *
     * @param   CcSubjs|PropelObjectCollection $ccSubjs The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 CcPermsQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCcSubjs($ccSubjs, $comparison = null)
    {
        if ($ccSubjs instanceof CcSubjs) {
            return $this
                ->addUsingAlias(CcPermsPeer::SUBJ, $ccSubjs->getDbId(), $comparison);
        } elseif ($ccSubjs instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(CcPermsPeer::SUBJ, $ccSubjs->toKeyValue('PrimaryKey', 'DbId'), $comparison);
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
     * @return CcPermsQuery The current query, for fluid interface
     */
    public function joinCcSubjs($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
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
    public function useCcSubjsQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinCcSubjs($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CcSubjs', 'CcSubjsQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   CcPerms $ccPerms Object to remove from the list of results
     *
     * @return CcPermsQuery The current query, for fluid interface
     */
    public function prune($ccPerms = null)
    {
        if ($ccPerms) {
            $this->addUsingAlias(CcPermsPeer::PERMID, $ccPerms->getPermid(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}
