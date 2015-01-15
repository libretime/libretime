<?php


/**
 * Base class that represents a query for the 'cc_show' table.
 *
 * 
 *
 * @method     CcShowQuery orderByDbId($order = Criteria::ASC) Order by the id column
 * @method     CcShowQuery orderByDbName($order = Criteria::ASC) Order by the name column
 * @method     CcShowQuery orderByDbUrl($order = Criteria::ASC) Order by the url column
 * @method     CcShowQuery orderByDbGenre($order = Criteria::ASC) Order by the genre column
 * @method     CcShowQuery orderByDbDescription($order = Criteria::ASC) Order by the description column
 * @method     CcShowQuery orderByDbColor($order = Criteria::ASC) Order by the color column
 * @method     CcShowQuery orderByDbBackgroundColor($order = Criteria::ASC) Order by the background_color column
 * @method     CcShowQuery orderByDbLiveStreamUsingAirtimeAuth($order = Criteria::ASC) Order by the live_stream_using_airtime_auth column
 * @method     CcShowQuery orderByDbLiveStreamUsingCustomAuth($order = Criteria::ASC) Order by the live_stream_using_custom_auth column
 * @method     CcShowQuery orderByDbLiveStreamUser($order = Criteria::ASC) Order by the live_stream_user column
 * @method     CcShowQuery orderByDbLiveStreamPass($order = Criteria::ASC) Order by the live_stream_pass column
 * @method     CcShowQuery orderByDbLinked($order = Criteria::ASC) Order by the linked column
 * @method     CcShowQuery orderByDbIsLinkable($order = Criteria::ASC) Order by the is_linkable column
 *
 * @method     CcShowQuery groupByDbId() Group by the id column
 * @method     CcShowQuery groupByDbName() Group by the name column
 * @method     CcShowQuery groupByDbUrl() Group by the url column
 * @method     CcShowQuery groupByDbGenre() Group by the genre column
 * @method     CcShowQuery groupByDbDescription() Group by the description column
 * @method     CcShowQuery groupByDbColor() Group by the color column
 * @method     CcShowQuery groupByDbBackgroundColor() Group by the background_color column
 * @method     CcShowQuery groupByDbLiveStreamUsingAirtimeAuth() Group by the live_stream_using_airtime_auth column
 * @method     CcShowQuery groupByDbLiveStreamUsingCustomAuth() Group by the live_stream_using_custom_auth column
 * @method     CcShowQuery groupByDbLiveStreamUser() Group by the live_stream_user column
 * @method     CcShowQuery groupByDbLiveStreamPass() Group by the live_stream_pass column
 * @method     CcShowQuery groupByDbLinked() Group by the linked column
 * @method     CcShowQuery groupByDbIsLinkable() Group by the is_linkable column
 *
 * @method     CcShowQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     CcShowQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     CcShowQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     CcShowQuery leftJoinCcShowInstances($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcShowInstances relation
 * @method     CcShowQuery rightJoinCcShowInstances($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcShowInstances relation
 * @method     CcShowQuery innerJoinCcShowInstances($relationAlias = '') Adds a INNER JOIN clause to the query using the CcShowInstances relation
 *
 * @method     CcShowQuery leftJoinCcShowDays($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcShowDays relation
 * @method     CcShowQuery rightJoinCcShowDays($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcShowDays relation
 * @method     CcShowQuery innerJoinCcShowDays($relationAlias = '') Adds a INNER JOIN clause to the query using the CcShowDays relation
 *
 * @method     CcShowQuery leftJoinCcShowRebroadcast($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcShowRebroadcast relation
 * @method     CcShowQuery rightJoinCcShowRebroadcast($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcShowRebroadcast relation
 * @method     CcShowQuery innerJoinCcShowRebroadcast($relationAlias = '') Adds a INNER JOIN clause to the query using the CcShowRebroadcast relation
 *
 * @method     CcShowQuery leftJoinCcShowHosts($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcShowHosts relation
 * @method     CcShowQuery rightJoinCcShowHosts($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcShowHosts relation
 * @method     CcShowQuery innerJoinCcShowHosts($relationAlias = '') Adds a INNER JOIN clause to the query using the CcShowHosts relation
 *
 * @method     CcShow findOne(PropelPDO $con = null) Return the first CcShow matching the query
 * @method     CcShow findOneOrCreate(PropelPDO $con = null) Return the first CcShow matching the query, or a new CcShow object populated from the query conditions when no match is found
 *
 * @method     CcShow findOneByDbId(int $id) Return the first CcShow filtered by the id column
 * @method     CcShow findOneByDbName(string $name) Return the first CcShow filtered by the name column
 * @method     CcShow findOneByDbUrl(string $url) Return the first CcShow filtered by the url column
 * @method     CcShow findOneByDbGenre(string $genre) Return the first CcShow filtered by the genre column
 * @method     CcShow findOneByDbDescription(string $description) Return the first CcShow filtered by the description column
 * @method     CcShow findOneByDbColor(string $color) Return the first CcShow filtered by the color column
 * @method     CcShow findOneByDbBackgroundColor(string $background_color) Return the first CcShow filtered by the background_color column
 * @method     CcShow findOneByDbLiveStreamUsingAirtimeAuth(boolean $live_stream_using_airtime_auth) Return the first CcShow filtered by the live_stream_using_airtime_auth column
 * @method     CcShow findOneByDbLiveStreamUsingCustomAuth(boolean $live_stream_using_custom_auth) Return the first CcShow filtered by the live_stream_using_custom_auth column
 * @method     CcShow findOneByDbLiveStreamUser(string $live_stream_user) Return the first CcShow filtered by the live_stream_user column
 * @method     CcShow findOneByDbLiveStreamPass(string $live_stream_pass) Return the first CcShow filtered by the live_stream_pass column
 * @method     CcShow findOneByDbLinked(boolean $linked) Return the first CcShow filtered by the linked column
 * @method     CcShow findOneByDbIsLinkable(boolean $is_linkable) Return the first CcShow filtered by the is_linkable column
 *
 * @method     array findByDbId(int $id) Return CcShow objects filtered by the id column
 * @method     array findByDbName(string $name) Return CcShow objects filtered by the name column
 * @method     array findByDbUrl(string $url) Return CcShow objects filtered by the url column
 * @method     array findByDbGenre(string $genre) Return CcShow objects filtered by the genre column
 * @method     array findByDbDescription(string $description) Return CcShow objects filtered by the description column
 * @method     array findByDbColor(string $color) Return CcShow objects filtered by the color column
 * @method     array findByDbBackgroundColor(string $background_color) Return CcShow objects filtered by the background_color column
 * @method     array findByDbLiveStreamUsingAirtimeAuth(boolean $live_stream_using_airtime_auth) Return CcShow objects filtered by the live_stream_using_airtime_auth column
 * @method     array findByDbLiveStreamUsingCustomAuth(boolean $live_stream_using_custom_auth) Return CcShow objects filtered by the live_stream_using_custom_auth column
 * @method     array findByDbLiveStreamUser(string $live_stream_user) Return CcShow objects filtered by the live_stream_user column
 * @method     array findByDbLiveStreamPass(string $live_stream_pass) Return CcShow objects filtered by the live_stream_pass column
 * @method     array findByDbLinked(boolean $linked) Return CcShow objects filtered by the linked column
 * @method     array findByDbIsLinkable(boolean $is_linkable) Return CcShow objects filtered by the is_linkable column
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcShowQuery extends ModelCriteria
{

	/**
	 * Initializes internal state of BaseCcShowQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'airtime', $modelName = 'CcShow', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new CcShowQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    CcShowQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof CcShowQuery) {
			return $criteria;
		}
		$query = new CcShowQuery();
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
	 * @return    CcShow|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = CcShowPeer::getInstanceFromPool((string) $key))) && $this->getFormatter()->isObjectFormatter()) {
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
	 * @return    CcShowQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(CcShowPeer::ID, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    CcShowQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(CcShowPeer::ID, $keys, Criteria::IN);
	}

	/**
	 * Filter the query on the id column
	 * 
	 * @param     int|array $dbId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcShowQuery The current query, for fluid interface
	 */
	public function filterByDbId($dbId = null, $comparison = null)
	{
		if (is_array($dbId) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(CcShowPeer::ID, $dbId, $comparison);
	}

	/**
	 * Filter the query on the name column
	 * 
	 * @param     string $dbName The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcShowQuery The current query, for fluid interface
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
		return $this->addUsingAlias(CcShowPeer::NAME, $dbName, $comparison);
	}

	/**
	 * Filter the query on the url column
	 * 
	 * @param     string $dbUrl The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcShowQuery The current query, for fluid interface
	 */
	public function filterByDbUrl($dbUrl = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbUrl)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbUrl)) {
				$dbUrl = str_replace('*', '%', $dbUrl);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcShowPeer::URL, $dbUrl, $comparison);
	}

	/**
	 * Filter the query on the genre column
	 * 
	 * @param     string $dbGenre The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcShowQuery The current query, for fluid interface
	 */
	public function filterByDbGenre($dbGenre = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbGenre)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbGenre)) {
				$dbGenre = str_replace('*', '%', $dbGenre);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcShowPeer::GENRE, $dbGenre, $comparison);
	}

	/**
	 * Filter the query on the description column
	 * 
	 * @param     string $dbDescription The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcShowQuery The current query, for fluid interface
	 */
	public function filterByDbDescription($dbDescription = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbDescription)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbDescription)) {
				$dbDescription = str_replace('*', '%', $dbDescription);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcShowPeer::DESCRIPTION, $dbDescription, $comparison);
	}

	/**
	 * Filter the query on the color column
	 * 
	 * @param     string $dbColor The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcShowQuery The current query, for fluid interface
	 */
	public function filterByDbColor($dbColor = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbColor)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbColor)) {
				$dbColor = str_replace('*', '%', $dbColor);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcShowPeer::COLOR, $dbColor, $comparison);
	}

	/**
	 * Filter the query on the background_color column
	 * 
	 * @param     string $dbBackgroundColor The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcShowQuery The current query, for fluid interface
	 */
	public function filterByDbBackgroundColor($dbBackgroundColor = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbBackgroundColor)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbBackgroundColor)) {
				$dbBackgroundColor = str_replace('*', '%', $dbBackgroundColor);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcShowPeer::BACKGROUND_COLOR, $dbBackgroundColor, $comparison);
	}

	/**
	 * Filter the query on the live_stream_using_airtime_auth column
	 * 
	 * @param     boolean|string $dbLiveStreamUsingAirtimeAuth The value to use as filter.
	 *            Accepts strings ('false', 'off', '-', 'no', 'n', and '0' are false, the rest is true)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcShowQuery The current query, for fluid interface
	 */
	public function filterByDbLiveStreamUsingAirtimeAuth($dbLiveStreamUsingAirtimeAuth = null, $comparison = null)
	{
		if (is_string($dbLiveStreamUsingAirtimeAuth)) {
			$live_stream_using_airtime_auth = in_array(strtolower($dbLiveStreamUsingAirtimeAuth), array('false', 'off', '-', 'no', 'n', '0')) ? false : true;
		}
		return $this->addUsingAlias(CcShowPeer::LIVE_STREAM_USING_AIRTIME_AUTH, $dbLiveStreamUsingAirtimeAuth, $comparison);
	}

	/**
	 * Filter the query on the live_stream_using_custom_auth column
	 * 
	 * @param     boolean|string $dbLiveStreamUsingCustomAuth The value to use as filter.
	 *            Accepts strings ('false', 'off', '-', 'no', 'n', and '0' are false, the rest is true)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcShowQuery The current query, for fluid interface
	 */
	public function filterByDbLiveStreamUsingCustomAuth($dbLiveStreamUsingCustomAuth = null, $comparison = null)
	{
		if (is_string($dbLiveStreamUsingCustomAuth)) {
			$live_stream_using_custom_auth = in_array(strtolower($dbLiveStreamUsingCustomAuth), array('false', 'off', '-', 'no', 'n', '0')) ? false : true;
		}
		return $this->addUsingAlias(CcShowPeer::LIVE_STREAM_USING_CUSTOM_AUTH, $dbLiveStreamUsingCustomAuth, $comparison);
	}

	/**
	 * Filter the query on the live_stream_user column
	 * 
	 * @param     string $dbLiveStreamUser The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcShowQuery The current query, for fluid interface
	 */
	public function filterByDbLiveStreamUser($dbLiveStreamUser = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbLiveStreamUser)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbLiveStreamUser)) {
				$dbLiveStreamUser = str_replace('*', '%', $dbLiveStreamUser);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcShowPeer::LIVE_STREAM_USER, $dbLiveStreamUser, $comparison);
	}

	/**
	 * Filter the query on the live_stream_pass column
	 * 
	 * @param     string $dbLiveStreamPass The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcShowQuery The current query, for fluid interface
	 */
	public function filterByDbLiveStreamPass($dbLiveStreamPass = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbLiveStreamPass)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbLiveStreamPass)) {
				$dbLiveStreamPass = str_replace('*', '%', $dbLiveStreamPass);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcShowPeer::LIVE_STREAM_PASS, $dbLiveStreamPass, $comparison);
	}

	/**
	 * Filter the query on the linked column
	 * 
	 * @param     boolean|string $dbLinked The value to use as filter.
	 *            Accepts strings ('false', 'off', '-', 'no', 'n', and '0' are false, the rest is true)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcShowQuery The current query, for fluid interface
	 */
	public function filterByDbLinked($dbLinked = null, $comparison = null)
	{
		if (is_string($dbLinked)) {
			$linked = in_array(strtolower($dbLinked), array('false', 'off', '-', 'no', 'n', '0')) ? false : true;
		}
		return $this->addUsingAlias(CcShowPeer::LINKED, $dbLinked, $comparison);
	}

	/**
	 * Filter the query on the is_linkable column
	 * 
	 * @param     boolean|string $dbIsLinkable The value to use as filter.
	 *            Accepts strings ('false', 'off', '-', 'no', 'n', and '0' are false, the rest is true)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcShowQuery The current query, for fluid interface
	 */
	public function filterByDbIsLinkable($dbIsLinkable = null, $comparison = null)
	{
		if (is_string($dbIsLinkable)) {
			$is_linkable = in_array(strtolower($dbIsLinkable), array('false', 'off', '-', 'no', 'n', '0')) ? false : true;
		}
		return $this->addUsingAlias(CcShowPeer::IS_LINKABLE, $dbIsLinkable, $comparison);
	}

	/**
	 * Filter the query by a related CcShowInstances object
	 *
	 * @param     CcShowInstances $ccShowInstances  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcShowQuery The current query, for fluid interface
	 */
	public function filterByCcShowInstances($ccShowInstances, $comparison = null)
	{
		return $this
			->addUsingAlias(CcShowPeer::ID, $ccShowInstances->getDbShowId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcShowInstances relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcShowQuery The current query, for fluid interface
	 */
	public function joinCcShowInstances($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('CcShowInstances');
		
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
			$this->addJoinObject($join, 'CcShowInstances');
		}
		
		return $this;
	}

	/**
	 * Use the CcShowInstances relation CcShowInstances object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcShowInstancesQuery A secondary query class using the current class as primary query
	 */
	public function useCcShowInstancesQuery($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinCcShowInstances($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CcShowInstances', 'CcShowInstancesQuery');
	}

	/**
	 * Filter the query by a related CcShowDays object
	 *
	 * @param     CcShowDays $ccShowDays  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcShowQuery The current query, for fluid interface
	 */
	public function filterByCcShowDays($ccShowDays, $comparison = null)
	{
		return $this
			->addUsingAlias(CcShowPeer::ID, $ccShowDays->getDbShowId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcShowDays relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcShowQuery The current query, for fluid interface
	 */
	public function joinCcShowDays($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('CcShowDays');
		
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
			$this->addJoinObject($join, 'CcShowDays');
		}
		
		return $this;
	}

	/**
	 * Use the CcShowDays relation CcShowDays object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcShowDaysQuery A secondary query class using the current class as primary query
	 */
	public function useCcShowDaysQuery($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinCcShowDays($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CcShowDays', 'CcShowDaysQuery');
	}

	/**
	 * Filter the query by a related CcShowRebroadcast object
	 *
	 * @param     CcShowRebroadcast $ccShowRebroadcast  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcShowQuery The current query, for fluid interface
	 */
	public function filterByCcShowRebroadcast($ccShowRebroadcast, $comparison = null)
	{
		return $this
			->addUsingAlias(CcShowPeer::ID, $ccShowRebroadcast->getDbShowId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcShowRebroadcast relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcShowQuery The current query, for fluid interface
	 */
	public function joinCcShowRebroadcast($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('CcShowRebroadcast');
		
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
			$this->addJoinObject($join, 'CcShowRebroadcast');
		}
		
		return $this;
	}

	/**
	 * Use the CcShowRebroadcast relation CcShowRebroadcast object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcShowRebroadcastQuery A secondary query class using the current class as primary query
	 */
	public function useCcShowRebroadcastQuery($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinCcShowRebroadcast($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CcShowRebroadcast', 'CcShowRebroadcastQuery');
	}

	/**
	 * Filter the query by a related CcShowHosts object
	 *
	 * @param     CcShowHosts $ccShowHosts  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcShowQuery The current query, for fluid interface
	 */
	public function filterByCcShowHosts($ccShowHosts, $comparison = null)
	{
		return $this
			->addUsingAlias(CcShowPeer::ID, $ccShowHosts->getDbShow(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcShowHosts relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcShowQuery The current query, for fluid interface
	 */
	public function joinCcShowHosts($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('CcShowHosts');
		
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
			$this->addJoinObject($join, 'CcShowHosts');
		}
		
		return $this;
	}

	/**
	 * Use the CcShowHosts relation CcShowHosts object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcShowHostsQuery A secondary query class using the current class as primary query
	 */
	public function useCcShowHostsQuery($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinCcShowHosts($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CcShowHosts', 'CcShowHostsQuery');
	}

	/**
	 * Exclude object from result
	 *
	 * @param     CcShow $ccShow Object to remove from the list of results
	 *
	 * @return    CcShowQuery The current query, for fluid interface
	 */
	public function prune($ccShow = null)
	{
		if ($ccShow) {
			$this->addUsingAlias(CcShowPeer::ID, $ccShow->getDbId(), Criteria::NOT_EQUAL);
	  }
	  
		return $this;
	}

} // BaseCcShowQuery
