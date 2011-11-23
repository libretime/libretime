<?php


/**
 * Base class that represents a query for the 'cc_playlist' table.
 *
 * 
 *
 * @method     CcPlaylistQuery orderByDbId($order = Criteria::ASC) Order by the id column
 * @method     CcPlaylistQuery orderByDbName($order = Criteria::ASC) Order by the name column
 * @method     CcPlaylistQuery orderByDbState($order = Criteria::ASC) Order by the state column
 * @method     CcPlaylistQuery orderByDbCurrentlyaccessing($order = Criteria::ASC) Order by the currentlyaccessing column
 * @method     CcPlaylistQuery orderByDbEditedby($order = Criteria::ASC) Order by the editedby column
 * @method     CcPlaylistQuery orderByDbMtime($order = Criteria::ASC) Order by the mtime column
 * @method     CcPlaylistQuery orderByDbCreator($order = Criteria::ASC) Order by the creator column
 * @method     CcPlaylistQuery orderByDbDescription($order = Criteria::ASC) Order by the description column
 *
 * @method     CcPlaylistQuery groupByDbId() Group by the id column
 * @method     CcPlaylistQuery groupByDbName() Group by the name column
 * @method     CcPlaylistQuery groupByDbState() Group by the state column
 * @method     CcPlaylistQuery groupByDbCurrentlyaccessing() Group by the currentlyaccessing column
 * @method     CcPlaylistQuery groupByDbEditedby() Group by the editedby column
 * @method     CcPlaylistQuery groupByDbMtime() Group by the mtime column
 * @method     CcPlaylistQuery groupByDbCreator() Group by the creator column
 * @method     CcPlaylistQuery groupByDbDescription() Group by the description column
 *
 * @method     CcPlaylistQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     CcPlaylistQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     CcPlaylistQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     CcPlaylistQuery leftJoinCcSubjs($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcSubjs relation
 * @method     CcPlaylistQuery rightJoinCcSubjs($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcSubjs relation
 * @method     CcPlaylistQuery innerJoinCcSubjs($relationAlias = '') Adds a INNER JOIN clause to the query using the CcSubjs relation
 *
 * @method     CcPlaylistQuery leftJoinCcPlaylistcontents($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcPlaylistcontents relation
 * @method     CcPlaylistQuery rightJoinCcPlaylistcontents($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcPlaylistcontents relation
 * @method     CcPlaylistQuery innerJoinCcPlaylistcontents($relationAlias = '') Adds a INNER JOIN clause to the query using the CcPlaylistcontents relation
 *
 * @method     CcPlaylist findOne(PropelPDO $con = null) Return the first CcPlaylist matching the query
 * @method     CcPlaylist findOneOrCreate(PropelPDO $con = null) Return the first CcPlaylist matching the query, or a new CcPlaylist object populated from the query conditions when no match is found
 *
 * @method     CcPlaylist findOneByDbId(int $id) Return the first CcPlaylist filtered by the id column
 * @method     CcPlaylist findOneByDbName(string $name) Return the first CcPlaylist filtered by the name column
 * @method     CcPlaylist findOneByDbState(string $state) Return the first CcPlaylist filtered by the state column
 * @method     CcPlaylist findOneByDbCurrentlyaccessing(int $currentlyaccessing) Return the first CcPlaylist filtered by the currentlyaccessing column
 * @method     CcPlaylist findOneByDbEditedby(int $editedby) Return the first CcPlaylist filtered by the editedby column
 * @method     CcPlaylist findOneByDbMtime(string $mtime) Return the first CcPlaylist filtered by the mtime column
 * @method     CcPlaylist findOneByDbCreator(string $creator) Return the first CcPlaylist filtered by the creator column
 * @method     CcPlaylist findOneByDbDescription(string $description) Return the first CcPlaylist filtered by the description column
 *
 * @method     array findByDbId(int $id) Return CcPlaylist objects filtered by the id column
 * @method     array findByDbName(string $name) Return CcPlaylist objects filtered by the name column
 * @method     array findByDbState(string $state) Return CcPlaylist objects filtered by the state column
 * @method     array findByDbCurrentlyaccessing(int $currentlyaccessing) Return CcPlaylist objects filtered by the currentlyaccessing column
 * @method     array findByDbEditedby(int $editedby) Return CcPlaylist objects filtered by the editedby column
 * @method     array findByDbMtime(string $mtime) Return CcPlaylist objects filtered by the mtime column
 * @method     array findByDbCreator(string $creator) Return CcPlaylist objects filtered by the creator column
 * @method     array findByDbDescription(string $description) Return CcPlaylist objects filtered by the description column
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcPlaylistQuery extends ModelCriteria
{

	/**
	 * Initializes internal state of BaseCcPlaylistQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'airtime', $modelName = 'CcPlaylist', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new CcPlaylistQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    CcPlaylistQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof CcPlaylistQuery) {
			return $criteria;
		}
		$query = new CcPlaylistQuery();
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
	 * @return    CcPlaylist|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = CcPlaylistPeer::getInstanceFromPool((string) $key))) && $this->getFormatter()->isObjectFormatter()) {
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
	 * @return    CcPlaylistQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(CcPlaylistPeer::ID, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    CcPlaylistQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(CcPlaylistPeer::ID, $keys, Criteria::IN);
	}

	/**
	 * Filter the query on the id column
	 * 
	 * @param     int|array $dbId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcPlaylistQuery The current query, for fluid interface
	 */
	public function filterByDbId($dbId = null, $comparison = null)
	{
		if (is_array($dbId) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(CcPlaylistPeer::ID, $dbId, $comparison);
	}

	/**
	 * Filter the query on the name column
	 * 
	 * @param     string $dbName The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcPlaylistQuery The current query, for fluid interface
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
		return $this->addUsingAlias(CcPlaylistPeer::NAME, $dbName, $comparison);
	}

	/**
	 * Filter the query on the state column
	 * 
	 * @param     string $dbState The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcPlaylistQuery The current query, for fluid interface
	 */
	public function filterByDbState($dbState = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbState)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbState)) {
				$dbState = str_replace('*', '%', $dbState);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcPlaylistPeer::STATE, $dbState, $comparison);
	}

	/**
	 * Filter the query on the currentlyaccessing column
	 * 
	 * @param     int|array $dbCurrentlyaccessing The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcPlaylistQuery The current query, for fluid interface
	 */
	public function filterByDbCurrentlyaccessing($dbCurrentlyaccessing = null, $comparison = null)
	{
		if (is_array($dbCurrentlyaccessing)) {
			$useMinMax = false;
			if (isset($dbCurrentlyaccessing['min'])) {
				$this->addUsingAlias(CcPlaylistPeer::CURRENTLYACCESSING, $dbCurrentlyaccessing['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbCurrentlyaccessing['max'])) {
				$this->addUsingAlias(CcPlaylistPeer::CURRENTLYACCESSING, $dbCurrentlyaccessing['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcPlaylistPeer::CURRENTLYACCESSING, $dbCurrentlyaccessing, $comparison);
	}

	/**
	 * Filter the query on the editedby column
	 * 
	 * @param     int|array $dbEditedby The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcPlaylistQuery The current query, for fluid interface
	 */
	public function filterByDbEditedby($dbEditedby = null, $comparison = null)
	{
		if (is_array($dbEditedby)) {
			$useMinMax = false;
			if (isset($dbEditedby['min'])) {
				$this->addUsingAlias(CcPlaylistPeer::EDITEDBY, $dbEditedby['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbEditedby['max'])) {
				$this->addUsingAlias(CcPlaylistPeer::EDITEDBY, $dbEditedby['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcPlaylistPeer::EDITEDBY, $dbEditedby, $comparison);
	}

	/**
	 * Filter the query on the mtime column
	 * 
	 * @param     string|array $dbMtime The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcPlaylistQuery The current query, for fluid interface
	 */
	public function filterByDbMtime($dbMtime = null, $comparison = null)
	{
		if (is_array($dbMtime)) {
			$useMinMax = false;
			if (isset($dbMtime['min'])) {
				$this->addUsingAlias(CcPlaylistPeer::MTIME, $dbMtime['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbMtime['max'])) {
				$this->addUsingAlias(CcPlaylistPeer::MTIME, $dbMtime['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcPlaylistPeer::MTIME, $dbMtime, $comparison);
	}

	/**
	 * Filter the query on the creator column
	 * 
	 * @param     string $dbCreator The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcPlaylistQuery The current query, for fluid interface
	 */
	public function filterByDbCreator($dbCreator = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbCreator)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbCreator)) {
				$dbCreator = str_replace('*', '%', $dbCreator);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcPlaylistPeer::CREATOR, $dbCreator, $comparison);
	}

	/**
	 * Filter the query on the description column
	 * 
	 * @param     string $dbDescription The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcPlaylistQuery The current query, for fluid interface
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
		return $this->addUsingAlias(CcPlaylistPeer::DESCRIPTION, $dbDescription, $comparison);
	}

	/**
	 * Filter the query by a related CcSubjs object
	 *
	 * @param     CcSubjs $ccSubjs  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcPlaylistQuery The current query, for fluid interface
	 */
	public function filterByCcSubjs($ccSubjs, $comparison = null)
	{
		return $this
			->addUsingAlias(CcPlaylistPeer::EDITEDBY, $ccSubjs->getDbId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcSubjs relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcPlaylistQuery The current query, for fluid interface
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
	 * @return    CcPlaylistQuery The current query, for fluid interface
	 */
	public function filterByCcPlaylistcontents($ccPlaylistcontents, $comparison = null)
	{
		return $this
			->addUsingAlias(CcPlaylistPeer::ID, $ccPlaylistcontents->getDbPlaylistId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcPlaylistcontents relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcPlaylistQuery The current query, for fluid interface
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
	 * Exclude object from result
	 *
	 * @param     CcPlaylist $ccPlaylist Object to remove from the list of results
	 *
	 * @return    CcPlaylistQuery The current query, for fluid interface
	 */
	public function prune($ccPlaylist = null)
	{
		if ($ccPlaylist) {
			$this->addUsingAlias(CcPlaylistPeer::ID, $ccPlaylist->getDbId(), Criteria::NOT_EQUAL);
	  }
	  
		return $this;
	}

} // BaseCcPlaylistQuery
