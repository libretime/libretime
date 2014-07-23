<?php


/**
 * Base class that represents a query for the 'cc_smemb' table.
 *
 *
 *
 * @method CcSmembQuery orderById($order = Criteria::ASC) Order by the id column
 * @method CcSmembQuery orderByUid($order = Criteria::ASC) Order by the uid column
 * @method CcSmembQuery orderByGid($order = Criteria::ASC) Order by the gid column
 * @method CcSmembQuery orderByLevel($order = Criteria::ASC) Order by the level column
 * @method CcSmembQuery orderByMid($order = Criteria::ASC) Order by the mid column
 *
 * @method CcSmembQuery groupById() Group by the id column
 * @method CcSmembQuery groupByUid() Group by the uid column
 * @method CcSmembQuery groupByGid() Group by the gid column
 * @method CcSmembQuery groupByLevel() Group by the level column
 * @method CcSmembQuery groupByMid() Group by the mid column
 *
 * @method CcSmembQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method CcSmembQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method CcSmembQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method CcSmemb findOne(PropelPDO $con = null) Return the first CcSmemb matching the query
 * @method CcSmemb findOneOrCreate(PropelPDO $con = null) Return the first CcSmemb matching the query, or a new CcSmemb object populated from the query conditions when no match is found
 *
 * @method CcSmemb findOneByUid(int $uid) Return the first CcSmemb filtered by the uid column
 * @method CcSmemb findOneByGid(int $gid) Return the first CcSmemb filtered by the gid column
 * @method CcSmemb findOneByLevel(int $level) Return the first CcSmemb filtered by the level column
 * @method CcSmemb findOneByMid(int $mid) Return the first CcSmemb filtered by the mid column
 *
 * @method array findById(int $id) Return CcSmemb objects filtered by the id column
 * @method array findByUid(int $uid) Return CcSmemb objects filtered by the uid column
 * @method array findByGid(int $gid) Return CcSmemb objects filtered by the gid column
 * @method array findByLevel(int $level) Return CcSmemb objects filtered by the level column
 * @method array findByMid(int $mid) Return CcSmemb objects filtered by the mid column
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcSmembQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseCcSmembQuery object.
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
            $modelName = 'CcSmemb';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new CcSmembQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   CcSmembQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return CcSmembQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof CcSmembQuery) {
            return $criteria;
        }
        $query = new CcSmembQuery(null, null, $modelAlias);

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
     * @return   CcSmemb|CcSmemb[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = CcSmembPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(CcSmembPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 CcSmemb A model object, or null if the key is not found
     * @throws PropelException
     */
     public function findOneById($key, $con = null)
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
     * @return                 CcSmemb A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT "id", "uid", "gid", "level", "mid" FROM "cc_smemb" WHERE "id" = :p0';
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
            $obj = new CcSmemb();
            $obj->hydrate($row);
            CcSmembPeer::addInstanceToPool($obj, (string) $key);
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
     * @return CcSmemb|CcSmemb[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|CcSmemb[]|mixed the list of results, formatted by the current formatter
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
     * @return CcSmembQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(CcSmembPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return CcSmembQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(CcSmembPeer::ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the id column
     *
     * Example usage:
     * <code>
     * $query->filterById(1234); // WHERE id = 1234
     * $query->filterById(array(12, 34)); // WHERE id IN (12, 34)
     * $query->filterById(array('min' => 12)); // WHERE id >= 12
     * $query->filterById(array('max' => 12)); // WHERE id <= 12
     * </code>
     *
     * @param     mixed $id The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcSmembQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(CcSmembPeer::ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(CcSmembPeer::ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CcSmembPeer::ID, $id, $comparison);
    }

    /**
     * Filter the query on the uid column
     *
     * Example usage:
     * <code>
     * $query->filterByUid(1234); // WHERE uid = 1234
     * $query->filterByUid(array(12, 34)); // WHERE uid IN (12, 34)
     * $query->filterByUid(array('min' => 12)); // WHERE uid >= 12
     * $query->filterByUid(array('max' => 12)); // WHERE uid <= 12
     * </code>
     *
     * @param     mixed $uid The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcSmembQuery The current query, for fluid interface
     */
    public function filterByUid($uid = null, $comparison = null)
    {
        if (is_array($uid)) {
            $useMinMax = false;
            if (isset($uid['min'])) {
                $this->addUsingAlias(CcSmembPeer::UID, $uid['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($uid['max'])) {
                $this->addUsingAlias(CcSmembPeer::UID, $uid['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CcSmembPeer::UID, $uid, $comparison);
    }

    /**
     * Filter the query on the gid column
     *
     * Example usage:
     * <code>
     * $query->filterByGid(1234); // WHERE gid = 1234
     * $query->filterByGid(array(12, 34)); // WHERE gid IN (12, 34)
     * $query->filterByGid(array('min' => 12)); // WHERE gid >= 12
     * $query->filterByGid(array('max' => 12)); // WHERE gid <= 12
     * </code>
     *
     * @param     mixed $gid The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcSmembQuery The current query, for fluid interface
     */
    public function filterByGid($gid = null, $comparison = null)
    {
        if (is_array($gid)) {
            $useMinMax = false;
            if (isset($gid['min'])) {
                $this->addUsingAlias(CcSmembPeer::GID, $gid['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($gid['max'])) {
                $this->addUsingAlias(CcSmembPeer::GID, $gid['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CcSmembPeer::GID, $gid, $comparison);
    }

    /**
     * Filter the query on the level column
     *
     * Example usage:
     * <code>
     * $query->filterByLevel(1234); // WHERE level = 1234
     * $query->filterByLevel(array(12, 34)); // WHERE level IN (12, 34)
     * $query->filterByLevel(array('min' => 12)); // WHERE level >= 12
     * $query->filterByLevel(array('max' => 12)); // WHERE level <= 12
     * </code>
     *
     * @param     mixed $level The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcSmembQuery The current query, for fluid interface
     */
    public function filterByLevel($level = null, $comparison = null)
    {
        if (is_array($level)) {
            $useMinMax = false;
            if (isset($level['min'])) {
                $this->addUsingAlias(CcSmembPeer::LEVEL, $level['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($level['max'])) {
                $this->addUsingAlias(CcSmembPeer::LEVEL, $level['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CcSmembPeer::LEVEL, $level, $comparison);
    }

    /**
     * Filter the query on the mid column
     *
     * Example usage:
     * <code>
     * $query->filterByMid(1234); // WHERE mid = 1234
     * $query->filterByMid(array(12, 34)); // WHERE mid IN (12, 34)
     * $query->filterByMid(array('min' => 12)); // WHERE mid >= 12
     * $query->filterByMid(array('max' => 12)); // WHERE mid <= 12
     * </code>
     *
     * @param     mixed $mid The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcSmembQuery The current query, for fluid interface
     */
    public function filterByMid($mid = null, $comparison = null)
    {
        if (is_array($mid)) {
            $useMinMax = false;
            if (isset($mid['min'])) {
                $this->addUsingAlias(CcSmembPeer::MID, $mid['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($mid['max'])) {
                $this->addUsingAlias(CcSmembPeer::MID, $mid['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CcSmembPeer::MID, $mid, $comparison);
    }

    /**
     * Exclude object from result
     *
     * @param   CcSmemb $ccSmemb Object to remove from the list of results
     *
     * @return CcSmembQuery The current query, for fluid interface
     */
    public function prune($ccSmemb = null)
    {
        if ($ccSmemb) {
            $this->addUsingAlias(CcSmembPeer::ID, $ccSmemb->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}
