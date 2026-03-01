<?php


/**
 * Base class that represents a query for the 'cc_login_attempts' table.
 *
 *
 *
 * @method CcLoginAttemptsQuery orderByDbIP($order = Criteria::ASC) Order by the ip column
 * @method CcLoginAttemptsQuery orderByDbAttempts($order = Criteria::ASC) Order by the attempts column
 *
 * @method CcLoginAttemptsQuery groupByDbIP() Group by the ip column
 * @method CcLoginAttemptsQuery groupByDbAttempts() Group by the attempts column
 *
 * @method CcLoginAttemptsQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method CcLoginAttemptsQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method CcLoginAttemptsQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method CcLoginAttempts findOne(PropelPDO $con = null) Return the first CcLoginAttempts matching the query
 * @method CcLoginAttempts findOneOrCreate(PropelPDO $con = null) Return the first CcLoginAttempts matching the query, or a new CcLoginAttempts object populated from the query conditions when no match is found
 *
 * @method CcLoginAttempts findOneByDbAttempts(int $attempts) Return the first CcLoginAttempts filtered by the attempts column
 *
 * @method array findByDbIP(string $ip) Return CcLoginAttempts objects filtered by the ip column
 * @method array findByDbAttempts(int $attempts) Return CcLoginAttempts objects filtered by the attempts column
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcLoginAttemptsQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseCcLoginAttemptsQuery object.
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
            $modelName = 'CcLoginAttempts';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new CcLoginAttemptsQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   CcLoginAttemptsQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return CcLoginAttemptsQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof CcLoginAttemptsQuery) {
            return $criteria;
        }
        $query = new CcLoginAttemptsQuery(null, null, $modelAlias);

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
     * @return   CcLoginAttempts|CcLoginAttempts[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = CcLoginAttemptsPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(CcLoginAttemptsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 CcLoginAttempts A model object, or null if the key is not found
     * @throws PropelException
     */
     public function findOneByDbIP($key, $con = null)
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
     * @return                 CcLoginAttempts A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT "ip", "attempts" FROM "cc_login_attempts" WHERE "ip" = :p0';
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
            $obj = new CcLoginAttempts();
            $obj->hydrate($row);
            CcLoginAttemptsPeer::addInstanceToPool($obj, (string) $key);
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
     * @return CcLoginAttempts|CcLoginAttempts[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|CcLoginAttempts[]|mixed the list of results, formatted by the current formatter
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
     * @return CcLoginAttemptsQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(CcLoginAttemptsPeer::IP, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return CcLoginAttemptsQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(CcLoginAttemptsPeer::IP, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the ip column
     *
     * Example usage:
     * <code>
     * $query->filterByDbIP('fooValue');   // WHERE ip = 'fooValue'
     * $query->filterByDbIP('%fooValue%'); // WHERE ip LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbIP The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcLoginAttemptsQuery The current query, for fluid interface
     */
    public function filterByDbIP($dbIP = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($dbIP)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $dbIP)) {
                $dbIP = str_replace('*', '%', $dbIP);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CcLoginAttemptsPeer::IP, $dbIP, $comparison);
    }

    /**
     * Filter the query on the attempts column
     *
     * Example usage:
     * <code>
     * $query->filterByDbAttempts(1234); // WHERE attempts = 1234
     * $query->filterByDbAttempts(array(12, 34)); // WHERE attempts IN (12, 34)
     * $query->filterByDbAttempts(array('min' => 12)); // WHERE attempts >= 12
     * $query->filterByDbAttempts(array('max' => 12)); // WHERE attempts <= 12
     * </code>
     *
     * @param     mixed $dbAttempts The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcLoginAttemptsQuery The current query, for fluid interface
     */
    public function filterByDbAttempts($dbAttempts = null, $comparison = null)
    {
        if (is_array($dbAttempts)) {
            $useMinMax = false;
            if (isset($dbAttempts['min'])) {
                $this->addUsingAlias(CcLoginAttemptsPeer::ATTEMPTS, $dbAttempts['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dbAttempts['max'])) {
                $this->addUsingAlias(CcLoginAttemptsPeer::ATTEMPTS, $dbAttempts['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CcLoginAttemptsPeer::ATTEMPTS, $dbAttempts, $comparison);
    }

    /**
     * Exclude object from result
     *
     * @param   CcLoginAttempts $ccLoginAttempts Object to remove from the list of results
     *
     * @return CcLoginAttemptsQuery The current query, for fluid interface
     */
    public function prune($ccLoginAttempts = null)
    {
        if ($ccLoginAttempts) {
            $this->addUsingAlias(CcLoginAttemptsPeer::IP, $ccLoginAttempts->getDbIP(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}
