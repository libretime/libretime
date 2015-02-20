<?php


/**
 * Base class that represents a query for the 'cc_sess' table.
 *
 *
 *
 * @method CcSessQuery orderBySessid($order = Criteria::ASC) Order by the sessid column
 * @method CcSessQuery orderByUserid($order = Criteria::ASC) Order by the userid column
 * @method CcSessQuery orderByLogin($order = Criteria::ASC) Order by the login column
 * @method CcSessQuery orderByTs($order = Criteria::ASC) Order by the ts column
 *
 * @method CcSessQuery groupBySessid() Group by the sessid column
 * @method CcSessQuery groupByUserid() Group by the userid column
 * @method CcSessQuery groupByLogin() Group by the login column
 * @method CcSessQuery groupByTs() Group by the ts column
 *
 * @method CcSessQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method CcSessQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method CcSessQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method CcSessQuery leftJoinCcSubjs($relationAlias = null) Adds a LEFT JOIN clause to the query using the CcSubjs relation
 * @method CcSessQuery rightJoinCcSubjs($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CcSubjs relation
 * @method CcSessQuery innerJoinCcSubjs($relationAlias = null) Adds a INNER JOIN clause to the query using the CcSubjs relation
 *
 * @method CcSess findOne(PropelPDO $con = null) Return the first CcSess matching the query
 * @method CcSess findOneOrCreate(PropelPDO $con = null) Return the first CcSess matching the query, or a new CcSess object populated from the query conditions when no match is found
 *
 * @method CcSess findOneByUserid(int $userid) Return the first CcSess filtered by the userid column
 * @method CcSess findOneByLogin(string $login) Return the first CcSess filtered by the login column
 * @method CcSess findOneByTs(string $ts) Return the first CcSess filtered by the ts column
 *
 * @method array findBySessid(string $sessid) Return CcSess objects filtered by the sessid column
 * @method array findByUserid(int $userid) Return CcSess objects filtered by the userid column
 * @method array findByLogin(string $login) Return CcSess objects filtered by the login column
 * @method array findByTs(string $ts) Return CcSess objects filtered by the ts column
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcSessQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseCcSessQuery object.
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
            $modelName = 'CcSess';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new CcSessQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   CcSessQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return CcSessQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof CcSessQuery) {
            return $criteria;
        }
        $query = new CcSessQuery(null, null, $modelAlias);

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
     * @return   CcSess|CcSess[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = CcSessPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(CcSessPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 CcSess A model object, or null if the key is not found
     * @throws PropelException
     */
     public function findOneBySessid($key, $con = null)
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
     * @return                 CcSess A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT "sessid", "userid", "login", "ts" FROM "cc_sess" WHERE "sessid" = :p0';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key, PDO::PARAM_STR);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $obj = new CcSess();
            $obj->hydrate($row);
            CcSessPeer::addInstanceToPool($obj, (string) $key);
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
     * @return CcSess|CcSess[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|CcSess[]|mixed the list of results, formatted by the current formatter
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
     * @return CcSessQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(CcSessPeer::SESSID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return CcSessQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(CcSessPeer::SESSID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the sessid column
     *
     * Example usage:
     * <code>
     * $query->filterBySessid('fooValue');   // WHERE sessid = 'fooValue'
     * $query->filterBySessid('%fooValue%'); // WHERE sessid LIKE '%fooValue%'
     * </code>
     *
     * @param     string $sessid The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcSessQuery The current query, for fluid interface
     */
    public function filterBySessid($sessid = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($sessid)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $sessid)) {
                $sessid = str_replace('*', '%', $sessid);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CcSessPeer::SESSID, $sessid, $comparison);
    }

    /**
     * Filter the query on the userid column
     *
     * Example usage:
     * <code>
     * $query->filterByUserid(1234); // WHERE userid = 1234
     * $query->filterByUserid(array(12, 34)); // WHERE userid IN (12, 34)
     * $query->filterByUserid(array('min' => 12)); // WHERE userid >= 12
     * $query->filterByUserid(array('max' => 12)); // WHERE userid <= 12
     * </code>
     *
     * @see       filterByCcSubjs()
     *
     * @param     mixed $userid The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcSessQuery The current query, for fluid interface
     */
    public function filterByUserid($userid = null, $comparison = null)
    {
        if (is_array($userid)) {
            $useMinMax = false;
            if (isset($userid['min'])) {
                $this->addUsingAlias(CcSessPeer::USERID, $userid['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($userid['max'])) {
                $this->addUsingAlias(CcSessPeer::USERID, $userid['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CcSessPeer::USERID, $userid, $comparison);
    }

    /**
     * Filter the query on the login column
     *
     * Example usage:
     * <code>
     * $query->filterByLogin('fooValue');   // WHERE login = 'fooValue'
     * $query->filterByLogin('%fooValue%'); // WHERE login LIKE '%fooValue%'
     * </code>
     *
     * @param     string $login The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcSessQuery The current query, for fluid interface
     */
    public function filterByLogin($login = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($login)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $login)) {
                $login = str_replace('*', '%', $login);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CcSessPeer::LOGIN, $login, $comparison);
    }

    /**
     * Filter the query on the ts column
     *
     * Example usage:
     * <code>
     * $query->filterByTs('2011-03-14'); // WHERE ts = '2011-03-14'
     * $query->filterByTs('now'); // WHERE ts = '2011-03-14'
     * $query->filterByTs(array('max' => 'yesterday')); // WHERE ts < '2011-03-13'
     * </code>
     *
     * @param     mixed $ts The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcSessQuery The current query, for fluid interface
     */
    public function filterByTs($ts = null, $comparison = null)
    {
        if (is_array($ts)) {
            $useMinMax = false;
            if (isset($ts['min'])) {
                $this->addUsingAlias(CcSessPeer::TS, $ts['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($ts['max'])) {
                $this->addUsingAlias(CcSessPeer::TS, $ts['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CcSessPeer::TS, $ts, $comparison);
    }

    /**
     * Filter the query by a related CcSubjs object
     *
     * @param   CcSubjs|PropelObjectCollection $ccSubjs The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 CcSessQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCcSubjs($ccSubjs, $comparison = null)
    {
        if ($ccSubjs instanceof CcSubjs) {
            return $this
                ->addUsingAlias(CcSessPeer::USERID, $ccSubjs->getDbId(), $comparison);
        } elseif ($ccSubjs instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(CcSessPeer::USERID, $ccSubjs->toKeyValue('PrimaryKey', 'DbId'), $comparison);
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
     * @return CcSessQuery The current query, for fluid interface
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
     * @param   CcSess $ccSess Object to remove from the list of results
     *
     * @return CcSessQuery The current query, for fluid interface
     */
    public function prune($ccSess = null)
    {
        if ($ccSess) {
            $this->addUsingAlias(CcSessPeer::SESSID, $ccSess->getSessid(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}
