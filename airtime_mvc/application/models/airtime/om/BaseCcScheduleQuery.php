<?php


/**
 * Base class that represents a query for the 'cc_schedule' table.
 *
 * 
 *
 * @method     CcScheduleQuery orderByDbId($order = Criteria::ASC) Order by the id column
 * @method     CcScheduleQuery orderByDbStarts($order = Criteria::ASC) Order by the starts column
 * @method     CcScheduleQuery orderByDbEnds($order = Criteria::ASC) Order by the ends column
 * @method     CcScheduleQuery orderByDbFileId($order = Criteria::ASC) Order by the file_id column
 * @method     CcScheduleQuery orderByDbStreamId($order = Criteria::ASC) Order by the stream_id column
 * @method     CcScheduleQuery orderByDbClipLength($order = Criteria::ASC) Order by the clip_length column
 * @method     CcScheduleQuery orderByDbFadeIn($order = Criteria::ASC) Order by the fade_in column
 * @method     CcScheduleQuery orderByDbFadeOut($order = Criteria::ASC) Order by the fade_out column
 * @method     CcScheduleQuery orderByDbCueIn($order = Criteria::ASC) Order by the cue_in column
 * @method     CcScheduleQuery orderByDbCueOut($order = Criteria::ASC) Order by the cue_out column
 * @method     CcScheduleQuery orderByDbMediaItemPlayed($order = Criteria::ASC) Order by the media_item_played column
 * @method     CcScheduleQuery orderByDbInstanceId($order = Criteria::ASC) Order by the instance_id column
 * @method     CcScheduleQuery orderByDbPlayoutStatus($order = Criteria::ASC) Order by the playout_status column
 * @method     CcScheduleQuery orderByDbBroadcasted($order = Criteria::ASC) Order by the broadcasted column
 * @method     CcScheduleQuery orderByDbPosition($order = Criteria::ASC) Order by the position column
 *
 * @method     CcScheduleQuery groupByDbId() Group by the id column
 * @method     CcScheduleQuery groupByDbStarts() Group by the starts column
 * @method     CcScheduleQuery groupByDbEnds() Group by the ends column
 * @method     CcScheduleQuery groupByDbFileId() Group by the file_id column
 * @method     CcScheduleQuery groupByDbStreamId() Group by the stream_id column
 * @method     CcScheduleQuery groupByDbClipLength() Group by the clip_length column
 * @method     CcScheduleQuery groupByDbFadeIn() Group by the fade_in column
 * @method     CcScheduleQuery groupByDbFadeOut() Group by the fade_out column
 * @method     CcScheduleQuery groupByDbCueIn() Group by the cue_in column
 * @method     CcScheduleQuery groupByDbCueOut() Group by the cue_out column
 * @method     CcScheduleQuery groupByDbMediaItemPlayed() Group by the media_item_played column
 * @method     CcScheduleQuery groupByDbInstanceId() Group by the instance_id column
 * @method     CcScheduleQuery groupByDbPlayoutStatus() Group by the playout_status column
 * @method     CcScheduleQuery groupByDbBroadcasted() Group by the broadcasted column
 * @method     CcScheduleQuery groupByDbPosition() Group by the position column
 *
 * @method     CcScheduleQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     CcScheduleQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     CcScheduleQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     CcScheduleQuery leftJoinCcShowInstances($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcShowInstances relation
 * @method     CcScheduleQuery rightJoinCcShowInstances($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcShowInstances relation
 * @method     CcScheduleQuery innerJoinCcShowInstances($relationAlias = '') Adds a INNER JOIN clause to the query using the CcShowInstances relation
 *
 * @method     CcScheduleQuery leftJoinCcFiles($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcFiles relation
 * @method     CcScheduleQuery rightJoinCcFiles($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcFiles relation
 * @method     CcScheduleQuery innerJoinCcFiles($relationAlias = '') Adds a INNER JOIN clause to the query using the CcFiles relation
 *
 * @method     CcScheduleQuery leftJoinCcWebstream($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcWebstream relation
 * @method     CcScheduleQuery rightJoinCcWebstream($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcWebstream relation
 * @method     CcScheduleQuery innerJoinCcWebstream($relationAlias = '') Adds a INNER JOIN clause to the query using the CcWebstream relation
 *
 * @method     CcScheduleQuery leftJoinCcWebstreamMetadata($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcWebstreamMetadata relation
 * @method     CcScheduleQuery rightJoinCcWebstreamMetadata($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcWebstreamMetadata relation
 * @method     CcScheduleQuery innerJoinCcWebstreamMetadata($relationAlias = '') Adds a INNER JOIN clause to the query using the CcWebstreamMetadata relation
 *
 * @method     CcSchedule findOne(PropelPDO $con = null) Return the first CcSchedule matching the query
 * @method     CcSchedule findOneOrCreate(PropelPDO $con = null) Return the first CcSchedule matching the query, or a new CcSchedule object populated from the query conditions when no match is found
 *
 * @method     CcSchedule findOneByDbId(int $id) Return the first CcSchedule filtered by the id column
 * @method     CcSchedule findOneByDbStarts(string $starts) Return the first CcSchedule filtered by the starts column
 * @method     CcSchedule findOneByDbEnds(string $ends) Return the first CcSchedule filtered by the ends column
 * @method     CcSchedule findOneByDbFileId(int $file_id) Return the first CcSchedule filtered by the file_id column
 * @method     CcSchedule findOneByDbStreamId(int $stream_id) Return the first CcSchedule filtered by the stream_id column
 * @method     CcSchedule findOneByDbClipLength(string $clip_length) Return the first CcSchedule filtered by the clip_length column
 * @method     CcSchedule findOneByDbFadeIn(string $fade_in) Return the first CcSchedule filtered by the fade_in column
 * @method     CcSchedule findOneByDbFadeOut(string $fade_out) Return the first CcSchedule filtered by the fade_out column
 * @method     CcSchedule findOneByDbCueIn(string $cue_in) Return the first CcSchedule filtered by the cue_in column
 * @method     CcSchedule findOneByDbCueOut(string $cue_out) Return the first CcSchedule filtered by the cue_out column
 * @method     CcSchedule findOneByDbMediaItemPlayed(boolean $media_item_played) Return the first CcSchedule filtered by the media_item_played column
 * @method     CcSchedule findOneByDbInstanceId(int $instance_id) Return the first CcSchedule filtered by the instance_id column
 * @method     CcSchedule findOneByDbPlayoutStatus(int $playout_status) Return the first CcSchedule filtered by the playout_status column
 * @method     CcSchedule findOneByDbBroadcasted(int $broadcasted) Return the first CcSchedule filtered by the broadcasted column
 * @method     CcSchedule findOneByDbPosition(int $position) Return the first CcSchedule filtered by the position column
 *
 * @method     array findByDbId(int $id) Return CcSchedule objects filtered by the id column
 * @method     array findByDbStarts(string $starts) Return CcSchedule objects filtered by the starts column
 * @method     array findByDbEnds(string $ends) Return CcSchedule objects filtered by the ends column
 * @method     array findByDbFileId(int $file_id) Return CcSchedule objects filtered by the file_id column
 * @method     array findByDbStreamId(int $stream_id) Return CcSchedule objects filtered by the stream_id column
 * @method     array findByDbClipLength(string $clip_length) Return CcSchedule objects filtered by the clip_length column
 * @method     array findByDbFadeIn(string $fade_in) Return CcSchedule objects filtered by the fade_in column
 * @method     array findByDbFadeOut(string $fade_out) Return CcSchedule objects filtered by the fade_out column
 * @method     array findByDbCueIn(string $cue_in) Return CcSchedule objects filtered by the cue_in column
 * @method     array findByDbCueOut(string $cue_out) Return CcSchedule objects filtered by the cue_out column
 * @method     array findByDbMediaItemPlayed(boolean $media_item_played) Return CcSchedule objects filtered by the media_item_played column
 * @method     array findByDbInstanceId(int $instance_id) Return CcSchedule objects filtered by the instance_id column
 * @method     array findByDbPlayoutStatus(int $playout_status) Return CcSchedule objects filtered by the playout_status column
 * @method     array findByDbBroadcasted(int $broadcasted) Return CcSchedule objects filtered by the broadcasted column
 * @method     array findByDbPosition(int $position) Return CcSchedule objects filtered by the position column
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcScheduleQuery extends ModelCriteria
{

	/**
	 * Initializes internal state of BaseCcScheduleQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'airtime', $modelName = 'CcSchedule', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new CcScheduleQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    CcScheduleQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof CcScheduleQuery) {
			return $criteria;
		}
		$query = new CcScheduleQuery();
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
	 * @return    CcSchedule|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = CcSchedulePeer::getInstanceFromPool((string) $key))) && $this->getFormatter()->isObjectFormatter()) {
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
	 * @return    CcScheduleQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(CcSchedulePeer::ID, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    CcScheduleQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(CcSchedulePeer::ID, $keys, Criteria::IN);
	}

	/**
	 * Filter the query on the id column
	 * 
	 * @param     int|array $dbId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcScheduleQuery The current query, for fluid interface
	 */
	public function filterByDbId($dbId = null, $comparison = null)
	{
		if (is_array($dbId) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(CcSchedulePeer::ID, $dbId, $comparison);
	}

	/**
	 * Filter the query on the starts column
	 * 
	 * @param     string|array $dbStarts The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcScheduleQuery The current query, for fluid interface
	 */
	public function filterByDbStarts($dbStarts = null, $comparison = null)
	{
		if (is_array($dbStarts)) {
			$useMinMax = false;
			if (isset($dbStarts['min'])) {
				$this->addUsingAlias(CcSchedulePeer::STARTS, $dbStarts['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbStarts['max'])) {
				$this->addUsingAlias(CcSchedulePeer::STARTS, $dbStarts['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcSchedulePeer::STARTS, $dbStarts, $comparison);
	}

	/**
	 * Filter the query on the ends column
	 * 
	 * @param     string|array $dbEnds The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcScheduleQuery The current query, for fluid interface
	 */
	public function filterByDbEnds($dbEnds = null, $comparison = null)
	{
		if (is_array($dbEnds)) {
			$useMinMax = false;
			if (isset($dbEnds['min'])) {
				$this->addUsingAlias(CcSchedulePeer::ENDS, $dbEnds['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbEnds['max'])) {
				$this->addUsingAlias(CcSchedulePeer::ENDS, $dbEnds['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcSchedulePeer::ENDS, $dbEnds, $comparison);
	}

	/**
	 * Filter the query on the file_id column
	 * 
	 * @param     int|array $dbFileId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcScheduleQuery The current query, for fluid interface
	 */
	public function filterByDbFileId($dbFileId = null, $comparison = null)
	{
		if (is_array($dbFileId)) {
			$useMinMax = false;
			if (isset($dbFileId['min'])) {
				$this->addUsingAlias(CcSchedulePeer::FILE_ID, $dbFileId['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbFileId['max'])) {
				$this->addUsingAlias(CcSchedulePeer::FILE_ID, $dbFileId['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcSchedulePeer::FILE_ID, $dbFileId, $comparison);
	}

	/**
	 * Filter the query on the stream_id column
	 * 
	 * @param     int|array $dbStreamId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcScheduleQuery The current query, for fluid interface
	 */
	public function filterByDbStreamId($dbStreamId = null, $comparison = null)
	{
		if (is_array($dbStreamId)) {
			$useMinMax = false;
			if (isset($dbStreamId['min'])) {
				$this->addUsingAlias(CcSchedulePeer::STREAM_ID, $dbStreamId['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbStreamId['max'])) {
				$this->addUsingAlias(CcSchedulePeer::STREAM_ID, $dbStreamId['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcSchedulePeer::STREAM_ID, $dbStreamId, $comparison);
	}

	/**
	 * Filter the query on the clip_length column
	 * 
	 * @param     string $dbClipLength The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcScheduleQuery The current query, for fluid interface
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
		return $this->addUsingAlias(CcSchedulePeer::CLIP_LENGTH, $dbClipLength, $comparison);
	}

	/**
	 * Filter the query on the fade_in column
	 * 
	 * @param     string|array $dbFadeIn The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcScheduleQuery The current query, for fluid interface
	 */
	public function filterByDbFadeIn($dbFadeIn = null, $comparison = null)
	{
		if (is_array($dbFadeIn)) {
			$useMinMax = false;
			if (isset($dbFadeIn['min'])) {
				$this->addUsingAlias(CcSchedulePeer::FADE_IN, $dbFadeIn['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbFadeIn['max'])) {
				$this->addUsingAlias(CcSchedulePeer::FADE_IN, $dbFadeIn['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcSchedulePeer::FADE_IN, $dbFadeIn, $comparison);
	}

	/**
	 * Filter the query on the fade_out column
	 * 
	 * @param     string|array $dbFadeOut The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcScheduleQuery The current query, for fluid interface
	 */
	public function filterByDbFadeOut($dbFadeOut = null, $comparison = null)
	{
		if (is_array($dbFadeOut)) {
			$useMinMax = false;
			if (isset($dbFadeOut['min'])) {
				$this->addUsingAlias(CcSchedulePeer::FADE_OUT, $dbFadeOut['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbFadeOut['max'])) {
				$this->addUsingAlias(CcSchedulePeer::FADE_OUT, $dbFadeOut['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcSchedulePeer::FADE_OUT, $dbFadeOut, $comparison);
	}

	/**
	 * Filter the query on the cue_in column
	 * 
	 * @param     string $dbCueIn The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcScheduleQuery The current query, for fluid interface
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
		return $this->addUsingAlias(CcSchedulePeer::CUE_IN, $dbCueIn, $comparison);
	}

	/**
	 * Filter the query on the cue_out column
	 * 
	 * @param     string $dbCueOut The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcScheduleQuery The current query, for fluid interface
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
		return $this->addUsingAlias(CcSchedulePeer::CUE_OUT, $dbCueOut, $comparison);
	}

	/**
	 * Filter the query on the media_item_played column
	 * 
	 * @param     boolean|string $dbMediaItemPlayed The value to use as filter.
	 *            Accepts strings ('false', 'off', '-', 'no', 'n', and '0' are false, the rest is true)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcScheduleQuery The current query, for fluid interface
	 */
	public function filterByDbMediaItemPlayed($dbMediaItemPlayed = null, $comparison = null)
	{
		if (is_string($dbMediaItemPlayed)) {
			$media_item_played = in_array(strtolower($dbMediaItemPlayed), array('false', 'off', '-', 'no', 'n', '0')) ? false : true;
		}
		return $this->addUsingAlias(CcSchedulePeer::MEDIA_ITEM_PLAYED, $dbMediaItemPlayed, $comparison);
	}

	/**
	 * Filter the query on the instance_id column
	 * 
	 * @param     int|array $dbInstanceId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcScheduleQuery The current query, for fluid interface
	 */
	public function filterByDbInstanceId($dbInstanceId = null, $comparison = null)
	{
		if (is_array($dbInstanceId)) {
			$useMinMax = false;
			if (isset($dbInstanceId['min'])) {
				$this->addUsingAlias(CcSchedulePeer::INSTANCE_ID, $dbInstanceId['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbInstanceId['max'])) {
				$this->addUsingAlias(CcSchedulePeer::INSTANCE_ID, $dbInstanceId['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcSchedulePeer::INSTANCE_ID, $dbInstanceId, $comparison);
	}

	/**
	 * Filter the query on the playout_status column
	 * 
	 * @param     int|array $dbPlayoutStatus The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcScheduleQuery The current query, for fluid interface
	 */
	public function filterByDbPlayoutStatus($dbPlayoutStatus = null, $comparison = null)
	{
		if (is_array($dbPlayoutStatus)) {
			$useMinMax = false;
			if (isset($dbPlayoutStatus['min'])) {
				$this->addUsingAlias(CcSchedulePeer::PLAYOUT_STATUS, $dbPlayoutStatus['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbPlayoutStatus['max'])) {
				$this->addUsingAlias(CcSchedulePeer::PLAYOUT_STATUS, $dbPlayoutStatus['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcSchedulePeer::PLAYOUT_STATUS, $dbPlayoutStatus, $comparison);
	}

	/**
	 * Filter the query on the broadcasted column
	 * 
	 * @param     int|array $dbBroadcasted The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcScheduleQuery The current query, for fluid interface
	 */
	public function filterByDbBroadcasted($dbBroadcasted = null, $comparison = null)
	{
		if (is_array($dbBroadcasted)) {
			$useMinMax = false;
			if (isset($dbBroadcasted['min'])) {
				$this->addUsingAlias(CcSchedulePeer::BROADCASTED, $dbBroadcasted['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbBroadcasted['max'])) {
				$this->addUsingAlias(CcSchedulePeer::BROADCASTED, $dbBroadcasted['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcSchedulePeer::BROADCASTED, $dbBroadcasted, $comparison);
	}

	/**
	 * Filter the query on the position column
	 * 
	 * @param     int|array $dbPosition The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcScheduleQuery The current query, for fluid interface
	 */
	public function filterByDbPosition($dbPosition = null, $comparison = null)
	{
		if (is_array($dbPosition)) {
			$useMinMax = false;
			if (isset($dbPosition['min'])) {
				$this->addUsingAlias(CcSchedulePeer::POSITION, $dbPosition['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbPosition['max'])) {
				$this->addUsingAlias(CcSchedulePeer::POSITION, $dbPosition['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcSchedulePeer::POSITION, $dbPosition, $comparison);
	}

	/**
	 * Filter the query by a related CcShowInstances object
	 *
	 * @param     CcShowInstances $ccShowInstances  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcScheduleQuery The current query, for fluid interface
	 */
	public function filterByCcShowInstances($ccShowInstances, $comparison = null)
	{
		return $this
			->addUsingAlias(CcSchedulePeer::INSTANCE_ID, $ccShowInstances->getDbId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcShowInstances relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcScheduleQuery The current query, for fluid interface
	 */
	public function joinCcShowInstances($relationAlias = '', $joinType = Criteria::INNER_JOIN)
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
	public function useCcShowInstancesQuery($relationAlias = '', $joinType = Criteria::INNER_JOIN)
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
	 * @return    CcScheduleQuery The current query, for fluid interface
	 */
	public function filterByCcFiles($ccFiles, $comparison = null)
	{
		return $this
			->addUsingAlias(CcSchedulePeer::FILE_ID, $ccFiles->getDbId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcFiles relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcScheduleQuery The current query, for fluid interface
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
	 * @return    CcScheduleQuery The current query, for fluid interface
	 */
	public function filterByCcWebstream($ccWebstream, $comparison = null)
	{
		return $this
			->addUsingAlias(CcSchedulePeer::STREAM_ID, $ccWebstream->getDbId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcWebstream relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcScheduleQuery The current query, for fluid interface
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
	 * Filter the query by a related CcWebstreamMetadata object
	 *
	 * @param     CcWebstreamMetadata $ccWebstreamMetadata  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcScheduleQuery The current query, for fluid interface
	 */
	public function filterByCcWebstreamMetadata($ccWebstreamMetadata, $comparison = null)
	{
		return $this
			->addUsingAlias(CcSchedulePeer::ID, $ccWebstreamMetadata->getDbInstanceId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcWebstreamMetadata relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcScheduleQuery The current query, for fluid interface
	 */
	public function joinCcWebstreamMetadata($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('CcWebstreamMetadata');
		
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
			$this->addJoinObject($join, 'CcWebstreamMetadata');
		}
		
		return $this;
	}

	/**
	 * Use the CcWebstreamMetadata relation CcWebstreamMetadata object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcWebstreamMetadataQuery A secondary query class using the current class as primary query
	 */
	public function useCcWebstreamMetadataQuery($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinCcWebstreamMetadata($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CcWebstreamMetadata', 'CcWebstreamMetadataQuery');
	}

	/**
	 * Exclude object from result
	 *
	 * @param     CcSchedule $ccSchedule Object to remove from the list of results
	 *
	 * @return    CcScheduleQuery The current query, for fluid interface
	 */
	public function prune($ccSchedule = null)
	{
		if ($ccSchedule) {
			$this->addUsingAlias(CcSchedulePeer::ID, $ccSchedule->getDbId(), Criteria::NOT_EQUAL);
	  }
	  
		return $this;
	}

} // BaseCcScheduleQuery
