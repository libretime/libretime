<?php


/**
 * Base class that represents a query for the 'cc_blockcriteria' table.
 *
 * 
 *
 * @method     CcBlockcriteriaQuery orderByDbId($order = Criteria::ASC) Order by the id column
 * @method     CcBlockcriteriaQuery orderByDbCriteria($order = Criteria::ASC) Order by the criteria column
 * @method     CcBlockcriteriaQuery orderByDbModifier($order = Criteria::ASC) Order by the modifier column
 * @method     CcBlockcriteriaQuery orderByDbValue($order = Criteria::ASC) Order by the value column
 * @method     CcBlockcriteriaQuery orderByDbExtra($order = Criteria::ASC) Order by the extra column
 * @method     CcBlockcriteriaQuery orderByDbBlockId($order = Criteria::ASC) Order by the block_id column
 *
 * @method     CcBlockcriteriaQuery groupByDbId() Group by the id column
 * @method     CcBlockcriteriaQuery groupByDbCriteria() Group by the criteria column
 * @method     CcBlockcriteriaQuery groupByDbModifier() Group by the modifier column
 * @method     CcBlockcriteriaQuery groupByDbValue() Group by the value column
 * @method     CcBlockcriteriaQuery groupByDbExtra() Group by the extra column
 * @method     CcBlockcriteriaQuery groupByDbBlockId() Group by the block_id column
 *
 * @method     CcBlockcriteriaQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     CcBlockcriteriaQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     CcBlockcriteriaQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     CcBlockcriteriaQuery leftJoinCcBlock($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcBlock relation
 * @method     CcBlockcriteriaQuery rightJoinCcBlock($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcBlock relation
 * @method     CcBlockcriteriaQuery innerJoinCcBlock($relationAlias = '') Adds a INNER JOIN clause to the query using the CcBlock relation
 *
 * @method     CcBlockcriteria findOne(PropelPDO $con = null) Return the first CcBlockcriteria matching the query
 * @method     CcBlockcriteria findOneOrCreate(PropelPDO $con = null) Return the first CcBlockcriteria matching the query, or a new CcBlockcriteria object populated from the query conditions when no match is found
 *
 * @method     CcBlockcriteria findOneByDbId(int $id) Return the first CcBlockcriteria filtered by the id column
 * @method     CcBlockcriteria findOneByDbCriteria(string $criteria) Return the first CcBlockcriteria filtered by the criteria column
 * @method     CcBlockcriteria findOneByDbModifier(string $modifier) Return the first CcBlockcriteria filtered by the modifier column
 * @method     CcBlockcriteria findOneByDbValue(string $value) Return the first CcBlockcriteria filtered by the value column
 * @method     CcBlockcriteria findOneByDbExtra(string $extra) Return the first CcBlockcriteria filtered by the extra column
 * @method     CcBlockcriteria findOneByDbBlockId(int $block_id) Return the first CcBlockcriteria filtered by the block_id column
 *
 * @method     array findByDbId(int $id) Return CcBlockcriteria objects filtered by the id column
 * @method     array findByDbCriteria(string $criteria) Return CcBlockcriteria objects filtered by the criteria column
 * @method     array findByDbModifier(string $modifier) Return CcBlockcriteria objects filtered by the modifier column
 * @method     array findByDbValue(string $value) Return CcBlockcriteria objects filtered by the value column
 * @method     array findByDbExtra(string $extra) Return CcBlockcriteria objects filtered by the extra column
 * @method     array findByDbBlockId(int $block_id) Return CcBlockcriteria objects filtered by the block_id column
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
	public function __construct($dbName = 'airtime', $modelName = 'CcBlockcriteria', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new CcBlockcriteriaQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    CcBlockcriteriaQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof CcBlockcriteriaQuery) {
			return $criteria;
		}
		$query = new CcBlockcriteriaQuery();
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
	 * @return    CcBlockcriteria|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = CcBlockcriteriaPeer::getInstanceFromPool((string) $key))) && $this->getFormatter()->isObjectFormatter()) {
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
	 * @return    CcBlockcriteriaQuery The current query, for fluid interface
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
	 * @return    CcBlockcriteriaQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(CcBlockcriteriaPeer::ID, $keys, Criteria::IN);
	}

	/**
	 * Filter the query on the id column
	 * 
	 * @param     int|array $dbId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcBlockcriteriaQuery The current query, for fluid interface
	 */
	public function filterByDbId($dbId = null, $comparison = null)
	{
		if (is_array($dbId) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(CcBlockcriteriaPeer::ID, $dbId, $comparison);
	}

	/**
	 * Filter the query on the criteria column
	 * 
	 * @param     string $dbCriteria The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcBlockcriteriaQuery The current query, for fluid interface
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
	 * @param     string $dbModifier The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcBlockcriteriaQuery The current query, for fluid interface
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
	 * @param     string $dbValue The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcBlockcriteriaQuery The current query, for fluid interface
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
	 * @param     string $dbExtra The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcBlockcriteriaQuery The current query, for fluid interface
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
	 * Filter the query on the block_id column
	 * 
	 * @param     int|array $dbBlockId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcBlockcriteriaQuery The current query, for fluid interface
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
	 * @param     CcBlock $ccBlock  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcBlockcriteriaQuery The current query, for fluid interface
	 */
	public function filterByCcBlock($ccBlock, $comparison = null)
	{
		return $this
			->addUsingAlias(CcBlockcriteriaPeer::BLOCK_ID, $ccBlock->getDbId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcBlock relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcBlockcriteriaQuery The current query, for fluid interface
	 */
	public function joinCcBlock($relationAlias = '', $joinType = Criteria::INNER_JOIN)
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
		if($relationAlias) {
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
	 * @return    CcBlockQuery A secondary query class using the current class as primary query
	 */
	public function useCcBlockQuery($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinCcBlock($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CcBlock', 'CcBlockQuery');
	}

	/**
	 * Exclude object from result
	 *
	 * @param     CcBlockcriteria $ccBlockcriteria Object to remove from the list of results
	 *
	 * @return    CcBlockcriteriaQuery The current query, for fluid interface
	 */
	public function prune($ccBlockcriteria = null)
	{
		if ($ccBlockcriteria) {
			$this->addUsingAlias(CcBlockcriteriaPeer::ID, $ccBlockcriteria->getDbId(), Criteria::NOT_EQUAL);
	  }
	  
		return $this;
	}

} // BaseCcBlockcriteriaQuery
