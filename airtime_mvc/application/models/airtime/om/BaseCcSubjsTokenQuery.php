<?php


/**
 * Base class that represents a query for the 'cc_subjs_token' table.
 *
 * 
 *
 * @method     CcSubjsTokenQuery orderByDbId($order = Criteria::ASC) Order by the id column
 * @method     CcSubjsTokenQuery orderByDbUserId($order = Criteria::ASC) Order by the user_id column
 * @method     CcSubjsTokenQuery orderByDbAction($order = Criteria::ASC) Order by the action column
 * @method     CcSubjsTokenQuery orderByDbToken($order = Criteria::ASC) Order by the token column
 * @method     CcSubjsTokenQuery orderByDbCreated($order = Criteria::ASC) Order by the created column
 *
 * @method     CcSubjsTokenQuery groupByDbId() Group by the id column
 * @method     CcSubjsTokenQuery groupByDbUserId() Group by the user_id column
 * @method     CcSubjsTokenQuery groupByDbAction() Group by the action column
 * @method     CcSubjsTokenQuery groupByDbToken() Group by the token column
 * @method     CcSubjsTokenQuery groupByDbCreated() Group by the created column
 *
 * @method     CcSubjsTokenQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     CcSubjsTokenQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     CcSubjsTokenQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     CcSubjsTokenQuery leftJoinCcSubjs($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcSubjs relation
 * @method     CcSubjsTokenQuery rightJoinCcSubjs($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcSubjs relation
 * @method     CcSubjsTokenQuery innerJoinCcSubjs($relationAlias = '') Adds a INNER JOIN clause to the query using the CcSubjs relation
 *
 * @method     CcSubjsToken findOne(PropelPDO $con = null) Return the first CcSubjsToken matching the query
 * @method     CcSubjsToken findOneOrCreate(PropelPDO $con = null) Return the first CcSubjsToken matching the query, or a new CcSubjsToken object populated from the query conditions when no match is found
 *
 * @method     CcSubjsToken findOneByDbId(int $id) Return the first CcSubjsToken filtered by the id column
 * @method     CcSubjsToken findOneByDbUserId(int $user_id) Return the first CcSubjsToken filtered by the user_id column
 * @method     CcSubjsToken findOneByDbAction(string $action) Return the first CcSubjsToken filtered by the action column
 * @method     CcSubjsToken findOneByDbToken(string $token) Return the first CcSubjsToken filtered by the token column
 * @method     CcSubjsToken findOneByDbCreated(string $created) Return the first CcSubjsToken filtered by the created column
 *
 * @method     array findByDbId(int $id) Return CcSubjsToken objects filtered by the id column
 * @method     array findByDbUserId(int $user_id) Return CcSubjsToken objects filtered by the user_id column
 * @method     array findByDbAction(string $action) Return CcSubjsToken objects filtered by the action column
 * @method     array findByDbToken(string $token) Return CcSubjsToken objects filtered by the token column
 * @method     array findByDbCreated(string $created) Return CcSubjsToken objects filtered by the created column
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcSubjsTokenQuery extends ModelCriteria
{

	/**
	 * Initializes internal state of BaseCcSubjsTokenQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'airtime', $modelName = 'CcSubjsToken', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new CcSubjsTokenQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    CcSubjsTokenQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof CcSubjsTokenQuery) {
			return $criteria;
		}
		$query = new CcSubjsTokenQuery();
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
	 * @return    CcSubjsToken|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = CcSubjsTokenPeer::getInstanceFromPool((string) $key))) && $this->getFormatter()->isObjectFormatter()) {
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
	 * @return    CcSubjsTokenQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(CcSubjsTokenPeer::ID, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    CcSubjsTokenQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(CcSubjsTokenPeer::ID, $keys, Criteria::IN);
	}

	/**
	 * Filter the query on the id column
	 * 
	 * @param     int|array $dbId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcSubjsTokenQuery The current query, for fluid interface
	 */
	public function filterByDbId($dbId = null, $comparison = null)
	{
		if (is_array($dbId) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(CcSubjsTokenPeer::ID, $dbId, $comparison);
	}

	/**
	 * Filter the query on the user_id column
	 * 
	 * @param     int|array $dbUserId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcSubjsTokenQuery The current query, for fluid interface
	 */
	public function filterByDbUserId($dbUserId = null, $comparison = null)
	{
		if (is_array($dbUserId)) {
			$useMinMax = false;
			if (isset($dbUserId['min'])) {
				$this->addUsingAlias(CcSubjsTokenPeer::USER_ID, $dbUserId['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbUserId['max'])) {
				$this->addUsingAlias(CcSubjsTokenPeer::USER_ID, $dbUserId['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcSubjsTokenPeer::USER_ID, $dbUserId, $comparison);
	}

	/**
	 * Filter the query on the action column
	 * 
	 * @param     string $dbAction The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcSubjsTokenQuery The current query, for fluid interface
	 */
	public function filterByDbAction($dbAction = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbAction)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbAction)) {
				$dbAction = str_replace('*', '%', $dbAction);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcSubjsTokenPeer::ACTION, $dbAction, $comparison);
	}

	/**
	 * Filter the query on the token column
	 * 
	 * @param     string $dbToken The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcSubjsTokenQuery The current query, for fluid interface
	 */
	public function filterByDbToken($dbToken = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbToken)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbToken)) {
				$dbToken = str_replace('*', '%', $dbToken);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcSubjsTokenPeer::TOKEN, $dbToken, $comparison);
	}

	/**
	 * Filter the query on the created column
	 * 
	 * @param     string|array $dbCreated The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcSubjsTokenQuery The current query, for fluid interface
	 */
	public function filterByDbCreated($dbCreated = null, $comparison = null)
	{
		if (is_array($dbCreated)) {
			$useMinMax = false;
			if (isset($dbCreated['min'])) {
				$this->addUsingAlias(CcSubjsTokenPeer::CREATED, $dbCreated['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbCreated['max'])) {
				$this->addUsingAlias(CcSubjsTokenPeer::CREATED, $dbCreated['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcSubjsTokenPeer::CREATED, $dbCreated, $comparison);
	}

	/**
	 * Filter the query by a related CcSubjs object
	 *
	 * @param     CcSubjs $ccSubjs  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcSubjsTokenQuery The current query, for fluid interface
	 */
	public function filterByCcSubjs($ccSubjs, $comparison = null)
	{
		return $this
			->addUsingAlias(CcSubjsTokenPeer::USER_ID, $ccSubjs->getDbId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcSubjs relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcSubjsTokenQuery The current query, for fluid interface
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
	 * @param     CcSubjsToken $ccSubjsToken Object to remove from the list of results
	 *
	 * @return    CcSubjsTokenQuery The current query, for fluid interface
	 */
	public function prune($ccSubjsToken = null)
	{
		if ($ccSubjsToken) {
			$this->addUsingAlias(CcSubjsTokenPeer::ID, $ccSubjsToken->getDbId(), Criteria::NOT_EQUAL);
	  }
	  
		return $this;
	}

} // BaseCcSubjsTokenQuery
