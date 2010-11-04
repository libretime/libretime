<?php


/**
 * Base class that represents a query for the 'cc_playlistcontents' table.
 *
 * 
 *
 * @method     CcPlaylistcontentsQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     CcPlaylistcontentsQuery orderByPlaylistId($order = Criteria::ASC) Order by the playlist_id column
 * @method     CcPlaylistcontentsQuery orderByFileId($order = Criteria::ASC) Order by the file_id column
 * @method     CcPlaylistcontentsQuery orderByPosition($order = Criteria::ASC) Order by the position column
 * @method     CcPlaylistcontentsQuery orderByCliplength($order = Criteria::ASC) Order by the cliplength column
 * @method     CcPlaylistcontentsQuery orderByCuein($order = Criteria::ASC) Order by the cuein column
 * @method     CcPlaylistcontentsQuery orderByCueout($order = Criteria::ASC) Order by the cueout column
 * @method     CcPlaylistcontentsQuery orderByFadein($order = Criteria::ASC) Order by the fadein column
 * @method     CcPlaylistcontentsQuery orderByFadeout($order = Criteria::ASC) Order by the fadeout column
 *
 * @method     CcPlaylistcontentsQuery groupById() Group by the id column
 * @method     CcPlaylistcontentsQuery groupByPlaylistId() Group by the playlist_id column
 * @method     CcPlaylistcontentsQuery groupByFileId() Group by the file_id column
 * @method     CcPlaylistcontentsQuery groupByPosition() Group by the position column
 * @method     CcPlaylistcontentsQuery groupByCliplength() Group by the cliplength column
 * @method     CcPlaylistcontentsQuery groupByCuein() Group by the cuein column
 * @method     CcPlaylistcontentsQuery groupByCueout() Group by the cueout column
 * @method     CcPlaylistcontentsQuery groupByFadein() Group by the fadein column
 * @method     CcPlaylistcontentsQuery groupByFadeout() Group by the fadeout column
 *
 * @method     CcPlaylistcontentsQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     CcPlaylistcontentsQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     CcPlaylistcontentsQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     CcPlaylistcontentsQuery leftJoinCcFiles($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcFiles relation
 * @method     CcPlaylistcontentsQuery rightJoinCcFiles($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcFiles relation
 * @method     CcPlaylistcontentsQuery innerJoinCcFiles($relationAlias = '') Adds a INNER JOIN clause to the query using the CcFiles relation
 *
 * @method     CcPlaylistcontentsQuery leftJoinCcPlaylist($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcPlaylist relation
 * @method     CcPlaylistcontentsQuery rightJoinCcPlaylist($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcPlaylist relation
 * @method     CcPlaylistcontentsQuery innerJoinCcPlaylist($relationAlias = '') Adds a INNER JOIN clause to the query using the CcPlaylist relation
 *
 * @method     CcPlaylistcontents findOne(PropelPDO $con = null) Return the first CcPlaylistcontents matching the query
 * @method     CcPlaylistcontents findOneOrCreate(PropelPDO $con = null) Return the first CcPlaylistcontents matching the query, or a new CcPlaylistcontents object populated from the query conditions when no match is found
 *
 * @method     CcPlaylistcontents findOneById(int $id) Return the first CcPlaylistcontents filtered by the id column
 * @method     CcPlaylistcontents findOneByPlaylistId(int $playlist_id) Return the first CcPlaylistcontents filtered by the playlist_id column
 * @method     CcPlaylistcontents findOneByFileId(int $file_id) Return the first CcPlaylistcontents filtered by the file_id column
 * @method     CcPlaylistcontents findOneByPosition(int $position) Return the first CcPlaylistcontents filtered by the position column
 * @method     CcPlaylistcontents findOneByCliplength(string $cliplength) Return the first CcPlaylistcontents filtered by the cliplength column
 * @method     CcPlaylistcontents findOneByCuein(string $cuein) Return the first CcPlaylistcontents filtered by the cuein column
 * @method     CcPlaylistcontents findOneByCueout(string $cueout) Return the first CcPlaylistcontents filtered by the cueout column
 * @method     CcPlaylistcontents findOneByFadein(string $fadein) Return the first CcPlaylistcontents filtered by the fadein column
 * @method     CcPlaylistcontents findOneByFadeout(string $fadeout) Return the first CcPlaylistcontents filtered by the fadeout column
 *
 * @method     array findById(int $id) Return CcPlaylistcontents objects filtered by the id column
 * @method     array findByPlaylistId(int $playlist_id) Return CcPlaylistcontents objects filtered by the playlist_id column
 * @method     array findByFileId(int $file_id) Return CcPlaylistcontents objects filtered by the file_id column
 * @method     array findByPosition(int $position) Return CcPlaylistcontents objects filtered by the position column
 * @method     array findByCliplength(string $cliplength) Return CcPlaylistcontents objects filtered by the cliplength column
 * @method     array findByCuein(string $cuein) Return CcPlaylistcontents objects filtered by the cuein column
 * @method     array findByCueout(string $cueout) Return CcPlaylistcontents objects filtered by the cueout column
 * @method     array findByFadein(string $fadein) Return CcPlaylistcontents objects filtered by the fadein column
 * @method     array findByFadeout(string $fadeout) Return CcPlaylistcontents objects filtered by the fadeout column
 *
 * @package    propel.generator.campcaster.om
 */
