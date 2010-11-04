<?php


/**
 * Base class that represents a query for the 'cc_playlist' table.
 *
 * 
 *
 * @method     CcPlaylistQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     CcPlaylistQuery orderByName($order = Criteria::ASC) Order by the name column
 * @method     CcPlaylistQuery orderByState($order = Criteria::ASC) Order by the state column
 * @method     CcPlaylistQuery orderByCurrentlyaccessing($order = Criteria::ASC) Order by the currentlyaccessing column
 * @method     CcPlaylistQuery orderByEditedby($order = Criteria::ASC) Order by the editedby column
 * @method     CcPlaylistQuery orderByMtime($order = Criteria::ASC) Order by the mtime column
 * @method     CcPlaylistQuery orderByCreator($order = Criteria::ASC) Order by the creator column
 * @method     CcPlaylistQuery orderByDescription($order = Criteria::ASC) Order by the description column
 *
 * @method     CcPlaylistQuery groupById() Group by the id column
 * @method     CcPlaylistQuery groupByName() Group by the name column
 * @method     CcPlaylistQuery groupByState() Group by the state column
 * @method     CcPlaylistQuery groupByCurrentlyaccessing() Group by the currentlyaccessing column
 * @method     CcPlaylistQuery groupByEditedby() Group by the editedby column
 * @method     CcPlaylistQuery groupByMtime() Group by the mtime column
 * @method     CcPlaylistQuery groupByCreator() Group by the creator column
 * @method     CcPlaylistQuery groupByDescription() Group by the description column
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
 * @method     CcPlaylist findOneById(int $id) Return the first CcPlaylist filtered by the id column
 * @method     CcPlaylist findOneByName(string $name) Return the first CcPlaylist filtered by the name column
 * @method     CcPlaylist findOneByState(string $state) Return the first CcPlaylist filtered by the state column
 * @method     CcPlaylist findOneByCurrentlyaccessing(int $currentlyaccessing) Return the first CcPlaylist filtered by the currentlyaccessing column
 * @method     CcPlaylist findOneByEditedby(int $editedby) Return the first CcPlaylist filtered by the editedby column
 * @method     CcPlaylist findOneByMtime(string $mtime) Return the first CcPlaylist filtered by the mtime column
 * @method     CcPlaylist findOneByCreator(string $creator) Return the first CcPlaylist filtered by the creator column
 * @method     CcPlaylist findOneByDescription(string $description) Return the first CcPlaylist filtered by the description column
 *
 * @method     array findById(int $id) Return CcPlaylist objects filtered by the id column
 * @method     array findByName(string $name) Return CcPlaylist objects filtered by the name column
 * @method     array findByState(string $state) Return CcPlaylist objects filtered by the state column
 * @method     array findByCurrentlyaccessing(int $currentlyaccessing) Return CcPlaylist objects filtered by the currentlyaccessing column
 * @method     array findByEditedby(int $editedby) Return CcPlaylist objects filtered by the editedby column
 * @method     array findByMtime(string $mtime) Return CcPlaylist objects filtered by the mtime column
 * @method     array findByCreator(string $creator) Return CcPlaylist objects filtered by the creator column
 * @method     array findByDescription(string $description) Return CcPlaylist objects filtered by the description column
 *
 * @package    propel.generator.campcaster.om
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
	public function __construct($dbName = 'campcaster', $modelName = 'CcPlaylist', $modelAlias = null)
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
	 * @param     int|array $id The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcPlaylistQuery The current query, for fluid interface
	 */
	public function filterById($id = null, $comparison = null)
	{
		if (is_array($id) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(CcPlaylistPeer::ID, $id, $comparison);
	}

	/**
	 * Filter the query on the name column
	 * 
	 * @param     string $name The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcPlaylistQuery The current query, for fluid interface
	 */
	public function filterByName($name = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($name)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $name)) {
				$name = str_replace('*', '%', $name);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcPlaylistPeer::NAME, $name, $comparison);
	}

	/**
	 * Filter the query on the state column
	 * 
	 * @param     string $state The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcPlaylistQuery The current query, for fluid interface
	 */
	public function filterByState($state = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($state)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $state)) {
				$state = str_replace('*', '%', $state);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcPlaylistPeer::STATE, $state, $comparison);
	}

	/**
	 * Filter the query on the currentlyaccessing column
	 * 
	 * @param     int|array $currentlyaccessing The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcPlaylistQuery The current query, for fluid interface
	 */
	public function filterByCurrentlyaccessing($currentlyaccessing = null, $comparison = null)
	{
		if (is_array($currentlyaccessing)) {
			$useMinMax = false;
			if (isset($currentlyaccessing['min'])) {
				$this->addUsingAlias(CcPlaylistPeer::CURRENTLYACCESSING, $currentlyaccessing['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($currentlyaccessing['max'])) {
				$this->addUsingAlias(CcPlaylistPeer::CURRENTLYACCESSING, $currentlyaccessing['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcPlaylistPeer::CURRENTLYACCESSING, $currentlyaccessing, $comparison);
	}

	/**
	 * Filter the query on the editedby column
	 * 
	 * @param     int|array $editedby The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcPlaylistQuery The current query, for fluid interface
	 */
	public function filterByEditedby($editedby = null, $comparison = null)
	{
		if (is_array($editedby)) {
			$useMinMax = false;
			if (isset($editedby['min'])) {
				$this->addUsingAlias(CcPlaylistPeer::EDITEDBY, $editedby['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($editedby['max'])) {
				$this->addUsingAlias(CcPlaylistPeer::EDITEDBY, $editedby['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcPlaylistPeer::EDITEDBY, $editedby, $comparison);
	}

	/**
	 * Filter the query on the mtime column
	 * 
	 * @param     string|array $mtime The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcPlaylistQuery The current query, for fluid interface
	 */
	public function filterByMtime($mtime = null, $comparison = null)
	{
		if (is_array($mtime)) {
			$useMinMax = false;
			if (isset($mtime['min'])) {
				$this->addUsingAlias(CcPlaylistPeer::MTIME, $mtime['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($mtime['max'])) {
				$this->addUsingAlias(CcPlaylistPeer::MTIME, $mtime['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcPlaylistPeer::MTIME, $mtime, $comparison);
	}

	/**
	 * Filter the query on the creator column
	 * 
	 * @param     string $creator The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcPlaylistQuery The current query, for fluid interface
	 */
	public function filterByCreator($creator = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($creator)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $creator)) {
				$creator = str_replace('*', '%', $creator);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcPlaylistPeer::CREATOR, $creator, $comparison);
	}

	/**
	 * Filter the query on the description column
	 * 
	 * @param     string $description The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcPlaylistQuery The current query, for fluid interface
	 */
	public function filterByDescription($description = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($description)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $description)) {
				$description = str_replace('*', '%', $description);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcPlaylistPeer::DESCRIPTION, $description, $comparison);
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
			->addUsingAlias(CcPlaylistPeer::EDITEDBY, $ccSubjs->getId(), $comparison);
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
			->addUsingAlias(CcPlaylistPeer::ID, $ccPlaylistcontents->getPlaylistId(), $comparison);
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
			$this->addUsingAlias(CcPlaylistPeer::ID, $ccPlaylist->getId(), Criteria::NOT_EQUAL);
	  }
	  
		return $this;
	}

} // BaseCcPlaylistQuery
