<?php


/**
 * Base class that represents a query for the 'cc_track_types' table.
 *
 *
 *
 * @method CcTracktypesQuery orderByDbId($order = Criteria::ASC) Order by the id column
 * @method CcTracktypesQuery orderByDbCode($order = Criteria::ASC) Order by the code column
 * @method CcTracktypesQuery orderByDbVisibility($order = Criteria::ASC) Order by the visibility column
 * @method CcTracktypesQuery orderByDbTypeName($order = Criteria::ASC) Order by the type_name column
 * @method CcTracktypesQuery orderByDbDescription($order = Criteria::ASC) Order by the description column
 * @method CcTracktypesQuery orderByDbAnalyzeCuePoints($order = Criteria::ASC) Order by the analyze_cue_points column
 *
 * @method CcTracktypesQuery groupByDbId() Group by the id column
 * @method CcTracktypesQuery groupByDbCode() Group by the code column
 * @method CcTracktypesQuery groupByDbVisibility() Group by the visibility column
 * @method CcTracktypesQuery groupByDbTypeName() Group by the type_name column
 * @method CcTracktypesQuery groupByDbDescription() Group by the description column
 * @method CcTracktypesQuery groupByDbAnalyzeCuePoints() Group by the analyze_cue_points column
 *
 * @method CcTracktypesQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method CcTracktypesQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method CcTracktypesQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method CcTracktypesQuery leftJoinCcFiles($relationAlias = null) Adds a LEFT JOIN clause to the query using the CcFiles relation
 * @method CcTracktypesQuery rightJoinCcFiles($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CcFiles relation
 * @method CcTracktypesQuery innerJoinCcFiles($relationAlias = null) Adds a INNER JOIN clause to the query using the CcFiles relation
 *
 * @method CcTracktypes findOne(PropelPDO $con = null) Return the first CcTracktypes matching the query
 * @method CcTracktypes findOneOrCreate(PropelPDO $con = null) Return the first CcTracktypes matching the query, or a new CcTracktypes object populated from the query conditions when no match is found
 *
 * @method CcTracktypes findOneByDbCode(string $code) Return the first CcTracktypes filtered by the code column
 * @method CcTracktypes findOneByDbVisibility(boolean $visibility) Return the first CcTracktypes filtered by the visibility column
 * @method CcTracktypes findOneByDbTypeName(string $type_name) Return the first CcTracktypes filtered by the type_name column
 * @method CcTracktypes findOneByDbDescription(string $description) Return the first CcTracktypes filtered by the description column
 * @method CcTracktypes findOneByDbAnalyzeCuePoints(boolean $analyze_cue_points) Return the first CcTracktypes filtered by the analyze_cue_points column
 *
 * @method array findByDbId(int $id) Return CcTracktypes objects filtered by the id column
 * @method array findByDbCode(string $code) Return CcTracktypes objects filtered by the code column
 * @method array findByDbVisibility(boolean $visibility) Return CcTracktypes objects filtered by the visibility column
 * @method array findByDbTypeName(string $type_name) Return CcTracktypes objects filtered by the type_name column
 * @method array findByDbDescription(string $description) Return CcTracktypes objects filtered by the description column
 * @method array findByDbAnalyzeCuePoints(boolean $analyze_cue_points) Return CcTracktypes objects filtered by the analyze_cue_points column
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcTracktypesQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseCcTracktypesQuery object.
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
            $modelName = 'CcTracktypes';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new CcTracktypesQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   CcTracktypesQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return CcTracktypesQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof CcTracktypesQuery) {
            return $criteria;
        }
        $query = new CcTracktypesQuery(null, null, $modelAlias);

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
     * @return   CcTracktypes|CcTracktypes[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = CcTracktypesPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(CcTracktypesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 CcTracktypes A model object, or null if the key is not found
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
     * @return                 CcTracktypes A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT "id", "code", "visibility", "type_name", "description", "analyze_cue_points" FROM "cc_track_types" WHERE "id" = :p0';
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
            $obj = new CcTracktypes();
            $obj->hydrate($row);
            CcTracktypesPeer::addInstanceToPool($obj, (string) $key);
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
     * @return CcTracktypes|CcTracktypes[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|CcTracktypes[]|mixed the list of results, formatted by the current formatter
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
     * @return CcTracktypesQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(CcTracktypesPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return CcTracktypesQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(CcTracktypesPeer::ID, $keys, Criteria::IN);
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
     * @return CcTracktypesQuery The current query, for fluid interface
     */
    public function filterByDbId($dbId = null, $comparison = null)
    {
        if (is_array($dbId)) {
            $useMinMax = false;
            if (isset($dbId['min'])) {
                $this->addUsingAlias(CcTracktypesPeer::ID, $dbId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dbId['max'])) {
                $this->addUsingAlias(CcTracktypesPeer::ID, $dbId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CcTracktypesPeer::ID, $dbId, $comparison);
    }

    /**
     * Filter the query on the code column
     *
     * Example usage:
     * <code>
     * $query->filterByDbCode('fooValue');   // WHERE code = 'fooValue'
     * $query->filterByDbCode('%fooValue%'); // WHERE code LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbCode The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcTracktypesQuery The current query, for fluid interface
     */
    public function filterByDbCode($dbCode = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($dbCode)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $dbCode)) {
                $dbCode = str_replace('*', '%', $dbCode);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CcTracktypesPeer::CODE, $dbCode, $comparison);
    }

    /**
     * Filter the query on the visibility column
     *
     * Example usage:
     * <code>
     * $query->filterByDbVisibility(true); // WHERE visibility = true
     * $query->filterByDbVisibility('yes'); // WHERE visibility = true
     * </code>
     *
     * @param     boolean|string $dbVisibility The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcTracktypesQuery The current query, for fluid interface
     */
    public function filterByDbVisibility($dbVisibility = null, $comparison = null)
    {
        if (is_string($dbVisibility)) {
            $dbVisibility = in_array(strtolower($dbVisibility), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(CcTracktypesPeer::VISIBILITY, $dbVisibility, $comparison);
    }

    /**
     * Filter the query on the type_name column
     *
     * Example usage:
     * <code>
     * $query->filterByDbTypeName('fooValue');   // WHERE type_name = 'fooValue'
     * $query->filterByDbTypeName('%fooValue%'); // WHERE type_name LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbTypeName The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcTracktypesQuery The current query, for fluid interface
     */
    public function filterByDbTypeName($dbTypeName = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($dbTypeName)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $dbTypeName)) {
                $dbTypeName = str_replace('*', '%', $dbTypeName);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CcTracktypesPeer::TYPE_NAME, $dbTypeName, $comparison);
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
     * @return CcTracktypesQuery The current query, for fluid interface
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

        return $this->addUsingAlias(CcTracktypesPeer::DESCRIPTION, $dbDescription, $comparison);
    }

    /**
     * Filter the query on the analyze_cue_points column
     *
     * Example usage:
     * <code>
     * $query->filterByDbAnalyzeCuePoints(true); // WHERE analyze_cue_points = true
     * $query->filterByDbAnalyzeCuePoints('yes'); // WHERE analyze_cue_points = true
     * </code>
     *
     * @param     boolean|string $dbAnalyzeCuePoints The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcTracktypesQuery The current query, for fluid interface
     */
    public function filterByDbAnalyzeCuePoints($dbAnalyzeCuePoints = null, $comparison = null)
    {
        if (is_string($dbAnalyzeCuePoints)) {
            $dbAnalyzeCuePoints = in_array(strtolower($dbAnalyzeCuePoints), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(CcTracktypesPeer::ANALYZE_CUE_POINTS, $dbAnalyzeCuePoints, $comparison);
    }

    /**
     * Filter the query by a related CcFiles object
     *
     * @param   CcFiles|PropelObjectCollection $ccFiles  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 CcTracktypesQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCcFiles($ccFiles, $comparison = null)
    {
        if ($ccFiles instanceof CcFiles) {
            return $this
                ->addUsingAlias(CcTracktypesPeer::ID, $ccFiles->getDbTrackTypeId(), $comparison);
        } elseif ($ccFiles instanceof PropelObjectCollection) {
            return $this
                ->useCcFilesQuery()
                ->filterByPrimaryKeys($ccFiles->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByCcFiles() only accepts arguments of type CcFiles or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CcFiles relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return CcTracktypesQuery The current query, for fluid interface
     */
    public function joinCcFiles($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CcFiles');

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
            $this->addJoinObject($join, 'CcFiles');
        }

        return $this;
    }

    /**
     * Use the CcFiles relation CcFiles object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   CcFilesQuery A secondary query class using the current class as primary query
     */
    public function useCcFilesQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinCcFiles($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CcFiles', 'CcFilesQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   CcTracktypes $ccTracktypes Object to remove from the list of results
     *
     * @return CcTracktypesQuery The current query, for fluid interface
     */
    public function prune($ccTracktypes = null)
    {
        if ($ccTracktypes) {
            $this->addUsingAlias(CcTracktypesPeer::ID, $ccTracktypes->getDbId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}
