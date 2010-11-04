<?php


/**
 * Base class that represents a query for the 'cc_subjs' table.
 *
 * 
 *
 * @method     CcSubjsQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     CcSubjsQuery orderByLogin($order = Criteria::ASC) Order by the login column
 * @method     CcSubjsQuery orderByPass($order = Criteria::ASC) Order by the pass column
 * @method     CcSubjsQuery orderByType($order = Criteria::ASC) Order by the type column
 * @method     CcSubjsQuery orderByRealname($order = Criteria::ASC) Order by the realname column
 * @method     CcSubjsQuery orderByLastlogin($order = Criteria::ASC) Order by the lastlogin column
 * @method     CcSubjsQuery orderByLastfail($order = Criteria::ASC) Order by the lastfail column
 *
 * @method     CcSubjsQuery groupById() Group by the id column
 * @method     CcSubjsQuery groupByLogin() Group by the login column
 * @method     CcSubjsQuery groupByPass() Group by the pass column
 * @method     CcSubjsQuery groupByType() Group by the type column
 * @method     CcSubjsQuery groupByRealname() Group by the realname column
 * @method     CcSubjsQuery groupByLastlogin() Group by the lastlogin column
 * @method     CcSubjsQuery groupByLastfail() Group by the lastfail column
 *
 * @method     CcSubjsQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     CcSubjsQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     CcSubjsQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     CcSubjsQuery leftJoinCcAccess($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcAccess relation
 * @method     CcSubjsQuery rightJoinCcAccess($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcAccess relation
 * @method     CcSubjsQuery innerJoinCcAccess($relationAlias = '') Adds a INNER JOIN clause to the query using the CcAccess relation
 *
 * @method     CcSubjsQuery leftJoinCcFiles($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcFiles relation
 * @method     CcSubjsQuery rightJoinCcFiles($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcFiles relation
 * @method     CcSubjsQuery innerJoinCcFiles($relationAlias = '') Adds a INNER JOIN clause to the query using the CcFiles relation
 *
 * @method     CcSubjsQuery leftJoinCcPerms($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcPerms relation
 * @method     CcSubjsQuery rightJoinCcPerms($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcPerms relation
 * @method     CcSubjsQuery innerJoinCcPerms($relationAlias = '') Adds a INNER JOIN clause to the query using the CcPerms relation
 *
 * @method     CcSubjsQuery leftJoinCcPlaylist($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcPlaylist relation
 * @method     CcSubjsQuery rightJoinCcPlaylist($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcPlaylist relation
 * @method     CcSubjsQuery innerJoinCcPlaylist($relationAlias = '') Adds a INNER JOIN clause to the query using the CcPlaylist relation
 *
 * @method     CcSubjsQuery leftJoinCcPref($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcPref relation
 * @method     CcSubjsQuery rightJoinCcPref($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcPref relation
 * @method     CcSubjsQuery innerJoinCcPref($relationAlias = '') Adds a INNER JOIN clause to the query using the CcPref relation
 *
 * @method     CcSubjsQuery leftJoinCcSess($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcSess relation
 * @method     CcSubjsQuery rightJoinCcSess($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcSess relation
 * @method     CcSubjsQuery innerJoinCcSess($relationAlias = '') Adds a INNER JOIN clause to the query using the CcSess relation
 *
 * @method     CcSubjs findOne(PropelPDO $con = null) Return the first CcSubjs matching the query
 * @method     CcSubjs findOneOrCreate(PropelPDO $con = null) Return the first CcSubjs matching the query, or a new CcSubjs object populated from the query conditions when no match is found
 *
 * @method     CcSubjs findOneById(int $id) Return the first CcSubjs filtered by the id column
 * @method     CcSubjs findOneByLogin(string $login) Return the first CcSubjs filtered by the login column
 * @method     CcSubjs findOneByPass(string $pass) Return the first CcSubjs filtered by the pass column
 * @method     CcSubjs findOneByType(string $type) Return the first CcSubjs filtered by the type column
 * @method     CcSubjs findOneByRealname(string $realname) Return the first CcSubjs filtered by the realname column
 * @method     CcSubjs findOneByLastlogin(string $lastlogin) Return the first CcSubjs filtered by the lastlogin column
 * @method     CcSubjs findOneByLastfail(string $lastfail) Return the first CcSubjs filtered by the lastfail column
 *
 * @method     array findById(int $id) Return CcSubjs objects filtered by the id column
 * @method     array findByLogin(string $login) Return CcSubjs objects filtered by the login column
 * @method     array findByPass(string $pass) Return CcSubjs objects filtered by the pass column
 * @method     array findByType(string $type) Return CcSubjs objects filtered by the type column
 * @method     array findByRealname(string $realname) Return CcSubjs objects filtered by the realname column
 * @method     array findByLastlogin(string $lastlogin) Return CcSubjs objects filtered by the lastlogin column
 * @method     array findByLastfail(string $lastfail) Return CcSubjs objects filtered by the lastfail column
 *
 * @package    propel.generator.campcaster.om
 */
