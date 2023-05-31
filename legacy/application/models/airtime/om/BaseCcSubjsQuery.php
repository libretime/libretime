<?php


/**
 * Base class that represents a query for the 'cc_subjs' table.
 *
 *
 *
 * @method CcSubjsQuery orderByDbId($order = Criteria::ASC) Order by the id column
 * @method CcSubjsQuery orderByDbLogin($order = Criteria::ASC) Order by the login column
 * @method CcSubjsQuery orderByDbPass($order = Criteria::ASC) Order by the pass column
 * @method CcSubjsQuery orderByDbType($order = Criteria::ASC) Order by the type column
 * @method CcSubjsQuery orderByDbIsActive($order = Criteria::ASC) Order by the is_active column
 * @method CcSubjsQuery orderByDbFirstName($order = Criteria::ASC) Order by the first_name column
 * @method CcSubjsQuery orderByDbLastName($order = Criteria::ASC) Order by the last_name column
 * @method CcSubjsQuery orderByDbLastlogin($order = Criteria::ASC) Order by the lastlogin column
 * @method CcSubjsQuery orderByDbLastfail($order = Criteria::ASC) Order by the lastfail column
 * @method CcSubjsQuery orderByDbSkypeContact($order = Criteria::ASC) Order by the skype_contact column
 * @method CcSubjsQuery orderByDbJabberContact($order = Criteria::ASC) Order by the jabber_contact column
 * @method CcSubjsQuery orderByDbEmail($order = Criteria::ASC) Order by the email column
 * @method CcSubjsQuery orderByDbCellPhone($order = Criteria::ASC) Order by the cell_phone column
 * @method CcSubjsQuery orderByDbLoginAttempts($order = Criteria::ASC) Order by the login_attempts column
 *
 * @method CcSubjsQuery groupByDbId() Group by the id column
 * @method CcSubjsQuery groupByDbLogin() Group by the login column
 * @method CcSubjsQuery groupByDbPass() Group by the pass column
 * @method CcSubjsQuery groupByDbType() Group by the type column
 * @method CcSubjsQuery groupByDbIsActive() Group by the is_active column
 * @method CcSubjsQuery groupByDbFirstName() Group by the first_name column
 * @method CcSubjsQuery groupByDbLastName() Group by the last_name column
 * @method CcSubjsQuery groupByDbLastlogin() Group by the lastlogin column
 * @method CcSubjsQuery groupByDbLastfail() Group by the lastfail column
 * @method CcSubjsQuery groupByDbSkypeContact() Group by the skype_contact column
 * @method CcSubjsQuery groupByDbJabberContact() Group by the jabber_contact column
 * @method CcSubjsQuery groupByDbEmail() Group by the email column
 * @method CcSubjsQuery groupByDbCellPhone() Group by the cell_phone column
 * @method CcSubjsQuery groupByDbLoginAttempts() Group by the login_attempts column
 *
 * @method CcSubjsQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method CcSubjsQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method CcSubjsQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method CcSubjsQuery leftJoinCcFilesRelatedByDbOwnerId($relationAlias = null) Adds a LEFT JOIN clause to the query using the CcFilesRelatedByDbOwnerId relation
 * @method CcSubjsQuery rightJoinCcFilesRelatedByDbOwnerId($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CcFilesRelatedByDbOwnerId relation
 * @method CcSubjsQuery innerJoinCcFilesRelatedByDbOwnerId($relationAlias = null) Adds a INNER JOIN clause to the query using the CcFilesRelatedByDbOwnerId relation
 *
 * @method CcSubjsQuery leftJoinCcFilesRelatedByDbEditedby($relationAlias = null) Adds a LEFT JOIN clause to the query using the CcFilesRelatedByDbEditedby relation
 * @method CcSubjsQuery rightJoinCcFilesRelatedByDbEditedby($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CcFilesRelatedByDbEditedby relation
 * @method CcSubjsQuery innerJoinCcFilesRelatedByDbEditedby($relationAlias = null) Adds a INNER JOIN clause to the query using the CcFilesRelatedByDbEditedby relation
 *
 * @method CcSubjsQuery leftJoinCcShowHosts($relationAlias = null) Adds a LEFT JOIN clause to the query using the CcShowHosts relation
 * @method CcSubjsQuery rightJoinCcShowHosts($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CcShowHosts relation
 * @method CcSubjsQuery innerJoinCcShowHosts($relationAlias = null) Adds a INNER JOIN clause to the query using the CcShowHosts relation
 *
 * @method CcSubjsQuery leftJoinCcPlaylist($relationAlias = null) Adds a LEFT JOIN clause to the query using the CcPlaylist relation
 * @method CcSubjsQuery rightJoinCcPlaylist($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CcPlaylist relation
 * @method CcSubjsQuery innerJoinCcPlaylist($relationAlias = null) Adds a INNER JOIN clause to the query using the CcPlaylist relation
 *
 * @method CcSubjsQuery leftJoinCcBlock($relationAlias = null) Adds a LEFT JOIN clause to the query using the CcBlock relation
 * @method CcSubjsQuery rightJoinCcBlock($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CcBlock relation
 * @method CcSubjsQuery innerJoinCcBlock($relationAlias = null) Adds a INNER JOIN clause to the query using the CcBlock relation
 *
 * @method CcSubjsQuery leftJoinCcPref($relationAlias = null) Adds a LEFT JOIN clause to the query using the CcPref relation
 * @method CcSubjsQuery rightJoinCcPref($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CcPref relation
 * @method CcSubjsQuery innerJoinCcPref($relationAlias = null) Adds a INNER JOIN clause to the query using the CcPref relation
 *
 * @method CcSubjsQuery leftJoinCcSubjsToken($relationAlias = null) Adds a LEFT JOIN clause to the query using the CcSubjsToken relation
 * @method CcSubjsQuery rightJoinCcSubjsToken($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CcSubjsToken relation
 * @method CcSubjsQuery innerJoinCcSubjsToken($relationAlias = null) Adds a INNER JOIN clause to the query using the CcSubjsToken relation
 *
 * @method CcSubjsQuery leftJoinPodcast($relationAlias = null) Adds a LEFT JOIN clause to the query using the Podcast relation
 * @method CcSubjsQuery rightJoinPodcast($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Podcast relation
 * @method CcSubjsQuery innerJoinPodcast($relationAlias = null) Adds a INNER JOIN clause to the query using the Podcast relation
 *
 * @method CcSubjs findOne(PropelPDO $con = null) Return the first CcSubjs matching the query
 * @method CcSubjs findOneOrCreate(PropelPDO $con = null) Return the first CcSubjs matching the query, or a new CcSubjs object populated from the query conditions when no match is found
 *
 * @method CcSubjs findOneByDbLogin(string $login) Return the first CcSubjs filtered by the login column
 * @method CcSubjs findOneByDbPass(string $pass) Return the first CcSubjs filtered by the pass column
 * @method CcSubjs findOneByDbType(string $type) Return the first CcSubjs filtered by the type column
 * @method CcSubjs findOneByDbIsActive(boolean $is_active) Return the first CcSubjs filtered by the is_active column
 * @method CcSubjs findOneByDbFirstName(string $first_name) Return the first CcSubjs filtered by the first_name column
 * @method CcSubjs findOneByDbLastName(string $last_name) Return the first CcSubjs filtered by the last_name column
 * @method CcSubjs findOneByDbLastlogin(string $lastlogin) Return the first CcSubjs filtered by the lastlogin column
 * @method CcSubjs findOneByDbLastfail(string $lastfail) Return the first CcSubjs filtered by the lastfail column
 * @method CcSubjs findOneByDbSkypeContact(string $skype_contact) Return the first CcSubjs filtered by the skype_contact column
 * @method CcSubjs findOneByDbJabberContact(string $jabber_contact) Return the first CcSubjs filtered by the jabber_contact column
 * @method CcSubjs findOneByDbEmail(string $email) Return the first CcSubjs filtered by the email column
 * @method CcSubjs findOneByDbCellPhone(string $cell_phone) Return the first CcSubjs filtered by the cell_phone column
 * @method CcSubjs findOneByDbLoginAttempts(int $login_attempts) Return the first CcSubjs filtered by the login_attempts column
 *
 * @method array findByDbId(int $id) Return CcSubjs objects filtered by the id column
 * @method array findByDbLogin(string $login) Return CcSubjs objects filtered by the login column
 * @method array findByDbPass(string $pass) Return CcSubjs objects filtered by the pass column
 * @method array findByDbType(string $type) Return CcSubjs objects filtered by the type column
 * @method array findByDbIsActive(boolean $is_active) Return CcSubjs objects filtered by the is_active column
 * @method array findByDbFirstName(string $first_name) Return CcSubjs objects filtered by the first_name column
 * @method array findByDbLastName(string $last_name) Return CcSubjs objects filtered by the last_name column
 * @method array findByDbLastlogin(string $lastlogin) Return CcSubjs objects filtered by the lastlogin column
 * @method array findByDbLastfail(string $lastfail) Return CcSubjs objects filtered by the lastfail column
 * @method array findByDbSkypeContact(string $skype_contact) Return CcSubjs objects filtered by the skype_contact column
 * @method array findByDbJabberContact(string $jabber_contact) Return CcSubjs objects filtered by the jabber_contact column
 * @method array findByDbEmail(string $email) Return CcSubjs objects filtered by the email column
 * @method array findByDbCellPhone(string $cell_phone) Return CcSubjs objects filtered by the cell_phone column
 * @method array findByDbLoginAttempts(int $login_attempts) Return CcSubjs objects filtered by the login_attempts column
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcSubjsQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseCcSubjsQuery object.
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
            $modelName = 'CcSubjs';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new CcSubjsQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   CcSubjsQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return CcSubjsQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof CcSubjsQuery) {
            return $criteria;
        }
        $query = new CcSubjsQuery(null, null, $modelAlias);

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
     * @return   CcSubjs|CcSubjs[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = CcSubjsPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(CcSubjsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 CcSubjs A model object, or null if the key is not found
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
     * @return                 CcSubjs A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT "id", "login", "pass", "type", "is_active", "first_name", "last_name", "lastlogin", "lastfail", "skype_contact", "jabber_contact", "email", "cell_phone", "login_attempts" FROM "cc_subjs" WHERE "id" = :p0';
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
            $obj = new CcSubjs();
            $obj->hydrate($row);
            CcSubjsPeer::addInstanceToPool($obj, (string) $key);
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
     * @return CcSubjs|CcSubjs[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|CcSubjs[]|mixed the list of results, formatted by the current formatter
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
     * @return CcSubjsQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(CcSubjsPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return CcSubjsQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(CcSubjsPeer::ID, $keys, Criteria::IN);
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
     * @return CcSubjsQuery The current query, for fluid interface
     */
    public function filterByDbId($dbId = null, $comparison = null)
    {
        if (is_array($dbId)) {
            $useMinMax = false;
            if (isset($dbId['min'])) {
                $this->addUsingAlias(CcSubjsPeer::ID, $dbId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dbId['max'])) {
                $this->addUsingAlias(CcSubjsPeer::ID, $dbId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CcSubjsPeer::ID, $dbId, $comparison);
    }

    /**
     * Filter the query on the login column
     *
     * Example usage:
     * <code>
     * $query->filterByDbLogin('fooValue');   // WHERE login = 'fooValue'
     * $query->filterByDbLogin('%fooValue%'); // WHERE login LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbLogin The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcSubjsQuery The current query, for fluid interface
     */
    public function filterByDbLogin($dbLogin = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($dbLogin)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $dbLogin)) {
                $dbLogin = str_replace('*', '%', $dbLogin);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CcSubjsPeer::LOGIN, $dbLogin, $comparison);
    }

    /**
     * Filter the query on the pass column
     *
     * Example usage:
     * <code>
     * $query->filterByDbPass('fooValue');   // WHERE pass = 'fooValue'
     * $query->filterByDbPass('%fooValue%'); // WHERE pass LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbPass The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcSubjsQuery The current query, for fluid interface
     */
    public function filterByDbPass($dbPass = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($dbPass)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $dbPass)) {
                $dbPass = str_replace('*', '%', $dbPass);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CcSubjsPeer::PASS, $dbPass, $comparison);
    }

    /**
     * Filter the query on the type column
     *
     * Example usage:
     * <code>
     * $query->filterByDbType('fooValue');   // WHERE type = 'fooValue'
     * $query->filterByDbType('%fooValue%'); // WHERE type LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbType The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcSubjsQuery The current query, for fluid interface
     */
    public function filterByDbType($dbType = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($dbType)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $dbType)) {
                $dbType = str_replace('*', '%', $dbType);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CcSubjsPeer::TYPE, $dbType, $comparison);
    }

    /**
     * Filter the query on the is_active column
     *
     * Example usage:
     * <code>
     * $query->filterByDbIsActive(true); // WHERE is_active = true
     * $query->filterByDbIsActive('yes'); // WHERE is_active = true
     * </code>
     *
     * @param     boolean|string $dbIsActive The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcSubjsQuery The current query, for fluid interface
     */
    public function filterByDbIsActive($dbIsActive = null, $comparison = null)
    {
        if (is_string($dbIsActive)) {
            $dbIsActive = in_array(strtolower($dbIsActive), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(CcSubjsPeer::IS_ACTIVE, $dbIsActive, $comparison);
    }

    /**
     * Filter the query on the first_name column
     *
     * Example usage:
     * <code>
     * $query->filterByDbFirstName('fooValue');   // WHERE first_name = 'fooValue'
     * $query->filterByDbFirstName('%fooValue%'); // WHERE first_name LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbFirstName The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcSubjsQuery The current query, for fluid interface
     */
    public function filterByDbFirstName($dbFirstName = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($dbFirstName)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $dbFirstName)) {
                $dbFirstName = str_replace('*', '%', $dbFirstName);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CcSubjsPeer::FIRST_NAME, $dbFirstName, $comparison);
    }

    /**
     * Filter the query on the last_name column
     *
     * Example usage:
     * <code>
     * $query->filterByDbLastName('fooValue');   // WHERE last_name = 'fooValue'
     * $query->filterByDbLastName('%fooValue%'); // WHERE last_name LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbLastName The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcSubjsQuery The current query, for fluid interface
     */
    public function filterByDbLastName($dbLastName = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($dbLastName)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $dbLastName)) {
                $dbLastName = str_replace('*', '%', $dbLastName);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CcSubjsPeer::LAST_NAME, $dbLastName, $comparison);
    }

    /**
     * Filter the query on the lastlogin column
     *
     * Example usage:
     * <code>
     * $query->filterByDbLastlogin('2011-03-14'); // WHERE lastlogin = '2011-03-14'
     * $query->filterByDbLastlogin('now'); // WHERE lastlogin = '2011-03-14'
     * $query->filterByDbLastlogin(array('max' => 'yesterday')); // WHERE lastlogin < '2011-03-13'
     * </code>
     *
     * @param     mixed $dbLastlogin The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcSubjsQuery The current query, for fluid interface
     */
    public function filterByDbLastlogin($dbLastlogin = null, $comparison = null)
    {
        if (is_array($dbLastlogin)) {
            $useMinMax = false;
            if (isset($dbLastlogin['min'])) {
                $this->addUsingAlias(CcSubjsPeer::LASTLOGIN, $dbLastlogin['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dbLastlogin['max'])) {
                $this->addUsingAlias(CcSubjsPeer::LASTLOGIN, $dbLastlogin['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CcSubjsPeer::LASTLOGIN, $dbLastlogin, $comparison);
    }

    /**
     * Filter the query on the lastfail column
     *
     * Example usage:
     * <code>
     * $query->filterByDbLastfail('2011-03-14'); // WHERE lastfail = '2011-03-14'
     * $query->filterByDbLastfail('now'); // WHERE lastfail = '2011-03-14'
     * $query->filterByDbLastfail(array('max' => 'yesterday')); // WHERE lastfail < '2011-03-13'
     * </code>
     *
     * @param     mixed $dbLastfail The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcSubjsQuery The current query, for fluid interface
     */
    public function filterByDbLastfail($dbLastfail = null, $comparison = null)
    {
        if (is_array($dbLastfail)) {
            $useMinMax = false;
            if (isset($dbLastfail['min'])) {
                $this->addUsingAlias(CcSubjsPeer::LASTFAIL, $dbLastfail['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dbLastfail['max'])) {
                $this->addUsingAlias(CcSubjsPeer::LASTFAIL, $dbLastfail['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CcSubjsPeer::LASTFAIL, $dbLastfail, $comparison);
    }

    /**
     * Filter the query on the skype_contact column
     *
     * Example usage:
     * <code>
     * $query->filterByDbSkypeContact('fooValue');   // WHERE skype_contact = 'fooValue'
     * $query->filterByDbSkypeContact('%fooValue%'); // WHERE skype_contact LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbSkypeContact The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcSubjsQuery The current query, for fluid interface
     */
    public function filterByDbSkypeContact($dbSkypeContact = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($dbSkypeContact)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $dbSkypeContact)) {
                $dbSkypeContact = str_replace('*', '%', $dbSkypeContact);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CcSubjsPeer::SKYPE_CONTACT, $dbSkypeContact, $comparison);
    }

    /**
     * Filter the query on the jabber_contact column
     *
     * Example usage:
     * <code>
     * $query->filterByDbJabberContact('fooValue');   // WHERE jabber_contact = 'fooValue'
     * $query->filterByDbJabberContact('%fooValue%'); // WHERE jabber_contact LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbJabberContact The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcSubjsQuery The current query, for fluid interface
     */
    public function filterByDbJabberContact($dbJabberContact = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($dbJabberContact)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $dbJabberContact)) {
                $dbJabberContact = str_replace('*', '%', $dbJabberContact);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CcSubjsPeer::JABBER_CONTACT, $dbJabberContact, $comparison);
    }

    /**
     * Filter the query on the email column
     *
     * Example usage:
     * <code>
     * $query->filterByDbEmail('fooValue');   // WHERE email = 'fooValue'
     * $query->filterByDbEmail('%fooValue%'); // WHERE email LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbEmail The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcSubjsQuery The current query, for fluid interface
     */
    public function filterByDbEmail($dbEmail = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($dbEmail)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $dbEmail)) {
                $dbEmail = str_replace('*', '%', $dbEmail);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CcSubjsPeer::EMAIL, $dbEmail, $comparison);
    }

    /**
     * Filter the query on the cell_phone column
     *
     * Example usage:
     * <code>
     * $query->filterByDbCellPhone('fooValue');   // WHERE cell_phone = 'fooValue'
     * $query->filterByDbCellPhone('%fooValue%'); // WHERE cell_phone LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbCellPhone The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcSubjsQuery The current query, for fluid interface
     */
    public function filterByDbCellPhone($dbCellPhone = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($dbCellPhone)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $dbCellPhone)) {
                $dbCellPhone = str_replace('*', '%', $dbCellPhone);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CcSubjsPeer::CELL_PHONE, $dbCellPhone, $comparison);
    }

    /**
     * Filter the query on the login_attempts column
     *
     * Example usage:
     * <code>
     * $query->filterByDbLoginAttempts(1234); // WHERE login_attempts = 1234
     * $query->filterByDbLoginAttempts(array(12, 34)); // WHERE login_attempts IN (12, 34)
     * $query->filterByDbLoginAttempts(array('min' => 12)); // WHERE login_attempts >= 12
     * $query->filterByDbLoginAttempts(array('max' => 12)); // WHERE login_attempts <= 12
     * </code>
     *
     * @param     mixed $dbLoginAttempts The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcSubjsQuery The current query, for fluid interface
     */
    public function filterByDbLoginAttempts($dbLoginAttempts = null, $comparison = null)
    {
        if (is_array($dbLoginAttempts)) {
            $useMinMax = false;
            if (isset($dbLoginAttempts['min'])) {
                $this->addUsingAlias(CcSubjsPeer::LOGIN_ATTEMPTS, $dbLoginAttempts['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dbLoginAttempts['max'])) {
                $this->addUsingAlias(CcSubjsPeer::LOGIN_ATTEMPTS, $dbLoginAttempts['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CcSubjsPeer::LOGIN_ATTEMPTS, $dbLoginAttempts, $comparison);
    }

    /**
     * Filter the query by a related CcFiles object
     *
     * @param   CcFiles|PropelObjectCollection $ccFiles  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 CcSubjsQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCcFilesRelatedByDbOwnerId($ccFiles, $comparison = null)
    {
        if ($ccFiles instanceof CcFiles) {
            return $this
                ->addUsingAlias(CcSubjsPeer::ID, $ccFiles->getDbOwnerId(), $comparison);
        } elseif ($ccFiles instanceof PropelObjectCollection) {
            return $this
                ->useCcFilesRelatedByDbOwnerIdQuery()
                ->filterByPrimaryKeys($ccFiles->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByCcFilesRelatedByDbOwnerId() only accepts arguments of type CcFiles or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CcFilesRelatedByDbOwnerId relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return CcSubjsQuery The current query, for fluid interface
     */
    public function joinCcFilesRelatedByDbOwnerId($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CcFilesRelatedByDbOwnerId');

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
            $this->addJoinObject($join, 'CcFilesRelatedByDbOwnerId');
        }

        return $this;
    }

    /**
     * Use the CcFilesRelatedByDbOwnerId relation CcFiles object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   CcFilesQuery A secondary query class using the current class as primary query
     */
    public function useCcFilesRelatedByDbOwnerIdQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinCcFilesRelatedByDbOwnerId($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CcFilesRelatedByDbOwnerId', 'CcFilesQuery');
    }

    /**
     * Filter the query by a related CcFiles object
     *
     * @param   CcFiles|PropelObjectCollection $ccFiles  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 CcSubjsQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCcFilesRelatedByDbEditedby($ccFiles, $comparison = null)
    {
        if ($ccFiles instanceof CcFiles) {
            return $this
                ->addUsingAlias(CcSubjsPeer::ID, $ccFiles->getDbEditedby(), $comparison);
        } elseif ($ccFiles instanceof PropelObjectCollection) {
            return $this
                ->useCcFilesRelatedByDbEditedbyQuery()
                ->filterByPrimaryKeys($ccFiles->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByCcFilesRelatedByDbEditedby() only accepts arguments of type CcFiles or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CcFilesRelatedByDbEditedby relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return CcSubjsQuery The current query, for fluid interface
     */
    public function joinCcFilesRelatedByDbEditedby($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CcFilesRelatedByDbEditedby');

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
            $this->addJoinObject($join, 'CcFilesRelatedByDbEditedby');
        }

        return $this;
    }

    /**
     * Use the CcFilesRelatedByDbEditedby relation CcFiles object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   CcFilesQuery A secondary query class using the current class as primary query
     */
    public function useCcFilesRelatedByDbEditedbyQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinCcFilesRelatedByDbEditedby($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CcFilesRelatedByDbEditedby', 'CcFilesQuery');
    }

    /**
     * Filter the query by a related CcShowHosts object
     *
     * @param   CcShowHosts|PropelObjectCollection $ccShowHosts  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 CcSubjsQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCcShowHosts($ccShowHosts, $comparison = null)
    {
        if ($ccShowHosts instanceof CcShowHosts) {
            return $this
                ->addUsingAlias(CcSubjsPeer::ID, $ccShowHosts->getDbHost(), $comparison);
        } elseif ($ccShowHosts instanceof PropelObjectCollection) {
            return $this
                ->useCcShowHostsQuery()
                ->filterByPrimaryKeys($ccShowHosts->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByCcShowHosts() only accepts arguments of type CcShowHosts or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CcShowHosts relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return CcSubjsQuery The current query, for fluid interface
     */
    public function joinCcShowHosts($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CcShowHosts');

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
            $this->addJoinObject($join, 'CcShowHosts');
        }

        return $this;
    }

    /**
     * Use the CcShowHosts relation CcShowHosts object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   CcShowHostsQuery A secondary query class using the current class as primary query
     */
    public function useCcShowHostsQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinCcShowHosts($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CcShowHosts', 'CcShowHostsQuery');
    }

    /**
     * Filter the query by a related CcPlaylist object
     *
     * @param   CcPlaylist|PropelObjectCollection $ccPlaylist  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 CcSubjsQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCcPlaylist($ccPlaylist, $comparison = null)
    {
        if ($ccPlaylist instanceof CcPlaylist) {
            return $this
                ->addUsingAlias(CcSubjsPeer::ID, $ccPlaylist->getDbCreatorId(), $comparison);
        } elseif ($ccPlaylist instanceof PropelObjectCollection) {
            return $this
                ->useCcPlaylistQuery()
                ->filterByPrimaryKeys($ccPlaylist->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByCcPlaylist() only accepts arguments of type CcPlaylist or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CcPlaylist relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return CcSubjsQuery The current query, for fluid interface
     */
    public function joinCcPlaylist($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CcPlaylist');

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
            $this->addJoinObject($join, 'CcPlaylist');
        }

        return $this;
    }

    /**
     * Use the CcPlaylist relation CcPlaylist object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   CcPlaylistQuery A secondary query class using the current class as primary query
     */
    public function useCcPlaylistQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinCcPlaylist($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CcPlaylist', 'CcPlaylistQuery');
    }

    /**
     * Filter the query by a related CcBlock object
     *
     * @param   CcBlock|PropelObjectCollection $ccBlock  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 CcSubjsQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCcBlock($ccBlock, $comparison = null)
    {
        if ($ccBlock instanceof CcBlock) {
            return $this
                ->addUsingAlias(CcSubjsPeer::ID, $ccBlock->getDbCreatorId(), $comparison);
        } elseif ($ccBlock instanceof PropelObjectCollection) {
            return $this
                ->useCcBlockQuery()
                ->filterByPrimaryKeys($ccBlock->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByCcBlock() only accepts arguments of type CcBlock or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CcBlock relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return CcSubjsQuery The current query, for fluid interface
     */
    public function joinCcBlock($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CcBlock');

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
            $this->addJoinObject($join, 'CcBlock');
        }

        return $this;
    }

    /**
     * Use the CcBlock relation CcBlock object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   CcBlockQuery A secondary query class using the current class as primary query
     */
    public function useCcBlockQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinCcBlock($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CcBlock', 'CcBlockQuery');
    }

    /**
     * Filter the query by a related CcPref object
     *
     * @param   CcPref|PropelObjectCollection $ccPref  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 CcSubjsQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCcPref($ccPref, $comparison = null)
    {
        if ($ccPref instanceof CcPref) {
            return $this
                ->addUsingAlias(CcSubjsPeer::ID, $ccPref->getSubjid(), $comparison);
        } elseif ($ccPref instanceof PropelObjectCollection) {
            return $this
                ->useCcPrefQuery()
                ->filterByPrimaryKeys($ccPref->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByCcPref() only accepts arguments of type CcPref or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CcPref relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return CcSubjsQuery The current query, for fluid interface
     */
    public function joinCcPref($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CcPref');

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
            $this->addJoinObject($join, 'CcPref');
        }

        return $this;
    }

    /**
     * Use the CcPref relation CcPref object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   CcPrefQuery A secondary query class using the current class as primary query
     */
    public function useCcPrefQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinCcPref($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CcPref', 'CcPrefQuery');
    }

    /**
     * Filter the query by a related CcSubjsToken object
     *
     * @param   CcSubjsToken|PropelObjectCollection $ccSubjsToken  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 CcSubjsQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCcSubjsToken($ccSubjsToken, $comparison = null)
    {
        if ($ccSubjsToken instanceof CcSubjsToken) {
            return $this
                ->addUsingAlias(CcSubjsPeer::ID, $ccSubjsToken->getDbUserId(), $comparison);
        } elseif ($ccSubjsToken instanceof PropelObjectCollection) {
            return $this
                ->useCcSubjsTokenQuery()
                ->filterByPrimaryKeys($ccSubjsToken->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByCcSubjsToken() only accepts arguments of type CcSubjsToken or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CcSubjsToken relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return CcSubjsQuery The current query, for fluid interface
     */
    public function joinCcSubjsToken($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CcSubjsToken');

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
            $this->addJoinObject($join, 'CcSubjsToken');
        }

        return $this;
    }

    /**
     * Use the CcSubjsToken relation CcSubjsToken object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   CcSubjsTokenQuery A secondary query class using the current class as primary query
     */
    public function useCcSubjsTokenQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinCcSubjsToken($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CcSubjsToken', 'CcSubjsTokenQuery');
    }

    /**
     * Filter the query by a related Podcast object
     *
     * @param   Podcast|PropelObjectCollection $podcast  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 CcSubjsQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByPodcast($podcast, $comparison = null)
    {
        if ($podcast instanceof Podcast) {
            return $this
                ->addUsingAlias(CcSubjsPeer::ID, $podcast->getDbOwner(), $comparison);
        } elseif ($podcast instanceof PropelObjectCollection) {
            return $this
                ->usePodcastQuery()
                ->filterByPrimaryKeys($podcast->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByPodcast() only accepts arguments of type Podcast or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Podcast relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return CcSubjsQuery The current query, for fluid interface
     */
    public function joinPodcast($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Podcast');

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
            $this->addJoinObject($join, 'Podcast');
        }

        return $this;
    }

    /**
     * Use the Podcast relation Podcast object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   PodcastQuery A secondary query class using the current class as primary query
     */
    public function usePodcastQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinPodcast($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Podcast', 'PodcastQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   CcSubjs $ccSubjs Object to remove from the list of results
     *
     * @return CcSubjsQuery The current query, for fluid interface
     */
    public function prune($ccSubjs = null)
    {
        if ($ccSubjs) {
            $this->addUsingAlias(CcSubjsPeer::ID, $ccSubjs->getDbId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}
