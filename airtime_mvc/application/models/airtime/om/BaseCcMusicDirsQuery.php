<?php


/**
 * Base class that represents a query for the 'cc_music_dirs' table.
 *
 * 
 *
 * @method     CcMusicDirsQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     CcMusicDirsQuery orderByDirectory($order = Criteria::ASC) Order by the directory column
 * @method     CcMusicDirsQuery orderByType($order = Criteria::ASC) Order by the type column
 * @method     CcMusicDirsQuery orderByExists($order = Criteria::ASC) Order by the exists column
 * @method     CcMusicDirsQuery orderByWatched($order = Criteria::ASC) Order by the watched column
 *
 * @method     CcMusicDirsQuery groupById() Group by the id column
 * @method     CcMusicDirsQuery groupByDirectory() Group by the directory column
 * @method     CcMusicDirsQuery groupByType() Group by the type column
 * @method     CcMusicDirsQuery groupByExists() Group by the exists column
 * @method     CcMusicDirsQuery groupByWatched() Group by the watched column
 *
 * @method     CcMusicDirsQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     CcMusicDirsQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     CcMusicDirsQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     CcMusicDirsQuery leftJoinCcFiles($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcFiles relation
 * @method     CcMusicDirsQuery rightJoinCcFiles($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcFiles relation
 * @method     CcMusicDirsQuery innerJoinCcFiles($relationAlias = '') Adds a INNER JOIN clause to the query using the CcFiles relation
 *
 * @method     CcMusicDirs findOne(PropelPDO $con = null) Return the first CcMusicDirs matching the query
 * @method     CcMusicDirs findOneOrCreate(PropelPDO $con = null) Return the first CcMusicDirs matching the query, or a new CcMusicDirs object populated from the query conditions when no match is found
 *
 * @method     CcMusicDirs findOneById(int $id) Return the first CcMusicDirs filtered by the id column
 * @method     CcMusicDirs findOneByDirectory(string $directory) Return the first CcMusicDirs filtered by the directory column
 * @method     CcMusicDirs findOneByType(string $type) Return the first CcMusicDirs filtered by the type column
 * @method     CcMusicDirs findOneByExists(boolean $exists) Return the first CcMusicDirs filtered by the exists column
 * @method     CcMusicDirs findOneByWatched(boolean $watched) Return the first CcMusicDirs filtered by the watched column
 *
 * @method     array findById(int $id) Return CcMusicDirs objects filtered by the id column
 * @method     array findByDirectory(string $directory) Return CcMusicDirs objects filtered by the directory column
 * @method     array findByType(string $type) Return CcMusicDirs objects filtered by the type column
 * @method     array findByExists(boolean $exists) Return CcMusicDirs objects filtered by the exists column
 * @method     array findByWatched(boolean $watched) Return CcMusicDirs objects filtered by the watched column
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcMusicDirsQuery extends ModelCriteria
{

	/**
	 * Initializes internal state of BaseCcMusicDirsQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'airtime', $modelName = 'CcMusicDirs', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new CcMusicDirsQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    CcMusicDirsQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof CcMusicDirsQuery) {
			return $criteria;
		}
		$query = new CcMusicDirsQuery();
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
	 * @return    CcMusicDirs|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = CcMusicDirsPeer::getInstanceFromPool((string) $key))) && $this->getFormatter()->isObjectFormatter()) {
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
	 * @return    CcMusicDirsQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(CcMusicDirsPeer::ID, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    CcMusicDirsQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(CcMusicDirsPeer::ID, $keys, Criteria::IN);
	}

	/**
	 * Filter the query on the id column
	 * 
	 * @param     int|array $id The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcMusicDirsQuery The current query, for fluid interface
	 */
	public function filterById($id = null, $comparison = null)
	{
		if (is_array($id) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(CcMusicDirsPeer::ID, $id, $comparison);
	}

	/**
	 * Filter the query on the directory column
	 * 
	 * @param     string $directory The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcMusicDirsQuery The current query, for fluid interface
	 */
	public function filterByDirectory($directory = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($directory)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $directory)) {
				$directory = str_replace('*', '%', $directory);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcMusicDirsPeer::DIRECTORY, $directory, $comparison);
	}

	/**
	 * Filter the query on the type column
	 * 
	 * @param     string $type The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcMusicDirsQuery The current query, for fluid interface
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
		return $this->addUsingAlias(CcMusicDirsPeer::TYPE, $type, $comparison);
	}

	/**
	 * Filter the query on the exists column
	 * 
	 * @param     boolean|string $exists The value to use as filter.
	 *            Accepts strings ('false', 'off', '-', 'no', 'n', and '0' are false, the rest is true)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcMusicDirsQuery The current query, for fluid interface
	 */
	public function filterByExists($exists = null, $comparison = null)
	{
		if (is_string($exists)) {
			$exists = in_array(strtolower($exists), array('false', 'off', '-', 'no', 'n', '0')) ? false : true;
		}
		return $this->addUsingAlias(CcMusicDirsPeer::EXISTS, $exists, $comparison);
	}

	/**
	 * Filter the query on the watched column
	 * 
	 * @param     boolean|string $watched The value to use as filter.
	 *            Accepts strings ('false', 'off', '-', 'no', 'n', and '0' are false, the rest is true)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcMusicDirsQuery The current query, for fluid interface
	 */
	public function filterByWatched($watched = null, $comparison = null)
	{
		if (is_string($watched)) {
			$watched = in_array(strtolower($watched), array('false', 'off', '-', 'no', 'n', '0')) ? false : true;
		}
		return $this->addUsingAlias(CcMusicDirsPeer::WATCHED, $watched, $comparison);
	}

	/**
	 * Filter the query by a related CcFiles object
	 *
	 * @param     CcFiles $ccFiles  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcMusicDirsQuery The current query, for fluid interface
	 */
	public function filterByCcFiles($ccFiles, $comparison = null)
	{
		return $this
			->addUsingAlias(CcMusicDirsPeer::ID, $ccFiles->getDbDirectory(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcFiles relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcMusicDirsQuery The current query, for fluid interface
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
	 * Exclude object from result
	 *
	 * @param     CcMusicDirs $ccMusicDirs Object to remove from the list of results
	 *
	 * @return    CcMusicDirsQuery The current query, for fluid interface
	 */
	public function prune($ccMusicDirs = null)
	{
		if ($ccMusicDirs) {
			$this->addUsingAlias(CcMusicDirsPeer::ID, $ccMusicDirs->getId(), Criteria::NOT_EQUAL);
	  }
	  
		return $this;
	}

} // BaseCcMusicDirsQuery
