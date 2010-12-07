<?php


/**
 * Base class that represents a query for the 'cc_schedule' table.
 *
 * 
 *
 * @method     CcScheduleQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     CcScheduleQuery orderByPlaylistId($order = Criteria::ASC) Order by the playlist_id column
 * @method     CcScheduleQuery orderByStarts($order = Criteria::ASC) Order by the starts column
 * @method     CcScheduleQuery orderByEnds($order = Criteria::ASC) Order by the ends column
 * @method     CcScheduleQuery orderByGroupId($order = Criteria::ASC) Order by the group_id column
 * @method     CcScheduleQuery orderByFileId($order = Criteria::ASC) Order by the file_id column
 * @method     CcScheduleQuery orderByClipLength($order = Criteria::ASC) Order by the clip_length column
 * @method     CcScheduleQuery orderByFadeIn($order = Criteria::ASC) Order by the fade_in column
 * @method     CcScheduleQuery orderByFadeOut($order = Criteria::ASC) Order by the fade_out column
 * @method     CcScheduleQuery orderByCueIn($order = Criteria::ASC) Order by the cue_in column
 * @method     CcScheduleQuery orderByCueOut($order = Criteria::ASC) Order by the cue_out column
 *
 * @method     CcScheduleQuery groupById() Group by the id column
 * @method     CcScheduleQuery groupByPlaylistId() Group by the playlist_id column
 * @method     CcScheduleQuery groupByStarts() Group by the starts column
 * @method     CcScheduleQuery groupByEnds() Group by the ends column
 * @method     CcScheduleQuery groupByGroupId() Group by the group_id column
 * @method     CcScheduleQuery groupByFileId() Group by the file_id column
 * @method     CcScheduleQuery groupByClipLength() Group by the clip_length column
 * @method     CcScheduleQuery groupByFadeIn() Group by the fade_in column
 * @method     CcScheduleQuery groupByFadeOut() Group by the fade_out column
 * @method     CcScheduleQuery groupByCueIn() Group by the cue_in column
 * @method     CcScheduleQuery groupByCueOut() Group by the cue_out column
 *
 * @method     CcScheduleQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     CcScheduleQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     CcScheduleQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     CcSchedule findOne(PropelPDO $con = null) Return the first CcSchedule matching the query
 * @method     CcSchedule findOneOrCreate(PropelPDO $con = null) Return the first CcSchedule matching the query, or a new CcSchedule object populated from the query conditions when no match is found
 *
 * @method     CcSchedule findOneById(string $id) Return the first CcSchedule filtered by the id column
 * @method     CcSchedule findOneByPlaylistId(int $playlist_id) Return the first CcSchedule filtered by the playlist_id column
 * @method     CcSchedule findOneByStarts(string $starts) Return the first CcSchedule filtered by the starts column
 * @method     CcSchedule findOneByEnds(string $ends) Return the first CcSchedule filtered by the ends column
 * @method     CcSchedule findOneByGroupId(int $group_id) Return the first CcSchedule filtered by the group_id column
 * @method     CcSchedule findOneByFileId(int $file_id) Return the first CcSchedule filtered by the file_id column
 * @method     CcSchedule findOneByClipLength(string $clip_length) Return the first CcSchedule filtered by the clip_length column
 * @method     CcSchedule findOneByFadeIn(string $fade_in) Return the first CcSchedule filtered by the fade_in column
 * @method     CcSchedule findOneByFadeOut(string $fade_out) Return the first CcSchedule filtered by the fade_out column
 * @method     CcSchedule findOneByCueIn(string $cue_in) Return the first CcSchedule filtered by the cue_in column
 * @method     CcSchedule findOneByCueOut(string $cue_out) Return the first CcSchedule filtered by the cue_out column
 *
 * @method     array findById(string $id) Return CcSchedule objects filtered by the id column
 * @method     array findByPlaylistId(int $playlist_id) Return CcSchedule objects filtered by the playlist_id column
 * @method     array findByStarts(string $starts) Return CcSchedule objects filtered by the starts column
 * @method     array findByEnds(string $ends) Return CcSchedule objects filtered by the ends column
 * @method     array findByGroupId(int $group_id) Return CcSchedule objects filtered by the group_id column
 * @method     array findByFileId(int $file_id) Return CcSchedule objects filtered by the file_id column
 * @method     array findByClipLength(string $clip_length) Return CcSchedule objects filtered by the clip_length column
 * @method     array findByFadeIn(string $fade_in) Return CcSchedule objects filtered by the fade_in column
 * @method     array findByFadeOut(string $fade_out) Return CcSchedule objects filtered by the fade_out column
 * @method     array findByCueIn(string $cue_in) Return CcSchedule objects filtered by the cue_in column
 * @method     array findByCueOut(string $cue_out) Return CcSchedule objects filtered by the cue_out column
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
	 * @param     string|array $id The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcScheduleQuery The current query, for fluid interface
	 */
	public function filterById($id = null, $comparison = null)
	{
		if (is_array($id) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(CcSchedulePeer::ID, $id, $comparison);
	}

	/**
	 * Filter the query on the playlist_id column
	 * 
	 * @param     int|array $playlistId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcScheduleQuery The current query, for fluid interface
	 */
	public function filterByPlaylistId($playlistId = null, $comparison = null)
	{
		if (is_array($playlistId)) {
			$useMinMax = false;
			if (isset($playlistId['min'])) {
				$this->addUsingAlias(CcSchedulePeer::PLAYLIST_ID, $playlistId['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($playlistId['max'])) {
				$this->addUsingAlias(CcSchedulePeer::PLAYLIST_ID, $playlistId['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcSchedulePeer::PLAYLIST_ID, $playlistId, $comparison);
	}

	/**
	 * Filter the query on the starts column
	 * 
	 * @param     string|array $starts The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcScheduleQuery The current query, for fluid interface
	 */
	public function filterByStarts($starts = null, $comparison = null)
	{
		if (is_array($starts)) {
			$useMinMax = false;
			if (isset($starts['min'])) {
				$this->addUsingAlias(CcSchedulePeer::STARTS, $starts['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($starts['max'])) {
				$this->addUsingAlias(CcSchedulePeer::STARTS, $starts['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcSchedulePeer::STARTS, $starts, $comparison);
	}

	/**
	 * Filter the query on the ends column
	 * 
	 * @param     string|array $ends The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcScheduleQuery The current query, for fluid interface
	 */
	public function filterByEnds($ends = null, $comparison = null)
	{
		if (is_array($ends)) {
			$useMinMax = false;
			if (isset($ends['min'])) {
				$this->addUsingAlias(CcSchedulePeer::ENDS, $ends['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($ends['max'])) {
				$this->addUsingAlias(CcSchedulePeer::ENDS, $ends['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcSchedulePeer::ENDS, $ends, $comparison);
	}

	/**
	 * Filter the query on the group_id column
	 * 
	 * @param     int|array $groupId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcScheduleQuery The current query, for fluid interface
	 */
	public function filterByGroupId($groupId = null, $comparison = null)
	{
		if (is_array($groupId)) {
			$useMinMax = false;
			if (isset($groupId['min'])) {
				$this->addUsingAlias(CcSchedulePeer::GROUP_ID, $groupId['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($groupId['max'])) {
				$this->addUsingAlias(CcSchedulePeer::GROUP_ID, $groupId['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcSchedulePeer::GROUP_ID, $groupId, $comparison);
	}

	/**
	 * Filter the query on the file_id column
	 * 
	 * @param     int|array $fileId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcScheduleQuery The current query, for fluid interface
	 */
	public function filterByFileId($fileId = null, $comparison = null)
	{
		if (is_array($fileId)) {
			$useMinMax = false;
			if (isset($fileId['min'])) {
				$this->addUsingAlias(CcSchedulePeer::FILE_ID, $fileId['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($fileId['max'])) {
				$this->addUsingAlias(CcSchedulePeer::FILE_ID, $fileId['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcSchedulePeer::FILE_ID, $fileId, $comparison);
	}

	/**
	 * Filter the query on the clip_length column
	 * 
	 * @param     string|array $clipLength The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcScheduleQuery The current query, for fluid interface
	 */
	public function filterByClipLength($clipLength = null, $comparison = null)
	{
		if (is_array($clipLength)) {
			$useMinMax = false;
			if (isset($clipLength['min'])) {
				$this->addUsingAlias(CcSchedulePeer::CLIP_LENGTH, $clipLength['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($clipLength['max'])) {
				$this->addUsingAlias(CcSchedulePeer::CLIP_LENGTH, $clipLength['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcSchedulePeer::CLIP_LENGTH, $clipLength, $comparison);
	}

	/**
	 * Filter the query on the fade_in column
	 * 
	 * @param     string|array $fadeIn The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcScheduleQuery The current query, for fluid interface
	 */
	public function filterByFadeIn($fadeIn = null, $comparison = null)
	{
		if (is_array($fadeIn)) {
			$useMinMax = false;
			if (isset($fadeIn['min'])) {
				$this->addUsingAlias(CcSchedulePeer::FADE_IN, $fadeIn['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($fadeIn['max'])) {
				$this->addUsingAlias(CcSchedulePeer::FADE_IN, $fadeIn['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcSchedulePeer::FADE_IN, $fadeIn, $comparison);
	}

	/**
	 * Filter the query on the fade_out column
	 * 
	 * @param     string|array $fadeOut The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcScheduleQuery The current query, for fluid interface
	 */
	public function filterByFadeOut($fadeOut = null, $comparison = null)
	{
		if (is_array($fadeOut)) {
			$useMinMax = false;
			if (isset($fadeOut['min'])) {
				$this->addUsingAlias(CcSchedulePeer::FADE_OUT, $fadeOut['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($fadeOut['max'])) {
				$this->addUsingAlias(CcSchedulePeer::FADE_OUT, $fadeOut['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcSchedulePeer::FADE_OUT, $fadeOut, $comparison);
	}

	/**
	 * Filter the query on the cue_in column
	 * 
	 * @param     string|array $cueIn The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcScheduleQuery The current query, for fluid interface
	 */
	public function filterByCueIn($cueIn = null, $comparison = null)
	{
		if (is_array($cueIn)) {
			$useMinMax = false;
			if (isset($cueIn['min'])) {
				$this->addUsingAlias(CcSchedulePeer::CUE_IN, $cueIn['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($cueIn['max'])) {
				$this->addUsingAlias(CcSchedulePeer::CUE_IN, $cueIn['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcSchedulePeer::CUE_IN, $cueIn, $comparison);
	}

	/**
	 * Filter the query on the cue_out column
	 * 
	 * @param     string|array $cueOut The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcScheduleQuery The current query, for fluid interface
	 */
	public function filterByCueOut($cueOut = null, $comparison = null)
	{
		if (is_array($cueOut)) {
			$useMinMax = false;
			if (isset($cueOut['min'])) {
				$this->addUsingAlias(CcSchedulePeer::CUE_OUT, $cueOut['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($cueOut['max'])) {
				$this->addUsingAlias(CcSchedulePeer::CUE_OUT, $cueOut['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcSchedulePeer::CUE_OUT, $cueOut, $comparison);
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
			$this->addUsingAlias(CcSchedulePeer::ID, $ccSchedule->getId(), Criteria::NOT_EQUAL);
	  }
	  
		return $this;
	}

} // BaseCcScheduleQuery
