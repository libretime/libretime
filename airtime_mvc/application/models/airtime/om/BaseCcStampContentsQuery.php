<?php


/**
 * Base class that represents a query for the 'cc_stamp_contents' table.
 *
 * 
 *
 * @method     CcStampContentsQuery orderByDbId($order = Criteria::ASC) Order by the id column
 * @method     CcStampContentsQuery orderByDbStampId($order = Criteria::ASC) Order by the stamp_id column
 * @method     CcStampContentsQuery orderByDbFileId($order = Criteria::ASC) Order by the file_id column
 * @method     CcStampContentsQuery orderByDbStreamId($order = Criteria::ASC) Order by the stream_id column
 * @method     CcStampContentsQuery orderByDbBlockId($order = Criteria::ASC) Order by the block_id column
 * @method     CcStampContentsQuery orderByDbPlaylistId($order = Criteria::ASC) Order by the playlist_id column
 * @method     CcStampContentsQuery orderByDbPosition($order = Criteria::ASC) Order by the position column
 * @method     CcStampContentsQuery orderByDbClipLength($order = Criteria::ASC) Order by the clip_length column
 * @method     CcStampContentsQuery orderByDbCueIn($order = Criteria::ASC) Order by the cue_in column
 * @method     CcStampContentsQuery orderByDbCueOut($order = Criteria::ASC) Order by the cue_out column
 * @method     CcStampContentsQuery orderByDbFadeIn($order = Criteria::ASC) Order by the fade_in column
 * @method     CcStampContentsQuery orderByDbFadeOut($order = Criteria::ASC) Order by the fade_out column
 *
 * @method     CcStampContentsQuery groupByDbId() Group by the id column
 * @method     CcStampContentsQuery groupByDbStampId() Group by the stamp_id column
 * @method     CcStampContentsQuery groupByDbFileId() Group by the file_id column
 * @method     CcStampContentsQuery groupByDbStreamId() Group by the stream_id column
 * @method     CcStampContentsQuery groupByDbBlockId() Group by the block_id column
 * @method     CcStampContentsQuery groupByDbPlaylistId() Group by the playlist_id column
 * @method     CcStampContentsQuery groupByDbPosition() Group by the position column
 * @method     CcStampContentsQuery groupByDbClipLength() Group by the clip_length column
 * @method     CcStampContentsQuery groupByDbCueIn() Group by the cue_in column
 * @method     CcStampContentsQuery groupByDbCueOut() Group by the cue_out column
 * @method     CcStampContentsQuery groupByDbFadeIn() Group by the fade_in column
 * @method     CcStampContentsQuery groupByDbFadeOut() Group by the fade_out column
 *
 * @method     CcStampContentsQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     CcStampContentsQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     CcStampContentsQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     CcStampContentsQuery leftJoinCcStamp($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcStamp relation
 * @method     CcStampContentsQuery rightJoinCcStamp($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcStamp relation
 * @method     CcStampContentsQuery innerJoinCcStamp($relationAlias = '') Adds a INNER JOIN clause to the query using the CcStamp relation
 *
 * @method     CcStampContentsQuery leftJoinCcFiles($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcFiles relation
 * @method     CcStampContentsQuery rightJoinCcFiles($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcFiles relation
 * @method     CcStampContentsQuery innerJoinCcFiles($relationAlias = '') Adds a INNER JOIN clause to the query using the CcFiles relation
 *
 * @method     CcStampContentsQuery leftJoinCcWebstream($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcWebstream relation
 * @method     CcStampContentsQuery rightJoinCcWebstream($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcWebstream relation
 * @method     CcStampContentsQuery innerJoinCcWebstream($relationAlias = '') Adds a INNER JOIN clause to the query using the CcWebstream relation
 *
 * @method     CcStampContentsQuery leftJoinCcBlock($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcBlock relation
 * @method     CcStampContentsQuery rightJoinCcBlock($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcBlock relation
 * @method     CcStampContentsQuery innerJoinCcBlock($relationAlias = '') Adds a INNER JOIN clause to the query using the CcBlock relation
 *
 * @method     CcStampContentsQuery leftJoinCcPlaylist($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcPlaylist relation
 * @method     CcStampContentsQuery rightJoinCcPlaylist($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcPlaylist relation
 * @method     CcStampContentsQuery innerJoinCcPlaylist($relationAlias = '') Adds a INNER JOIN clause to the query using the CcPlaylist relation
 *
 * @method     CcStampContents findOne(PropelPDO $con = null) Return the first CcStampContents matching the query
 * @method     CcStampContents findOneOrCreate(PropelPDO $con = null) Return the first CcStampContents matching the query, or a new CcStampContents object populated from the query conditions when no match is found
 *
 * @method     CcStampContents findOneByDbId(int $id) Return the first CcStampContents filtered by the id column
 * @method     CcStampContents findOneByDbStampId(int $stamp_id) Return the first CcStampContents filtered by the stamp_id column
 * @method     CcStampContents findOneByDbFileId(int $file_id) Return the first CcStampContents filtered by the file_id column
 * @method     CcStampContents findOneByDbStreamId(int $stream_id) Return the first CcStampContents filtered by the stream_id column
 * @method     CcStampContents findOneByDbBlockId(int $block_id) Return the first CcStampContents filtered by the block_id column
 * @method     CcStampContents findOneByDbPlaylistId(int $playlist_id) Return the first CcStampContents filtered by the playlist_id column
 * @method     CcStampContents findOneByDbPosition(int $position) Return the first CcStampContents filtered by the position column
 * @method     CcStampContents findOneByDbClipLength(string $clip_length) Return the first CcStampContents filtered by the clip_length column
 * @method     CcStampContents findOneByDbCueIn(string $cue_in) Return the first CcStampContents filtered by the cue_in column
 * @method     CcStampContents findOneByDbCueOut(string $cue_out) Return the first CcStampContents filtered by the cue_out column
 * @method     CcStampContents findOneByDbFadeIn(string $fade_in) Return the first CcStampContents filtered by the fade_in column
 * @method     CcStampContents findOneByDbFadeOut(string $fade_out) Return the first CcStampContents filtered by the fade_out column
 *
 * @method     array findByDbId(int $id) Return CcStampContents objects filtered by the id column
 * @method     array findByDbStampId(int $stamp_id) Return CcStampContents objects filtered by the stamp_id column
 * @method     array findByDbFileId(int $file_id) Return CcStampContents objects filtered by the file_id column
 * @method     array findByDbStreamId(int $stream_id) Return CcStampContents objects filtered by the stream_id column
 * @method     array findByDbBlockId(int $block_id) Return CcStampContents objects filtered by the block_id column
 * @method     array findByDbPlaylistId(int $playlist_id) Return CcStampContents objects filtered by the playlist_id column
 * @method     array findByDbPosition(int $position) Return CcStampContents objects filtered by the position column
 * @method     array findByDbClipLength(string $clip_length) Return CcStampContents objects filtered by the clip_length column
 * @method     array findByDbCueIn(string $cue_in) Return CcStampContents objects filtered by the cue_in column
 * @method     array findByDbCueOut(string $cue_out) Return CcStampContents objects filtered by the cue_out column
 * @method     array findByDbFadeIn(string $fade_in) Return CcStampContents objects filtered by the fade_in column
 * @method     array findByDbFadeOut(string $fade_out) Return CcStampContents objects filtered by the fade_out column
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcStampContentsQuery extends ModelCriteria
{

	/**
	 * Initializes internal state of BaseCcStampContentsQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'airtime', $modelName = 'CcStampContents', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new CcStampContentsQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    CcStampContentsQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof CcStampContentsQuery) {
			return $criteria;
		}
		$query = new CcStampContentsQuery();
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
	 * @return    CcStampContents|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = CcStampContentsPeer::getInstanceFromPool((string) $key))) && $this->getFormatter()->isObjectFormatter()) {
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
	 * @return    CcStampContentsQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(CcStampContentsPeer::ID, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    CcStampContentsQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(CcStampContentsPeer::ID, $keys, Criteria::IN);
	}

	/**
	 * Filter the query on the id column
	 * 
	 * @param     int|array $dbId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcStampContentsQuery The current query, for fluid interface
	 */
	public function filterByDbId($dbId = null, $comparison = null)
	{
		if (is_array($dbId) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(CcStampContentsPeer::ID, $dbId, $comparison);
	}

	/**
	 * Filter the query on the stamp_id column
	 * 
	 * @param     int|array $dbStampId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcStampContentsQuery The current query, for fluid interface
	 */
	public function filterByDbStampId($dbStampId = null, $comparison = null)
	{
		if (is_array($dbStampId)) {
			$useMinMax = false;
			if (isset($dbStampId['min'])) {
				$this->addUsingAlias(CcStampContentsPeer::STAMP_ID, $dbStampId['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbStampId['max'])) {
				$this->addUsingAlias(CcStampContentsPeer::STAMP_ID, $dbStampId['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcStampContentsPeer::STAMP_ID, $dbStampId, $comparison);
	}

	/**
	 * Filter the query on the file_id column
	 * 
	 * @param     int|array $dbFileId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcStampContentsQuery The current query, for fluid interface
	 */
	public function filterByDbFileId($dbFileId = null, $comparison = null)
	{
		if (is_array($dbFileId)) {
			$useMinMax = false;
			if (isset($dbFileId['min'])) {
				$this->addUsingAlias(CcStampContentsPeer::FILE_ID, $dbFileId['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbFileId['max'])) {
				$this->addUsingAlias(CcStampContentsPeer::FILE_ID, $dbFileId['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcStampContentsPeer::FILE_ID, $dbFileId, $comparison);
	}

	/**
	 * Filter the query on the stream_id column
	 * 
	 * @param     int|array $dbStreamId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcStampContentsQuery The current query, for fluid interface
	 */
	public function filterByDbStreamId($dbStreamId = null, $comparison = null)
	{
		if (is_array($dbStreamId)) {
			$useMinMax = false;
			if (isset($dbStreamId['min'])) {
				$this->addUsingAlias(CcStampContentsPeer::STREAM_ID, $dbStreamId['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbStreamId['max'])) {
				$this->addUsingAlias(CcStampContentsPeer::STREAM_ID, $dbStreamId['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcStampContentsPeer::STREAM_ID, $dbStreamId, $comparison);
	}

	/**
	 * Filter the query on the block_id column
	 * 
	 * @param     int|array $dbBlockId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcStampContentsQuery The current query, for fluid interface
	 */
	public function filterByDbBlockId($dbBlockId = null, $comparison = null)
	{
		if (is_array($dbBlockId)) {
			$useMinMax = false;
			if (isset($dbBlockId['min'])) {
				$this->addUsingAlias(CcStampContentsPeer::BLOCK_ID, $dbBlockId['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbBlockId['max'])) {
				$this->addUsingAlias(CcStampContentsPeer::BLOCK_ID, $dbBlockId['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcStampContentsPeer::BLOCK_ID, $dbBlockId, $comparison);
	}

	/**
	 * Filter the query on the playlist_id column
	 * 
	 * @param     int|array $dbPlaylistId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcStampContentsQuery The current query, for fluid interface
	 */
	public function filterByDbPlaylistId($dbPlaylistId = null, $comparison = null)
	{
		if (is_array($dbPlaylistId)) {
			$useMinMax = false;
			if (isset($dbPlaylistId['min'])) {
				$this->addUsingAlias(CcStampContentsPeer::PLAYLIST_ID, $dbPlaylistId['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbPlaylistId['max'])) {
				$this->addUsingAlias(CcStampContentsPeer::PLAYLIST_ID, $dbPlaylistId['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcStampContentsPeer::PLAYLIST_ID, $dbPlaylistId, $comparison);
	}

	/**
	 * Filter the query on the position column
	 * 
	 * @param     int|array $dbPosition The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcStampContentsQuery The current query, for fluid interface
	 */
	public function filterByDbPosition($dbPosition = null, $comparison = null)
	{
		if (is_array($dbPosition)) {
			$useMinMax = false;
			if (isset($dbPosition['min'])) {
				$this->addUsingAlias(CcStampContentsPeer::POSITION, $dbPosition['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbPosition['max'])) {
				$this->addUsingAlias(CcStampContentsPeer::POSITION, $dbPosition['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcStampContentsPeer::POSITION, $dbPosition, $comparison);
	}

	/**
	 * Filter the query on the clip_length column
	 * 
	 * @param     string $dbClipLength The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcStampContentsQuery The current query, for fluid interface
	 */
	public function filterByDbClipLength($dbClipLength = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbClipLength)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbClipLength)) {
				$dbClipLength = str_replace('*', '%', $dbClipLength);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcStampContentsPeer::CLIP_LENGTH, $dbClipLength, $comparison);
	}

	/**
	 * Filter the query on the cue_in column
	 * 
	 * @param     string $dbCueIn The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcStampContentsQuery The current query, for fluid interface
	 */
	public function filterByDbCueIn($dbCueIn = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbCueIn)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbCueIn)) {
				$dbCueIn = str_replace('*', '%', $dbCueIn);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcStampContentsPeer::CUE_IN, $dbCueIn, $comparison);
	}

	/**
	 * Filter the query on the cue_out column
	 * 
	 * @param     string $dbCueOut The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcStampContentsQuery The current query, for fluid interface
	 */
	public function filterByDbCueOut($dbCueOut = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbCueOut)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbCueOut)) {
				$dbCueOut = str_replace('*', '%', $dbCueOut);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcStampContentsPeer::CUE_OUT, $dbCueOut, $comparison);
	}

	/**
	 * Filter the query on the fade_in column
	 * 
	 * @param     string $dbFadeIn The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcStampContentsQuery The current query, for fluid interface
	 */
	public function filterByDbFadeIn($dbFadeIn = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbFadeIn)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbFadeIn)) {
				$dbFadeIn = str_replace('*', '%', $dbFadeIn);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcStampContentsPeer::FADE_IN, $dbFadeIn, $comparison);
	}

	/**
	 * Filter the query on the fade_out column
	 * 
	 * @param     string $dbFadeOut The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcStampContentsQuery The current query, for fluid interface
	 */
	public function filterByDbFadeOut($dbFadeOut = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbFadeOut)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbFadeOut)) {
				$dbFadeOut = str_replace('*', '%', $dbFadeOut);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcStampContentsPeer::FADE_OUT, $dbFadeOut, $comparison);
	}

	/**
	 * Filter the query by a related CcStamp object
	 *
	 * @param     CcStamp $ccStamp  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcStampContentsQuery The current query, for fluid interface
	 */
	public function filterByCcStamp($ccStamp, $comparison = null)
	{
		return $this
			->addUsingAlias(CcStampContentsPeer::STAMP_ID, $ccStamp->getDbId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcStamp relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcStampContentsQuery The current query, for fluid interface
	 */
	public function joinCcStamp($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('CcStamp');
		
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
			$this->addJoinObject($join, 'CcStamp');
		}
		
		return $this;
	}

	/**
	 * Use the CcStamp relation CcStamp object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcStampQuery A secondary query class using the current class as primary query
	 */
	public function useCcStampQuery($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinCcStamp($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CcStamp', 'CcStampQuery');
	}

	/**
	 * Filter the query by a related CcFiles object
	 *
	 * @param     CcFiles $ccFiles  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcStampContentsQuery The current query, for fluid interface
	 */
	public function filterByCcFiles($ccFiles, $comparison = null)
	{
		return $this
			->addUsingAlias(CcStampContentsPeer::FILE_ID, $ccFiles->getDbId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcFiles relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcStampContentsQuery The current query, for fluid interface
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
	 * Filter the query by a related CcWebstream object
	 *
	 * @param     CcWebstream $ccWebstream  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcStampContentsQuery The current query, for fluid interface
	 */
	public function filterByCcWebstream($ccWebstream, $comparison = null)
	{
		return $this
			->addUsingAlias(CcStampContentsPeer::STREAM_ID, $ccWebstream->getDbId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcWebstream relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcStampContentsQuery The current query, for fluid interface
	 */
	public function joinCcWebstream($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('CcWebstream');
		
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
			$this->addJoinObject($join, 'CcWebstream');
		}
		
		return $this;
	}

	/**
	 * Use the CcWebstream relation CcWebstream object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcWebstreamQuery A secondary query class using the current class as primary query
	 */
	public function useCcWebstreamQuery($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinCcWebstream($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CcWebstream', 'CcWebstreamQuery');
	}

	/**
	 * Filter the query by a related CcBlock object
	 *
	 * @param     CcBlock $ccBlock  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcStampContentsQuery The current query, for fluid interface
	 */
	public function filterByCcBlock($ccBlock, $comparison = null)
	{
		return $this
			->addUsingAlias(CcStampContentsPeer::BLOCK_ID, $ccBlock->getDbId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcBlock relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcStampContentsQuery The current query, for fluid interface
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
	 * Filter the query by a related CcPlaylist object
	 *
	 * @param     CcPlaylist $ccPlaylist  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcStampContentsQuery The current query, for fluid interface
	 */
	public function filterByCcPlaylist($ccPlaylist, $comparison = null)
	{
		return $this
			->addUsingAlias(CcStampContentsPeer::PLAYLIST_ID, $ccPlaylist->getDbId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcPlaylist relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcStampContentsQuery The current query, for fluid interface
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
	 * @param     CcStampContents $ccStampContents Object to remove from the list of results
	 *
	 * @return    CcStampContentsQuery The current query, for fluid interface
	 */
	public function prune($ccStampContents = null)
	{
		if ($ccStampContents) {
			$this->addUsingAlias(CcStampContentsPeer::ID, $ccStampContents->getDbId(), Criteria::NOT_EQUAL);
	  }
	  
		return $this;
	}

} // BaseCcStampContentsQuery
