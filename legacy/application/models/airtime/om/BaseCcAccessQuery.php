<?php


/**
 * Base class that represents a query for the 'cc_access' table.
 *
 *
 *
 * @method     CcAccessQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     CcAccessQuery orderByGunid($order = Criteria::ASC) Order by the gunid column
 * @method     CcAccessQuery orderByToken($order = Criteria::ASC) Order by the token column
 * @method     CcAccessQuery orderByChsum($order = Criteria::ASC) Order by the chsum column
 * @method     CcAccessQuery orderByExt($order = Criteria::ASC) Order by the ext column
 * @method     CcAccessQuery orderByType($order = Criteria::ASC) Order by the type column
 * @method     CcAccessQuery orderByParent($order = Criteria::ASC) Order by the parent column
 * @method     CcAccessQuery orderByOwner($order = Criteria::ASC) Order by the owner column
 * @method     CcAccessQuery orderByTs($order = Criteria::ASC) Order by the ts column
 *
 * @method     CcAccessQuery groupById() Group by the id column
 * @method     CcAccessQuery groupByGunid() Group by the gunid column
 * @method     CcAccessQuery groupByToken() Group by the token column
 * @method     CcAccessQuery groupByChsum() Group by the chsum column
 * @method     CcAccessQuery groupByExt() Group by the ext column
 * @method     CcAccessQuery groupByType() Group by the type column
 * @method     CcAccessQuery groupByParent() Group by the parent column
 * @method     CcAccessQuery groupByOwner() Group by the owner column
 * @method     CcAccessQuery groupByTs() Group by the ts column
 *
 * @method     CcAccessQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     CcAccessQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     CcAccessQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     CcAccessQuery leftJoinCcSubjs($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcSubjs relation
 * @method     CcAccessQuery rightJoinCcSubjs($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcSubjs relation
 * @method     CcAccessQuery innerJoinCcSubjs($relationAlias = '') Adds a INNER JOIN clause to the query using the CcSubjs relation
 *
 * @method     CcAccess findOne(PropelPDO $con = null) Return the first CcAccess matching the query
 * @method     CcAccess findOneOrCreate(PropelPDO $con = null) Return the first CcAccess matching the query, or a new CcAccess object populated from the query conditions when no match is found
 *
 * @method     CcAccess findOneById(int $id) Return the first CcAccess filtered by the id column
 * @method     CcAccess findOneByGunid(string $gunid) Return the first CcAccess filtered by the gunid column
 * @method     CcAccess findOneByToken(string $token) Return the first CcAccess filtered by the token column
 * @method     CcAccess findOneByChsum(string $chsum) Return the first CcAccess filtered by the chsum column
 * @method     CcAccess findOneByExt(string $ext) Return the first CcAccess filtered by the ext column
 * @method     CcAccess findOneByType(string $type) Return the first CcAccess filtered by the type column
 * @method     CcAccess findOneByParent(string $parent) Return the first CcAccess filtered by the parent column
 * @method     CcAccess findOneByOwner(int $owner) Return the first CcAccess filtered by the owner column
 * @method     CcAccess findOneByTs(string $ts) Return the first CcAccess filtered by the ts column
 *
 * @method     array findById(int $id) Return CcAccess objects filtered by the id column
 * @method     array findByGunid(string $gunid) Return CcAccess objects filtered by the gunid column
 * @method     array findByToken(string $token) Return CcAccess objects filtered by the token column
 * @method     array findByChsum(string $chsum) Return CcAccess objects filtered by the chsum column
 * @method     array findByExt(string $ext) Return CcAccess objects filtered by the ext column
 * @method     array findByType(string $type) Return CcAccess objects filtered by the type column
 * @method     array findByParent(string $parent) Return CcAccess objects filtered by the parent column
 * @method     array findByOwner(int $owner) Return CcAccess objects filtered by the owner column
 * @method     array findByTs(string $ts) Return CcAccess objects filtered by the ts column
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcAccessQuery extends ModelCriteria
{

	/**
	 * Initializes internal state of BaseCcAccessQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'airtime', $modelName = 'CcAccess', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new CcAccessQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    CcAccessQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof CcAccessQuery) {
			return $criteria;
		}
		$query = new CcAccessQuery();
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
	 * @return    CcAccess|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = CcAccessPeer::getInstanceFromPool((string) $key))) && $this->getFormatter()->isObjectFormatter()) {
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
	 * @return    CcAccessQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(CcAccessPeer::ID, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    CcAccessQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(CcAccessPeer::ID, $keys, Criteria::IN);
	}

	/**
	 * Filter the query on the id column
	 *
	 * @param     int|array $id The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcAccessQuery The current query, for fluid interface
	 */
	public function filterById($id = null, $comparison = null)
	{
		if (is_array($id) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(CcAccessPeer::ID, $id, $comparison);
	}

	/**
	 * Filter the query on the gunid column
	 *
	 * @param     string $gunid The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcAccessQuery The current query, for fluid interface
	 */
	public function filterByGunid($gunid = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($gunid)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $gunid)) {
				$gunid = str_replace('*', '%', $gunid);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcAccessPeer::GUNID, $gunid, $comparison);
	}

	/**
	 * Filter the query on the token column
	 *
	 * @param     string|array $token The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcAccessQuery The current query, for fluid interface
	 */
	public function filterByToken($token = null, $comparison = null)
	{
		if (is_array($token)) {
			$useMinMax = false;
			if (isset($token['min'])) {
				$this->addUsingAlias(CcAccessPeer::TOKEN, $token['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($token['max'])) {
				$this->addUsingAlias(CcAccessPeer::TOKEN, $token['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcAccessPeer::TOKEN, $token, $comparison);
	}

	/**
	 * Filter the query on the chsum column
	 *
	 * @param     string $chsum The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcAccessQuery The current query, for fluid interface
	 */
	public function filterByChsum($chsum = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($chsum)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $chsum)) {
				$chsum = str_replace('*', '%', $chsum);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcAccessPeer::CHSUM, $chsum, $comparison);
	}

	/**
	 * Filter the query on the ext column
	 *
	 * @param     string $ext The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcAccessQuery The current query, for fluid interface
	 */
	public function filterByExt($ext = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($ext)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $ext)) {
				$ext = str_replace('*', '%', $ext);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcAccessPeer::EXT, $ext, $comparison);
	}

	/**
	 * Filter the query on the type column
	 *
	 * @param     string $type The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcAccessQuery The current query, for fluid interface
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
		return $this->addUsingAlias(CcAccessPeer::TYPE, $type, $comparison);
	}

	/**
	 * Filter the query on the parent column
	 *
	 * @param     string|array $parent The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcAccessQuery The current query, for fluid interface
	 */
	public function filterByParent($parent = null, $comparison = null)
	{
		if (is_array($parent)) {
			$useMinMax = false;
			if (isset($parent['min'])) {
				$this->addUsingAlias(CcAccessPeer::PARENT, $parent['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($parent['max'])) {
				$this->addUsingAlias(CcAccessPeer::PARENT, $parent['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcAccessPeer::PARENT, $parent, $comparison);
	}

	/**
	 * Filter the query on the owner column
	 *
	 * @param     int|array $owner The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcAccessQuery The current query, for fluid interface
	 */
	public function filterByOwner($owner = null, $comparison = null)
	{
		if (is_array($owner)) {
			$useMinMax = false;
			if (isset($owner['min'])) {
				$this->addUsingAlias(CcAccessPeer::OWNER, $owner['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($owner['max'])) {
				$this->addUsingAlias(CcAccessPeer::OWNER, $owner['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcAccessPeer::OWNER, $owner, $comparison);
	}

	/**
	 * Filter the query on the ts column
	 *
	 * @param     string|array $ts The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcAccessQuery The current query, for fluid interface
	 */
	public function filterByTs($ts = null, $comparison = null)
	{
		if (is_array($ts)) {
			$useMinMax = false;
			if (isset($ts['min'])) {
				$this->addUsingAlias(CcAccessPeer::TS, $ts['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($ts['max'])) {
				$this->addUsingAlias(CcAccessPeer::TS, $ts['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcAccessPeer::TS, $ts, $comparison);
	}

	/**
	 * Filter the query by a related CcSubjs object
	 *
	 * @param     CcSubjs $ccSubjs  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcAccessQuery The current query, for fluid interface
	 */
	public function filterByCcSubjs($ccSubjs, $comparison = null)
	{
		return $this
			->addUsingAlias(CcAccessPeer::OWNER, $ccSubjs->getDbId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcSubjs relation
	 *
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcAccessQuery The current query, for fluid interface
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
	 * @param     CcAccess $ccAccess Object to remove from the list of results
	 *
	 * @return    CcAccessQuery The current query, for fluid interface
	 */
	public function prune($ccAccess = null)
	{
		if ($ccAccess) {
			$this->addUsingAlias(CcAccessPeer::ID, $ccAccess->getId(), Criteria::NOT_EQUAL);
	  }

		return $this;
	}

} // BaseCcAccessQuery
