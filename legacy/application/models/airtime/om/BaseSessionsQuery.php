<?php


/**
 * Base class that represents a query for the 'sessions' table.
 *
 *
 *
 * @method SessionsQuery orderByDbId($order = Criteria::ASC) Order by the id column
 * @method SessionsQuery orderByDbModified($order = Criteria::ASC) Order by the modified column
 * @method SessionsQuery orderByDbLifetime($order = Criteria::ASC) Order by the lifetime column
 * @method SessionsQuery orderByDbData($order = Criteria::ASC) Order by the data column
 *
 * @method SessionsQuery groupByDbId() Group by the id column
 * @method SessionsQuery groupByDbModified() Group by the modified column
 * @method SessionsQuery groupByDbLifetime() Group by the lifetime column
 * @method SessionsQuery groupByDbData() Group by the data column
 *
 * @method SessionsQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method SessionsQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method SessionsQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method Sessions findOne(PropelPDO $con = null) Return the first Sessions matching the query
 * @method Sessions findOneOrCreate(PropelPDO $con = null) Return the first Sessions matching the query, or a new Sessions object populated from the query conditions when no match is found
 *
 * @method Sessions findOneByDbModified(int $modified) Return the first Sessions filtered by the modified column
 * @method Sessions findOneByDbLifetime(int $lifetime) Return the first Sessions filtered by the lifetime column
 * @method Sessions findOneByDbData(string $data) Return the first Sessions filtered by the data column
 *
 * @method array findByDbId(string $id) Return Sessions objects filtered by the id column
 * @method array findByDbModified(int $modified) Return Sessions objects filtered by the modified column
 * @method array findByDbLifetime(int $lifetime) Return Sessions objects filtered by the lifetime column
 * @method array findByDbData(string $data) Return Sessions objects filtered by the data column
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseSessionsQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseSessionsQuery object.
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
            $modelName = 'Sessions';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new SessionsQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   SessionsQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return SessionsQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof SessionsQuery) {
            return $criteria;
        }
        $query = new SessionsQuery(null, null, $modelAlias);

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
     * @return   Sessions|Sessions[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = SessionsPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(SessionsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 Sessions A model object, or null if the key is not found
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
     * @return                 Sessions A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT "id", "modified", "lifetime", "data" FROM "sessions" WHERE "id" = :p0';
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
            $obj = new Sessions();
            $obj->hydrate($row);
            SessionsPeer::addInstanceToPool($obj, (string) $key);
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
     * @return Sessions|Sessions[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|Sessions[]|mixed the list of results, formatted by the current formatter
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
     * @return SessionsQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(SessionsPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return SessionsQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(SessionsPeer::ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the id column
     *
     * Example usage:
     * <code>
     * $query->filterByDbId('fooValue');   // WHERE id = 'fooValue'
     * $query->filterByDbId('%fooValue%'); // WHERE id LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbId The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SessionsQuery The current query, for fluid interface
     */
    public function filterByDbId($dbId = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($dbId)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $dbId)) {
                $dbId = str_replace('*', '%', $dbId);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(SessionsPeer::ID, $dbId, $comparison);
    }

    /**
     * Filter the query on the modified column
     *
     * Example usage:
     * <code>
     * $query->filterByDbModified(1234); // WHERE modified = 1234
     * $query->filterByDbModified(array(12, 34)); // WHERE modified IN (12, 34)
     * $query->filterByDbModified(array('min' => 12)); // WHERE modified >= 12
     * $query->filterByDbModified(array('max' => 12)); // WHERE modified <= 12
     * </code>
     *
     * @param     mixed $dbModified The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SessionsQuery The current query, for fluid interface
     */
    public function filterByDbModified($dbModified = null, $comparison = null)
    {
        if (is_array($dbModified)) {
            $useMinMax = false;
            if (isset($dbModified['min'])) {
                $this->addUsingAlias(SessionsPeer::MODIFIED, $dbModified['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dbModified['max'])) {
                $this->addUsingAlias(SessionsPeer::MODIFIED, $dbModified['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SessionsPeer::MODIFIED, $dbModified, $comparison);
    }

    /**
     * Filter the query on the lifetime column
     *
     * Example usage:
     * <code>
     * $query->filterByDbLifetime(1234); // WHERE lifetime = 1234
     * $query->filterByDbLifetime(array(12, 34)); // WHERE lifetime IN (12, 34)
     * $query->filterByDbLifetime(array('min' => 12)); // WHERE lifetime >= 12
     * $query->filterByDbLifetime(array('max' => 12)); // WHERE lifetime <= 12
     * </code>
     *
     * @param     mixed $dbLifetime The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SessionsQuery The current query, for fluid interface
     */
    public function filterByDbLifetime($dbLifetime = null, $comparison = null)
    {
        if (is_array($dbLifetime)) {
            $useMinMax = false;
            if (isset($dbLifetime['min'])) {
                $this->addUsingAlias(SessionsPeer::LIFETIME, $dbLifetime['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dbLifetime['max'])) {
                $this->addUsingAlias(SessionsPeer::LIFETIME, $dbLifetime['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SessionsPeer::LIFETIME, $dbLifetime, $comparison);
    }

    /**
     * Filter the query on the data column
     *
     * Example usage:
     * <code>
     * $query->filterByDbData('fooValue');   // WHERE data = 'fooValue'
     * $query->filterByDbData('%fooValue%'); // WHERE data LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbData The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SessionsQuery The current query, for fluid interface
     */
    public function filterByDbData($dbData = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($dbData)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $dbData)) {
                $dbData = str_replace('*', '%', $dbData);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(SessionsPeer::DATA, $dbData, $comparison);
    }

    /**
     * Exclude object from result
     *
     * @param   Sessions $sessions Object to remove from the list of results
     *
     * @return SessionsQuery The current query, for fluid interface
     */
    public function prune($sessions = null)
    {
        if ($sessions) {
            $this->addUsingAlias(SessionsPeer::ID, $sessions->getDbId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}
