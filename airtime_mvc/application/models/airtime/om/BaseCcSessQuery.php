<?php


/**
 * Base class that represents a query for the 'cc_sess' table.
 *
 * 
 *
 * @method     CcSessQuery orderBySessid($order = Criteria::ASC) Order by the sessid column
 * @method     CcSessQuery orderByUserid($order = Criteria::ASC) Order by the userid column
 * @method     CcSessQuery orderByLogin($order = Criteria::ASC) Order by the login column
 * @method     CcSessQuery orderByTs($order = Criteria::ASC) Order by the ts column
 *
 * @method     CcSessQuery groupBySessid() Group by the sessid column
 * @method     CcSessQuery groupByUserid() Group by the userid column
 * @method     CcSessQuery groupByLogin() Group by the login column
 * @method     CcSessQuery groupByTs() Group by the ts column
 *
 * @method     CcSessQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     CcSessQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     CcSessQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     CcSessQuery leftJoinCcSubjs($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcSubjs relation
 * @method     CcSessQuery rightJoinCcSubjs($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcSubjs relation
 * @method     CcSessQuery innerJoinCcSubjs($relationAlias = '') Adds a INNER JOIN clause to the query using the CcSubjs relation
 *
 * @method     CcSess findOne(PropelPDO $con = null) Return the first CcSess matching the query
 * @method     CcSess findOneOrCreate(PropelPDO $con = null) Return the first CcSess matching the query, or a new CcSess object populated from the query conditions when no match is found
 *
 * @method     CcSess findOneBySessid(string $sessid) Return the first CcSess filtered by the sessid column
 * @method     CcSess findOneByUserid(int $userid) Return the first CcSess filtered by the userid column
 * @method     CcSess findOneByLogin(string $login) Return the first CcSess filtered by the login column
 * @method     CcSess findOneByTs(string $ts) Return the first CcSess filtered by the ts column
 *
 * @method     array findBySessid(string $sessid) Return CcSess objects filtered by the sessid column
 * @method     array findByUserid(int $userid) Return CcSess objects filtered by the userid column
 * @method     array findByLogin(string $login) Return CcSess objects filtered by the login column
 * @method     array findByTs(string $ts) Return CcSess objects filtered by the ts column
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcSessQuery extends ModelCriteria
{

	/**
	 * Initializes internal state of BaseCcSessQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'airtime', $modelName = 'CcSess', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new CcSessQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    CcSessQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof CcSessQuery) {
			return $criteria;
		}
		$query = new CcSessQuery();
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
	 * @return    CcSess|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = CcSessPeer::getInstanceFromPool((string) $key))) && $this->getFormatter()->isObjectFormatter()) {
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
	 * @return    CcSessQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(CcSessPeer::SESSID, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    CcSessQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(CcSessPeer::SESSID, $keys, Criteria::IN);
	}

	/**
	 * Filter the query on the sessid column
	 * 
	 * @param     string $sessid The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcSessQuery The current query, for fluid interface
	 */
	public function filterBySessid($sessid = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($sessid)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $sessid)) {
				$sessid = str_replace('*', '%', $sessid);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcSessPeer::SESSID, $sessid, $comparison);
	}

	/**
	 * Filter the query on the userid column
	 * 
	 * @param     int|array $userid The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcSessQuery The current query, for fluid interface
	 */
	public function filterByUserid($userid = null, $comparison = null)
	{
		if (is_array($userid)) {
			$useMinMax = false;
			if (isset($userid['min'])) {
				$this->addUsingAlias(CcSessPeer::USERID, $userid['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($userid['max'])) {
				$this->addUsingAlias(CcSessPeer::USERID, $userid['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcSessPeer::USERID, $userid, $comparison);
	}

	/**
	 * Filter the query on the login column
	 * 
	 * @param     string $login The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcSessQuery The current query, for fluid interface
	 */
	public function filterByLogin($login = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($login)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $login)) {
				$login = str_replace('*', '%', $login);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcSessPeer::LOGIN, $login, $comparison);
	}

	/**
	 * Filter the query on the ts column
	 * 
	 * @param     string|array $ts The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcSessQuery The current query, for fluid interface
	 */
	public function filterByTs($ts = null, $comparison = null)
	{
		if (is_array($ts)) {
			$useMinMax = false;
			if (isset($ts['min'])) {
				$this->addUsingAlias(CcSessPeer::TS, $ts['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($ts['max'])) {
				$this->addUsingAlias(CcSessPeer::TS, $ts['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcSessPeer::TS, $ts, $comparison);
	}

	/**
	 * Filter the query by a related CcSubjs object
	 *
	 * @param     CcSubjs $ccSubjs  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcSessQuery The current query, for fluid interface
	 */
	public function filterByCcSubjs($ccSubjs, $comparison = null)
	{
		return $this
			->addUsingAlias(CcSessPeer::USERID, $ccSubjs->getDbId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcSubjs relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcSessQuery The current query, for fluid interface
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
	 * @param     CcSess $ccSess Object to remove from the list of results
	 *
	 * @return    CcSessQuery The current query, for fluid interface
	 */
	public function prune($ccSess = null)
	{
		if ($ccSess) {
			$this->addUsingAlias(CcSessPeer::SESSID, $ccSess->getSessid(), Criteria::NOT_EQUAL);
	  }
	  
		return $this;
	}

} // BaseCcSessQuery
