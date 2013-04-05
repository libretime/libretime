<?php


/**
 * Base class that represents a query for the 'cc_show_stamp' table.
 *
 * 
 *
 * @method     CcShowStampQuery orderByDbId($order = Criteria::ASC) Order by the id column
 * @method     CcShowStampQuery orderByDbShowId($order = Criteria::ASC) Order by the show_id column
 * @method     CcShowStampQuery orderByDbInstanceId($order = Criteria::ASC) Order by the instance_id column
 * @method     CcShowStampQuery orderByDbFileId($order = Criteria::ASC) Order by the file_id column
 * @method     CcShowStampQuery orderByDbStreamId($order = Criteria::ASC) Order by the stream_id column
 * @method     CcShowStampQuery orderByDbBlockId($order = Criteria::ASC) Order by the block_id column
 * @method     CcShowStampQuery orderByDbPlaylistId($order = Criteria::ASC) Order by the playlist_id column
 * @method     CcShowStampQuery orderByDbPosition($order = Criteria::ASC) Order by the position column
 * @method     CcShowStampQuery orderByDbClipLength($order = Criteria::ASC) Order by the clip_length column
 * @method     CcShowStampQuery orderByDbCueIn($order = Criteria::ASC) Order by the cue_in column
 * @method     CcShowStampQuery orderByDbCueOut($order = Criteria::ASC) Order by the cue_out column
 * @method     CcShowStampQuery orderByDbFadeIn($order = Criteria::ASC) Order by the fade_in column
 * @method     CcShowStampQuery orderByDbFadeOut($order = Criteria::ASC) Order by the fade_out column
 *
 * @method     CcShowStampQuery groupByDbId() Group by the id column
 * @method     CcShowStampQuery groupByDbShowId() Group by the show_id column
 * @method     CcShowStampQuery groupByDbInstanceId() Group by the instance_id column
 * @method     CcShowStampQuery groupByDbFileId() Group by the file_id column
 * @method     CcShowStampQuery groupByDbStreamId() Group by the stream_id column
 * @method     CcShowStampQuery groupByDbBlockId() Group by the block_id column
 * @method     CcShowStampQuery groupByDbPlaylistId() Group by the playlist_id column
 * @method     CcShowStampQuery groupByDbPosition() Group by the position column
 * @method     CcShowStampQuery groupByDbClipLength() Group by the clip_length column
 * @method     CcShowStampQuery groupByDbCueIn() Group by the cue_in column
 * @method     CcShowStampQuery groupByDbCueOut() Group by the cue_out column
 * @method     CcShowStampQuery groupByDbFadeIn() Group by the fade_in column
 * @method     CcShowStampQuery groupByDbFadeOut() Group by the fade_out column
 *
 * @method     CcShowStampQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     CcShowStampQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     CcShowStampQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     CcShowStampQuery leftJoinCcShow($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcShow relation
 * @method     CcShowStampQuery rightJoinCcShow($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcShow relation
 * @method     CcShowStampQuery innerJoinCcShow($relationAlias = '') Adds a INNER JOIN clause to the query using the CcShow relation
 *
 * @method     CcShowStampQuery leftJoinCcShowInstances($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcShowInstances relation
 * @method     CcShowStampQuery rightJoinCcShowInstances($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcShowInstances relation
 * @method     CcShowStampQuery innerJoinCcShowInstances($relationAlias = '') Adds a INNER JOIN clause to the query using the CcShowInstances relation
 *
 * @method     CcShowStampQuery leftJoinCcFiles($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcFiles relation
 * @method     CcShowStampQuery rightJoinCcFiles($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcFiles relation
 * @method     CcShowStampQuery innerJoinCcFiles($relationAlias = '') Adds a INNER JOIN clause to the query using the CcFiles relation
 *
 * @method     CcShowStampQuery leftJoinCcWebstream($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcWebstream relation
 * @method     CcShowStampQuery rightJoinCcWebstream($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcWebstream relation
 * @method     CcShowStampQuery innerJoinCcWebstream($relationAlias = '') Adds a INNER JOIN clause to the query using the CcWebstream relation
 *
 * @method     CcShowStampQuery leftJoinCcBlock($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcBlock relation
 * @method     CcShowStampQuery rightJoinCcBlock($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcBlock relation
 * @method     CcShowStampQuery innerJoinCcBlock($relationAlias = '') Adds a INNER JOIN clause to the query using the CcBlock relation
 *
 * @method     CcShowStampQuery leftJoinCcPlaylist($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcPlaylist relation
 * @method     CcShowStampQuery rightJoinCcPlaylist($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcPlaylist relation
 * @method     CcShowStampQuery innerJoinCcPlaylist($relationAlias = '') Adds a INNER JOIN clause to the query using the CcPlaylist relation
 *
 * @method     CcShowStamp findOne(PropelPDO $con = null) Return the first CcShowStamp matching the query
 * @method     CcShowStamp findOneOrCreate(PropelPDO $con = null) Return the first CcShowStamp matching the query, or a new CcShowStamp object populated from the query conditions when no match is found
 *
 * @method     CcShowStamp findOneByDbId(int $id) Return the first CcShowStamp filtered by the id column
 * @method     CcShowStamp findOneByDbShowId(int $show_id) Return the first CcShowStamp filtered by the show_id column
 * @method     CcShowStamp findOneByDbInstanceId(int $instance_id) Return the first CcShowStamp filtered by the instance_id column
 * @method     CcShowStamp findOneByDbFileId(int $file_id) Return the first CcShowStamp filtered by the file_id column
 * @method     CcShowStamp findOneByDbStreamId(int $stream_id) Return the first CcShowStamp filtered by the stream_id column
 * @method     CcShowStamp findOneByDbBlockId(int $block_id) Return the first CcShowStamp filtered by the block_id column
 * @method     CcShowStamp findOneByDbPlaylistId(int $playlist_id) Return the first CcShowStamp filtered by the playlist_id column
 * @method     CcShowStamp findOneByDbPosition(int $position) Return the first CcShowStamp filtered by the position column
 * @method     CcShowStamp findOneByDbClipLength(string $clip_length) Return the first CcShowStamp filtered by the clip_length column
 * @method     CcShowStamp findOneByDbCueIn(string $cue_in) Return the first CcShowStamp filtered by the cue_in column
 * @method     CcShowStamp findOneByDbCueOut(string $cue_out) Return the first CcShowStamp filtered by the cue_out column
 * @method     CcShowStamp findOneByDbFadeIn(string $fade_in) Return the first CcShowStamp filtered by the fade_in column
 * @method     CcShowStamp findOneByDbFadeOut(string $fade_out) Return the first CcShowStamp filtered by the fade_out column
 *
 * @method     array findByDbId(int $id) Return CcShowStamp objects filtered by the id column
 * @method     array findByDbShowId(int $show_id) Return CcShowStamp objects filtered by the show_id column
 * @method     array findByDbInstanceId(int $instance_id) Return CcShowStamp objects filtered by the instance_id column
 * @method     array findByDbFileId(int $file_id) Return CcShowStamp objects filtered by the file_id column
 * @method     array findByDbStreamId(int $stream_id) Return CcShowStamp objects filtered by the stream_id column
 * @method     array findByDbBlockId(int $block_id) Return CcShowStamp objects filtered by the block_id column
 * @method     array findByDbPlaylistId(int $playlist_id) Return CcShowStamp objects filtered by the playlist_id column
 * @method     array findByDbPosition(int $position) Return CcShowStamp objects filtered by the position column
 * @method     array findByDbClipLength(string $clip_length) Return CcShowStamp objects filtered by the clip_length column
 * @method     array findByDbCueIn(string $cue_in) Return CcShowStamp objects filtered by the cue_in column
 * @method     array findByDbCueOut(string $cue_out) Return CcShowStamp objects filtered by the cue_out column
 * @method     array findByDbFadeIn(string $fade_in) Return CcShowStamp objects filtered by the fade_in column
 * @method     array findByDbFadeOut(string $fade_out) Return CcShowStamp objects filtered by the fade_out column
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcShowStampQuery extends ModelCriteria
{

	/**
	 * Initializes internal state of BaseCcShowStampQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'airtime', $modelName = 'CcShowStamp', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new CcShowStampQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    CcShowStampQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof CcShowStampQuery) {
			return $criteria;
		}
		$query = new CcShowStampQuery();
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
	 * @return    CcShowStamp|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = CcShowStampPeer::getInstanceFromPool((string) $key))) && $this->getFormatter()->isObjectFormatter()) {
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
	 * @return    CcShowStampQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(CcShowStampPeer::ID, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    CcShowStampQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(CcShowStampPeer::ID, $keys, Criteria::IN);
	}

	/**
	 * Filter the query on the id column
	 * 
	 * @param     int|array $dbId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcShowStampQuery The current query, for fluid interface
	 */
	public function filterByDbId($dbId = null, $comparison = null)
	{
		if (is_array($dbId) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(CcShowStampPeer::ID, $dbId, $comparison);
	}

	/**
	 * Filter the query on the show_id column
	 * 
	 * @param     int|array $dbShowId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcShowStampQuery The current query, for fluid interface
	 */
	public function filterByDbShowId($dbShowId = null, $comparison = null)
	{
		if (is_array($dbShowId)) {
			$useMinMax = false;
			if (isset($dbShowId['min'])) {
				$this->addUsingAlias(CcShowStampPeer::SHOW_ID, $dbShowId['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbShowId['max'])) {
				$this->addUsingAlias(CcShowStampPeer::SHOW_ID, $dbShowId['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcShowStampPeer::SHOW_ID, $dbShowId, $comparison);
	}

	/**
	 * Filter the query on the instance_id column
	 * 
	 * @param     int|array $dbInstanceId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcShowStampQuery The current query, for fluid interface
	 */
	public function filterByDbInstanceId($dbInstanceId = null, $comparison = null)
	{
		if (is_array($dbInstanceId)) {
			$useMinMax = false;
			if (isset($dbInstanceId['min'])) {
				$this->addUsingAlias(CcShowStampPeer::INSTANCE_ID, $dbInstanceId['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbInstanceId['max'])) {
				$this->addUsingAlias(CcShowStampPeer::INSTANCE_ID, $dbInstanceId['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcShowStampPeer::INSTANCE_ID, $dbInstanceId, $comparison);
	}

	/**
	 * Filter the query on the file_id column
	 * 
	 * @param     int|array $dbFileId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcShowStampQuery The current query, for fluid interface
	 */
	public function filterByDbFileId($dbFileId = null, $comparison = null)
	{
		if (is_array($dbFileId)) {
			$useMinMax = false;
			if (isset($dbFileId['min'])) {
				$this->addUsingAlias(CcShowStampPeer::FILE_ID, $dbFileId['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbFileId['max'])) {
				$this->addUsingAlias(CcShowStampPeer::FILE_ID, $dbFileId['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcShowStampPeer::FILE_ID, $dbFileId, $comparison);
	}

	/**
	 * Filter the query on the stream_id column
	 * 
	 * @param     int|array $dbStreamId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcShowStampQuery The current query, for fluid interface
	 */
	public function filterByDbStreamId($dbStreamId = null, $comparison = null)
	{
		if (is_array($dbStreamId)) {
			$useMinMax = false;
			if (isset($dbStreamId['min'])) {
				$this->addUsingAlias(CcShowStampPeer::STREAM_ID, $dbStreamId['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbStreamId['max'])) {
				$this->addUsingAlias(CcShowStampPeer::STREAM_ID, $dbStreamId['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcShowStampPeer::STREAM_ID, $dbStreamId, $comparison);
	}

	/**
	 * Filter the query on the block_id column
	 * 
	 * @param     int|array $dbBlockId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcShowStampQuery The current query, for fluid interface
	 */
	public function filterByDbBlockId($dbBlockId = null, $comparison = null)
	{
		if (is_array($dbBlockId)) {
			$useMinMax = false;
			if (isset($dbBlockId['min'])) {
				$this->addUsingAlias(CcShowStampPeer::BLOCK_ID, $dbBlockId['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbBlockId['max'])) {
				$this->addUsingAlias(CcShowStampPeer::BLOCK_ID, $dbBlockId['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcShowStampPeer::BLOCK_ID, $dbBlockId, $comparison);
	}

	/**
	 * Filter the query on the playlist_id column
	 * 
	 * @param     int|array $dbPlaylistId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcShowStampQuery The current query, for fluid interface
	 */
	public function filterByDbPlaylistId($dbPlaylistId = null, $comparison = null)
	{
		if (is_array($dbPlaylistId)) {
			$useMinMax = false;
			if (isset($dbPlaylistId['min'])) {
				$this->addUsingAlias(CcShowStampPeer::PLAYLIST_ID, $dbPlaylistId['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbPlaylistId['max'])) {
				$this->addUsingAlias(CcShowStampPeer::PLAYLIST_ID, $dbPlaylistId['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcShowStampPeer::PLAYLIST_ID, $dbPlaylistId, $comparison);
	}

	/**
	 * Filter the query on the position column
	 * 
	 * @param     int|array $dbPosition The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcShowStampQuery The current query, for fluid interface
	 */
	public function filterByDbPosition($dbPosition = null, $comparison = null)
	{
		if (is_array($dbPosition)) {
			$useMinMax = false;
			if (isset($dbPosition['min'])) {
				$this->addUsingAlias(CcShowStampPeer::POSITION, $dbPosition['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbPosition['max'])) {
				$this->addUsingAlias(CcShowStampPeer::POSITION, $dbPosition['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcShowStampPeer::POSITION, $dbPosition, $comparison);
	}

	/**
	 * Filter the query on the clip_length column
	 * 
	 * @param     string $dbClipLength The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcShowStampQuery The current query, for fluid interface
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
		return $this->addUsingAlias(CcShowStampPeer::CLIP_LENGTH, $dbClipLength, $comparison);
	}

	/**
	 * Filter the query on the cue_in column
	 * 
	 * @param     string $dbCueIn The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcShowStampQuery The current query, for fluid interface
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
		return $this->addUsingAlias(CcShowStampPeer::CUE_IN, $dbCueIn, $comparison);
	}

	/**
	 * Filter the query on the cue_out column
	 * 
	 * @param     string $dbCueOut The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcShowStampQuery The current query, for fluid interface
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
		return $this->addUsingAlias(CcShowStampPeer::CUE_OUT, $dbCueOut, $comparison);
	}

	/**
	 * Filter the query on the fade_in column
	 * 
	 * @param     string $dbFadeIn The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcShowStampQuery The current query, for fluid interface
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
		return $this->addUsingAlias(CcShowStampPeer::FADE_IN, $dbFadeIn, $comparison);
	}

	/**
	 * Filter the query on the fade_out column
	 * 
	 * @param     string $dbFadeOut The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcShowStampQuery The current query, for fluid interface
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
		return $this->addUsingAlias(CcShowStampPeer::FADE_OUT, $dbFadeOut, $comparison);
	}

	/**
	 * Filter the query by a related CcShow object
	 *
	 * @param     CcShow $ccShow  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcShowStampQuery The current query, for fluid interface
	 */
	public function filterByCcShow($ccShow, $comparison = null)
	{
		return $this
			->addUsingAlias(CcShowStampPeer::SHOW_ID, $ccShow->getDbId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcShow relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcShowStampQuery The current query, for fluid interface
	 */
	public function joinCcShow($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('CcShow');
		
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
			$this->addJoinObject($join, 'CcShow');
		}
		
		return $this;
	}

	/**
	 * Use the CcShow relation CcShow object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcShowQuery A secondary query class using the current class as primary query
	 */
	public function useCcShowQuery($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinCcShow($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CcShow', 'CcShowQuery');
	}

	/**
	 * Filter the query by a related CcShowInstances object
	 *
	 * @param     CcShowInstances $ccShowInstances  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcShowStampQuery The current query, for fluid interface
	 */
	public function filterByCcShowInstances($ccShowInstances, $comparison = null)
	{
		return $this
			->addUsingAlias(CcShowStampPeer::INSTANCE_ID, $ccShowInstances->getDbId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcShowInstances relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcShowStampQuery The current query, for fluid interface
	 */
	public function joinCcShowInstances($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
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
	public function useCcShowInstancesQuery($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinCcShowInstances($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CcShowInstances', 'CcShowInstancesQuery');
	}

	/**
	 * Filter the query by a related CcFiles object
	 *
	 * @param     CcFiles $ccFiles  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcShowStampQuery The current query, for fluid interface
	 */
	public function filterByCcFiles($ccFiles, $comparison = null)
	{
		return $this
			->addUsingAlias(CcShowStampPeer::FILE_ID, $ccFiles->getDbId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcFiles relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcShowStampQuery The current query, for fluid interface
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
	 * @return    CcShowStampQuery The current query, for fluid interface
	 */
	public function filterByCcWebstream($ccWebstream, $comparison = null)
	{
		return $this
			->addUsingAlias(CcShowStampPeer::STREAM_ID, $ccWebstream->getDbId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcWebstream relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcShowStampQuery The current query, for fluid interface
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
	 * @return    CcShowStampQuery The current query, for fluid interface
	 */
	public function filterByCcBlock($ccBlock, $comparison = null)
	{
		return $this
			->addUsingAlias(CcShowStampPeer::BLOCK_ID, $ccBlock->getDbId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcBlock relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcShowStampQuery The current query, for fluid interface
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
	 * @return    CcShowStampQuery The current query, for fluid interface
	 */
	public function filterByCcPlaylist($ccPlaylist, $comparison = null)
	{
		return $this
			->addUsingAlias(CcShowStampPeer::PLAYLIST_ID, $ccPlaylist->getDbId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcPlaylist relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcShowStampQuery The current query, for fluid interface
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
	 * @param     CcShowStamp $ccShowStamp Object to remove from the list of results
	 *
	 * @return    CcShowStampQuery The current query, for fluid interface
	 */
	public function prune($ccShowStamp = null)
	{
		if ($ccShowStamp) {
			$this->addUsingAlias(CcShowStampPeer::ID, $ccShowStamp->getDbId(), Criteria::NOT_EQUAL);
	  }
	  
		return $this;
	}

} // BaseCcShowStampQuery