abstract class BaseCcSubjsQuery extends ModelCriteria
{

	/**
	 * Initializes internal state of BaseCcSubjsQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'campcaster', $modelName = 'CcSubjs', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new CcSubjsQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    CcSubjsQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof CcSubjsQuery) {
			return $criteria;
		}
		$query = new CcSubjsQuery();
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
	 * @return    CcSubjs|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = CcSubjsPeer::getInstanceFromPool((string) $key))) && $this->getFormatter()->isObjectFormatter()) {
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
	 * @return    CcSubjsQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(CcSubjsPeer::ID, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    CcSubjsQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(CcSubjsPeer::ID, $keys, Criteria::IN);
	}

	/**
	 * Filter the query on the id column
	 * 
	 * @param     int|array $id The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcSubjsQuery The current query, for fluid interface
	 */
	public function filterById($id = null, $comparison = null)
	{
		if (is_array($id) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(CcSubjsPeer::ID, $id, $comparison);
	}

	/**
	 * Filter the query on the login column
	 * 
	 * @param     string $login The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcSubjsQuery The current query, for fluid interface
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
		return $this->addUsingAlias(CcSubjsPeer::LOGIN, $login, $comparison);
	}

	/**
	 * Filter the query on the pass column
	 * 
	 * @param     string $pass The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcSubjsQuery The current query, for fluid interface
	 */
	public function filterByPass($pass = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($pass)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $pass)) {
				$pass = str_replace('*', '%', $pass);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcSubjsPeer::PASS, $pass, $comparison);
	}

	/**
	 * Filter the query on the type column
	 * 
	 * @param     string $type The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcSubjsQuery The current query, for fluid interface
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
		return $this->addUsingAlias(CcSubjsPeer::TYPE, $type, $comparison);
	}

	/**
	 * Filter the query on the realname column
	 * 
	 * @param     string $realname The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcSubjsQuery The current query, for fluid interface
	 */
	public function filterByRealname($realname = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($realname)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $realname)) {
				$realname = str_replace('*', '%', $realname);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcSubjsPeer::REALNAME, $realname, $comparison);
	}

	/**
	 * Filter the query on the lastlogin column
	 * 
	 * @param     string|array $lastlogin The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcSubjsQuery The current query, for fluid interface
	 */
	public function filterByLastlogin($lastlogin = null, $comparison = null)
	{
		if (is_array($lastlogin)) {
			$useMinMax = false;
			if (isset($lastlogin['min'])) {
				$this->addUsingAlias(CcSubjsPeer::LASTLOGIN, $lastlogin['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($lastlogin['max'])) {
				$this->addUsingAlias(CcSubjsPeer::LASTLOGIN, $lastlogin['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcSubjsPeer::LASTLOGIN, $lastlogin, $comparison);
	}

	/**
	 * Filter the query on the lastfail column
	 * 
	 * @param     string|array $lastfail The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcSubjsQuery The current query, for fluid interface
	 */
	public function filterByLastfail($lastfail = null, $comparison = null)
	{
		if (is_array($lastfail)) {
			$useMinMax = false;
			if (isset($lastfail['min'])) {
				$this->addUsingAlias(CcSubjsPeer::LASTFAIL, $lastfail['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($lastfail['max'])) {
				$this->addUsingAlias(CcSubjsPeer::LASTFAIL, $lastfail['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcSubjsPeer::LASTFAIL, $lastfail, $comparison);
	}

	/**
	 * Filter the query by a related CcAccess object
	 *
	 * @param     CcAccess $ccAccess  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcSubjsQuery The current query, for fluid interface
	 */
	public function filterByCcAccess($ccAccess, $comparison = null)
	{
		return $this
			->addUsingAlias(CcSubjsPeer::ID, $ccAccess->getOwner(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcAccess relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcSubjsQuery The current query, for fluid interface
	 */
	public function joinCcAccess($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('CcAccess');
		
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
			$this->addJoinObject($join, 'CcAccess');
		}
		
		return $this;
	}

	/**
	 * Use the CcAccess relation CcAccess object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcAccessQuery A secondary query class using the current class as primary query
	 */
	public function useCcAccessQuery($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinCcAccess($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CcAccess', 'CcAccessQuery');
	}

	/**
	 * Filter the query by a related CcFiles object
	 *
	 * @param     CcFiles $ccFiles  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcSubjsQuery The current query, for fluid interface
	 */
	public function filterByCcFiles($ccFiles, $comparison = null)
	{
		return $this
			->addUsingAlias(CcSubjsPeer::ID, $ccFiles->getEditedby(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcFiles relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcSubjsQuery The current query, for fluid interface
	 */
	public function joinCcFiles($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('CcFiles');
		
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
			$this->addJoinObject($join, 'CcFiles');
		}
		
		return $this;
	}

	/**
	 * Use the CcFiles relation CcFiles object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcFilesQuery A secondary query class using the current class as primary query
	 */
	public function useCcFilesQuery($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinCcFiles($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CcFiles', 'CcFilesQuery');
	}

	/**
	 * Filter the query by a related CcPerms object
	 *
	 * @param     CcPerms $ccPerms  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcSubjsQuery The current query, for fluid interface
	 */
	public function filterByCcPerms($ccPerms, $comparison = null)
	{
		return $this
			->addUsingAlias(CcSubjsPeer::ID, $ccPerms->getSubj(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcPerms relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcSubjsQuery The current query, for fluid interface
	 */
	public function joinCcPerms($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('CcPerms');
		
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
			$this->addJoinObject($join, 'CcPerms');
		}
		
		return $this;
	}

	/**
	 * Use the CcPerms relation CcPerms object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcPermsQuery A secondary query class using the current class as primary query
	 */
	public function useCcPermsQuery($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinCcPerms($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CcPerms', 'CcPermsQuery');
	}

	/**
	 * Filter the query by a related CcPlaylist object
	 *
	 * @param     CcPlaylist $ccPlaylist  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcSubjsQuery The current query, for fluid interface
	 */
	public function filterByCcPlaylist($ccPlaylist, $comparison = null)
	{
		return $this
			->addUsingAlias(CcSubjsPeer::ID, $ccPlaylist->getEditedby(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcPlaylist relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcSubjsQuery The current query, for fluid interface
	 */
	public function joinCcPlaylist($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('CcPlaylist');
		
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
			$this->addJoinObject($join, 'CcPlaylist');
		}
		
		return $this;
	}

	/**
	 * Use the CcPlaylist relation CcPlaylist object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcPlaylistQuery A secondary query class using the current class as primary query
	 */
	public function useCcPlaylistQuery($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinCcPlaylist($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CcPlaylist', 'CcPlaylistQuery');
	}

	/**
	 * Filter the query by a related CcPref object
	 *
	 * @param     CcPref $ccPref  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcSubjsQuery The current query, for fluid interface
	 */
	public function filterByCcPref($ccPref, $comparison = null)
	{
		return $this
			->addUsingAlias(CcSubjsPeer::ID, $ccPref->getSubjid(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcPref relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcSubjsQuery The current query, for fluid interface
	 */
	public function joinCcPref($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('CcPref');
		
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
			$this->addJoinObject($join, 'CcPref');
		}
		
		return $this;
	}

	/**
	 * Use the CcPref relation CcPref object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcPrefQuery A secondary query class using the current class as primary query
	 */
	public function useCcPrefQuery($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinCcPref($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CcPref', 'CcPrefQuery');
	}

	/**
	 * Filter the query by a related CcSess object
	 *
	 * @param     CcSess $ccSess  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcSubjsQuery The current query, for fluid interface
	 */
	public function filterByCcSess($ccSess, $comparison = null)
	{
		return $this
			->addUsingAlias(CcSubjsPeer::ID, $ccSess->getUserid(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcSess relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcSubjsQuery The current query, for fluid interface
	 */
	public function joinCcSess($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('CcSess');
		
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
			$this->addJoinObject($join, 'CcSess');
		}
		
		return $this;
	}

	/**
	 * Use the CcSess relation CcSess object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcSessQuery A secondary query class using the current class as primary query
	 */
	public function useCcSessQuery($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinCcSess($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CcSess', 'CcSessQuery');
	}

	/**
	 * Exclude object from result
	 *
	 * @param     CcSubjs $ccSubjs Object to remove from the list of results
	 *
	 * @return    CcSubjsQuery The current query, for fluid interface
	 */
	public function prune($ccSubjs = null)
	{
		if ($ccSubjs) {
			$this->addUsingAlias(CcSubjsPeer::ID, $ccSubjs->getId(), Criteria::NOT_EQUAL);
	  }
	  
		return $this;
	}

} // BaseCcSubjsQuery
