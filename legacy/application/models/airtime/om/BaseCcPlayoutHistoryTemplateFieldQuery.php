<?php


/**
 * Base class that represents a query for the 'cc_playout_history_template_field' table.
 *
 *
 *
 * @method CcPlayoutHistoryTemplateFieldQuery orderByDbId($order = Criteria::ASC) Order by the id column
 * @method CcPlayoutHistoryTemplateFieldQuery orderByDbTemplateId($order = Criteria::ASC) Order by the template_id column
 * @method CcPlayoutHistoryTemplateFieldQuery orderByDbName($order = Criteria::ASC) Order by the name column
 * @method CcPlayoutHistoryTemplateFieldQuery orderByDbLabel($order = Criteria::ASC) Order by the label column
 * @method CcPlayoutHistoryTemplateFieldQuery orderByDbType($order = Criteria::ASC) Order by the type column
 * @method CcPlayoutHistoryTemplateFieldQuery orderByDbIsFileMD($order = Criteria::ASC) Order by the is_file_md column
 * @method CcPlayoutHistoryTemplateFieldQuery orderByDbPosition($order = Criteria::ASC) Order by the position column
 *
 * @method CcPlayoutHistoryTemplateFieldQuery groupByDbId() Group by the id column
 * @method CcPlayoutHistoryTemplateFieldQuery groupByDbTemplateId() Group by the template_id column
 * @method CcPlayoutHistoryTemplateFieldQuery groupByDbName() Group by the name column
 * @method CcPlayoutHistoryTemplateFieldQuery groupByDbLabel() Group by the label column
 * @method CcPlayoutHistoryTemplateFieldQuery groupByDbType() Group by the type column
 * @method CcPlayoutHistoryTemplateFieldQuery groupByDbIsFileMD() Group by the is_file_md column
 * @method CcPlayoutHistoryTemplateFieldQuery groupByDbPosition() Group by the position column
 *
 * @method CcPlayoutHistoryTemplateFieldQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method CcPlayoutHistoryTemplateFieldQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method CcPlayoutHistoryTemplateFieldQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method CcPlayoutHistoryTemplateFieldQuery leftJoinCcPlayoutHistoryTemplate($relationAlias = null) Adds a LEFT JOIN clause to the query using the CcPlayoutHistoryTemplate relation
 * @method CcPlayoutHistoryTemplateFieldQuery rightJoinCcPlayoutHistoryTemplate($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CcPlayoutHistoryTemplate relation
 * @method CcPlayoutHistoryTemplateFieldQuery innerJoinCcPlayoutHistoryTemplate($relationAlias = null) Adds a INNER JOIN clause to the query using the CcPlayoutHistoryTemplate relation
 *
 * @method CcPlayoutHistoryTemplateField findOne(PropelPDO $con = null) Return the first CcPlayoutHistoryTemplateField matching the query
 * @method CcPlayoutHistoryTemplateField findOneOrCreate(PropelPDO $con = null) Return the first CcPlayoutHistoryTemplateField matching the query, or a new CcPlayoutHistoryTemplateField object populated from the query conditions when no match is found
 *
 * @method CcPlayoutHistoryTemplateField findOneByDbTemplateId(int $template_id) Return the first CcPlayoutHistoryTemplateField filtered by the template_id column
 * @method CcPlayoutHistoryTemplateField findOneByDbName(string $name) Return the first CcPlayoutHistoryTemplateField filtered by the name column
 * @method CcPlayoutHistoryTemplateField findOneByDbLabel(string $label) Return the first CcPlayoutHistoryTemplateField filtered by the label column
 * @method CcPlayoutHistoryTemplateField findOneByDbType(string $type) Return the first CcPlayoutHistoryTemplateField filtered by the type column
 * @method CcPlayoutHistoryTemplateField findOneByDbIsFileMD(boolean $is_file_md) Return the first CcPlayoutHistoryTemplateField filtered by the is_file_md column
 * @method CcPlayoutHistoryTemplateField findOneByDbPosition(int $position) Return the first CcPlayoutHistoryTemplateField filtered by the position column
 *
 * @method array findByDbId(int $id) Return CcPlayoutHistoryTemplateField objects filtered by the id column
 * @method array findByDbTemplateId(int $template_id) Return CcPlayoutHistoryTemplateField objects filtered by the template_id column
 * @method array findByDbName(string $name) Return CcPlayoutHistoryTemplateField objects filtered by the name column
 * @method array findByDbLabel(string $label) Return CcPlayoutHistoryTemplateField objects filtered by the label column
 * @method array findByDbType(string $type) Return CcPlayoutHistoryTemplateField objects filtered by the type column
 * @method array findByDbIsFileMD(boolean $is_file_md) Return CcPlayoutHistoryTemplateField objects filtered by the is_file_md column
 * @method array findByDbPosition(int $position) Return CcPlayoutHistoryTemplateField objects filtered by the position column
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcPlayoutHistoryTemplateFieldQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseCcPlayoutHistoryTemplateFieldQuery object.
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
            $modelName = 'CcPlayoutHistoryTemplateField';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new CcPlayoutHistoryTemplateFieldQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   CcPlayoutHistoryTemplateFieldQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return CcPlayoutHistoryTemplateFieldQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof CcPlayoutHistoryTemplateFieldQuery) {
            return $criteria;
        }
        $query = new CcPlayoutHistoryTemplateFieldQuery(null, null, $modelAlias);

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
     * @return   CcPlayoutHistoryTemplateField|CcPlayoutHistoryTemplateField[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = CcPlayoutHistoryTemplateFieldPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(CcPlayoutHistoryTemplateFieldPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 CcPlayoutHistoryTemplateField A model object, or null if the key is not found
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
     * @return                 CcPlayoutHistoryTemplateField A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT "id", "template_id", "name", "label", "type", "is_file_md", "position" FROM "cc_playout_history_template_field" WHERE "id" = :p0';
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
            $obj = new CcPlayoutHistoryTemplateField();
            $obj->hydrate($row);
            CcPlayoutHistoryTemplateFieldPeer::addInstanceToPool($obj, (string) $key);
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
     * @return CcPlayoutHistoryTemplateField|CcPlayoutHistoryTemplateField[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|CcPlayoutHistoryTemplateField[]|mixed the list of results, formatted by the current formatter
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
     * @return CcPlayoutHistoryTemplateFieldQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(CcPlayoutHistoryTemplateFieldPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return CcPlayoutHistoryTemplateFieldQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(CcPlayoutHistoryTemplateFieldPeer::ID, $keys, Criteria::IN);
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
     * @return CcPlayoutHistoryTemplateFieldQuery The current query, for fluid interface
     */
    public function filterByDbId($dbId = null, $comparison = null)
    {
        if (is_array($dbId)) {
            $useMinMax = false;
            if (isset($dbId['min'])) {
                $this->addUsingAlias(CcPlayoutHistoryTemplateFieldPeer::ID, $dbId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dbId['max'])) {
                $this->addUsingAlias(CcPlayoutHistoryTemplateFieldPeer::ID, $dbId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CcPlayoutHistoryTemplateFieldPeer::ID, $dbId, $comparison);
    }

    /**
     * Filter the query on the template_id column
     *
     * Example usage:
     * <code>
     * $query->filterByDbTemplateId(1234); // WHERE template_id = 1234
     * $query->filterByDbTemplateId(array(12, 34)); // WHERE template_id IN (12, 34)
     * $query->filterByDbTemplateId(array('min' => 12)); // WHERE template_id >= 12
     * $query->filterByDbTemplateId(array('max' => 12)); // WHERE template_id <= 12
     * </code>
     *
     * @see       filterByCcPlayoutHistoryTemplate()
     *
     * @param     mixed $dbTemplateId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcPlayoutHistoryTemplateFieldQuery The current query, for fluid interface
     */
    public function filterByDbTemplateId($dbTemplateId = null, $comparison = null)
    {
        if (is_array($dbTemplateId)) {
            $useMinMax = false;
            if (isset($dbTemplateId['min'])) {
                $this->addUsingAlias(CcPlayoutHistoryTemplateFieldPeer::TEMPLATE_ID, $dbTemplateId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dbTemplateId['max'])) {
                $this->addUsingAlias(CcPlayoutHistoryTemplateFieldPeer::TEMPLATE_ID, $dbTemplateId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CcPlayoutHistoryTemplateFieldPeer::TEMPLATE_ID, $dbTemplateId, $comparison);
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
     * @return CcPlayoutHistoryTemplateFieldQuery The current query, for fluid interface
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

        return $this->addUsingAlias(CcPlayoutHistoryTemplateFieldPeer::NAME, $dbName, $comparison);
    }

    /**
     * Filter the query on the label column
     *
     * Example usage:
     * <code>
     * $query->filterByDbLabel('fooValue');   // WHERE label = 'fooValue'
     * $query->filterByDbLabel('%fooValue%'); // WHERE label LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbLabel The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcPlayoutHistoryTemplateFieldQuery The current query, for fluid interface
     */
    public function filterByDbLabel($dbLabel = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($dbLabel)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $dbLabel)) {
                $dbLabel = str_replace('*', '%', $dbLabel);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CcPlayoutHistoryTemplateFieldPeer::LABEL, $dbLabel, $comparison);
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
     * @return CcPlayoutHistoryTemplateFieldQuery The current query, for fluid interface
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

        return $this->addUsingAlias(CcPlayoutHistoryTemplateFieldPeer::TYPE, $dbType, $comparison);
    }

    /**
     * Filter the query on the is_file_md column
     *
     * Example usage:
     * <code>
     * $query->filterByDbIsFileMD(true); // WHERE is_file_md = true
     * $query->filterByDbIsFileMD('yes'); // WHERE is_file_md = true
     * </code>
     *
     * @param     boolean|string $dbIsFileMD The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcPlayoutHistoryTemplateFieldQuery The current query, for fluid interface
     */
    public function filterByDbIsFileMD($dbIsFileMD = null, $comparison = null)
    {
        if (is_string($dbIsFileMD)) {
            $dbIsFileMD = in_array(strtolower($dbIsFileMD), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(CcPlayoutHistoryTemplateFieldPeer::IS_FILE_MD, $dbIsFileMD, $comparison);
    }

    /**
     * Filter the query on the position column
     *
     * Example usage:
     * <code>
     * $query->filterByDbPosition(1234); // WHERE position = 1234
     * $query->filterByDbPosition(array(12, 34)); // WHERE position IN (12, 34)
     * $query->filterByDbPosition(array('min' => 12)); // WHERE position >= 12
     * $query->filterByDbPosition(array('max' => 12)); // WHERE position <= 12
     * </code>
     *
     * @param     mixed $dbPosition The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcPlayoutHistoryTemplateFieldQuery The current query, for fluid interface
     */
    public function filterByDbPosition($dbPosition = null, $comparison = null)
    {
        if (is_array($dbPosition)) {
            $useMinMax = false;
            if (isset($dbPosition['min'])) {
                $this->addUsingAlias(CcPlayoutHistoryTemplateFieldPeer::POSITION, $dbPosition['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dbPosition['max'])) {
                $this->addUsingAlias(CcPlayoutHistoryTemplateFieldPeer::POSITION, $dbPosition['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CcPlayoutHistoryTemplateFieldPeer::POSITION, $dbPosition, $comparison);
    }

    /**
     * Filter the query by a related CcPlayoutHistoryTemplate object
     *
     * @param   CcPlayoutHistoryTemplate|PropelObjectCollection $ccPlayoutHistoryTemplate The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 CcPlayoutHistoryTemplateFieldQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCcPlayoutHistoryTemplate($ccPlayoutHistoryTemplate, $comparison = null)
    {
        if ($ccPlayoutHistoryTemplate instanceof CcPlayoutHistoryTemplate) {
            return $this
                ->addUsingAlias(CcPlayoutHistoryTemplateFieldPeer::TEMPLATE_ID, $ccPlayoutHistoryTemplate->getDbId(), $comparison);
        } elseif ($ccPlayoutHistoryTemplate instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(CcPlayoutHistoryTemplateFieldPeer::TEMPLATE_ID, $ccPlayoutHistoryTemplate->toKeyValue('PrimaryKey', 'DbId'), $comparison);
        } else {
            throw new PropelException('filterByCcPlayoutHistoryTemplate() only accepts arguments of type CcPlayoutHistoryTemplate or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CcPlayoutHistoryTemplate relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return CcPlayoutHistoryTemplateFieldQuery The current query, for fluid interface
     */
    public function joinCcPlayoutHistoryTemplate($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CcPlayoutHistoryTemplate');

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
            $this->addJoinObject($join, 'CcPlayoutHistoryTemplate');
        }

        return $this;
    }

    /**
     * Use the CcPlayoutHistoryTemplate relation CcPlayoutHistoryTemplate object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   CcPlayoutHistoryTemplateQuery A secondary query class using the current class as primary query
     */
    public function useCcPlayoutHistoryTemplateQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinCcPlayoutHistoryTemplate($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CcPlayoutHistoryTemplate', 'CcPlayoutHistoryTemplateQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   CcPlayoutHistoryTemplateField $ccPlayoutHistoryTemplateField Object to remove from the list of results
     *
     * @return CcPlayoutHistoryTemplateFieldQuery The current query, for fluid interface
     */
    public function prune($ccPlayoutHistoryTemplateField = null)
    {
        if ($ccPlayoutHistoryTemplateField) {
            $this->addUsingAlias(CcPlayoutHistoryTemplateFieldPeer::ID, $ccPlayoutHistoryTemplateField->getDbId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}