abstract class BaseCcPlaylistcontentsQuery extends ModelCriteria
{

	/**
	 * Initializes internal state of BaseCcPlaylistcontentsQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'campcaster', $modelName = 'CcPlaylistcontents', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new CcPlaylistcontentsQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    CcPlaylistcontentsQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof CcPlaylistcontentsQuery) {
			return $criteria;
		}
		$query = new CcPlaylistcontentsQuery();
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
	 * @return    CcPlaylistcontents|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = CcPlaylistcontentsPeer::getInstanceFromPool((string) $key))) && $this->getFormatter()->isObjectFormatter()) {
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
	 * @return    CcPlaylistcontentsQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(CcPlaylistcontentsPeer::ID, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    CcPlaylistcontentsQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(CcPlaylistcontentsPeer::ID, $keys, Criteria::IN);
	}

	/**
	 * Filter the query on the id column
	 * 
	 * @param     int|array $id The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcPlaylistcontentsQuery The current query, for fluid interface
	 */
	public function filterById($id = null, $comparison = null)
	{
		if (is_array($id) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(CcPlaylistcontentsPeer::ID, $id, $comparison);
	}

	/**
	 * Filter the query on the playlist_id column
	 * 
	 * @param     int|array $playlistId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcPlaylistcontentsQuery The current query, for fluid interface
	 */
	public function filterByPlaylistId($playlistId = null, $comparison = null)
	{
		if (is_array($playlistId)) {
			$useMinMax = false;
			if (isset($playlistId['min'])) {
				$this->addUsingAlias(CcPlaylistcontentsPeer::PLAYLIST_ID, $playlistId['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($playlistId['max'])) {
				$this->addUsingAlias(CcPlaylistcontentsPeer::PLAYLIST_ID, $playlistId['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcPlaylistcontentsPeer::PLAYLIST_ID, $playlistId, $comparison);
	}

	/**
	 * Filter the query on the file_id column
	 * 
	 * @param     int|array $fileId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcPlaylistcontentsQuery The current query, for fluid interface
	 */
	public function filterByFileId($fileId = null, $comparison = null)
	{
		if (is_array($fileId)) {
			$useMinMax = false;
			if (isset($fileId['min'])) {
				$this->addUsingAlias(CcPlaylistcontentsPeer::FILE_ID, $fileId['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($fileId['max'])) {
				$this->addUsingAlias(CcPlaylistcontentsPeer::FILE_ID, $fileId['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcPlaylistcontentsPeer::FILE_ID, $fileId, $comparison);
	}

	/**
	 * Filter the query on the position column
	 * 
	 * @param     int|array $position The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcPlaylistcontentsQuery The current query, for fluid interface
	 */
	public function filterByPosition($position = null, $comparison = null)
	{
		if (is_array($position)) {
			$useMinMax = false;
			if (isset($position['min'])) {
				$this->addUsingAlias(CcPlaylistcontentsPeer::POSITION, $position['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($position['max'])) {
				$this->addUsingAlias(CcPlaylistcontentsPeer::POSITION, $position['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcPlaylistcontentsPeer::POSITION, $position, $comparison);
	}

	/**
	 * Filter the query on the cliplength column
	 * 
	 * @param     string|array $cliplength The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcPlaylistcontentsQuery The current query, for fluid interface
	 */
	public function filterByCliplength($cliplength = null, $comparison = null)
	{
		if (is_array($cliplength)) {
			$useMinMax = false;
			if (isset($cliplength['min'])) {
				$this->addUsingAlias(CcPlaylistcontentsPeer::CLIPLENGTH, $cliplength['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($cliplength['max'])) {
				$this->addUsingAlias(CcPlaylistcontentsPeer::CLIPLENGTH, $cliplength['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcPlaylistcontentsPeer::CLIPLENGTH, $cliplength, $comparison);
	}

	/**
	 * Filter the query on the cuein column
	 * 
	 * @param     string|array $cuein The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcPlaylistcontentsQuery The current query, for fluid interface
	 */
	public function filterByCuein($cuein = null, $comparison = null)
	{
		if (is_array($cuein)) {
			$useMinMax = false;
			if (isset($cuein['min'])) {
				$this->addUsingAlias(CcPlaylistcontentsPeer::CUEIN, $cuein['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($cuein['max'])) {
				$this->addUsingAlias(CcPlaylistcontentsPeer::CUEIN, $cuein['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcPlaylistcontentsPeer::CUEIN, $cuein, $comparison);
	}

	/**
	 * Filter the query on the cueout column
	 * 
	 * @param     string|array $cueout The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcPlaylistcontentsQuery The current query, for fluid interface
	 */
	public function filterByCueout($cueout = null, $comparison = null)
	{
		if (is_array($cueout)) {
			$useMinMax = false;
			if (isset($cueout['min'])) {
				$this->addUsingAlias(CcPlaylistcontentsPeer::CUEOUT, $cueout['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($cueout['max'])) {
				$this->addUsingAlias(CcPlaylistcontentsPeer::CUEOUT, $cueout['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcPlaylistcontentsPeer::CUEOUT, $cueout, $comparison);
	}

	/**
	 * Filter the query on the fadein column
	 * 
	 * @param     string|array $fadein The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcPlaylistcontentsQuery The current query, for fluid interface
	 */
	public function filterByFadein($fadein = null, $comparison = null)
	{
		if (is_array($fadein)) {
			$useMinMax = false;
			if (isset($fadein['min'])) {
				$this->addUsingAlias(CcPlaylistcontentsPeer::FADEIN, $fadein['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($fadein['max'])) {
				$this->addUsingAlias(CcPlaylistcontentsPeer::FADEIN, $fadein['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcPlaylistcontentsPeer::FADEIN, $fadein, $comparison);
	}

	/**
	 * Filter the query on the fadeout column
	 * 
	 * @param     string|array $fadeout The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcPlaylistcontentsQuery The current query, for fluid interface
	 */
	public function filterByFadeout($fadeout = null, $comparison = null)
	{
		if (is_array($fadeout)) {
			$useMinMax = false;
			if (isset($fadeout['min'])) {
				$this->addUsingAlias(CcPlaylistcontentsPeer::FADEOUT, $fadeout['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($fadeout['max'])) {
				$this->addUsingAlias(CcPlaylistcontentsPeer::FADEOUT, $fadeout['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcPlaylistcontentsPeer::FADEOUT, $fadeout, $comparison);
	}

	/**
	 * Filter the query by a related CcFiles object
	 *
	 * @param     CcFiles $ccFiles  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcPlaylistcontentsQuery The current query, for fluid interface
	 */
	public function filterByCcFiles($ccFiles, $comparison = null)
	{
		return $this
			->addUsingAlias(CcPlaylistcontentsPeer::FILE_ID, $ccFiles->getId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcFiles relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcPlaylistcontentsQuery The current query, for fluid interface
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
	 * Filter the query by a related CcPlaylist object
	 *
	 * @param     CcPlaylist $ccPlaylist  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcPlaylistcontentsQuery The current query, for fluid interface
	 */
	public function filterByCcPlaylist($ccPlaylist, $comparison = null)
	{
		return $this
			->addUsingAlias(CcPlaylistcontentsPeer::PLAYLIST_ID, $ccPlaylist->getId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcPlaylist relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcPlaylistcontentsQuery The current query, for fluid interface
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
	 * Exclude object from result
	 *
	 * @param     CcPlaylistcontents $ccPlaylistcontents Object to remove from the list of results
	 *
	 * @return    CcPlaylistcontentsQuery The current query, for fluid interface
	 */
	public function prune($ccPlaylistcontents = null)
	{
		if ($ccPlaylistcontents) {
			$this->addUsingAlias(CcPlaylistcontentsPeer::ID, $ccPlaylistcontents->getId(), Criteria::NOT_EQUAL);
	  }
	  
		return $this;
	}

} // BaseCcPlaylistcontentsQuery
