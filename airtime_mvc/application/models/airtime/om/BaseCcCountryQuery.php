<?php


/**
 * Base class that represents a query for the 'cc_country' table.
 *
 *
 *
 * @method CcCountryQuery orderByDbIsoCode($order = Criteria::ASC) Order by the isocode column
 * @method CcCountryQuery orderByDbName($order = Criteria::ASC) Order by the name column
 *
 * @method CcCountryQuery groupByDbIsoCode() Group by the isocode column
 * @method CcCountryQuery groupByDbName() Group by the name column
 *
 * @method CcCountryQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method CcCountryQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method CcCountryQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method CcCountry findOne(PropelPDO $con = null) Return the first CcCountry matching the query
 * @method CcCountry findOneOrCreate(PropelPDO $con = null) Return the first CcCountry matching the query, or a new CcCountry object populated from the query conditions when no match is found
 *
 * @method CcCountry findOneByDbName(string $name) Return the first CcCountry filtered by the name column
 *
 * @method array findByDbIsoCode(string $isocode) Return CcCountry objects filtered by the isocode column
 * @method array findByDbName(string $name) Return CcCountry objects filtered by the name column
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcCountryQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseCcCountryQuery object.
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
            $modelName = 'CcCountry';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new CcCountryQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   CcCountryQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return CcCountryQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof CcCountryQuery) {
            return $criteria;
        }
        $query = new CcCountryQuery(null, null, $modelAlias);

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
     * @return   CcCountry|CcCountry[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = CcCountryPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(CcCountryPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 CcCountry A model object, or null if the key is not found
     * @throws PropelException
     */
     public function findOneByDbIsoCode($key, $con = null)
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
     * @return                 CcCountry A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT "isocode", "name" FROM "cc_country" WHERE "isocode" = :p0';
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
            $obj = new CcCountry();
            $obj->hydrate($row);
            CcCountryPeer::addInstanceToPool($obj, (string) $key);
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
     * @return CcCountry|CcCountry[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|CcCountry[]|mixed the list of results, formatted by the current formatter
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
     * @return CcCountryQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(CcCountryPeer::ISOCODE, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return CcCountryQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(CcCountryPeer::ISOCODE, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the isocode column
     *
     * Example usage:
     * <code>
     * $query->filterByDbIsoCode('fooValue');   // WHERE isocode = 'fooValue'
     * $query->filterByDbIsoCode('%fooValue%'); // WHERE isocode LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbIsoCode The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcCountryQuery The current query, for fluid interface
     */
    public function filterByDbIsoCode($dbIsoCode = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($dbIsoCode)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $dbIsoCode)) {
                $dbIsoCode = str_replace('*', '%', $dbIsoCode);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CcCountryPeer::ISOCODE, $dbIsoCode, $comparison);
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
     * @return CcCountryQuery The current query, for fluid interface
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

        return $this->addUsingAlias(CcCountryPeer::NAME, $dbName, $comparison);
    }

    /**
     * Exclude object from result
     *
     * @param   CcCountry $ccCountry Object to remove from the list of results
     *
     * @return CcCountryQuery The current query, for fluid interface
     */
    public function prune($ccCountry = null)
    {
        if ($ccCountry) {
            $this->addUsingAlias(CcCountryPeer::ISOCODE, $ccCountry->getDbIsoCode(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}
