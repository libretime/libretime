<?php


/**
 * Base class that represents a query for the 'cc_show_hosts' table.
 *
 * 
 *
 * @method     CcShowHostsQuery orderByDbId($order = Criteria::ASC) Order by the id column
 * @method     CcShowHostsQuery orderByDbShow($order = Criteria::ASC) Order by the show_id column
 * @method     CcShowHostsQuery orderByDbHost($order = Criteria::ASC) Order by the subjs_id column
 *
 * @method     CcShowHostsQuery groupByDbId() Group by the id column
 * @method     CcShowHostsQuery groupByDbShow() Group by the show_id column
 * @method     CcShowHostsQuery groupByDbHost() Group by the subjs_id column
 *
 * @method     CcShowHostsQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     CcShowHostsQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     CcShowHostsQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     CcShowHostsQuery leftJoinCcShow($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcShow relation
 * @method     CcShowHostsQuery rightJoinCcShow($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcShow relation
 * @method     CcShowHostsQuery innerJoinCcShow($relationAlias = '') Adds a INNER JOIN clause to the query using the CcShow relation
 *
 * @method     CcShowHostsQuery leftJoinCcSubjs($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcSubjs relation
 * @method     CcShowHostsQuery rightJoinCcSubjs($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcSubjs relation
 * @method     CcShowHostsQuery innerJoinCcSubjs($relationAlias = '') Adds a INNER JOIN clause to the query using the CcSubjs relation
 *
 * @method     CcShowHosts findOne(PropelPDO $con = null) Return the first CcShowHosts matching the query
 * @method     CcShowHosts findOneOrCreate(PropelPDO $con = null) Return the first CcShowHosts matching the query, or a new CcShowHosts object populated from the query conditions when no match is found
 *
 * @method     CcShowHosts findOneByDbId(int $id) Return the first CcShowHosts filtered by the id column
 * @method     CcShowHosts findOneByDbShow(int $show_id) Return the first CcShowHosts filtered by the show_id column
 * @method     CcShowHosts findOneByDbHost(int $subjs_id) Return the first CcShowHosts filtered by the subjs_id column
 *
 * @method     array findByDbId(int $id) Return CcShowHosts objects filtered by the id column
 * @method     array findByDbShow(int $show_id) Return CcShowHosts objects filtered by the show_id column
 * @method     array findByDbHost(int $subjs_id) Return CcShowHosts objects filtered by the subjs_id column
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcShowHostsQuery extends ModelCriteria
{

	/**
	 * Initializes internal state of BaseCcShowHostsQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'airtime', $modelName = 'CcShowHosts', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new CcShowHostsQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    CcShowHostsQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof CcShowHostsQuery) {
			return $criteria;
		}
		$query = new CcShowHostsQuery();
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
	 * @return    CcShowHosts|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = CcShowHostsPeer::getInstanceFromPool((string) $key))) && $this->getFormatter()->isObjectFormatter()) {
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
	 * @return    CcShowHostsQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(CcShowHostsPeer::ID, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    CcShowHostsQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(CcShowHostsPeer::ID, $keys, Criteria::IN);
	}

	/**
	 * Filter the query on the id column
	 * 
	 * @param     int|array $dbId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcShowHostsQuery The current query, for fluid interface
	 */
	public function filterByDbId($dbId = null, $comparison = null)
	{
		if (is_array($dbId) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(CcShowHostsPeer::ID, $dbId, $comparison);
	}

	/**
	 * Filter the query on the show_id column
	 * 
	 * @param     int|array $dbShow The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcShowHostsQuery The current query, for fluid interface
	 */
	public function filterByDbShow($dbShow = null, $comparison = null)
	{
		if (is_array($dbShow)) {
			$useMinMax = false;
			if (isset($dbShow['min'])) {
				$this->addUsingAlias(CcShowHostsPeer::SHOW_ID, $dbShow['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbShow['max'])) {
				$this->addUsingAlias(CcShowHostsPeer::SHOW_ID, $dbShow['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcShowHostsPeer::SHOW_ID, $dbShow, $comparison);
	}

	/**
	 * Filter the query on the subjs_id column
	 * 
	 * @param     int|array $dbHost The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcShowHostsQuery The current query, for fluid interface
	 */
	public function filterByDbHost($dbHost = null, $comparison = null)
	{
		if (is_array($dbHost)) {
			$useMinMax = false;
			if (isset($dbHost['min'])) {
				$this->addUsingAlias(CcShowHostsPeer::SUBJS_ID, $dbHost['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbHost['max'])) {
				$this->addUsingAlias(CcShowHostsPeer::SUBJS_ID, $dbHost['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcShowHostsPeer::SUBJS_ID, $dbHost, $comparison);
	}

	/**
	 * Filter the query by a related CcShow object
	 *
	 * @param     CcShow $ccShow  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcShowHostsQuery The current query, for fluid interface
	 */
	public function filterByCcShow($ccShow, $comparison = null)
	{
		return $this
			->addUsingAlias(CcShowHostsPeer::SHOW_ID, $ccShow->getDbId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcShow relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcShowHostsQuery The current query, for fluid interface
	 */
	public function joinCcShow($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('CcShow');
		
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
			$this->addJoinObject($join, 'CcShow');
		}
		
		return $this;
	}

	/**
	 * Use the CcShow relation CcShow object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcShowQuery A secondary query class using the current class as primary query
	 */
	public function useCcShowQuery($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinCcShow($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CcShow', 'CcShowQuery');
	}

	/**
	 * Filter the query by a related CcSubjs object
	 *
	 * @param     CcSubjs $ccSubjs  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcShowHostsQuery The current query, for fluid interface
	 */
	public function filterByCcSubjs($ccSubjs, $comparison = null)
	{
		return $this
			->addUsingAlias(CcShowHostsPeer::SUBJS_ID, $ccSubjs->getDbId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcSubjs relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcShowHostsQuery The current query, for fluid interface
	 */
	public function joinCcSubjs($relationAlias = '', $joinType = Criteria::INNER_JOIN)
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
	public function useCcSubjsQuery($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinCcSubjs($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CcSubjs', 'CcSubjsQuery');
	}

	/**
	 * Exclude object from result
	 *
	 * @param     CcShowHosts $ccShowHosts Object to remove from the list of results
	 *
	 * @return    CcShowHostsQuery The current query, for fluid interface
	 */
	public function prune($ccShowHosts = null)
	{
		if ($ccShowHosts) {
			$this->addUsingAlias(CcShowHostsPeer::ID, $ccShowHosts->getDbId(), Criteria::NOT_EQUAL);
	  }
	  
		return $this;
	}

} // BaseCcShowHostsQuery
