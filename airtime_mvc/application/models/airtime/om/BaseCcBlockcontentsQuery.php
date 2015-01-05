<?php


/**
 * Base class that represents a query for the 'cc_blockcontents' table.
 *
 * 
 *
 * @method     CcBlockcontentsQuery orderByDbId($order = Criteria::ASC) Order by the id column
 * @method     CcBlockcontentsQuery orderByDbBlockId($order = Criteria::ASC) Order by the block_id column
 * @method     CcBlockcontentsQuery orderByDbFileId($order = Criteria::ASC) Order by the file_id column
 * @method     CcBlockcontentsQuery orderByDbPosition($order = Criteria::ASC) Order by the position column
 * @method     CcBlockcontentsQuery orderByDbTrackOffset($order = Criteria::ASC) Order by the trackoffset column
 * @method     CcBlockcontentsQuery orderByDbCliplength($order = Criteria::ASC) Order by the cliplength column
 * @method     CcBlockcontentsQuery orderByDbCuein($order = Criteria::ASC) Order by the cuein column
 * @method     CcBlockcontentsQuery orderByDbCueout($order = Criteria::ASC) Order by the cueout column
 * @method     CcBlockcontentsQuery orderByDbFadein($order = Criteria::ASC) Order by the fadein column
 * @method     CcBlockcontentsQuery orderByDbFadeout($order = Criteria::ASC) Order by the fadeout column
 *
 * @method     CcBlockcontentsQuery groupByDbId() Group by the id column
 * @method     CcBlockcontentsQuery groupByDbBlockId() Group by the block_id column
 * @method     CcBlockcontentsQuery groupByDbFileId() Group by the file_id column
 * @method     CcBlockcontentsQuery groupByDbPosition() Group by the position column
 * @method     CcBlockcontentsQuery groupByDbTrackOffset() Group by the trackoffset column
 * @method     CcBlockcontentsQuery groupByDbCliplength() Group by the cliplength column
 * @method     CcBlockcontentsQuery groupByDbCuein() Group by the cuein column
 * @method     CcBlockcontentsQuery groupByDbCueout() Group by the cueout column
 * @method     CcBlockcontentsQuery groupByDbFadein() Group by the fadein column
 * @method     CcBlockcontentsQuery groupByDbFadeout() Group by the fadeout column
 *
 * @method     CcBlockcontentsQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     CcBlockcontentsQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     CcBlockcontentsQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     CcBlockcontentsQuery leftJoinCcFiles($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcFiles relation
 * @method     CcBlockcontentsQuery rightJoinCcFiles($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcFiles relation
 * @method     CcBlockcontentsQuery innerJoinCcFiles($relationAlias = '') Adds a INNER JOIN clause to the query using the CcFiles relation
 *
 * @method     CcBlockcontentsQuery leftJoinCcBlock($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcBlock relation
 * @method     CcBlockcontentsQuery rightJoinCcBlock($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcBlock relation
 * @method     CcBlockcontentsQuery innerJoinCcBlock($relationAlias = '') Adds a INNER JOIN clause to the query using the CcBlock relation
 *
 * @method     CcBlockcontents findOne(PropelPDO $con = null) Return the first CcBlockcontents matching the query
 * @method     CcBlockcontents findOneOrCreate(PropelPDO $con = null) Return the first CcBlockcontents matching the query, or a new CcBlockcontents object populated from the query conditions when no match is found
 *
 * @method     CcBlockcontents findOneByDbId(int $id) Return the first CcBlockcontents filtered by the id column
 * @method     CcBlockcontents findOneByDbBlockId(int $block_id) Return the first CcBlockcontents filtered by the block_id column
 * @method     CcBlockcontents findOneByDbFileId(int $file_id) Return the first CcBlockcontents filtered by the file_id column
 * @method     CcBlockcontents findOneByDbPosition(int $position) Return the first CcBlockcontents filtered by the position column
 * @method     CcBlockcontents findOneByDbTrackOffset(double $trackoffset) Return the first CcBlockcontents filtered by the trackoffset column
 * @method     CcBlockcontents findOneByDbCliplength(string $cliplength) Return the first CcBlockcontents filtered by the cliplength column
 * @method     CcBlockcontents findOneByDbCuein(string $cuein) Return the first CcBlockcontents filtered by the cuein column
 * @method     CcBlockcontents findOneByDbCueout(string $cueout) Return the first CcBlockcontents filtered by the cueout column
 * @method     CcBlockcontents findOneByDbFadein(string $fadein) Return the first CcBlockcontents filtered by the fadein column
 * @method     CcBlockcontents findOneByDbFadeout(string $fadeout) Return the first CcBlockcontents filtered by the fadeout column
 *
 * @method     array findByDbId(int $id) Return CcBlockcontents objects filtered by the id column
 * @method     array findByDbBlockId(int $block_id) Return CcBlockcontents objects filtered by the block_id column
 * @method     array findByDbFileId(int $file_id) Return CcBlockcontents objects filtered by the file_id column
 * @method     array findByDbPosition(int $position) Return CcBlockcontents objects filtered by the position column
 * @method     array findByDbTrackOffset(double $trackoffset) Return CcBlockcontents objects filtered by the trackoffset column
 * @method     array findByDbCliplength(string $cliplength) Return CcBlockcontents objects filtered by the cliplength column
 * @method     array findByDbCuein(string $cuein) Return CcBlockcontents objects filtered by the cuein column
 * @method     array findByDbCueout(string $cueout) Return CcBlockcontents objects filtered by the cueout column
 * @method     array findByDbFadein(string $fadein) Return CcBlockcontents objects filtered by the fadein column
 * @method     array findByDbFadeout(string $fadeout) Return CcBlockcontents objects filtered by the fadeout column
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcBlockcontentsQuery extends ModelCriteria
{

	/**
	 * Initializes internal state of BaseCcBlockcontentsQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'airtime', $modelName = 'CcBlockcontents', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new CcBlockcontentsQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    CcBlockcontentsQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof CcBlockcontentsQuery) {
			return $criteria;
		}
		$query = new CcBlockcontentsQuery();
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
	 * @return    CcBlockcontents|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = CcBlockcontentsPeer::getInstanceFromPool((string) $key))) && $this->getFormatter()->isObjectFormatter()) {
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
	 * @return    CcBlockcontentsQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(CcBlockcontentsPeer::ID, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    CcBlockcontentsQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(CcBlockcontentsPeer::ID, $keys, Criteria::IN);
	}

	/**
	 * Filter the query on the id column
	 * 
	 * @param     int|array $dbId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcBlockcontentsQuery The current query, for fluid interface
	 */
	public function filterByDbId($dbId = null, $comparison = null)
	{
		if (is_array($dbId) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(CcBlockcontentsPeer::ID, $dbId, $comparison);
	}

	/**
	 * Filter the query on the block_id column
	 * 
	 * @param     int|array $dbBlockId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcBlockcontentsQuery The current query, for fluid interface
	 */
	public function filterByDbBlockId($dbBlockId = null, $comparison = null)
	{
		if (is_array($dbBlockId)) {
			$useMinMax = false;
			if (isset($dbBlockId['min'])) {
				$this->addUsingAlias(CcBlockcontentsPeer::BLOCK_ID, $dbBlockId['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbBlockId['max'])) {
				$this->addUsingAlias(CcBlockcontentsPeer::BLOCK_ID, $dbBlockId['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcBlockcontentsPeer::BLOCK_ID, $dbBlockId, $comparison);
	}

	/**
	 * Filter the query on the file_id column
	 * 
	 * @param     int|array $dbFileId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcBlockcontentsQuery The current query, for fluid interface
	 */
	public function filterByDbFileId($dbFileId = null, $comparison = null)
	{
		if (is_array($dbFileId)) {
			$useMinMax = false;
			if (isset($dbFileId['min'])) {
				$this->addUsingAlias(CcBlockcontentsPeer::FILE_ID, $dbFileId['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbFileId['max'])) {
				$this->addUsingAlias(CcBlockcontentsPeer::FILE_ID, $dbFileId['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcBlockcontentsPeer::FILE_ID, $dbFileId, $comparison);
	}

	/**
	 * Filter the query on the position column
	 * 
	 * @param     int|array $dbPosition The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcBlockcontentsQuery The current query, for fluid interface
	 */
	public function filterByDbPosition($dbPosition = null, $comparison = null)
	{
		if (is_array($dbPosition)) {
			$useMinMax = false;
			if (isset($dbPosition['min'])) {
				$this->addUsingAlias(CcBlockcontentsPeer::POSITION, $dbPosition['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbPosition['max'])) {
				$this->addUsingAlias(CcBlockcontentsPeer::POSITION, $dbPosition['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcBlockcontentsPeer::POSITION, $dbPosition, $comparison);
	}

	/**
	 * Filter the query on the trackoffset column
	 * 
	 * @param     double|array $dbTrackOffset The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcBlockcontentsQuery The current query, for fluid interface
	 */
	public function filterByDbTrackOffset($dbTrackOffset = null, $comparison = null)
	{
		if (is_array($dbTrackOffset)) {
			$useMinMax = false;
			if (isset($dbTrackOffset['min'])) {
				$this->addUsingAlias(CcBlockcontentsPeer::TRACKOFFSET, $dbTrackOffset['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbTrackOffset['max'])) {
				$this->addUsingAlias(CcBlockcontentsPeer::TRACKOFFSET, $dbTrackOffset['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcBlockcontentsPeer::TRACKOFFSET, $dbTrackOffset, $comparison);
	}

	/**
	 * Filter the query on the cliplength column
	 * 
	 * @param     string $dbCliplength The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcBlockcontentsQuery The current query, for fluid interface
	 */
	public function filterByDbCliplength($dbCliplength = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbCliplength)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbCliplength)) {
				$dbCliplength = str_replace('*', '%', $dbCliplength);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcBlockcontentsPeer::CLIPLENGTH, $dbCliplength, $comparison);
	}

	/**
	 * Filter the query on the cuein column
	 * 
	 * @param     string $dbCuein The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcBlockcontentsQuery The current query, for fluid interface
	 */
	public function filterByDbCuein($dbCuein = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbCuein)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbCuein)) {
				$dbCuein = str_replace('*', '%', $dbCuein);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcBlockcontentsPeer::CUEIN, $dbCuein, $comparison);
	}

	/**
	 * Filter the query on the cueout column
	 * 
	 * @param     string $dbCueout The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcBlockcontentsQuery The current query, for fluid interface
	 */
	public function filterByDbCueout($dbCueout = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbCueout)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbCueout)) {
				$dbCueout = str_replace('*', '%', $dbCueout);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcBlockcontentsPeer::CUEOUT, $dbCueout, $comparison);
	}

	/**
	 * Filter the query on the fadein column
	 * 
	 * @param     string|array $dbFadein The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcBlockcontentsQuery The current query, for fluid interface
	 */
	public function filterByDbFadein($dbFadein = null, $comparison = null)
	{
		if (is_array($dbFadein)) {
			$useMinMax = false;
			if (isset($dbFadein['min'])) {
				$this->addUsingAlias(CcBlockcontentsPeer::FADEIN, $dbFadein['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbFadein['max'])) {
				$this->addUsingAlias(CcBlockcontentsPeer::FADEIN, $dbFadein['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcBlockcontentsPeer::FADEIN, $dbFadein, $comparison);
	}

	/**
	 * Filter the query on the fadeout column
	 * 
	 * @param     string|array $dbFadeout The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcBlockcontentsQuery The current query, for fluid interface
	 */
	public function filterByDbFadeout($dbFadeout = null, $comparison = null)
	{
		if (is_array($dbFadeout)) {
			$useMinMax = false;
			if (isset($dbFadeout['min'])) {
				$this->addUsingAlias(CcBlockcontentsPeer::FADEOUT, $dbFadeout['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbFadeout['max'])) {
				$this->addUsingAlias(CcBlockcontentsPeer::FADEOUT, $dbFadeout['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcBlockcontentsPeer::FADEOUT, $dbFadeout, $comparison);
	}

	/**
	 * Filter the query by a related CcFiles object
	 *
	 * @param     CcFiles $ccFiles  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcBlockcontentsQuery The current query, for fluid interface
	 */
	public function filterByCcFiles($ccFiles, $comparison = null)
	{
		return $this
			->addUsingAlias(CcBlockcontentsPeer::FILE_ID, $ccFiles->getDbId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcFiles relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcBlockcontentsQuery The current query, for fluid interface
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
	 * Filter the query by a related CcBlock object
	 *
	 * @param     CcBlock $ccBlock  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcBlockcontentsQuery The current query, for fluid interface
	 */
	public function filterByCcBlock($ccBlock, $comparison = null)
	{
		return $this
			->addUsingAlias(CcBlockcontentsPeer::BLOCK_ID, $ccBlock->getDbId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcBlock relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcBlockcontentsQuery The current query, for fluid interface
	 */
	public function joinCcBlock($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('CcBlock');
		
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
			$this->addJoinObject($join, 'CcBlock');
		}
		
		return $this;
	}

	/**
	 * Use the CcBlock relation CcBlock object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcBlockQuery A secondary query class using the current class as primary query
	 */
	public function useCcBlockQuery($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinCcBlock($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CcBlock', 'CcBlockQuery');
	}

	/**
	 * Exclude object from result
	 *
	 * @param     CcBlockcontents $ccBlockcontents Object to remove from the list of results
	 *
	 * @return    CcBlockcontentsQuery The current query, for fluid interface
	 */
	public function prune($ccBlockcontents = null)
	{
		if ($ccBlockcontents) {
			$this->addUsingAlias(CcBlockcontentsPeer::ID, $ccBlockcontents->getDbId(), Criteria::NOT_EQUAL);
	  }
	  
		return $this;
	}

	/**
	 * Code to execute before every DELETE statement
	 * 
	 * @param     PropelPDO $con The connection object used by the query
	 */
	protected function basePreDelete(PropelPDO $con)
	{
		// aggregate_column_relation behavior
		$this->findRelatedCcBlocks($con);
		
		return $this->preDelete($con);
	}

	/**
	 * Code to execute after every DELETE statement
	 * 
	 * @param     int $affectedRows the number of deleted rows
	 * @param     PropelPDO $con The connection object used by the query
	 */
	protected function basePostDelete($affectedRows, PropelPDO $con)
	{
		// aggregate_column_relation behavior
		$this->updateRelatedCcBlocks($con);
		
		return $this->postDelete($affectedRows, $con);
	}

	/**
	 * Code to execute before every UPDATE statement
	 * 
	 * @param     array $values The associatiove array of columns and values for the update
	 * @param     PropelPDO $con The connection object used by the query
	 * @param     boolean $forceIndividualSaves If false (default), the resulting call is a BasePeer::doUpdate(), ortherwise it is a series of save() calls on all the found objects
	 */
	protected function basePreUpdate(&$values, PropelPDO $con, $forceIndividualSaves = false)
	{
		// aggregate_column_relation behavior
		$this->findRelatedCcBlocks($con);
		
		return $this->preUpdate($values, $con, $forceIndividualSaves);
	}

	/**
	 * Code to execute after every UPDATE statement
	 * 
	 * @param     int $affectedRows the number of udated rows
	 * @param     PropelPDO $con The connection object used by the query
	 */
	protected function basePostUpdate($affectedRows, PropelPDO $con)
	{
		// aggregate_column_relation behavior
		$this->updateRelatedCcBlocks($con);
		
		return $this->postUpdate($affectedRows, $con);
	}

	// aggregate_column_relation behavior
	
	/**
	 * Finds the related CcBlock objects and keep them for later
	 *
	 * @param PropelPDO $con A connection object
	 */
	protected function findRelatedCcBlocks($con)
	{
		$criteria = clone $this;
		if ($this->useAliasInSQL) {
			$alias = $this->getModelAlias();
			$criteria->removeAlias($alias);
		} else {
			$alias = '';
		}
		$this->ccBlocks = CcBlockQuery::create()
			->joinCcBlockcontents($alias)
			->mergeWith($criteria)
			->find($con);
	}
	
	protected function updateRelatedCcBlocks($con)
	{
		foreach ($this->ccBlocks as $ccBlock) {
			$ccBlock->updateDbLength($con);
		}
		$this->ccBlocks = array();
	}

} // BaseCcBlockcontentsQuery
