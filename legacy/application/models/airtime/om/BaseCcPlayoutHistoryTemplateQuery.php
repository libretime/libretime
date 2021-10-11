<?php


/**
 * Base class that represents a query for the 'cc_playout_history_template' table.
 *
 *
 *
 * @method CcPlayoutHistoryTemplateQuery orderByDbId($order = Criteria::ASC) Order by the id column
 * @method CcPlayoutHistoryTemplateQuery orderByDbName($order = Criteria::ASC) Order by the name column
 * @method CcPlayoutHistoryTemplateQuery orderByDbType($order = Criteria::ASC) Order by the type column
 *
 * @method CcPlayoutHistoryTemplateQuery groupByDbId() Group by the id column
 * @method CcPlayoutHistoryTemplateQuery groupByDbName() Group by the name column
 * @method CcPlayoutHistoryTemplateQuery groupByDbType() Group by the type column
 *
 * @method CcPlayoutHistoryTemplateQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method CcPlayoutHistoryTemplateQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method CcPlayoutHistoryTemplateQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method CcPlayoutHistoryTemplateQuery leftJoinCcPlayoutHistoryTemplateField($relationAlias = null) Adds a LEFT JOIN clause to the query using the CcPlayoutHistoryTemplateField relation
 * @method CcPlayoutHistoryTemplateQuery rightJoinCcPlayoutHistoryTemplateField($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CcPlayoutHistoryTemplateField relation
 * @method CcPlayoutHistoryTemplateQuery innerJoinCcPlayoutHistoryTemplateField($relationAlias = null) Adds a INNER JOIN clause to the query using the CcPlayoutHistoryTemplateField relation
 *
 * @method CcPlayoutHistoryTemplate findOne(PropelPDO $con = null) Return the first CcPlayoutHistoryTemplate matching the query
 * @method CcPlayoutHistoryTemplate findOneOrCreate(PropelPDO $con = null) Return the first CcPlayoutHistoryTemplate matching the query, or a new CcPlayoutHistoryTemplate object populated from the query conditions when no match is found
 *
 * @method CcPlayoutHistoryTemplate findOneByDbName(string $name) Return the first CcPlayoutHistoryTemplate filtered by the name column
 * @method CcPlayoutHistoryTemplate findOneByDbType(string $type) Return the first CcPlayoutHistoryTemplate filtered by the type column
 *
 * @method array findByDbId(int $id) Return CcPlayoutHistoryTemplate objects filtered by the id column
 * @method array findByDbName(string $name) Return CcPlayoutHistoryTemplate objects filtered by the name column
 * @method array findByDbType(string $type) Return CcPlayoutHistoryTemplate objects filtered by the type column
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcPlayoutHistoryTemplateQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseCcPlayoutHistoryTemplateQuery object.
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
            $modelName = 'CcPlayoutHistoryTemplate';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new CcPlayoutHistoryTemplateQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   CcPlayoutHistoryTemplateQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return CcPlayoutHistoryTemplateQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof CcPlayoutHistoryTemplateQuery) {
            return $criteria;
        }
        $query = new CcPlayoutHistoryTemplateQuery(null, null, $modelAlias);

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
     * @return   CcPlayoutHistoryTemplate|CcPlayoutHistoryTemplate[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = CcPlayoutHistoryTemplatePeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(CcPlayoutHistoryTemplatePeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 CcPlayoutHistoryTemplate A model object, or null if the key is not found
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
     * @return                 CcPlayoutHistoryTemplate A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT "id", "name", "type" FROM "cc_playout_history_template" WHERE "id" = :p0';
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
            $obj = new CcPlayoutHistoryTemplate();
            $obj->hydrate($row);
            CcPlayoutHistoryTemplatePeer::addInstanceToPool($obj, (string) $key);
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
     * @return CcPlayoutHistoryTemplate|CcPlayoutHistoryTemplate[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|CcPlayoutHistoryTemplate[]|mixed the list of results, formatted by the current formatter
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
     * @return CcPlayoutHistoryTemplateQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(CcPlayoutHistoryTemplatePeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return CcPlayoutHistoryTemplateQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(CcPlayoutHistoryTemplatePeer::ID, $keys, Criteria::IN);
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
     * @return CcPlayoutHistoryTemplateQuery The current query, for fluid interface
     */
    public function filterByDbId($dbId = null, $comparison = null)
    {
        if (is_array($dbId)) {
            $useMinMax = false;
            if (isset($dbId['min'])) {
                $this->addUsingAlias(CcPlayoutHistoryTemplatePeer::ID, $dbId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dbId['max'])) {
                $this->addUsingAlias(CcPlayoutHistoryTemplatePeer::ID, $dbId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CcPlayoutHistoryTemplatePeer::ID, $dbId, $comparison);
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
     * @return CcPlayoutHistoryTemplateQuery The current query, for fluid interface
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

        return $this->addUsingAlias(CcPlayoutHistoryTemplatePeer::NAME, $dbName, $comparison);
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
     * @return CcPlayoutHistoryTemplateQuery The current query, for fluid interface
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

        return $this->addUsingAlias(CcPlayoutHistoryTemplatePeer::TYPE, $dbType, $comparison);
    }

    /**
     * Filter the query by a related CcPlayoutHistoryTemplateField object
     *
     * @param   CcPlayoutHistoryTemplateField|PropelObjectCollection $ccPlayoutHistoryTemplateField  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 CcPlayoutHistoryTemplateQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCcPlayoutHistoryTemplateField($ccPlayoutHistoryTemplateField, $comparison = null)
    {
        if ($ccPlayoutHistoryTemplateField instanceof CcPlayoutHistoryTemplateField) {
            return $this
                ->addUsingAlias(CcPlayoutHistoryTemplatePeer::ID, $ccPlayoutHistoryTemplateField->getDbTemplateId(), $comparison);
        } elseif ($ccPlayoutHistoryTemplateField instanceof PropelObjectCollection) {
            return $this
                ->useCcPlayoutHistoryTemplateFieldQuery()
                ->filterByPrimaryKeys($ccPlayoutHistoryTemplateField->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByCcPlayoutHistoryTemplateField() only accepts arguments of type CcPlayoutHistoryTemplateField or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CcPlayoutHistoryTemplateField relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return CcPlayoutHistoryTemplateQuery The current query, for fluid interface
     */
    public function joinCcPlayoutHistoryTemplateField($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CcPlayoutHistoryTemplateField');

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
            $this->addJoinObject($join, 'CcPlayoutHistoryTemplateField');
        }

        return $this;
    }

    /**
     * Use the CcPlayoutHistoryTemplateField relation CcPlayoutHistoryTemplateField object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   CcPlayoutHistoryTemplateFieldQuery A secondary query class using the current class as primary query
     */
    public function useCcPlayoutHistoryTemplateFieldQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinCcPlayoutHistoryTemplateField($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CcPlayoutHistoryTemplateField', 'CcPlayoutHistoryTemplateFieldQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   CcPlayoutHistoryTemplate $ccPlayoutHistoryTemplate Object to remove from the list of results
     *
     * @return CcPlayoutHistoryTemplateQuery The current query, for fluid interface
     */
    public function prune($ccPlayoutHistoryTemplate = null)
    {
        if ($ccPlayoutHistoryTemplate) {
            $this->addUsingAlias(CcPlayoutHistoryTemplatePeer::ID, $ccPlayoutHistoryTemplate->getDbId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}
