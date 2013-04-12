<?php


/**
 * Base class that represents a query for the 'cc_block' table.
 *
 * 
 *
 * @method     CcBlockQuery orderByDbId($order = Criteria::ASC) Order by the id column
 * @method     CcBlockQuery orderByDbName($order = Criteria::ASC) Order by the name column
 * @method     CcBlockQuery orderByDbMtime($order = Criteria::ASC) Order by the mtime column
 * @method     CcBlockQuery orderByDbUtime($order = Criteria::ASC) Order by the utime column
 * @method     CcBlockQuery orderByDbCreatorId($order = Criteria::ASC) Order by the creator_id column
 * @method     CcBlockQuery orderByDbDescription($order = Criteria::ASC) Order by the description column
 * @method     CcBlockQuery orderByDbLength($order = Criteria::ASC) Order by the length column
 * @method     CcBlockQuery orderByDbType($order = Criteria::ASC) Order by the type column
 *
 * @method     CcBlockQuery groupByDbId() Group by the id column
 * @method     CcBlockQuery groupByDbName() Group by the name column
 * @method     CcBlockQuery groupByDbMtime() Group by the mtime column
 * @method     CcBlockQuery groupByDbUtime() Group by the utime column
 * @method     CcBlockQuery groupByDbCreatorId() Group by the creator_id column
 * @method     CcBlockQuery groupByDbDescription() Group by the description column
 * @method     CcBlockQuery groupByDbLength() Group by the length column
 * @method     CcBlockQuery groupByDbType() Group by the type column
 *
 * @method     CcBlockQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     CcBlockQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     CcBlockQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     CcBlockQuery leftJoinCcSubjs($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcSubjs relation
 * @method     CcBlockQuery rightJoinCcSubjs($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcSubjs relation
 * @method     CcBlockQuery innerJoinCcSubjs($relationAlias = '') Adds a INNER JOIN clause to the query using the CcSubjs relation
 *
 * @method     CcBlockQuery leftJoinCcPlaylistcontents($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcPlaylistcontents relation
 * @method     CcBlockQuery rightJoinCcPlaylistcontents($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcPlaylistcontents relation
 * @method     CcBlockQuery innerJoinCcPlaylistcontents($relationAlias = '') Adds a INNER JOIN clause to the query using the CcPlaylistcontents relation
 *
 * @method     CcBlockQuery leftJoinCcBlockcontents($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcBlockcontents relation
 * @method     CcBlockQuery rightJoinCcBlockcontents($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcBlockcontents relation
 * @method     CcBlockQuery innerJoinCcBlockcontents($relationAlias = '') Adds a INNER JOIN clause to the query using the CcBlockcontents relation
 *
 * @method     CcBlockQuery leftJoinCcBlockcriteria($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcBlockcriteria relation
 * @method     CcBlockQuery rightJoinCcBlockcriteria($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcBlockcriteria relation
 * @method     CcBlockQuery innerJoinCcBlockcriteria($relationAlias = '') Adds a INNER JOIN clause to the query using the CcBlockcriteria relation
 *
 * @method     CcBlock findOne(PropelPDO $con = null) Return the first CcBlock matching the query
 * @method     CcBlock findOneOrCreate(PropelPDO $con = null) Return the first CcBlock matching the query, or a new CcBlock object populated from the query conditions when no match is found
 *
 * @method     CcBlock findOneByDbId(int $id) Return the first CcBlock filtered by the id column
 * @method     CcBlock findOneByDbName(string $name) Return the first CcBlock filtered by the name column
 * @method     CcBlock findOneByDbMtime(string $mtime) Return the first CcBlock filtered by the mtime column
 * @method     CcBlock findOneByDbUtime(string $utime) Return the first CcBlock filtered by the utime column
 * @method     CcBlock findOneByDbCreatorId(int $creator_id) Return the first CcBlock filtered by the creator_id column
 * @method     CcBlock findOneByDbDescription(string $description) Return the first CcBlock filtered by the description column
 * @method     CcBlock findOneByDbLength(string $length) Return the first CcBlock filtered by the length column
 * @method     CcBlock findOneByDbType(string $type) Return the first CcBlock filtered by the type column
 *
 * @method     array findByDbId(int $id) Return CcBlock objects filtered by the id column
 * @method     array findByDbName(string $name) Return CcBlock objects filtered by the name column
 * @method     array findByDbMtime(string $mtime) Return CcBlock objects filtered by the mtime column
 * @method     array findByDbUtime(string $utime) Return CcBlock objects filtered by the utime column
 * @method     array findByDbCreatorId(int $creator_id) Return CcBlock objects filtered by the creator_id column
 * @method     array findByDbDescription(string $description) Return CcBlock objects filtered by the description column
 * @method     array findByDbLength(string $length) Return CcBlock objects filtered by the length column
 * @method     array findByDbType(string $type) Return CcBlock objects filtered by the type column
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcBlockQuery extends ModelCriteria
{

	/**
	 * Initializes internal state of BaseCcBlockQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'airtime', $modelName = 'CcBlock', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new CcBlockQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    CcBlockQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof CcBlockQuery) {
			return $criteria;
		}
		$query = new CcBlockQuery();
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
	 * @return    CcBlock|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = CcBlockPeer::getInstanceFromPool((string) $key))) && $this->getFormatter()->isObjectFormatter()) {
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
	 * @return    CcBlockQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(CcBlockPeer::ID, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    CcBlockQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(CcBlockPeer::ID, $keys, Criteria::IN);
	}

	/**
	 * Filter the query on the id column
	 * 
	 * @param     int|array $dbId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcBlockQuery The current query, for fluid interface
	 */
	public function filterByDbId($dbId = null, $comparison = null)
	{
		if (is_array($dbId) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(CcBlockPeer::ID, $dbId, $comparison);
	}

	/**
	 * Filter the query on the name column
	 * 
	 * @param     string $dbName The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcBlockQuery The current query, for fluid interface
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
		return $this->addUsingAlias(CcBlockPeer::NAME, $dbName, $comparison);
	}

	/**
	 * Filter the query on the mtime column
	 * 
	 * @param     string|array $dbMtime The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcBlockQuery The current query, for fluid interface
	 */
	public function filterByDbMtime($dbMtime = null, $comparison = null)
	{
		if (is_array($dbMtime)) {
			$useMinMax = false;
			if (isset($dbMtime['min'])) {
				$this->addUsingAlias(CcBlockPeer::MTIME, $dbMtime['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbMtime['max'])) {
				$this->addUsingAlias(CcBlockPeer::MTIME, $dbMtime['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcBlockPeer::MTIME, $dbMtime, $comparison);
	}

	/**
	 * Filter the query on the utime column
	 * 
	 * @param     string|array $dbUtime The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcBlockQuery The current query, for fluid interface
	 */
	public function filterByDbUtime($dbUtime = null, $comparison = null)
	{
		if (is_array($dbUtime)) {
			$useMinMax = false;
			if (isset($dbUtime['min'])) {
				$this->addUsingAlias(CcBlockPeer::UTIME, $dbUtime['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbUtime['max'])) {
				$this->addUsingAlias(CcBlockPeer::UTIME, $dbUtime['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcBlockPeer::UTIME, $dbUtime, $comparison);
	}

	/**
	 * Filter the query on the creator_id column
	 * 
	 * @param     int|array $dbCreatorId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcBlockQuery The current query, for fluid interface
	 */
	public function filterByDbCreatorId($dbCreatorId = null, $comparison = null)
	{
		if (is_array($dbCreatorId)) {
			$useMinMax = false;
			if (isset($dbCreatorId['min'])) {
				$this->addUsingAlias(CcBlockPeer::CREATOR_ID, $dbCreatorId['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbCreatorId['max'])) {
				$this->addUsingAlias(CcBlockPeer::CREATOR_ID, $dbCreatorId['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcBlockPeer::CREATOR_ID, $dbCreatorId, $comparison);
	}

	/**
	 * Filter the query on the description column
	 * 
	 * @param     string $dbDescription The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcBlockQuery The current query, for fluid interface
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
		return $this->addUsingAlias(CcBlockPeer::DESCRIPTION, $dbDescription, $comparison);
	}

	/**
	 * Filter the query on the length column
	 * 
	 * @param     string $dbLength The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcBlockQuery The current query, for fluid interface
	 */
	public function filterByDbLength($dbLength = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbLength)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbLength)) {
				$dbLength = str_replace('*', '%', $dbLength);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcBlockPeer::LENGTH, $dbLength, $comparison);
	}

	/**
	 * Filter the query on the type column
	 * 
	 * @param     string $dbType The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcBlockQuery The current query, for fluid interface
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
		return $this->addUsingAlias(CcBlockPeer::TYPE, $dbType, $comparison);
	}

	/**
	 * Filter the query by a related CcSubjs object
	 *
	 * @param     CcSubjs $ccSubjs  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcBlockQuery The current query, for fluid interface
	 */
	public function filterByCcSubjs($ccSubjs, $comparison = null)
	{
		return $this
			->addUsingAlias(CcBlockPeer::CREATOR_ID, $ccSubjs->getDbId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcSubjs relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcBlockQuery The current query, for fluid interface
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
	 * Filter the query by a related CcPlaylistcontents object
	 *
	 * @param     CcPlaylistcontents $ccPlaylistcontents  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcBlockQuery The current query, for fluid interface
	 */
	public function filterByCcPlaylistcontents($ccPlaylistcontents, $comparison = null)
	{
		return $this
			->addUsingAlias(CcBlockPeer::ID, $ccPlaylistcontents->getDbBlockId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcPlaylistcontents relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcBlockQuery The current query, for fluid interface
	 */
	public function joinCcPlaylistcontents($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('CcPlaylistcontents');
		
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
			$this->addJoinObject($join, 'CcPlaylistcontents');
		}
		
		return $this;
	}

	/**
	 * Use the CcPlaylistcontents relation CcPlaylistcontents object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcPlaylistcontentsQuery A secondary query class using the current class as primary query
	 */
	public function useCcPlaylistcontentsQuery($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinCcPlaylistcontents($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CcPlaylistcontents', 'CcPlaylistcontentsQuery');
	}

	/**
	 * Filter the query by a related CcBlockcontents object
	 *
	 * @param     CcBlockcontents $ccBlockcontents  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcBlockQuery The current query, for fluid interface
	 */
	public function filterByCcBlockcontents($ccBlockcontents, $comparison = null)
	{
		return $this
			->addUsingAlias(CcBlockPeer::ID, $ccBlockcontents->getDbBlockId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcBlockcontents relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcBlockQuery The current query, for fluid interface
	 */
	public function joinCcBlockcontents($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('CcBlockcontents');
		
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
			$this->addJoinObject($join, 'CcBlockcontents');
		}
		
		return $this;
	}

	/**
	 * Use the CcBlockcontents relation CcBlockcontents object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcBlockcontentsQuery A secondary query class using the current class as primary query
	 */
	public function useCcBlockcontentsQuery($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinCcBlockcontents($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CcBlockcontents', 'CcBlockcontentsQuery');
	}

	/**
	 * Filter the query by a related CcBlockcriteria object
	 *
	 * @param     CcBlockcriteria $ccBlockcriteria  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcBlockQuery The current query, for fluid interface
	 */
	public function filterByCcBlockcriteria($ccBlockcriteria, $comparison = null)
	{
		return $this
			->addUsingAlias(CcBlockPeer::ID, $ccBlockcriteria->getDbBlockId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcBlockcriteria relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcBlockQuery The current query, for fluid interface
	 */
	public function joinCcBlockcriteria($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('CcBlockcriteria');
		
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
			$this->addJoinObject($join, 'CcBlockcriteria');
		}
		
		return $this;
	}

	/**
	 * Use the CcBlockcriteria relation CcBlockcriteria object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcBlockcriteriaQuery A secondary query class using the current class as primary query
	 */
	public function useCcBlockcriteriaQuery($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinCcBlockcriteria($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CcBlockcriteria', 'CcBlockcriteriaQuery');
	}

	/**
	 * Exclude object from result
	 *
	 * @param     CcBlock $ccBlock Object to remove from the list of results
	 *
	 * @return    CcBlockQuery The current query, for fluid interface
	 */
	public function prune($ccBlock = null)
	{
		if ($ccBlock) {
			$this->addUsingAlias(CcBlockPeer::ID, $ccBlock->getDbId(), Criteria::NOT_EQUAL);
	  }
	  
		return $this;
	}

} // BaseCcBlockQuery
