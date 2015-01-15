<?php


/**
 * Base class that represents a query for the 'cc_perms' table.
 *
 * 
 *
 * @method     CcPermsQuery orderByPermid($order = Criteria::ASC) Order by the permid column
 * @method     CcPermsQuery orderBySubj($order = Criteria::ASC) Order by the subj column
 * @method     CcPermsQuery orderByAction($order = Criteria::ASC) Order by the action column
 * @method     CcPermsQuery orderByObj($order = Criteria::ASC) Order by the obj column
 * @method     CcPermsQuery orderByType($order = Criteria::ASC) Order by the type column
 *
 * @method     CcPermsQuery groupByPermid() Group by the permid column
 * @method     CcPermsQuery groupBySubj() Group by the subj column
 * @method     CcPermsQuery groupByAction() Group by the action column
 * @method     CcPermsQuery groupByObj() Group by the obj column
 * @method     CcPermsQuery groupByType() Group by the type column
 *
 * @method     CcPermsQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     CcPermsQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     CcPermsQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     CcPermsQuery leftJoinCcSubjs($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcSubjs relation
 * @method     CcPermsQuery rightJoinCcSubjs($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcSubjs relation
 * @method     CcPermsQuery innerJoinCcSubjs($relationAlias = '') Adds a INNER JOIN clause to the query using the CcSubjs relation
 *
 * @method     CcPerms findOne(PropelPDO $con = null) Return the first CcPerms matching the query
 * @method     CcPerms findOneOrCreate(PropelPDO $con = null) Return the first CcPerms matching the query, or a new CcPerms object populated from the query conditions when no match is found
 *
 * @method     CcPerms findOneByPermid(int $permid) Return the first CcPerms filtered by the permid column
 * @method     CcPerms findOneBySubj(int $subj) Return the first CcPerms filtered by the subj column
 * @method     CcPerms findOneByAction(string $action) Return the first CcPerms filtered by the action column
 * @method     CcPerms findOneByObj(int $obj) Return the first CcPerms filtered by the obj column
 * @method     CcPerms findOneByType(string $type) Return the first CcPerms filtered by the type column
 *
 * @method     array findByPermid(int $permid) Return CcPerms objects filtered by the permid column
 * @method     array findBySubj(int $subj) Return CcPerms objects filtered by the subj column
 * @method     array findByAction(string $action) Return CcPerms objects filtered by the action column
 * @method     array findByObj(int $obj) Return CcPerms objects filtered by the obj column
 * @method     array findByType(string $type) Return CcPerms objects filtered by the type column
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcPermsQuery extends ModelCriteria
{

	/**
	 * Initializes internal state of BaseCcPermsQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'airtime', $modelName = 'CcPerms', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new CcPermsQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    CcPermsQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof CcPermsQuery) {
			return $criteria;
		}
		$query = new CcPermsQuery();
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
	 * @return    CcPerms|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = CcPermsPeer::getInstanceFromPool((string) $key))) && $this->getFormatter()->isObjectFormatter()) {
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
	 * @return    CcPermsQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(CcPermsPeer::PERMID, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    CcPermsQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(CcPermsPeer::PERMID, $keys, Criteria::IN);
	}

	/**
	 * Filter the query on the permid column
	 * 
	 * @param     int|array $permid The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcPermsQuery The current query, for fluid interface
	 */
	public function filterByPermid($permid = null, $comparison = null)
	{
		if (is_array($permid) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(CcPermsPeer::PERMID, $permid, $comparison);
	}

	/**
	 * Filter the query on the subj column
	 * 
	 * @param     int|array $subj The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcPermsQuery The current query, for fluid interface
	 */
	public function filterBySubj($subj = null, $comparison = null)
	{
		if (is_array($subj)) {
			$useMinMax = false;
			if (isset($subj['min'])) {
				$this->addUsingAlias(CcPermsPeer::SUBJ, $subj['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($subj['max'])) {
				$this->addUsingAlias(CcPermsPeer::SUBJ, $subj['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcPermsPeer::SUBJ, $subj, $comparison);
	}

	/**
	 * Filter the query on the action column
	 * 
	 * @param     string $action The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcPermsQuery The current query, for fluid interface
	 */
	public function filterByAction($action = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($action)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $action)) {
				$action = str_replace('*', '%', $action);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcPermsPeer::ACTION, $action, $comparison);
	}

	/**
	 * Filter the query on the obj column
	 * 
	 * @param     int|array $obj The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcPermsQuery The current query, for fluid interface
	 */
	public function filterByObj($obj = null, $comparison = null)
	{
		if (is_array($obj)) {
			$useMinMax = false;
			if (isset($obj['min'])) {
				$this->addUsingAlias(CcPermsPeer::OBJ, $obj['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($obj['max'])) {
				$this->addUsingAlias(CcPermsPeer::OBJ, $obj['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcPermsPeer::OBJ, $obj, $comparison);
	}

	/**
	 * Filter the query on the type column
	 * 
	 * @param     string $type The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcPermsQuery The current query, for fluid interface
	 */
	public function filterByType($type = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($type)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $type)) {
				$type = str_replace('*', '%', $type);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcPermsPeer::TYPE, $type, $comparison);
	}

	/**
	 * Filter the query by a related CcSubjs object
	 *
	 * @param     CcSubjs $ccSubjs  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcPermsQuery The current query, for fluid interface
	 */
	public function filterByCcSubjs($ccSubjs, $comparison = null)
	{
		return $this
			->addUsingAlias(CcPermsPeer::SUBJ, $ccSubjs->getDbId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcSubjs relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcPermsQuery The current query, for fluid interface
	 */
	public function joinCcSubjs($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
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
		if($relationAlias) {
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
	 * @return    CcSubjsQuery A secondary query class using the current class as primary query
	 */
	public function useCcSubjsQuery($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinCcSubjs($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CcSubjs', 'CcSubjsQuery');
	}

	/**
	 * Exclude object from result
	 *
	 * @param     CcPerms $ccPerms Object to remove from the list of results
	 *
	 * @return    CcPermsQuery The current query, for fluid interface
	 */
	public function prune($ccPerms = null)
	{
		if ($ccPerms) {
			$this->addUsingAlias(CcPermsPeer::PERMID, $ccPerms->getPermid(), Criteria::NOT_EQUAL);
	  }
	  
		return $this;
	}

} // BaseCcPermsQuery
