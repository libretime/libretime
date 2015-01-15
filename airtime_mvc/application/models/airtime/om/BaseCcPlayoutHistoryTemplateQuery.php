<?php


/**
 * Base class that represents a query for the 'cc_playout_history_template' table.
 *
 * 
 *
 * @method     CcPlayoutHistoryTemplateQuery orderByDbId($order = Criteria::ASC) Order by the id column
 * @method     CcPlayoutHistoryTemplateQuery orderByDbName($order = Criteria::ASC) Order by the name column
 * @method     CcPlayoutHistoryTemplateQuery orderByDbType($order = Criteria::ASC) Order by the type column
 *
 * @method     CcPlayoutHistoryTemplateQuery groupByDbId() Group by the id column
 * @method     CcPlayoutHistoryTemplateQuery groupByDbName() Group by the name column
 * @method     CcPlayoutHistoryTemplateQuery groupByDbType() Group by the type column
 *
 * @method     CcPlayoutHistoryTemplateQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     CcPlayoutHistoryTemplateQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     CcPlayoutHistoryTemplateQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     CcPlayoutHistoryTemplateQuery leftJoinCcPlayoutHistoryTemplateField($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcPlayoutHistoryTemplateField relation
 * @method     CcPlayoutHistoryTemplateQuery rightJoinCcPlayoutHistoryTemplateField($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcPlayoutHistoryTemplateField relation
 * @method     CcPlayoutHistoryTemplateQuery innerJoinCcPlayoutHistoryTemplateField($relationAlias = '') Adds a INNER JOIN clause to the query using the CcPlayoutHistoryTemplateField relation
 *
 * @method     CcPlayoutHistoryTemplate findOne(PropelPDO $con = null) Return the first CcPlayoutHistoryTemplate matching the query
 * @method     CcPlayoutHistoryTemplate findOneOrCreate(PropelPDO $con = null) Return the first CcPlayoutHistoryTemplate matching the query, or a new CcPlayoutHistoryTemplate object populated from the query conditions when no match is found
 *
 * @method     CcPlayoutHistoryTemplate findOneByDbId(int $id) Return the first CcPlayoutHistoryTemplate filtered by the id column
 * @method     CcPlayoutHistoryTemplate findOneByDbName(string $name) Return the first CcPlayoutHistoryTemplate filtered by the name column
 * @method     CcPlayoutHistoryTemplate findOneByDbType(string $type) Return the first CcPlayoutHistoryTemplate filtered by the type column
 *
 * @method     array findByDbId(int $id) Return CcPlayoutHistoryTemplate objects filtered by the id column
 * @method     array findByDbName(string $name) Return CcPlayoutHistoryTemplate objects filtered by the name column
 * @method     array findByDbType(string $type) Return CcPlayoutHistoryTemplate objects filtered by the type column
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
	public function __construct($dbName = 'airtime', $modelName = 'CcPlayoutHistoryTemplate', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new CcPlayoutHistoryTemplateQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    CcPlayoutHistoryTemplateQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof CcPlayoutHistoryTemplateQuery) {
			return $criteria;
		}
		$query = new CcPlayoutHistoryTemplateQuery();
		if (null !== $modelAlias) {
			$query->setModelAlias($modelAlias);
		}
		if ($criteria instanceof Criteria) {
			$query->mergeWith($criteria);
		}
		return $query;
	}

	/**
	 * Find object by primary key
	 * Use instance pooling to avoid a database query if the object exists
	 * <code>
	 * $obj  = $c->findPk(12, $con);
	 * </code>
	 * @param     mixed $key Primary key to use for the query
	 * @param     PropelPDO $con an optional connection object
	 *
	 * @return    CcPlayoutHistoryTemplate|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = CcPlayoutHistoryTemplatePeer::getInstanceFromPool((string) $key))) && $this->getFormatter()->isObjectFormatter()) {
			// the object is alredy in the instance pool
			return $obj;
		} else {
			// the object has not been requested yet, or the formatter is not an object formatter
			$criteria = $this->isKeepQuery() ? clone $this : $this;
			$stmt = $criteria
				->filterByPrimaryKey($key)
				->getSelectStatement($con);
			return $criteria->getFormatter()->init($criteria)->formatOne($stmt);
		}
	}

	/**
	 * Find objects by primary key
	 * <code>
	 * $objs = $c->findPks(array(12, 56, 832), $con);
	 * </code>
	 * @param     array $keys Primary keys to use for the query
	 * @param     PropelPDO $con an optional connection object
	 *
	 * @return    PropelObjectCollection|array|mixed the list of results, formatted by the current formatter
	 */
	public function findPks($keys, $con = null)
	{	
		$criteria = $this->isKeepQuery() ? clone $this : $this;
		return $this
			->filterByPrimaryKeys($keys)
			->find($con);
	}

	/**
	 * Filter the query by primary key
	 *
	 * @param     mixed $key Primary key to use for the query
	 *
	 * @return    CcPlayoutHistoryTemplateQuery The current query, for fluid interface
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
	 * @return    CcPlayoutHistoryTemplateQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(CcPlayoutHistoryTemplatePeer::ID, $keys, Criteria::IN);
	}

	/**
	 * Filter the query on the id column
	 * 
	 * @param     int|array $dbId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcPlayoutHistoryTemplateQuery The current query, for fluid interface
	 */
	public function filterByDbId($dbId = null, $comparison = null)
	{
		if (is_array($dbId) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(CcPlayoutHistoryTemplatePeer::ID, $dbId, $comparison);
	}

	/**
	 * Filter the query on the name column
	 * 
	 * @param     string $dbName The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcPlayoutHistoryTemplateQuery The current query, for fluid interface
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
	 * @param     string $dbType The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcPlayoutHistoryTemplateQuery The current query, for fluid interface
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
	 * @param     CcPlayoutHistoryTemplateField $ccPlayoutHistoryTemplateField  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcPlayoutHistoryTemplateQuery The current query, for fluid interface
	 */
	public function filterByCcPlayoutHistoryTemplateField($ccPlayoutHistoryTemplateField, $comparison = null)
	{
		return $this
			->addUsingAlias(CcPlayoutHistoryTemplatePeer::ID, $ccPlayoutHistoryTemplateField->getDbTemplateId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcPlayoutHistoryTemplateField relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcPlayoutHistoryTemplateQuery The current query, for fluid interface
	 */
	public function joinCcPlayoutHistoryTemplateField($relationAlias = '', $joinType = Criteria::INNER_JOIN)
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
		if($relationAlias) {
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
	 * @return    CcPlayoutHistoryTemplateFieldQuery A secondary query class using the current class as primary query
	 */
	public function useCcPlayoutHistoryTemplateFieldQuery($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinCcPlayoutHistoryTemplateField($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CcPlayoutHistoryTemplateField', 'CcPlayoutHistoryTemplateFieldQuery');
	}

	/**
	 * Exclude object from result
	 *
	 * @param     CcPlayoutHistoryTemplate $ccPlayoutHistoryTemplate Object to remove from the list of results
	 *
	 * @return    CcPlayoutHistoryTemplateQuery The current query, for fluid interface
	 */
	public function prune($ccPlayoutHistoryTemplate = null)
	{
		if ($ccPlayoutHistoryTemplate) {
			$this->addUsingAlias(CcPlayoutHistoryTemplatePeer::ID, $ccPlayoutHistoryTemplate->getDbId(), Criteria::NOT_EQUAL);
	  }
	  
		return $this;
	}

} // BaseCcPlayoutHistoryTemplateQuery
