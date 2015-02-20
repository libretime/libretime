<?php


/**
 * Base class that represents a query for the 'cc_subjs_token' table.
 *
 *
 *
 * @method CcSubjsTokenQuery orderByDbId($order = Criteria::ASC) Order by the id column
 * @method CcSubjsTokenQuery orderByDbUserId($order = Criteria::ASC) Order by the user_id column
 * @method CcSubjsTokenQuery orderByDbAction($order = Criteria::ASC) Order by the action column
 * @method CcSubjsTokenQuery orderByDbToken($order = Criteria::ASC) Order by the token column
 * @method CcSubjsTokenQuery orderByDbCreated($order = Criteria::ASC) Order by the created column
 *
 * @method CcSubjsTokenQuery groupByDbId() Group by the id column
 * @method CcSubjsTokenQuery groupByDbUserId() Group by the user_id column
 * @method CcSubjsTokenQuery groupByDbAction() Group by the action column
 * @method CcSubjsTokenQuery groupByDbToken() Group by the token column
 * @method CcSubjsTokenQuery groupByDbCreated() Group by the created column
 *
 * @method CcSubjsTokenQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method CcSubjsTokenQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method CcSubjsTokenQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method CcSubjsTokenQuery leftJoinCcSubjs($relationAlias = null) Adds a LEFT JOIN clause to the query using the CcSubjs relation
 * @method CcSubjsTokenQuery rightJoinCcSubjs($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CcSubjs relation
 * @method CcSubjsTokenQuery innerJoinCcSubjs($relationAlias = null) Adds a INNER JOIN clause to the query using the CcSubjs relation
 *
 * @method CcSubjsToken findOne(PropelPDO $con = null) Return the first CcSubjsToken matching the query
 * @method CcSubjsToken findOneOrCreate(PropelPDO $con = null) Return the first CcSubjsToken matching the query, or a new CcSubjsToken object populated from the query conditions when no match is found
 *
 * @method CcSubjsToken findOneByDbUserId(int $user_id) Return the first CcSubjsToken filtered by the user_id column
 * @method CcSubjsToken findOneByDbAction(string $action) Return the first CcSubjsToken filtered by the action column
 * @method CcSubjsToken findOneByDbToken(string $token) Return the first CcSubjsToken filtered by the token column
 * @method CcSubjsToken findOneByDbCreated(string $created) Return the first CcSubjsToken filtered by the created column
 *
 * @method array findByDbId(int $id) Return CcSubjsToken objects filtered by the id column
 * @method array findByDbUserId(int $user_id) Return CcSubjsToken objects filtered by the user_id column
 * @method array findByDbAction(string $action) Return CcSubjsToken objects filtered by the action column
 * @method array findByDbToken(string $token) Return CcSubjsToken objects filtered by the token column
 * @method array findByDbCreated(string $created) Return CcSubjsToken objects filtered by the created column
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcSubjsTokenQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseCcSubjsTokenQuery object.
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
            $modelName = 'CcSubjsToken';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new CcSubjsTokenQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   CcSubjsTokenQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return CcSubjsTokenQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof CcSubjsTokenQuery) {
            return $criteria;
        }
        $query = new CcSubjsTokenQuery(null, null, $modelAlias);

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
     * @return   CcSubjsToken|CcSubjsToken[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = CcSubjsTokenPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(CcSubjsTokenPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 CcSubjsToken A model object, or null if the key is not found
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
     * @return                 CcSubjsToken A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT "id", "user_id", "action", "token", "created" FROM "cc_subjs_token" WHERE "id" = :p0';
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
            $obj = new CcSubjsToken();
            $obj->hydrate($row);
            CcSubjsTokenPeer::addInstanceToPool($obj, (string) $key);
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
     * @return CcSubjsToken|CcSubjsToken[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|CcSubjsToken[]|mixed the list of results, formatted by the current formatter
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
     * @return CcSubjsTokenQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(CcSubjsTokenPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return CcSubjsTokenQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(CcSubjsTokenPeer::ID, $keys, Criteria::IN);
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
     * @return CcSubjsTokenQuery The current query, for fluid interface
     */
    public function filterByDbId($dbId = null, $comparison = null)
    {
        if (is_array($dbId)) {
            $useMinMax = false;
            if (isset($dbId['min'])) {
                $this->addUsingAlias(CcSubjsTokenPeer::ID, $dbId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dbId['max'])) {
                $this->addUsingAlias(CcSubjsTokenPeer::ID, $dbId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CcSubjsTokenPeer::ID, $dbId, $comparison);
    }

    /**
     * Filter the query on the user_id column
     *
     * Example usage:
     * <code>
     * $query->filterByDbUserId(1234); // WHERE user_id = 1234
     * $query->filterByDbUserId(array(12, 34)); // WHERE user_id IN (12, 34)
     * $query->filterByDbUserId(array('min' => 12)); // WHERE user_id >= 12
     * $query->filterByDbUserId(array('max' => 12)); // WHERE user_id <= 12
     * </code>
     *
     * @see       filterByCcSubjs()
     *
     * @param     mixed $dbUserId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcSubjsTokenQuery The current query, for fluid interface
     */
    public function filterByDbUserId($dbUserId = null, $comparison = null)
    {
        if (is_array($dbUserId)) {
            $useMinMax = false;
            if (isset($dbUserId['min'])) {
                $this->addUsingAlias(CcSubjsTokenPeer::USER_ID, $dbUserId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dbUserId['max'])) {
                $this->addUsingAlias(CcSubjsTokenPeer::USER_ID, $dbUserId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CcSubjsTokenPeer::USER_ID, $dbUserId, $comparison);
    }

    /**
     * Filter the query on the action column
     *
     * Example usage:
     * <code>
     * $query->filterByDbAction('fooValue');   // WHERE action = 'fooValue'
     * $query->filterByDbAction('%fooValue%'); // WHERE action LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbAction The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcSubjsTokenQuery The current query, for fluid interface
     */
    public function filterByDbAction($dbAction = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($dbAction)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $dbAction)) {
                $dbAction = str_replace('*', '%', $dbAction);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CcSubjsTokenPeer::ACTION, $dbAction, $comparison);
    }

    /**
     * Filter the query on the token column
     *
     * Example usage:
     * <code>
     * $query->filterByDbToken('fooValue');   // WHERE token = 'fooValue'
     * $query->filterByDbToken('%fooValue%'); // WHERE token LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbToken The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcSubjsTokenQuery The current query, for fluid interface
     */
    public function filterByDbToken($dbToken = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($dbToken)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $dbToken)) {
                $dbToken = str_replace('*', '%', $dbToken);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CcSubjsTokenPeer::TOKEN, $dbToken, $comparison);
    }

    /**
     * Filter the query on the created column
     *
     * Example usage:
     * <code>
     * $query->filterByDbCreated('2011-03-14'); // WHERE created = '2011-03-14'
     * $query->filterByDbCreated('now'); // WHERE created = '2011-03-14'
     * $query->filterByDbCreated(array('max' => 'yesterday')); // WHERE created < '2011-03-13'
     * </code>
     *
     * @param     mixed $dbCreated The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcSubjsTokenQuery The current query, for fluid interface
     */
    public function filterByDbCreated($dbCreated = null, $comparison = null)
    {
        if (is_array($dbCreated)) {
            $useMinMax = false;
            if (isset($dbCreated['min'])) {
                $this->addUsingAlias(CcSubjsTokenPeer::CREATED, $dbCreated['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dbCreated['max'])) {
                $this->addUsingAlias(CcSubjsTokenPeer::CREATED, $dbCreated['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CcSubjsTokenPeer::CREATED, $dbCreated, $comparison);
    }

    /**
     * Filter the query by a related CcSubjs object
     *
     * @param   CcSubjs|PropelObjectCollection $ccSubjs The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 CcSubjsTokenQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCcSubjs($ccSubjs, $comparison = null)
    {
        if ($ccSubjs instanceof CcSubjs) {
            return $this
                ->addUsingAlias(CcSubjsTokenPeer::USER_ID, $ccSubjs->getDbId(), $comparison);
        } elseif ($ccSubjs instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(CcSubjsTokenPeer::USER_ID, $ccSubjs->toKeyValue('PrimaryKey', 'DbId'), $comparison);
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
     * @return CcSubjsTokenQuery The current query, for fluid interface
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
     * @param   CcSubjsToken $ccSubjsToken Object to remove from the list of results
     *
     * @return CcSubjsTokenQuery The current query, for fluid interface
     */
    public function prune($ccSubjsToken = null)
    {
        if ($ccSubjsToken) {
            $this->addUsingAlias(CcSubjsTokenPeer::ID, $ccSubjsToken->getDbId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}
