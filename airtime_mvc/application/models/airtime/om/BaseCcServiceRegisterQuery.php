<?php


/**
 * Base class that represents a query for the 'cc_service_register' table.
 *
 *
 *
 * @method CcServiceRegisterQuery orderByDbName($order = Criteria::ASC) Order by the name column
 * @method CcServiceRegisterQuery orderByDbIp($order = Criteria::ASC) Order by the ip column
 *
 * @method CcServiceRegisterQuery groupByDbName() Group by the name column
 * @method CcServiceRegisterQuery groupByDbIp() Group by the ip column
 *
 * @method CcServiceRegisterQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method CcServiceRegisterQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method CcServiceRegisterQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method CcServiceRegister findOne(PropelPDO $con = null) Return the first CcServiceRegister matching the query
 * @method CcServiceRegister findOneOrCreate(PropelPDO $con = null) Return the first CcServiceRegister matching the query, or a new CcServiceRegister object populated from the query conditions when no match is found
 *
 * @method CcServiceRegister findOneByDbIp(string $ip) Return the first CcServiceRegister filtered by the ip column
 *
 * @method array findByDbName(string $name) Return CcServiceRegister objects filtered by the name column
 * @method array findByDbIp(string $ip) Return CcServiceRegister objects filtered by the ip column
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcServiceRegisterQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseCcServiceRegisterQuery object.
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
            $modelName = 'CcServiceRegister';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new CcServiceRegisterQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   CcServiceRegisterQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return CcServiceRegisterQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof CcServiceRegisterQuery) {
            return $criteria;
        }
        $query = new CcServiceRegisterQuery(null, null, $modelAlias);

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
     * @return   CcServiceRegister|CcServiceRegister[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = CcServiceRegisterPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(CcServiceRegisterPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 CcServiceRegister A model object, or null if the key is not found
     * @throws PropelException
     */
     public function findOneByDbName($key, $con = null)
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
     * @return                 CcServiceRegister A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT "name", "ip" FROM "cc_service_register" WHERE "name" = :p0';
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
            $obj = new CcServiceRegister();
            $obj->hydrate($row);
            CcServiceRegisterPeer::addInstanceToPool($obj, (string) $key);
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
     * @return CcServiceRegister|CcServiceRegister[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|CcServiceRegister[]|mixed the list of results, formatted by the current formatter
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
     * @return CcServiceRegisterQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(CcServiceRegisterPeer::NAME, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return CcServiceRegisterQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(CcServiceRegisterPeer::NAME, $keys, Criteria::IN);
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
     * @return CcServiceRegisterQuery The current query, for fluid interface
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

        return $this->addUsingAlias(CcServiceRegisterPeer::NAME, $dbName, $comparison);
    }

    /**
     * Filter the query on the ip column
     *
     * Example usage:
     * <code>
     * $query->filterByDbIp('fooValue');   // WHERE ip = 'fooValue'
     * $query->filterByDbIp('%fooValue%'); // WHERE ip LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbIp The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcServiceRegisterQuery The current query, for fluid interface
     */
    public function filterByDbIp($dbIp = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($dbIp)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $dbIp)) {
                $dbIp = str_replace('*', '%', $dbIp);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CcServiceRegisterPeer::IP, $dbIp, $comparison);
    }

    /**
     * Exclude object from result
     *
     * @param   CcServiceRegister $ccServiceRegister Object to remove from the list of results
     *
     * @return CcServiceRegisterQuery The current query, for fluid interface
     */
    public function prune($ccServiceRegister = null)
    {
        if ($ccServiceRegister) {
            $this->addUsingAlias(CcServiceRegisterPeer::NAME, $ccServiceRegister->getDbName(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}
