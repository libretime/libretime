<?php


/**
 * Base class that represents a query for the 'cc_schedule' table.
 *
 * 
 *
 * @method     CcScheduleQuery orderByDbId($order = Criteria::ASC) Order by the id column
 * @method     CcScheduleQuery orderByDbPlaylistId($order = Criteria::ASC) Order by the playlist_id column
 * @method     CcScheduleQuery orderByDbStarts($order = Criteria::ASC) Order by the starts column
 * @method     CcScheduleQuery orderByDbEnds($order = Criteria::ASC) Order by the ends column
 * @method     CcScheduleQuery orderByDbGroupId($order = Criteria::ASC) Order by the group_id column
 * @method     CcScheduleQuery orderByDbFileId($order = Criteria::ASC) Order by the file_id column
 * @method     CcScheduleQuery orderByDbClipLength($order = Criteria::ASC) Order by the clip_length column
 * @method     CcScheduleQuery orderByDbFadeIn($order = Criteria::ASC) Order by the fade_in column
 * @method     CcScheduleQuery orderByDbFadeOut($order = Criteria::ASC) Order by the fade_out column
 * @method     CcScheduleQuery orderByDbCueIn($order = Criteria::ASC) Order by the cue_in column
 * @method     CcScheduleQuery orderByDbCueOut($order = Criteria::ASC) Order by the cue_out column
 *
 * @method     CcScheduleQuery groupByDbId() Group by the id column
 * @method     CcScheduleQuery groupByDbPlaylistId() Group by the playlist_id column
 * @method     CcScheduleQuery groupByDbStarts() Group by the starts column
 * @method     CcScheduleQuery groupByDbEnds() Group by the ends column
 * @method     CcScheduleQuery groupByDbGroupId() Group by the group_id column
 * @method     CcScheduleQuery groupByDbFileId() Group by the file_id column
 * @method     CcScheduleQuery groupByDbClipLength() Group by the clip_length column
 * @method     CcScheduleQuery groupByDbFadeIn() Group by the fade_in column
 * @method     CcScheduleQuery groupByDbFadeOut() Group by the fade_out column
 * @method     CcScheduleQuery groupByDbCueIn() Group by the cue_in column
 * @method     CcScheduleQuery groupByDbCueOut() Group by the cue_out column
 *
 * @method     CcScheduleQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     CcScheduleQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     CcScheduleQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     CcSchedule findOne(PropelPDO $con = null) Return the first CcSchedule matching the query
 * @method     CcSchedule findOneOrCreate(PropelPDO $con = null) Return the first CcSchedule matching the query, or a new CcSchedule object populated from the query conditions when no match is found
 *
 * @method     CcSchedule findOneByDbId(string $id) Return the first CcSchedule filtered by the id column
 * @method     CcSchedule findOneByDbPlaylistId(int $playlist_id) Return the first CcSchedule filtered by the playlist_id column
 * @method     CcSchedule findOneByDbStarts(string $starts) Return the first CcSchedule filtered by the starts column
 * @method     CcSchedule findOneByDbEnds(string $ends) Return the first CcSchedule filtered by the ends column
 * @method     CcSchedule findOneByDbGroupId(int $group_id) Return the first CcSchedule filtered by the group_id column
 * @method     CcSchedule findOneByDbFileId(int $file_id) Return the first CcSchedule filtered by the file_id column
 * @method     CcSchedule findOneByDbClipLength(string $clip_length) Return the first CcSchedule filtered by the clip_length column
 * @method     CcSchedule findOneByDbFadeIn(string $fade_in) Return the first CcSchedule filtered by the fade_in column
 * @method     CcSchedule findOneByDbFadeOut(string $fade_out) Return the first CcSchedule filtered by the fade_out column
 * @method     CcSchedule findOneByDbCueIn(string $cue_in) Return the first CcSchedule filtered by the cue_in column
 * @method     CcSchedule findOneByDbCueOut(string $cue_out) Return the first CcSchedule filtered by the cue_out column
 *
 * @method     array findByDbId(string $id) Return CcSchedule objects filtered by the id column
 * @method     array findByDbPlaylistId(int $playlist_id) Return CcSchedule objects filtered by the playlist_id column
 * @method     array findByDbStarts(string $starts) Return CcSchedule objects filtered by the starts column
 * @method     array findByDbEnds(string $ends) Return CcSchedule objects filtered by the ends column
 * @method     array findByDbGroupId(int $group_id) Return CcSchedule objects filtered by the group_id column
 * @method     array findByDbFileId(int $file_id) Return CcSchedule objects filtered by the file_id column
 * @method     array findByDbClipLength(string $clip_length) Return CcSchedule objects filtered by the clip_length column
 * @method     array findByDbFadeIn(string $fade_in) Return CcSchedule objects filtered by the fade_in column
 * @method     array findByDbFadeOut(string $fade_out) Return CcSchedule objects filtered by the fade_out column
 * @method     array findByDbCueIn(string $cue_in) Return CcSchedule objects filtered by the cue_in column
 * @method     array findByDbCueOut(string $cue_out) Return CcSchedule objects filtered by the cue_out column
 *
 * @package    propel.generator.campcaster.om
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
	public function __construct($dbName = 'campcaster', $modelName = 'CcSchedule', $modelAlias = null)
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
	 * @param     string|array $dbId The value to use as filter.
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
	 * Filter the query on the playlist_id column
	 * 
	 * @param     int|array $dbPlaylistId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcScheduleQuery The current query, for fluid interface
	 */
	public function filterByDbPlaylistId($dbPlaylistId = null, $comparison = null)
	{
		if (is_array($dbPlaylistId)) {
			$useMinMax = false;
			if (isset($dbPlaylistId['min'])) {
				$this->addUsingAlias(CcSchedulePeer::PLAYLIST_ID, $dbPlaylistId['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbPlaylistId['max'])) {
				$this->addUsingAlias(CcSchedulePeer::PLAYLIST_ID, $dbPlaylistId['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcSchedulePeer::PLAYLIST_ID, $dbPlaylistId, $comparison);
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
	 * Filter the query on the group_id column
	 * 
	 * @param     int|array $dbGroupId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcScheduleQuery The current query, for fluid interface
	 */
	public function filterByDbGroupId($dbGroupId = null, $comparison = null)
	{
		if (is_array($dbGroupId)) {
			$useMinMax = false;
			if (isset($dbGroupId['min'])) {
				$this->addUsingAlias(CcSchedulePeer::GROUP_ID, $dbGroupId['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbGroupId['max'])) {
				$this->addUsingAlias(CcSchedulePeer::GROUP_ID, $dbGroupId['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcSchedulePeer::GROUP_ID, $dbGroupId, $comparison);
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
	 * Filter the query on the clip_length column
	 * 
	 * @param     string|array $dbClipLength The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcScheduleQuery The current query, for fluid interface
	 */
	public function filterByDbClipLength($dbClipLength = null, $comparison = null)
	{
		if (is_array($dbClipLength)) {
			$useMinMax = false;
			if (isset($dbClipLength['min'])) {
				$this->addUsingAlias(CcSchedulePeer::CLIP_LENGTH, $dbClipLength['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbClipLength['max'])) {
				$this->addUsingAlias(CcSchedulePeer::CLIP_LENGTH, $dbClipLength['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
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
	 * @param     string|array $dbCueIn The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcScheduleQuery The current query, for fluid interface
	 */
	public function filterByDbCueIn($dbCueIn = null, $comparison = null)
	{
		if (is_array($dbCueIn)) {
			$useMinMax = false;
			if (isset($dbCueIn['min'])) {
				$this->addUsingAlias(CcSchedulePeer::CUE_IN, $dbCueIn['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbCueIn['max'])) {
				$this->addUsingAlias(CcSchedulePeer::CUE_IN, $dbCueIn['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcSchedulePeer::CUE_IN, $dbCueIn, $comparison);
	}

	/**
	 * Filter the query on the cue_out column
	 * 
	 * @param     string|array $dbCueOut The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcScheduleQuery The current query, for fluid interface
	 */
	public function filterByDbCueOut($dbCueOut = null, $comparison = null)
	{
		if (is_array($dbCueOut)) {
			$useMinMax = false;
			if (isset($dbCueOut['min'])) {
				$this->addUsingAlias(CcSchedulePeer::CUE_OUT, $dbCueOut['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbCueOut['max'])) {
				$this->addUsingAlias(CcSchedulePeer::CUE_OUT, $dbCueOut['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcSchedulePeer::CUE_OUT, $dbCueOut, $comparison);
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
