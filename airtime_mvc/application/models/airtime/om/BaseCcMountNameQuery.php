<?php


/**
 * Base class that represents a query for the 'cc_mount_name' table.
 *
 * 
 *
 * @method     CcMountNameQuery orderByDbId($order = Criteria::ASC) Order by the id column
 * @method     CcMountNameQuery orderByDbMountName($order = Criteria::ASC) Order by the mount_name column
 *
 * @method     CcMountNameQuery groupByDbId() Group by the id column
 * @method     CcMountNameQuery groupByDbMountName() Group by the mount_name column
 *
 * @method     CcMountNameQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     CcMountNameQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     CcMountNameQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     CcMountNameQuery leftJoinCcListenerCount($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcListenerCount relation
 * @method     CcMountNameQuery rightJoinCcListenerCount($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcListenerCount relation
 * @method     CcMountNameQuery innerJoinCcListenerCount($relationAlias = '') Adds a INNER JOIN clause to the query using the CcListenerCount relation
 *
 * @method     CcMountName findOne(PropelPDO $con = null) Return the first CcMountName matching the query
 * @method     CcMountName findOneOrCreate(PropelPDO $con = null) Return the first CcMountName matching the query, or a new CcMountName object populated from the query conditions when no match is found
 *
 * @method     CcMountName findOneByDbId(int $id) Return the first CcMountName filtered by the id column
 * @method     CcMountName findOneByDbMountName(string $mount_name) Return the first CcMountName filtered by the mount_name column
 *
 * @method     array findByDbId(int $id) Return CcMountName objects filtered by the id column
 * @method     array findByDbMountName(string $mount_name) Return CcMountName objects filtered by the mount_name column
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcMountNameQuery extends ModelCriteria
{

	/**
	 * Initializes internal state of BaseCcMountNameQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'airtime', $modelName = 'CcMountName', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new CcMountNameQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    CcMountNameQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof CcMountNameQuery) {
			return $criteria;
		}
		$query = new CcMountNameQuery();
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
	 * @return    CcMountName|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = CcMountNamePeer::getInstanceFromPool((string) $key))) && $this->getFormatter()->isObjectFormatter()) {
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
	 * @return    CcMountNameQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(CcMountNamePeer::ID, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    CcMountNameQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(CcMountNamePeer::ID, $keys, Criteria::IN);
	}

	/**
	 * Filter the query on the id column
	 * 
	 * @param     int|array $dbId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcMountNameQuery The current query, for fluid interface
	 */
	public function filterByDbId($dbId = null, $comparison = null)
	{
		if (is_array($dbId) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(CcMountNamePeer::ID, $dbId, $comparison);
	}

	/**
	 * Filter the query on the mount_name column
	 * 
	 * @param     string $dbMountName The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcMountNameQuery The current query, for fluid interface
	 */
	public function filterByDbMountName($dbMountName = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbMountName)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbMountName)) {
				$dbMountName = str_replace('*', '%', $dbMountName);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcMountNamePeer::MOUNT_NAME, $dbMountName, $comparison);
	}

	/**
	 * Filter the query by a related CcListenerCount object
	 *
	 * @param     CcListenerCount $ccListenerCount  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcMountNameQuery The current query, for fluid interface
	 */
	public function filterByCcListenerCount($ccListenerCount, $comparison = null)
	{
		return $this
			->addUsingAlias(CcMountNamePeer::ID, $ccListenerCount->getDbMountNameId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcListenerCount relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcMountNameQuery The current query, for fluid interface
	 */
	public function joinCcListenerCount($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('CcListenerCount');
		
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
			$this->addJoinObject($join, 'CcListenerCount');
		}
		
		return $this;
	}

	/**
	 * Use the CcListenerCount relation CcListenerCount object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcListenerCountQuery A secondary query class using the current class as primary query
	 */
	public function useCcListenerCountQuery($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinCcListenerCount($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CcListenerCount', 'CcListenerCountQuery');
	}

	/**
	 * Exclude object from result
	 *
	 * @param     CcMountName $ccMountName Object to remove from the list of results
	 *
	 * @return    CcMountNameQuery The current query, for fluid interface
	 */
	public function prune($ccMountName = null)
	{
		if ($ccMountName) {
			$this->addUsingAlias(CcMountNamePeer::ID, $ccMountName->getDbId(), Criteria::NOT_EQUAL);
	  }
	  
		return $this;
	}

} // BaseCcMountNameQuery
