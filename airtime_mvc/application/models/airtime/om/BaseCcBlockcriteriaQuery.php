<?php


/**
 * Base class that represents a query for the 'cc_blockcriteria' table.
 *
 *
 *
 * @method CcBlockcriteriaQuery orderByDbId($order = Criteria::ASC) Order by the id column
 * @method CcBlockcriteriaQuery orderByDbCriteria($order = Criteria::ASC) Order by the criteria column
 * @method CcBlockcriteriaQuery orderByDbModifier($order = Criteria::ASC) Order by the modifier column
 * @method CcBlockcriteriaQuery orderByDbValue($order = Criteria::ASC) Order by the value column
 * @method CcBlockcriteriaQuery orderByDbExtra($order = Criteria::ASC) Order by the extra column
 * @method CcBlockcriteriaQuery orderByDbCriteriaGroup($order = Criteria::ASC) Order by the criteriagroup column
 * @method CcBlockcriteriaQuery orderByDbBlockId($order = Criteria::ASC) Order by the block_id column
 *
 * @method CcBlockcriteriaQuery groupByDbId() Group by the id column
 * @method CcBlockcriteriaQuery groupByDbCriteria() Group by the criteria column
 * @method CcBlockcriteriaQuery groupByDbModifier() Group by the modifier column
 * @method CcBlockcriteriaQuery groupByDbValue() Group by the value column
 * @method CcBlockcriteriaQuery groupByDbExtra() Group by the extra column
 * @method CcBlockcriteriaQuery groupByDbCriteriaGroup() Group by the criteriagroup column
 * @method CcBlockcriteriaQuery groupByDbBlockId() Group by the block_id column
 *
 * @method CcBlockcriteriaQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method CcBlockcriteriaQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method CcBlockcriteriaQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method CcBlockcriteriaQuery leftJoinCcBlock($relationAlias = null) Adds a LEFT JOIN clause to the query using the CcBlock relation
 * @method CcBlockcriteriaQuery rightJoinCcBlock($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CcBlock relation
 * @method CcBlockcriteriaQuery innerJoinCcBlock($relationAlias = null) Adds a INNER JOIN clause to the query using the CcBlock relation
 *
 * @method CcBlockcriteria findOne(PropelPDO $con = null) Return the first CcBlockcriteria matching the query
 * @method CcBlockcriteria findOneOrCreate(PropelPDO $con = null) Return the first CcBlockcriteria matching the query, or a new CcBlockcriteria object populated from the query conditions when no match is found
 *
 * @method CcBlockcriteria findOneByDbCriteria(string $criteria) Return the first CcBlockcriteria filtered by the criteria column
 * @method CcBlockcriteria findOneByDbModifier(string $modifier) Return the first CcBlockcriteria filtered by the modifier column
 * @method CcBlockcriteria findOneByDbValue(string $value) Return the first CcBlockcriteria filtered by the value column
 * @method CcBlockcriteria findOneByDbExtra(string $extra) Return the first CcBlockcriteria filtered by the extra column
 * @method CcBlockcriteria findOneByDbCriteriaGroup(int $criteriagroup) Return the first CcBlockcriteria filtered by the criteriagroup column
 * @method CcBlockcriteria findOneByDbBlockId(int $block_id) Return the first CcBlockcriteria filtered by the block_id column
 *
 * @method array findByDbId(int $id) Return CcBlockcriteria objects filtered by the id column
 * @method array findByDbCriteria(string $criteria) Return CcBlockcriteria objects filtered by the criteria column
 * @method array findByDbModifier(string $modifier) Return CcBlockcriteria objects filtered by the modifier column
 * @method array findByDbValue(string $value) Return CcBlockcriteria objects filtered by the value column
 * @method array findByDbExtra(string $extra) Return CcBlockcriteria objects filtered by the extra column
 * @method array findByDbCriteriaGroup(int $criteriagroup) Return CcBlockcriteria objects filtered by the criteriagroup column
 * @method array findByDbBlockId(int $block_id) Return CcBlockcriteria objects filtered by the block_id column
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcBlockcriteriaQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseCcBlockcriteriaQuery object.
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
            $modelName = 'CcBlockcriteria';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new CcBlockcriteriaQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   CcBlockcriteriaQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return CcBlockcriteriaQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof CcBlockcriteriaQuery) {
            return $criteria;
        }
        $query = new CcBlockcriteriaQuery(null, null, $modelAlias);

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
     * @return   CcBlockcriteria|CcBlockcriteria[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = CcBlockcriteriaPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(CcBlockcriteriaPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 CcBlockcriteria A model object, or null if the key is not found
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
     * @return                 CcBlockcriteria A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT "id", "criteria", "modifier", "value", "extra", "criteriagroup", "block_id" FROM "cc_blockcriteria" WHERE "id" = :p0';
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
            $obj = new CcBlockcriteria();
            $obj->hydrate($row);
            CcBlockcriteriaPeer::addInstanceToPool($obj, (string) $key);
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
     * @return CcBlockcriteria|CcBlockcriteria[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|CcBlockcriteria[]|mixed the list of results, formatted by the current formatter
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
     * @return CcBlockcriteriaQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(CcBlockcriteriaPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return CcBlockcriteriaQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(CcBlockcriteriaPeer::ID, $keys, Criteria::IN);
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
     * @return CcBlockcriteriaQuery The current query, for fluid interface
     */
    public function filterByDbId($dbId = null, $comparison = null)
    {
        if (is_array($dbId)) {
            $useMinMax = false;
            if (isset($dbId['min'])) {
                $this->addUsingAlias(CcBlockcriteriaPeer::ID, $dbId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dbId['max'])) {
                $this->addUsingAlias(CcBlockcriteriaPeer::ID, $dbId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CcBlockcriteriaPeer::ID, $dbId, $comparison);
    }

    /**
     * Filter the query on the criteria column
     *
     * Example usage:
     * <code>
     * $query->filterByDbCriteria('fooValue');   // WHERE criteria = 'fooValue'
     * $query->filterByDbCriteria('%fooValue%'); // WHERE criteria LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbCriteria The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcBlockcriteriaQuery The current query, for fluid interface
     */
    public function filterByDbCriteria($dbCriteria = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($dbCriteria)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $dbCriteria)) {
                $dbCriteria = str_replace('*', '%', $dbCriteria);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CcBlockcriteriaPeer::CRITERIA, $dbCriteria, $comparison);
    }

    /**
     * Filter the query on the modifier column
     *
     * Example usage:
     * <code>
     * $query->filterByDbModifier('fooValue');   // WHERE modifier = 'fooValue'
     * $query->filterByDbModifier('%fooValue%'); // WHERE modifier LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbModifier The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcBlockcriteriaQuery The current query, for fluid interface
     */
    public function filterByDbModifier($dbModifier = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($dbModifier)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $dbModifier)) {
                $dbModifier = str_replace('*', '%', $dbModifier);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CcBlockcriteriaPeer::MODIFIER, $dbModifier, $comparison);
    }

    /**
     * Filter the query on the value column
     *
     * Example usage:
     * <code>
     * $query->filterByDbValue('fooValue');   // WHERE value = 'fooValue'
     * $query->filterByDbValue('%fooValue%'); // WHERE value LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbValue The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcBlockcriteriaQuery The current query, for fluid interface
     */
    public function filterByDbValue($dbValue = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($dbValue)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $dbValue)) {
                $dbValue = str_replace('*', '%', $dbValue);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CcBlockcriteriaPeer::VALUE, $dbValue, $comparison);
    }

    /**
     * Filter the query on the extra column
     *
     * Example usage:
     * <code>
     * $query->filterByDbExtra('fooValue');   // WHERE extra = 'fooValue'
     * $query->filterByDbExtra('%fooValue%'); // WHERE extra LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbExtra The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcBlockcriteriaQuery The current query, for fluid interface
     */
    public function filterByDbExtra($dbExtra = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($dbExtra)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $dbExtra)) {
                $dbExtra = str_replace('*', '%', $dbExtra);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CcBlockcriteriaPeer::EXTRA, $dbExtra, $comparison);
    }

    /**
     * Filter the query on the criteriagroup column
     *
     * Example usage:
     * <code>
     * $query->filterByDbCriteriaGroup(1234); // WHERE criteriagroup = 1234
     * $query->filterByDbCriteriaGroup(array(12, 34)); // WHERE criteriagroup IN (12, 34)
     * $query->filterByDbCriteriaGroup(array('min' => 12)); // WHERE criteriagroup >= 12
     * $query->filterByDbCriteriaGroup(array('max' => 12)); // WHERE criteriagroup <= 12
     * </code>
     *
     * @param     mixed $dbCriteriaGroup The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcBlockcriteriaQuery The current query, for fluid interface
     */
    public function filterByDbCriteriaGroup($dbCriteriaGroup = null, $comparison = null)
    {
        if (is_array($dbCriteriaGroup)) {
            $useMinMax = false;
            if (isset($dbCriteriaGroup['min'])) {
                $this->addUsingAlias(CcBlockcriteriaPeer::CRITERIAGROUP, $dbCriteriaGroup['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dbCriteriaGroup['max'])) {
                $this->addUsingAlias(CcBlockcriteriaPeer::CRITERIAGROUP, $dbCriteriaGroup['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CcBlockcriteriaPeer::CRITERIAGROUP, $dbCriteriaGroup, $comparison);
    }

    /**
     * Filter the query on the block_id column
     *
     * Example usage:
     * <code>
     * $query->filterByDbBlockId(1234); // WHERE block_id = 1234
     * $query->filterByDbBlockId(array(12, 34)); // WHERE block_id IN (12, 34)
     * $query->filterByDbBlockId(array('min' => 12)); // WHERE block_id >= 12
     * $query->filterByDbBlockId(array('max' => 12)); // WHERE block_id <= 12
     * </code>
     *
     * @see       filterByCcBlock()
     *
     * @param     mixed $dbBlockId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcBlockcriteriaQuery The current query, for fluid interface
     */
    public function filterByDbBlockId($dbBlockId = null, $comparison = null)
    {
        if (is_array($dbBlockId)) {
            $useMinMax = false;
            if (isset($dbBlockId['min'])) {
                $this->addUsingAlias(CcBlockcriteriaPeer::BLOCK_ID, $dbBlockId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dbBlockId['max'])) {
                $this->addUsingAlias(CcBlockcriteriaPeer::BLOCK_ID, $dbBlockId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CcBlockcriteriaPeer::BLOCK_ID, $dbBlockId, $comparison);
    }

    /**
     * Filter the query by a related CcBlock object
     *
     * @param   CcBlock|PropelObjectCollection $ccBlock The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 CcBlockcriteriaQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCcBlock($ccBlock, $comparison = null)
    {
        if ($ccBlock instanceof CcBlock) {
            return $this
                ->addUsingAlias(CcBlockcriteriaPeer::BLOCK_ID, $ccBlock->getDbId(), $comparison);
        } elseif ($ccBlock instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(CcBlockcriteriaPeer::BLOCK_ID, $ccBlock->toKeyValue('PrimaryKey', 'DbId'), $comparison);
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
     * @return CcBlockcriteriaQuery The current query, for fluid interface
     */
    public function joinCcBlock($relationAlias = null, $joinType = Criteria::INNER_JOIN)
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
    public function useCcBlockQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinCcBlock($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CcBlock', 'CcBlockQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   CcBlockcriteria $ccBlockcriteria Object to remove from the list of results
     *
     * @return CcBlockcriteriaQuery The current query, for fluid interface
     */
    public function prune($ccBlockcriteria = null)
    {
        if ($ccBlockcriteria) {
            $this->addUsingAlias(CcBlockcriteriaPeer::ID, $ccBlockcriteria->getDbId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}
