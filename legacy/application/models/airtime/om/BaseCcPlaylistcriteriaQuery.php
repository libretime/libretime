<?php


/**
 * Base class that represents a query for the 'cc_playlistcriteria' table.
 *
 *
 *
 * @method     CcPlaylistcriteriaQuery orderByDbId($order = Criteria::ASC) Order by the id column
 * @method     CcPlaylistcriteriaQuery orderByDbCriteria($order = Criteria::ASC) Order by the criteria column
 * @method     CcPlaylistcriteriaQuery orderByDbModifier($order = Criteria::ASC) Order by the modifier column
 * @method     CcPlaylistcriteriaQuery orderByDbValue($order = Criteria::ASC) Order by the value column
 * @method     CcPlaylistcriteriaQuery orderByDbExtra($order = Criteria::ASC) Order by the extra column
 * @method     CcPlaylistcriteriaQuery orderByDbPlaylistId($order = Criteria::ASC) Order by the playlist_id column
 * @method     CcPlaylistcriteriaQuery orderByDbSetNumber($order = Criteria::ASC) Order by the set_number column
 *
 * @method     CcPlaylistcriteriaQuery groupByDbId() Group by the id column
 * @method     CcPlaylistcriteriaQuery groupByDbCriteria() Group by the criteria column
 * @method     CcPlaylistcriteriaQuery groupByDbModifier() Group by the modifier column
 * @method     CcPlaylistcriteriaQuery groupByDbValue() Group by the value column
 * @method     CcPlaylistcriteriaQuery groupByDbExtra() Group by the extra column
 * @method     CcPlaylistcriteriaQuery groupByDbPlaylistId() Group by the playlist_id column
 * @method     CcPlaylistcriteriaQuery groupByDbSetNumber() Group by the set_number column
 *
 * @method     CcPlaylistcriteriaQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     CcPlaylistcriteriaQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     CcPlaylistcriteriaQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     CcPlaylistcriteriaQuery leftJoinCcPlaylist($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcPlaylist relation
 * @method     CcPlaylistcriteriaQuery rightJoinCcPlaylist($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcPlaylist relation
 * @method     CcPlaylistcriteriaQuery innerJoinCcPlaylist($relationAlias = '') Adds a INNER JOIN clause to the query using the CcPlaylist relation
 *
 * @method     CcPlaylistcriteria findOne(PropelPDO $con = null) Return the first CcPlaylistcriteria matching the query
 * @method     CcPlaylistcriteria findOneOrCreate(PropelPDO $con = null) Return the first CcPlaylistcriteria matching the query, or a new CcPlaylistcriteria object populated from the query conditions when no match is found
 *
 * @method     CcPlaylistcriteria findOneByDbId(int $id) Return the first CcPlaylistcriteria filtered by the id column
 * @method     CcPlaylistcriteria findOneByDbCriteria(string $criteria) Return the first CcPlaylistcriteria filtered by the criteria column
 * @method     CcPlaylistcriteria findOneByDbModifier(string $modifier) Return the first CcPlaylistcriteria filtered by the modifier column
 * @method     CcPlaylistcriteria findOneByDbValue(string $value) Return the first CcPlaylistcriteria filtered by the value column
 * @method     CcPlaylistcriteria findOneByDbExtra(string $extra) Return the first CcPlaylistcriteria filtered by the extra column
 * @method     CcPlaylistcriteria findOneByDbPlaylistId(int $playlist_id) Return the first CcPlaylistcriteria filtered by the playlist_id column
 * @method     CcPlaylistcriteria findOneByDbSetNumber(int $set_number) Return the first CcPlaylistcriteria filtered by the set_number column
 *
 * @method     array findByDbId(int $id) Return CcPlaylistcriteria objects filtered by the id column
 * @method     array findByDbCriteria(string $criteria) Return CcPlaylistcriteria objects filtered by the criteria column
 * @method     array findByDbModifier(string $modifier) Return CcPlaylistcriteria objects filtered by the modifier column
 * @method     array findByDbValue(string $value) Return CcPlaylistcriteria objects filtered by the value column
 * @method     array findByDbExtra(string $extra) Return CcPlaylistcriteria objects filtered by the extra column
 * @method     array findByDbPlaylistId(int $playlist_id) Return CcPlaylistcriteria objects filtered by the playlist_id column
 * @method     array findByDbSetNumber(int $set_number) Return CcPlaylistcriteria objects filtered by the set_number column
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcPlaylistcriteriaQuery extends ModelCriteria
{

	/**
	 * Initializes internal state of BaseCcPlaylistcriteriaQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'airtime', $modelName = 'CcPlaylistcriteria', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new CcPlaylistcriteriaQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    CcPlaylistcriteriaQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof CcPlaylistcriteriaQuery) {
			return $criteria;
		}
		$query = new CcPlaylistcriteriaQuery();
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
	 * @return    CcPlaylistcriteria|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = CcPlaylistcriteriaPeer::getInstanceFromPool((string) $key))) && $this->getFormatter()->isObjectFormatter()) {
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
	 * @return    CcPlaylistcriteriaQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(CcPlaylistcriteriaPeer::ID, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    CcPlaylistcriteriaQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(CcPlaylistcriteriaPeer::ID, $keys, Criteria::IN);
	}

	/**
	 * Filter the query on the id column
	 *
	 * @param     int|array $dbId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcPlaylistcriteriaQuery The current query, for fluid interface
	 */
	public function filterByDbId($dbId = null, $comparison = null)
	{
		if (is_array($dbId) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(CcPlaylistcriteriaPeer::ID, $dbId, $comparison);
	}

	/**
	 * Filter the query on the criteria column
	 *
	 * @param     string $dbCriteria The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcPlaylistcriteriaQuery The current query, for fluid interface
	 */
	public function filterByDbCriteria($dbCriteria = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbCriteria)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbCriteria)) {
				$dbCriteria = str_replace('*', '%', $dbCriteria);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcPlaylistcriteriaPeer::CRITERIA, $dbCriteria, $comparison);
	}

	/**
	 * Filter the query on the modifier column
	 *
	 * @param     string $dbModifier The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcPlaylistcriteriaQuery The current query, for fluid interface
	 */
	public function filterByDbModifier($dbModifier = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbModifier)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbModifier)) {
				$dbModifier = str_replace('*', '%', $dbModifier);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcPlaylistcriteriaPeer::MODIFIER, $dbModifier, $comparison);
	}

	/**
	 * Filter the query on the value column
	 *
	 * @param     string $dbValue The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcPlaylistcriteriaQuery The current query, for fluid interface
	 */
	public function filterByDbValue($dbValue = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbValue)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbValue)) {
				$dbValue = str_replace('*', '%', $dbValue);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcPlaylistcriteriaPeer::VALUE, $dbValue, $comparison);
	}

	/**
	 * Filter the query on the extra column
	 *
	 * @param     string $dbExtra The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcPlaylistcriteriaQuery The current query, for fluid interface
	 */
	public function filterByDbExtra($dbExtra = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbExtra)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbExtra)) {
				$dbExtra = str_replace('*', '%', $dbExtra);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcPlaylistcriteriaPeer::EXTRA, $dbExtra, $comparison);
	}

	/**
	 * Filter the query on the playlist_id column
	 *
	 * @param     int|array $dbPlaylistId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcPlaylistcriteriaQuery The current query, for fluid interface
	 */
	public function filterByDbPlaylistId($dbPlaylistId = null, $comparison = null)
	{
		if (is_array($dbPlaylistId)) {
			$useMinMax = false;
			if (isset($dbPlaylistId['min'])) {
				$this->addUsingAlias(CcPlaylistcriteriaPeer::PLAYLIST_ID, $dbPlaylistId['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbPlaylistId['max'])) {
				$this->addUsingAlias(CcPlaylistcriteriaPeer::PLAYLIST_ID, $dbPlaylistId['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcPlaylistcriteriaPeer::PLAYLIST_ID, $dbPlaylistId, $comparison);
	}

	/**
	 * Filter the query on the set_number column
	 *
	 * @param     int|array $dbSetNumber The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcPlaylistcriteriaQuery The current query, for fluid interface
	 */
	public function filterByDbSetNumber($dbSetNumber = null, $comparison = null)
	{
		if (is_array($dbSetNumber)) {
			$useMinMax = false;
			if (isset($dbSetNumber['min'])) {
				$this->addUsingAlias(CcPlaylistcriteriaPeer::SET_NUMBER, $dbSetNumber['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbSetNumber['max'])) {
				$this->addUsingAlias(CcPlaylistcriteriaPeer::SET_NUMBER, $dbSetNumber['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcPlaylistcriteriaPeer::SET_NUMBER, $dbSetNumber, $comparison);
	}

	/**
	 * Filter the query by a related CcPlaylist object
	 *
	 * @param     CcPlaylist $ccPlaylist  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcPlaylistcriteriaQuery The current query, for fluid interface
	 */
	public function filterByCcPlaylist($ccPlaylist, $comparison = null)
	{
		return $this
			->addUsingAlias(CcPlaylistcriteriaPeer::PLAYLIST_ID, $ccPlaylist->getDbId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcPlaylist relation
	 *
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcPlaylistcriteriaQuery The current query, for fluid interface
	 */
	public function joinCcPlaylist($relationAlias = '', $joinType = Criteria::INNER_JOIN)
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
	public function useCcPlaylistQuery($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinCcPlaylist($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CcPlaylist', 'CcPlaylistQuery');
	}

	/**
	 * Exclude object from result
	 *
	 * @param     CcPlaylistcriteria $ccPlaylistcriteria Object to remove from the list of results
	 *
	 * @return    CcPlaylistcriteriaQuery The current query, for fluid interface
	 */
	public function prune($ccPlaylistcriteria = null)
	{
		if ($ccPlaylistcriteria) {
			$this->addUsingAlias(CcPlaylistcriteriaPeer::ID, $ccPlaylistcriteria->getDbId(), Criteria::NOT_EQUAL);
	  }

		return $this;
	}

} // BaseCcPlaylistcriteriaQuery
