<?php


/**
 * Base class that represents a query for the 'cc_block' table.
 *
 *
 *
 * @method CcBlockQuery orderByDbId($order = Criteria::ASC) Order by the id column
 * @method CcBlockQuery orderByDbName($order = Criteria::ASC) Order by the name column
 * @method CcBlockQuery orderByDbMtime($order = Criteria::ASC) Order by the mtime column
 * @method CcBlockQuery orderByDbUtime($order = Criteria::ASC) Order by the utime column
 * @method CcBlockQuery orderByDbCreatorId($order = Criteria::ASC) Order by the creator_id column
 * @method CcBlockQuery orderByDbDescription($order = Criteria::ASC) Order by the description column
 * @method CcBlockQuery orderByDbLength($order = Criteria::ASC) Order by the length column
 * @method CcBlockQuery orderByDbType($order = Criteria::ASC) Order by the type column
 *
 * @method CcBlockQuery groupByDbId() Group by the id column
 * @method CcBlockQuery groupByDbName() Group by the name column
 * @method CcBlockQuery groupByDbMtime() Group by the mtime column
 * @method CcBlockQuery groupByDbUtime() Group by the utime column
 * @method CcBlockQuery groupByDbCreatorId() Group by the creator_id column
 * @method CcBlockQuery groupByDbDescription() Group by the description column
 * @method CcBlockQuery groupByDbLength() Group by the length column
 * @method CcBlockQuery groupByDbType() Group by the type column
 *
 * @method CcBlockQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method CcBlockQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method CcBlockQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method CcBlockQuery leftJoinCcSubjs($relationAlias = null) Adds a LEFT JOIN clause to the query using the CcSubjs relation
 * @method CcBlockQuery rightJoinCcSubjs($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CcSubjs relation
 * @method CcBlockQuery innerJoinCcSubjs($relationAlias = null) Adds a INNER JOIN clause to the query using the CcSubjs relation
 *
 * @method CcBlockQuery leftJoinCcPlaylistcontents($relationAlias = null) Adds a LEFT JOIN clause to the query using the CcPlaylistcontents relation
 * @method CcBlockQuery rightJoinCcPlaylistcontents($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CcPlaylistcontents relation
 * @method CcBlockQuery innerJoinCcPlaylistcontents($relationAlias = null) Adds a INNER JOIN clause to the query using the CcPlaylistcontents relation
 *
 * @method CcBlockQuery leftJoinCcBlockcontents($relationAlias = null) Adds a LEFT JOIN clause to the query using the CcBlockcontents relation
 * @method CcBlockQuery rightJoinCcBlockcontents($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CcBlockcontents relation
 * @method CcBlockQuery innerJoinCcBlockcontents($relationAlias = null) Adds a INNER JOIN clause to the query using the CcBlockcontents relation
 *
 * @method CcBlockQuery leftJoinCcBlockcriteria($relationAlias = null) Adds a LEFT JOIN clause to the query using the CcBlockcriteria relation
 * @method CcBlockQuery rightJoinCcBlockcriteria($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CcBlockcriteria relation
 * @method CcBlockQuery innerJoinCcBlockcriteria($relationAlias = null) Adds a INNER JOIN clause to the query using the CcBlockcriteria relation
 *
 * @method CcBlock findOne(PropelPDO $con = null) Return the first CcBlock matching the query
 * @method CcBlock findOneOrCreate(PropelPDO $con = null) Return the first CcBlock matching the query, or a new CcBlock object populated from the query conditions when no match is found
 *
 * @method CcBlock findOneByDbName(string $name) Return the first CcBlock filtered by the name column
 * @method CcBlock findOneByDbMtime(string $mtime) Return the first CcBlock filtered by the mtime column
 * @method CcBlock findOneByDbUtime(string $utime) Return the first CcBlock filtered by the utime column
 * @method CcBlock findOneByDbCreatorId(int $creator_id) Return the first CcBlock filtered by the creator_id column
 * @method CcBlock findOneByDbDescription(string $description) Return the first CcBlock filtered by the description column
 * @method CcBlock findOneByDbLength(string $length) Return the first CcBlock filtered by the length column
 * @method CcBlock findOneByDbType(string $type) Return the first CcBlock filtered by the type column
 *
 * @method array findByDbId(int $id) Return CcBlock objects filtered by the id column
 * @method array findByDbName(string $name) Return CcBlock objects filtered by the name column
 * @method array findByDbMtime(string $mtime) Return CcBlock objects filtered by the mtime column
 * @method array findByDbUtime(string $utime) Return CcBlock objects filtered by the utime column
 * @method array findByDbCreatorId(int $creator_id) Return CcBlock objects filtered by the creator_id column
 * @method array findByDbDescription(string $description) Return CcBlock objects filtered by the description column
 * @method array findByDbLength(string $length) Return CcBlock objects filtered by the length column
 * @method array findByDbType(string $type) Return CcBlock objects filtered by the type column
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcBlockQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseCcBlockQuery object.
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
            $modelName = 'CcBlock';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new CcBlockQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   CcBlockQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return CcBlockQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof CcBlockQuery) {
            return $criteria;
        }
        $query = new CcBlockQuery(null, null, $modelAlias);

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
     * @return   CcBlock|CcBlock[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = CcBlockPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(CcBlockPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 CcBlock A model object, or null if the key is not found
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
     * @return                 CcBlock A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT "id", "name", "mtime", "utime", "creator_id", "description", "length", "type" FROM "cc_block" WHERE "id" = :p0';
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
            $obj = new CcBlock();
            $obj->hydrate($row);
            CcBlockPeer::addInstanceToPool($obj, (string) $key);
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
     * @return CcBlock|CcBlock[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|CcBlock[]|mixed the list of results, formatted by the current formatter
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
     * @return CcBlockQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(CcBlockPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return CcBlockQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(CcBlockPeer::ID, $keys, Criteria::IN);
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
     * @return CcBlockQuery The current query, for fluid interface
     */
    public function filterByDbId($dbId = null, $comparison = null)
    {
        if (is_array($dbId)) {
            $useMinMax = false;
            if (isset($dbId['min'])) {
                $this->addUsingAlias(CcBlockPeer::ID, $dbId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dbId['max'])) {
                $this->addUsingAlias(CcBlockPeer::ID, $dbId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CcBlockPeer::ID, $dbId, $comparison);
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
     * @return CcBlockQuery The current query, for fluid interface
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

        return $this->addUsingAlias(CcBlockPeer::NAME, $dbName, $comparison);
    }

    /**
     * Filter the query on the mtime column
     *
     * Example usage:
     * <code>
     * $query->filterByDbMtime('2011-03-14'); // WHERE mtime = '2011-03-14'
     * $query->filterByDbMtime('now'); // WHERE mtime = '2011-03-14'
     * $query->filterByDbMtime(array('max' => 'yesterday')); // WHERE mtime < '2011-03-13'
     * </code>
     *
     * @param     mixed $dbMtime The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcBlockQuery The current query, for fluid interface
     */
    public function filterByDbMtime($dbMtime = null, $comparison = null)
    {
        if (is_array($dbMtime)) {
            $useMinMax = false;
            if (isset($dbMtime['min'])) {
                $this->addUsingAlias(CcBlockPeer::MTIME, $dbMtime['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dbMtime['max'])) {
                $this->addUsingAlias(CcBlockPeer::MTIME, $dbMtime['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CcBlockPeer::MTIME, $dbMtime, $comparison);
    }

    /**
     * Filter the query on the utime column
     *
     * Example usage:
     * <code>
     * $query->filterByDbUtime('2011-03-14'); // WHERE utime = '2011-03-14'
     * $query->filterByDbUtime('now'); // WHERE utime = '2011-03-14'
     * $query->filterByDbUtime(array('max' => 'yesterday')); // WHERE utime < '2011-03-13'
     * </code>
     *
     * @param     mixed $dbUtime The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcBlockQuery The current query, for fluid interface
     */
    public function filterByDbUtime($dbUtime = null, $comparison = null)
    {
        if (is_array($dbUtime)) {
            $useMinMax = false;
            if (isset($dbUtime['min'])) {
                $this->addUsingAlias(CcBlockPeer::UTIME, $dbUtime['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dbUtime['max'])) {
                $this->addUsingAlias(CcBlockPeer::UTIME, $dbUtime['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CcBlockPeer::UTIME, $dbUtime, $comparison);
    }

    /**
     * Filter the query on the creator_id column
     *
     * Example usage:
     * <code>
     * $query->filterByDbCreatorId(1234); // WHERE creator_id = 1234
     * $query->filterByDbCreatorId(array(12, 34)); // WHERE creator_id IN (12, 34)
     * $query->filterByDbCreatorId(array('min' => 12)); // WHERE creator_id >= 12
     * $query->filterByDbCreatorId(array('max' => 12)); // WHERE creator_id <= 12
     * </code>
     *
     * @see       filterByCcSubjs()
     *
     * @param     mixed $dbCreatorId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcBlockQuery The current query, for fluid interface
     */
    public function filterByDbCreatorId($dbCreatorId = null, $comparison = null)
    {
        if (is_array($dbCreatorId)) {
            $useMinMax = false;
            if (isset($dbCreatorId['min'])) {
                $this->addUsingAlias(CcBlockPeer::CREATOR_ID, $dbCreatorId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dbCreatorId['max'])) {
                $this->addUsingAlias(CcBlockPeer::CREATOR_ID, $dbCreatorId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CcBlockPeer::CREATOR_ID, $dbCreatorId, $comparison);
    }

    /**
     * Filter the query on the description column
     *
     * Example usage:
     * <code>
     * $query->filterByDbDescription('fooValue');   // WHERE description = 'fooValue'
     * $query->filterByDbDescription('%fooValue%'); // WHERE description LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbDescription The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcBlockQuery The current query, for fluid interface
     */
    public function filterByDbDescription($dbDescription = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($dbDescription)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $dbDescription)) {
                $dbDescription = str_replace('*', '%', $dbDescription);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CcBlockPeer::DESCRIPTION, $dbDescription, $comparison);
    }

    /**
     * Filter the query on the length column
     *
     * Example usage:
     * <code>
     * $query->filterByDbLength('fooValue');   // WHERE length = 'fooValue'
     * $query->filterByDbLength('%fooValue%'); // WHERE length LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbLength The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcBlockQuery The current query, for fluid interface
     */
    public function filterByDbLength($dbLength = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($dbLength)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $dbLength)) {
                $dbLength = str_replace('*', '%', $dbLength);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CcBlockPeer::LENGTH, $dbLength, $comparison);
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
     * @return CcBlockQuery The current query, for fluid interface
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

        return $this->addUsingAlias(CcBlockPeer::TYPE, $dbType, $comparison);
    }

    /**
     * Filter the query by a related CcSubjs object
     *
     * @param   CcSubjs|PropelObjectCollection $ccSubjs The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 CcBlockQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCcSubjs($ccSubjs, $comparison = null)
    {
        if ($ccSubjs instanceof CcSubjs) {
            return $this
                ->addUsingAlias(CcBlockPeer::CREATOR_ID, $ccSubjs->getDbId(), $comparison);
        } elseif ($ccSubjs instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(CcBlockPeer::CREATOR_ID, $ccSubjs->toKeyValue('PrimaryKey', 'DbId'), $comparison);
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
     * @return CcBlockQuery The current query, for fluid interface
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
     * Filter the query by a related CcPlaylistcontents object
     *
     * @param   CcPlaylistcontents|PropelObjectCollection $ccPlaylistcontents  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 CcBlockQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCcPlaylistcontents($ccPlaylistcontents, $comparison = null)
    {
        if ($ccPlaylistcontents instanceof CcPlaylistcontents) {
            return $this
                ->addUsingAlias(CcBlockPeer::ID, $ccPlaylistcontents->getDbBlockId(), $comparison);
        } elseif ($ccPlaylistcontents instanceof PropelObjectCollection) {
            return $this
                ->useCcPlaylistcontentsQuery()
                ->filterByPrimaryKeys($ccPlaylistcontents->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByCcPlaylistcontents() only accepts arguments of type CcPlaylistcontents or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CcPlaylistcontents relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return CcBlockQuery The current query, for fluid interface
     */
    public function joinCcPlaylistcontents($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CcPlaylistcontents');

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
            $this->addJoinObject($join, 'CcPlaylistcontents');
        }

        return $this;
    }

    /**
     * Use the CcPlaylistcontents relation CcPlaylistcontents object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   CcPlaylistcontentsQuery A secondary query class using the current class as primary query
     */
    public function useCcPlaylistcontentsQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinCcPlaylistcontents($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CcPlaylistcontents', 'CcPlaylistcontentsQuery');
    }

    /**
     * Filter the query by a related CcBlockcontents object
     *
     * @param   CcBlockcontents|PropelObjectCollection $ccBlockcontents  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 CcBlockQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCcBlockcontents($ccBlockcontents, $comparison = null)
    {
        if ($ccBlockcontents instanceof CcBlockcontents) {
            return $this
                ->addUsingAlias(CcBlockPeer::ID, $ccBlockcontents->getDbBlockId(), $comparison);
        } elseif ($ccBlockcontents instanceof PropelObjectCollection) {
            return $this
                ->useCcBlockcontentsQuery()
                ->filterByPrimaryKeys($ccBlockcontents->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByCcBlockcontents() only accepts arguments of type CcBlockcontents or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CcBlockcontents relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return CcBlockQuery The current query, for fluid interface
     */
    public function joinCcBlockcontents($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CcBlockcontents');

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
            $this->addJoinObject($join, 'CcBlockcontents');
        }

        return $this;
    }

    /**
     * Use the CcBlockcontents relation CcBlockcontents object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   CcBlockcontentsQuery A secondary query class using the current class as primary query
     */
    public function useCcBlockcontentsQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinCcBlockcontents($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CcBlockcontents', 'CcBlockcontentsQuery');
    }

    /**
     * Filter the query by a related CcBlockcriteria object
     *
     * @param   CcBlockcriteria|PropelObjectCollection $ccBlockcriteria  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 CcBlockQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCcBlockcriteria($ccBlockcriteria, $comparison = null)
    {
        if ($ccBlockcriteria instanceof CcBlockcriteria) {
            return $this
                ->addUsingAlias(CcBlockPeer::ID, $ccBlockcriteria->getDbBlockId(), $comparison);
        } elseif ($ccBlockcriteria instanceof PropelObjectCollection) {
            return $this
                ->useCcBlockcriteriaQuery()
                ->filterByPrimaryKeys($ccBlockcriteria->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByCcBlockcriteria() only accepts arguments of type CcBlockcriteria or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CcBlockcriteria relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return CcBlockQuery The current query, for fluid interface
     */
    public function joinCcBlockcriteria($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CcBlockcriteria');

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
            $this->addJoinObject($join, 'CcBlockcriteria');
        }

        return $this;
    }

    /**
     * Use the CcBlockcriteria relation CcBlockcriteria object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   CcBlockcriteriaQuery A secondary query class using the current class as primary query
     */
    public function useCcBlockcriteriaQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinCcBlockcriteria($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CcBlockcriteria', 'CcBlockcriteriaQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   CcBlock $ccBlock Object to remove from the list of results
     *
     * @return CcBlockQuery The current query, for fluid interface
     */
    public function prune($ccBlock = null)
    {
        if ($ccBlock) {
            $this->addUsingAlias(CcBlockPeer::ID, $ccBlock->getDbId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}
