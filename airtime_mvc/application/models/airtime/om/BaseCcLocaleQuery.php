<?php


/**
 * Base class that represents a query for the 'cc_locale' table.
 *
 *
 *
 * @method CcLocaleQuery orderByDbId($order = Criteria::ASC) Order by the id column
 * @method CcLocaleQuery orderByDbLocaleCode($order = Criteria::ASC) Order by the locale_code column
 * @method CcLocaleQuery orderByDbLocaleLang($order = Criteria::ASC) Order by the locale_lang column
 *
 * @method CcLocaleQuery groupByDbId() Group by the id column
 * @method CcLocaleQuery groupByDbLocaleCode() Group by the locale_code column
 * @method CcLocaleQuery groupByDbLocaleLang() Group by the locale_lang column
 *
 * @method CcLocaleQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method CcLocaleQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method CcLocaleQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method CcLocale findOne(PropelPDO $con = null) Return the first CcLocale matching the query
 * @method CcLocale findOneOrCreate(PropelPDO $con = null) Return the first CcLocale matching the query, or a new CcLocale object populated from the query conditions when no match is found
 *
 * @method CcLocale findOneByDbLocaleCode(string $locale_code) Return the first CcLocale filtered by the locale_code column
 * @method CcLocale findOneByDbLocaleLang(string $locale_lang) Return the first CcLocale filtered by the locale_lang column
 *
 * @method array findByDbId(int $id) Return CcLocale objects filtered by the id column
 * @method array findByDbLocaleCode(string $locale_code) Return CcLocale objects filtered by the locale_code column
 * @method array findByDbLocaleLang(string $locale_lang) Return CcLocale objects filtered by the locale_lang column
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcLocaleQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseCcLocaleQuery object.
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
            $modelName = 'CcLocale';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new CcLocaleQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   CcLocaleQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return CcLocaleQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof CcLocaleQuery) {
            return $criteria;
        }
        $query = new CcLocaleQuery(null, null, $modelAlias);

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
     * @return   CcLocale|CcLocale[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = CcLocalePeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(CcLocalePeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 CcLocale A model object, or null if the key is not found
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
     * @return                 CcLocale A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT "id", "locale_code", "locale_lang" FROM "cc_locale" WHERE "id" = :p0';
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
            $obj = new CcLocale();
            $obj->hydrate($row);
            CcLocalePeer::addInstanceToPool($obj, (string) $key);
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
     * @return CcLocale|CcLocale[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|CcLocale[]|mixed the list of results, formatted by the current formatter
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
     * @return CcLocaleQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(CcLocalePeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return CcLocaleQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(CcLocalePeer::ID, $keys, Criteria::IN);
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
     * @return CcLocaleQuery The current query, for fluid interface
     */
    public function filterByDbId($dbId = null, $comparison = null)
    {
        if (is_array($dbId)) {
            $useMinMax = false;
            if (isset($dbId['min'])) {
                $this->addUsingAlias(CcLocalePeer::ID, $dbId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dbId['max'])) {
                $this->addUsingAlias(CcLocalePeer::ID, $dbId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CcLocalePeer::ID, $dbId, $comparison);
    }

    /**
     * Filter the query on the locale_code column
     *
     * Example usage:
     * <code>
     * $query->filterByDbLocaleCode('fooValue');   // WHERE locale_code = 'fooValue'
     * $query->filterByDbLocaleCode('%fooValue%'); // WHERE locale_code LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbLocaleCode The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcLocaleQuery The current query, for fluid interface
     */
    public function filterByDbLocaleCode($dbLocaleCode = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($dbLocaleCode)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $dbLocaleCode)) {
                $dbLocaleCode = str_replace('*', '%', $dbLocaleCode);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CcLocalePeer::LOCALE_CODE, $dbLocaleCode, $comparison);
    }

    /**
     * Filter the query on the locale_lang column
     *
     * Example usage:
     * <code>
     * $query->filterByDbLocaleLang('fooValue');   // WHERE locale_lang = 'fooValue'
     * $query->filterByDbLocaleLang('%fooValue%'); // WHERE locale_lang LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbLocaleLang The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcLocaleQuery The current query, for fluid interface
     */
    public function filterByDbLocaleLang($dbLocaleLang = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($dbLocaleLang)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $dbLocaleLang)) {
                $dbLocaleLang = str_replace('*', '%', $dbLocaleLang);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CcLocalePeer::LOCALE_LANG, $dbLocaleLang, $comparison);
    }

    /**
     * Exclude object from result
     *
     * @param   CcLocale $ccLocale Object to remove from the list of results
     *
     * @return CcLocaleQuery The current query, for fluid interface
     */
    public function prune($ccLocale = null)
    {
        if ($ccLocale) {
            $this->addUsingAlias(CcLocalePeer::ID, $ccLocale->getDbId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}
