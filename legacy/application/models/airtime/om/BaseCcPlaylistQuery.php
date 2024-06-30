<?php


/**
 * Base class that represents a query for the 'cc_playlist' table.
 *
 *
 *
 * @method CcPlaylistQuery orderByDbId($order = Criteria::ASC) Order by the id column
 * @method CcPlaylistQuery orderByDbName($order = Criteria::ASC) Order by the name column
 * @method CcPlaylistQuery orderByDbMtime($order = Criteria::ASC) Order by the mtime column
 * @method CcPlaylistQuery orderByDbUtime($order = Criteria::ASC) Order by the utime column
 * @method CcPlaylistQuery orderByDbCreatorId($order = Criteria::ASC) Order by the creator_id column
 * @method CcPlaylistQuery orderByDbDescription($order = Criteria::ASC) Order by the description column
 * @method CcPlaylistQuery orderByDbLength($order = Criteria::ASC) Order by the length column
 *
 * @method CcPlaylistQuery groupByDbId() Group by the id column
 * @method CcPlaylistQuery groupByDbName() Group by the name column
 * @method CcPlaylistQuery groupByDbMtime() Group by the mtime column
 * @method CcPlaylistQuery groupByDbUtime() Group by the utime column
 * @method CcPlaylistQuery groupByDbCreatorId() Group by the creator_id column
 * @method CcPlaylistQuery groupByDbDescription() Group by the description column
 * @method CcPlaylistQuery groupByDbLength() Group by the length column
 *
 * @method CcPlaylistQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method CcPlaylistQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method CcPlaylistQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method CcPlaylistQuery leftJoinCcSubjs($relationAlias = null) Adds a LEFT JOIN clause to the query using the CcSubjs relation
 * @method CcPlaylistQuery rightJoinCcSubjs($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CcSubjs relation
 * @method CcPlaylistQuery innerJoinCcSubjs($relationAlias = null) Adds a INNER JOIN clause to the query using the CcSubjs relation
 *
 * @method CcPlaylistQuery leftJoinCcShowRelatedByDbAutoPlaylistId($relationAlias = null) Adds a LEFT JOIN clause to the query using the CcShowRelatedByDbAutoPlaylistId relation
 * @method CcPlaylistQuery rightJoinCcShowRelatedByDbAutoPlaylistId($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CcShowRelatedByDbAutoPlaylistId relation
 * @method CcPlaylistQuery innerJoinCcShowRelatedByDbAutoPlaylistId($relationAlias = null) Adds a INNER JOIN clause to the query using the CcShowRelatedByDbAutoPlaylistId relation
 *
 * @method CcPlaylistQuery leftJoinCcShowRelatedByDbIntroPlaylistId($relationAlias = null) Adds a LEFT JOIN clause to the query using the CcShowRelatedByDbIntroPlaylistId relation
 * @method CcPlaylistQuery rightJoinCcShowRelatedByDbIntroPlaylistId($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CcShowRelatedByDbIntroPlaylistId relation
 * @method CcPlaylistQuery innerJoinCcShowRelatedByDbIntroPlaylistId($relationAlias = null) Adds a INNER JOIN clause to the query using the CcShowRelatedByDbIntroPlaylistId relation
 *
 * @method CcPlaylistQuery leftJoinCcShowRelatedByDbOutroPlaylistId($relationAlias = null) Adds a LEFT JOIN clause to the query using the CcShowRelatedByDbOutroPlaylistId relation
 * @method CcPlaylistQuery rightJoinCcShowRelatedByDbOutroPlaylistId($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CcShowRelatedByDbOutroPlaylistId relation
 * @method CcPlaylistQuery innerJoinCcShowRelatedByDbOutroPlaylistId($relationAlias = null) Adds a INNER JOIN clause to the query using the CcShowRelatedByDbOutroPlaylistId relation
 *
 * @method CcPlaylistQuery leftJoinCcPlaylistcontents($relationAlias = null) Adds a LEFT JOIN clause to the query using the CcPlaylistcontents relation
 * @method CcPlaylistQuery rightJoinCcPlaylistcontents($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CcPlaylistcontents relation
 * @method CcPlaylistQuery innerJoinCcPlaylistcontents($relationAlias = null) Adds a INNER JOIN clause to the query using the CcPlaylistcontents relation
 *
 * @method CcPlaylist findOne(PropelPDO $con = null) Return the first CcPlaylist matching the query
 * @method CcPlaylist findOneOrCreate(PropelPDO $con = null) Return the first CcPlaylist matching the query, or a new CcPlaylist object populated from the query conditions when no match is found
 *
 * @method CcPlaylist findOneByDbName(string $name) Return the first CcPlaylist filtered by the name column
 * @method CcPlaylist findOneByDbMtime(string $mtime) Return the first CcPlaylist filtered by the mtime column
 * @method CcPlaylist findOneByDbUtime(string $utime) Return the first CcPlaylist filtered by the utime column
 * @method CcPlaylist findOneByDbCreatorId(int $creator_id) Return the first CcPlaylist filtered by the creator_id column
 * @method CcPlaylist findOneByDbDescription(string $description) Return the first CcPlaylist filtered by the description column
 * @method CcPlaylist findOneByDbLength(string $length) Return the first CcPlaylist filtered by the length column
 *
 * @method array findByDbId(int $id) Return CcPlaylist objects filtered by the id column
 * @method array findByDbName(string $name) Return CcPlaylist objects filtered by the name column
 * @method array findByDbMtime(string $mtime) Return CcPlaylist objects filtered by the mtime column
 * @method array findByDbUtime(string $utime) Return CcPlaylist objects filtered by the utime column
 * @method array findByDbCreatorId(int $creator_id) Return CcPlaylist objects filtered by the creator_id column
 * @method array findByDbDescription(string $description) Return CcPlaylist objects filtered by the description column
 * @method array findByDbLength(string $length) Return CcPlaylist objects filtered by the length column
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcPlaylistQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseCcPlaylistQuery object.
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
            $modelName = 'CcPlaylist';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new CcPlaylistQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   CcPlaylistQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return CcPlaylistQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof CcPlaylistQuery) {
            return $criteria;
        }
        $query = new CcPlaylistQuery(null, null, $modelAlias);

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
     * @return   CcPlaylist|CcPlaylist[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = CcPlaylistPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(CcPlaylistPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 CcPlaylist A model object, or null if the key is not found
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
     * @return                 CcPlaylist A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT "id", "name", "mtime", "utime", "creator_id", "description", "length" FROM "cc_playlist" WHERE "id" = :p0';
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
            $obj = new CcPlaylist();
            $obj->hydrate($row);
            CcPlaylistPeer::addInstanceToPool($obj, (string) $key);
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
     * @return CcPlaylist|CcPlaylist[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|CcPlaylist[]|mixed the list of results, formatted by the current formatter
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
     * @return CcPlaylistQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(CcPlaylistPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return CcPlaylistQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(CcPlaylistPeer::ID, $keys, Criteria::IN);
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
     * @return CcPlaylistQuery The current query, for fluid interface
     */
    public function filterByDbId($dbId = null, $comparison = null)
    {
        if (is_array($dbId)) {
            $useMinMax = false;
            if (isset($dbId['min'])) {
                $this->addUsingAlias(CcPlaylistPeer::ID, $dbId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dbId['max'])) {
                $this->addUsingAlias(CcPlaylistPeer::ID, $dbId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CcPlaylistPeer::ID, $dbId, $comparison);
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
     * @return CcPlaylistQuery The current query, for fluid interface
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

        return $this->addUsingAlias(CcPlaylistPeer::NAME, $dbName, $comparison);
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
     * @return CcPlaylistQuery The current query, for fluid interface
     */
    public function filterByDbMtime($dbMtime = null, $comparison = null)
    {
        if (is_array($dbMtime)) {
            $useMinMax = false;
            if (isset($dbMtime['min'])) {
                $this->addUsingAlias(CcPlaylistPeer::MTIME, $dbMtime['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dbMtime['max'])) {
                $this->addUsingAlias(CcPlaylistPeer::MTIME, $dbMtime['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CcPlaylistPeer::MTIME, $dbMtime, $comparison);
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
     * @return CcPlaylistQuery The current query, for fluid interface
     */
    public function filterByDbUtime($dbUtime = null, $comparison = null)
    {
        if (is_array($dbUtime)) {
            $useMinMax = false;
            if (isset($dbUtime['min'])) {
                $this->addUsingAlias(CcPlaylistPeer::UTIME, $dbUtime['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dbUtime['max'])) {
                $this->addUsingAlias(CcPlaylistPeer::UTIME, $dbUtime['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CcPlaylistPeer::UTIME, $dbUtime, $comparison);
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
     * @return CcPlaylistQuery The current query, for fluid interface
     */
    public function filterByDbCreatorId($dbCreatorId = null, $comparison = null)
    {
        if (is_array($dbCreatorId)) {
            $useMinMax = false;
            if (isset($dbCreatorId['min'])) {
                $this->addUsingAlias(CcPlaylistPeer::CREATOR_ID, $dbCreatorId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dbCreatorId['max'])) {
                $this->addUsingAlias(CcPlaylistPeer::CREATOR_ID, $dbCreatorId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CcPlaylistPeer::CREATOR_ID, $dbCreatorId, $comparison);
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
     * @return CcPlaylistQuery The current query, for fluid interface
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

        return $this->addUsingAlias(CcPlaylistPeer::DESCRIPTION, $dbDescription, $comparison);
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
     * @return CcPlaylistQuery The current query, for fluid interface
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

        return $this->addUsingAlias(CcPlaylistPeer::LENGTH, $dbLength, $comparison);
    }

    /**
     * Filter the query by a related CcSubjs object
     *
     * @param   CcSubjs|PropelObjectCollection $ccSubjs The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 CcPlaylistQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCcSubjs($ccSubjs, $comparison = null)
    {
        if ($ccSubjs instanceof CcSubjs) {
            return $this
                ->addUsingAlias(CcPlaylistPeer::CREATOR_ID, $ccSubjs->getDbId(), $comparison);
        } elseif ($ccSubjs instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(CcPlaylistPeer::CREATOR_ID, $ccSubjs->toKeyValue('PrimaryKey', 'DbId'), $comparison);
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
     * @return CcPlaylistQuery The current query, for fluid interface
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
     * Filter the query by a related CcShow object
     *
     * @param   CcShow|PropelObjectCollection $ccShow  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 CcPlaylistQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCcShowRelatedByDbAutoPlaylistId($ccShow, $comparison = null)
    {
        if ($ccShow instanceof CcShow) {
            return $this
                ->addUsingAlias(CcPlaylistPeer::ID, $ccShow->getDbAutoPlaylistId(), $comparison);
        } elseif ($ccShow instanceof PropelObjectCollection) {
            return $this
                ->useCcShowRelatedByDbAutoPlaylistIdQuery()
                ->filterByPrimaryKeys($ccShow->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByCcShowRelatedByDbAutoPlaylistId() only accepts arguments of type CcShow or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CcShowRelatedByDbAutoPlaylistId relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return CcPlaylistQuery The current query, for fluid interface
     */
    public function joinCcShowRelatedByDbAutoPlaylistId($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CcShowRelatedByDbAutoPlaylistId');

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
            $this->addJoinObject($join, 'CcShowRelatedByDbAutoPlaylistId');
        }

        return $this;
    }

    /**
     * Use the CcShowRelatedByDbAutoPlaylistId relation CcShow object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   CcShowQuery A secondary query class using the current class as primary query
     */
    public function useCcShowRelatedByDbAutoPlaylistIdQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinCcShowRelatedByDbAutoPlaylistId($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CcShowRelatedByDbAutoPlaylistId', 'CcShowQuery');
    }

    /**
     * Filter the query by a related CcShow object
     *
     * @param   CcShow|PropelObjectCollection $ccShow  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 CcPlaylistQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCcShowRelatedByDbIntroPlaylistId($ccShow, $comparison = null)
    {
        if ($ccShow instanceof CcShow) {
            return $this
                ->addUsingAlias(CcPlaylistPeer::ID, $ccShow->getDbIntroPlaylistId(), $comparison);
        } elseif ($ccShow instanceof PropelObjectCollection) {
            return $this
                ->useCcShowRelatedByDbIntroPlaylistIdQuery()
                ->filterByPrimaryKeys($ccShow->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByCcShowRelatedByDbIntroPlaylistId() only accepts arguments of type CcShow or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CcShowRelatedByDbIntroPlaylistId relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return CcPlaylistQuery The current query, for fluid interface
     */
    public function joinCcShowRelatedByDbIntroPlaylistId($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CcShowRelatedByDbIntroPlaylistId');

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
            $this->addJoinObject($join, 'CcShowRelatedByDbIntroPlaylistId');
        }

        return $this;
    }

    /**
     * Use the CcShowRelatedByDbIntroPlaylistId relation CcShow object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   CcShowQuery A secondary query class using the current class as primary query
     */
    public function useCcShowRelatedByDbIntroPlaylistIdQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinCcShowRelatedByDbIntroPlaylistId($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CcShowRelatedByDbIntroPlaylistId', 'CcShowQuery');
    }

    /**
     * Filter the query by a related CcShow object
     *
     * @param   CcShow|PropelObjectCollection $ccShow  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 CcPlaylistQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCcShowRelatedByDbOutroPlaylistId($ccShow, $comparison = null)
    {
        if ($ccShow instanceof CcShow) {
            return $this
                ->addUsingAlias(CcPlaylistPeer::ID, $ccShow->getDbOutroPlaylistId(), $comparison);
        } elseif ($ccShow instanceof PropelObjectCollection) {
            return $this
                ->useCcShowRelatedByDbOutroPlaylistIdQuery()
                ->filterByPrimaryKeys($ccShow->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByCcShowRelatedByDbOutroPlaylistId() only accepts arguments of type CcShow or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CcShowRelatedByDbOutroPlaylistId relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return CcPlaylistQuery The current query, for fluid interface
     */
    public function joinCcShowRelatedByDbOutroPlaylistId($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CcShowRelatedByDbOutroPlaylistId');

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
            $this->addJoinObject($join, 'CcShowRelatedByDbOutroPlaylistId');
        }

        return $this;
    }

    /**
     * Use the CcShowRelatedByDbOutroPlaylistId relation CcShow object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   CcShowQuery A secondary query class using the current class as primary query
     */
    public function useCcShowRelatedByDbOutroPlaylistIdQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinCcShowRelatedByDbOutroPlaylistId($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CcShowRelatedByDbOutroPlaylistId', 'CcShowQuery');
    }

    /**
     * Filter the query by a related CcPlaylistcontents object
     *
     * @param   CcPlaylistcontents|PropelObjectCollection $ccPlaylistcontents  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 CcPlaylistQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCcPlaylistcontents($ccPlaylistcontents, $comparison = null)
    {
        if ($ccPlaylistcontents instanceof CcPlaylistcontents) {
            return $this
                ->addUsingAlias(CcPlaylistPeer::ID, $ccPlaylistcontents->getDbPlaylistId(), $comparison);
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
     * @return CcPlaylistQuery The current query, for fluid interface
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
     * Exclude object from result
     *
     * @param   CcPlaylist $ccPlaylist Object to remove from the list of results
     *
     * @return CcPlaylistQuery The current query, for fluid interface
     */
    public function prune($ccPlaylist = null)
    {
        if ($ccPlaylist) {
            $this->addUsingAlias(CcPlaylistPeer::ID, $ccPlaylist->getDbId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}
