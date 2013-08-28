<?php


/**
 * Base class that represents a query for the 'cc_show_instances' table.
 *
 * 
 *
 * @method     CcShowInstancesQuery orderByDbId($order = Criteria::ASC) Order by the id column
 * @method     CcShowInstancesQuery orderByDbStarts($order = Criteria::ASC) Order by the starts column
 * @method     CcShowInstancesQuery orderByDbEnds($order = Criteria::ASC) Order by the ends column
 * @method     CcShowInstancesQuery orderByDbShowId($order = Criteria::ASC) Order by the show_id column
 * @method     CcShowInstancesQuery orderByDbRecord($order = Criteria::ASC) Order by the record column
 * @method     CcShowInstancesQuery orderByDbRebroadcast($order = Criteria::ASC) Order by the rebroadcast column
 * @method     CcShowInstancesQuery orderByDbOriginalShow($order = Criteria::ASC) Order by the instance_id column
 * @method     CcShowInstancesQuery orderByDbRecordedFile($order = Criteria::ASC) Order by the file_id column
 * @method     CcShowInstancesQuery orderByDbTimeFilled($order = Criteria::ASC) Order by the time_filled column
 * @method     CcShowInstancesQuery orderByDbCreated($order = Criteria::ASC) Order by the created column
 * @method     CcShowInstancesQuery orderByDbLastScheduled($order = Criteria::ASC) Order by the last_scheduled column
 * @method     CcShowInstancesQuery orderByDbModifiedInstance($order = Criteria::ASC) Order by the modified_instance column
 *
 * @method     CcShowInstancesQuery groupByDbId() Group by the id column
 * @method     CcShowInstancesQuery groupByDbStarts() Group by the starts column
 * @method     CcShowInstancesQuery groupByDbEnds() Group by the ends column
 * @method     CcShowInstancesQuery groupByDbShowId() Group by the show_id column
 * @method     CcShowInstancesQuery groupByDbRecord() Group by the record column
 * @method     CcShowInstancesQuery groupByDbRebroadcast() Group by the rebroadcast column
 * @method     CcShowInstancesQuery groupByDbOriginalShow() Group by the instance_id column
 * @method     CcShowInstancesQuery groupByDbRecordedFile() Group by the file_id column
 * @method     CcShowInstancesQuery groupByDbTimeFilled() Group by the time_filled column
 * @method     CcShowInstancesQuery groupByDbCreated() Group by the created column
 * @method     CcShowInstancesQuery groupByDbLastScheduled() Group by the last_scheduled column
 * @method     CcShowInstancesQuery groupByDbModifiedInstance() Group by the modified_instance column
 *
 * @method     CcShowInstancesQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     CcShowInstancesQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     CcShowInstancesQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     CcShowInstancesQuery leftJoinCcShow($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcShow relation
 * @method     CcShowInstancesQuery rightJoinCcShow($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcShow relation
 * @method     CcShowInstancesQuery innerJoinCcShow($relationAlias = '') Adds a INNER JOIN clause to the query using the CcShow relation
 *
 * @method     CcShowInstancesQuery leftJoinCcShowInstancesRelatedByDbOriginalShow($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcShowInstancesRelatedByDbOriginalShow relation
 * @method     CcShowInstancesQuery rightJoinCcShowInstancesRelatedByDbOriginalShow($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcShowInstancesRelatedByDbOriginalShow relation
 * @method     CcShowInstancesQuery innerJoinCcShowInstancesRelatedByDbOriginalShow($relationAlias = '') Adds a INNER JOIN clause to the query using the CcShowInstancesRelatedByDbOriginalShow relation
 *
 * @method     CcShowInstancesQuery leftJoinCcFiles($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcFiles relation
 * @method     CcShowInstancesQuery rightJoinCcFiles($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcFiles relation
 * @method     CcShowInstancesQuery innerJoinCcFiles($relationAlias = '') Adds a INNER JOIN clause to the query using the CcFiles relation
 *
 * @method     CcShowInstancesQuery leftJoinCcShowInstancesRelatedByDbId($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcShowInstancesRelatedByDbId relation
 * @method     CcShowInstancesQuery rightJoinCcShowInstancesRelatedByDbId($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcShowInstancesRelatedByDbId relation
 * @method     CcShowInstancesQuery innerJoinCcShowInstancesRelatedByDbId($relationAlias = '') Adds a INNER JOIN clause to the query using the CcShowInstancesRelatedByDbId relation
 *
 * @method     CcShowInstancesQuery leftJoinCcSchedule($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcSchedule relation
 * @method     CcShowInstancesQuery rightJoinCcSchedule($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcSchedule relation
 * @method     CcShowInstancesQuery innerJoinCcSchedule($relationAlias = '') Adds a INNER JOIN clause to the query using the CcSchedule relation
 *
 * @method     CcShowInstancesQuery leftJoinCcPlayoutHistory($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcPlayoutHistory relation
 * @method     CcShowInstancesQuery rightJoinCcPlayoutHistory($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcPlayoutHistory relation
 * @method     CcShowInstancesQuery innerJoinCcPlayoutHistory($relationAlias = '') Adds a INNER JOIN clause to the query using the CcPlayoutHistory relation
 *
 * @method     CcShowInstances findOne(PropelPDO $con = null) Return the first CcShowInstances matching the query
 * @method     CcShowInstances findOneOrCreate(PropelPDO $con = null) Return the first CcShowInstances matching the query, or a new CcShowInstances object populated from the query conditions when no match is found
 *
 * @method     CcShowInstances findOneByDbId(int $id) Return the first CcShowInstances filtered by the id column
 * @method     CcShowInstances findOneByDbStarts(string $starts) Return the first CcShowInstances filtered by the starts column
 * @method     CcShowInstances findOneByDbEnds(string $ends) Return the first CcShowInstances filtered by the ends column
 * @method     CcShowInstances findOneByDbShowId(int $show_id) Return the first CcShowInstances filtered by the show_id column
 * @method     CcShowInstances findOneByDbRecord(int $record) Return the first CcShowInstances filtered by the record column
 * @method     CcShowInstances findOneByDbRebroadcast(int $rebroadcast) Return the first CcShowInstances filtered by the rebroadcast column
 * @method     CcShowInstances findOneByDbOriginalShow(int $instance_id) Return the first CcShowInstances filtered by the instance_id column
 * @method     CcShowInstances findOneByDbRecordedFile(int $file_id) Return the first CcShowInstances filtered by the file_id column
 * @method     CcShowInstances findOneByDbTimeFilled(string $time_filled) Return the first CcShowInstances filtered by the time_filled column
 * @method     CcShowInstances findOneByDbCreated(string $created) Return the first CcShowInstances filtered by the created column
 * @method     CcShowInstances findOneByDbLastScheduled(string $last_scheduled) Return the first CcShowInstances filtered by the last_scheduled column
 * @method     CcShowInstances findOneByDbModifiedInstance(boolean $modified_instance) Return the first CcShowInstances filtered by the modified_instance column
 *
 * @method     array findByDbId(int $id) Return CcShowInstances objects filtered by the id column
 * @method     array findByDbStarts(string $starts) Return CcShowInstances objects filtered by the starts column
 * @method     array findByDbEnds(string $ends) Return CcShowInstances objects filtered by the ends column
 * @method     array findByDbShowId(int $show_id) Return CcShowInstances objects filtered by the show_id column
 * @method     array findByDbRecord(int $record) Return CcShowInstances objects filtered by the record column
 * @method     array findByDbRebroadcast(int $rebroadcast) Return CcShowInstances objects filtered by the rebroadcast column
 * @method     array findByDbOriginalShow(int $instance_id) Return CcShowInstances objects filtered by the instance_id column
 * @method     array findByDbRecordedFile(int $file_id) Return CcShowInstances objects filtered by the file_id column
 * @method     array findByDbTimeFilled(string $time_filled) Return CcShowInstances objects filtered by the time_filled column
 * @method     array findByDbCreated(string $created) Return CcShowInstances objects filtered by the created column
 * @method     array findByDbLastScheduled(string $last_scheduled) Return CcShowInstances objects filtered by the last_scheduled column
 * @method     array findByDbModifiedInstance(boolean $modified_instance) Return CcShowInstances objects filtered by the modified_instance column
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcShowInstancesQuery extends ModelCriteria
{

	/**
	 * Initializes internal state of BaseCcShowInstancesQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'airtime', $modelName = 'CcShowInstances', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new CcShowInstancesQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    CcShowInstancesQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof CcShowInstancesQuery) {
			return $criteria;
		}
		$query = new CcShowInstancesQuery();
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
	 * @return    CcShowInstances|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = CcShowInstancesPeer::getInstanceFromPool((string) $key))) && $this->getFormatter()->isObjectFormatter()) {
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
	 * @return    CcShowInstancesQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(CcShowInstancesPeer::ID, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    CcShowInstancesQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(CcShowInstancesPeer::ID, $keys, Criteria::IN);
	}

	/**
	 * Filter the query on the id column
	 * 
	 * @param     int|array $dbId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcShowInstancesQuery The current query, for fluid interface
	 */
	public function filterByDbId($dbId = null, $comparison = null)
	{
		if (is_array($dbId) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(CcShowInstancesPeer::ID, $dbId, $comparison);
	}

	/**
	 * Filter the query on the starts column
	 * 
	 * @param     string|array $dbStarts The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcShowInstancesQuery The current query, for fluid interface
	 */
	public function filterByDbStarts($dbStarts = null, $comparison = null)
	{
		if (is_array($dbStarts)) {
			$useMinMax = false;
			if (isset($dbStarts['min'])) {
				$this->addUsingAlias(CcShowInstancesPeer::STARTS, $dbStarts['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbStarts['max'])) {
				$this->addUsingAlias(CcShowInstancesPeer::STARTS, $dbStarts['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcShowInstancesPeer::STARTS, $dbStarts, $comparison);
	}

	/**
	 * Filter the query on the ends column
	 * 
	 * @param     string|array $dbEnds The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcShowInstancesQuery The current query, for fluid interface
	 */
	public function filterByDbEnds($dbEnds = null, $comparison = null)
	{
		if (is_array($dbEnds)) {
			$useMinMax = false;
			if (isset($dbEnds['min'])) {
				$this->addUsingAlias(CcShowInstancesPeer::ENDS, $dbEnds['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbEnds['max'])) {
				$this->addUsingAlias(CcShowInstancesPeer::ENDS, $dbEnds['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcShowInstancesPeer::ENDS, $dbEnds, $comparison);
	}

	/**
	 * Filter the query on the show_id column
	 * 
	 * @param     int|array $dbShowId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcShowInstancesQuery The current query, for fluid interface
	 */
	public function filterByDbShowId($dbShowId = null, $comparison = null)
	{
		if (is_array($dbShowId)) {
			$useMinMax = false;
			if (isset($dbShowId['min'])) {
				$this->addUsingAlias(CcShowInstancesPeer::SHOW_ID, $dbShowId['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbShowId['max'])) {
				$this->addUsingAlias(CcShowInstancesPeer::SHOW_ID, $dbShowId['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcShowInstancesPeer::SHOW_ID, $dbShowId, $comparison);
	}

	/**
	 * Filter the query on the record column
	 * 
	 * @param     int|array $dbRecord The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcShowInstancesQuery The current query, for fluid interface
	 */
	public function filterByDbRecord($dbRecord = null, $comparison = null)
	{
		if (is_array($dbRecord)) {
			$useMinMax = false;
			if (isset($dbRecord['min'])) {
				$this->addUsingAlias(CcShowInstancesPeer::RECORD, $dbRecord['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbRecord['max'])) {
				$this->addUsingAlias(CcShowInstancesPeer::RECORD, $dbRecord['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcShowInstancesPeer::RECORD, $dbRecord, $comparison);
	}

	/**
	 * Filter the query on the rebroadcast column
	 * 
	 * @param     int|array $dbRebroadcast The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcShowInstancesQuery The current query, for fluid interface
	 */
	public function filterByDbRebroadcast($dbRebroadcast = null, $comparison = null)
	{
		if (is_array($dbRebroadcast)) {
			$useMinMax = false;
			if (isset($dbRebroadcast['min'])) {
				$this->addUsingAlias(CcShowInstancesPeer::REBROADCAST, $dbRebroadcast['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbRebroadcast['max'])) {
				$this->addUsingAlias(CcShowInstancesPeer::REBROADCAST, $dbRebroadcast['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcShowInstancesPeer::REBROADCAST, $dbRebroadcast, $comparison);
	}

	/**
	 * Filter the query on the instance_id column
	 * 
	 * @param     int|array $dbOriginalShow The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcShowInstancesQuery The current query, for fluid interface
	 */
	public function filterByDbOriginalShow($dbOriginalShow = null, $comparison = null)
	{
		if (is_array($dbOriginalShow)) {
			$useMinMax = false;
			if (isset($dbOriginalShow['min'])) {
				$this->addUsingAlias(CcShowInstancesPeer::INSTANCE_ID, $dbOriginalShow['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbOriginalShow['max'])) {
				$this->addUsingAlias(CcShowInstancesPeer::INSTANCE_ID, $dbOriginalShow['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcShowInstancesPeer::INSTANCE_ID, $dbOriginalShow, $comparison);
	}

	/**
	 * Filter the query on the file_id column
	 * 
	 * @param     int|array $dbRecordedFile The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcShowInstancesQuery The current query, for fluid interface
	 */
	public function filterByDbRecordedFile($dbRecordedFile = null, $comparison = null)
	{
		if (is_array($dbRecordedFile)) {
			$useMinMax = false;
			if (isset($dbRecordedFile['min'])) {
				$this->addUsingAlias(CcShowInstancesPeer::FILE_ID, $dbRecordedFile['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbRecordedFile['max'])) {
				$this->addUsingAlias(CcShowInstancesPeer::FILE_ID, $dbRecordedFile['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcShowInstancesPeer::FILE_ID, $dbRecordedFile, $comparison);
	}

	/**
	 * Filter the query on the time_filled column
	 * 
	 * @param     string $dbTimeFilled The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcShowInstancesQuery The current query, for fluid interface
	 */
	public function filterByDbTimeFilled($dbTimeFilled = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbTimeFilled)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbTimeFilled)) {
				$dbTimeFilled = str_replace('*', '%', $dbTimeFilled);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcShowInstancesPeer::TIME_FILLED, $dbTimeFilled, $comparison);
	}

	/**
	 * Filter the query on the created column
	 * 
	 * @param     string|array $dbCreated The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcShowInstancesQuery The current query, for fluid interface
	 */
	public function filterByDbCreated($dbCreated = null, $comparison = null)
	{
		if (is_array($dbCreated)) {
			$useMinMax = false;
			if (isset($dbCreated['min'])) {
				$this->addUsingAlias(CcShowInstancesPeer::CREATED, $dbCreated['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbCreated['max'])) {
				$this->addUsingAlias(CcShowInstancesPeer::CREATED, $dbCreated['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcShowInstancesPeer::CREATED, $dbCreated, $comparison);
	}

	/**
	 * Filter the query on the last_scheduled column
	 * 
	 * @param     string|array $dbLastScheduled The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcShowInstancesQuery The current query, for fluid interface
	 */
	public function filterByDbLastScheduled($dbLastScheduled = null, $comparison = null)
	{
		if (is_array($dbLastScheduled)) {
			$useMinMax = false;
			if (isset($dbLastScheduled['min'])) {
				$this->addUsingAlias(CcShowInstancesPeer::LAST_SCHEDULED, $dbLastScheduled['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbLastScheduled['max'])) {
				$this->addUsingAlias(CcShowInstancesPeer::LAST_SCHEDULED, $dbLastScheduled['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcShowInstancesPeer::LAST_SCHEDULED, $dbLastScheduled, $comparison);
	}

	/**
	 * Filter the query on the modified_instance column
	 * 
	 * @param     boolean|string $dbModifiedInstance The value to use as filter.
	 *            Accepts strings ('false', 'off', '-', 'no', 'n', and '0' are false, the rest is true)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcShowInstancesQuery The current query, for fluid interface
	 */
	public function filterByDbModifiedInstance($dbModifiedInstance = null, $comparison = null)
	{
		if (is_string($dbModifiedInstance)) {
			$modified_instance = in_array(strtolower($dbModifiedInstance), array('false', 'off', '-', 'no', 'n', '0')) ? false : true;
		}
		return $this->addUsingAlias(CcShowInstancesPeer::MODIFIED_INSTANCE, $dbModifiedInstance, $comparison);
	}

	/**
	 * Filter the query by a related CcShow object
	 *
	 * @param     CcShow $ccShow  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcShowInstancesQuery The current query, for fluid interface
	 */
	public function filterByCcShow($ccShow, $comparison = null)
	{
		return $this
			->addUsingAlias(CcShowInstancesPeer::SHOW_ID, $ccShow->getDbId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcShow relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcShowInstancesQuery The current query, for fluid interface
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
	 * @return    CcShowInstancesQuery The current query, for fluid interface
	 */
	public function filterByCcShowInstancesRelatedByDbOriginalShow($ccShowInstances, $comparison = null)
	{
		return $this
			->addUsingAlias(CcShowInstancesPeer::INSTANCE_ID, $ccShowInstances->getDbId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcShowInstancesRelatedByDbOriginalShow relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcShowInstancesQuery The current query, for fluid interface
	 */
	public function joinCcShowInstancesRelatedByDbOriginalShow($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('CcShowInstancesRelatedByDbOriginalShow');
		
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
			$this->addJoinObject($join, 'CcShowInstancesRelatedByDbOriginalShow');
		}
		
		return $this;
	}

	/**
	 * Use the CcShowInstancesRelatedByDbOriginalShow relation CcShowInstances object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcShowInstancesQuery A secondary query class using the current class as primary query
	 */
	public function useCcShowInstancesRelatedByDbOriginalShowQuery($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinCcShowInstancesRelatedByDbOriginalShow($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CcShowInstancesRelatedByDbOriginalShow', 'CcShowInstancesQuery');
	}

	/**
	 * Filter the query by a related CcFiles object
	 *
	 * @param     CcFiles $ccFiles  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcShowInstancesQuery The current query, for fluid interface
	 */
	public function filterByCcFiles($ccFiles, $comparison = null)
	{
		return $this
			->addUsingAlias(CcShowInstancesPeer::FILE_ID, $ccFiles->getDbId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcFiles relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcShowInstancesQuery The current query, for fluid interface
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
	 * Filter the query by a related CcShowInstances object
	 *
	 * @param     CcShowInstances $ccShowInstances  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcShowInstancesQuery The current query, for fluid interface
	 */
	public function filterByCcShowInstancesRelatedByDbId($ccShowInstances, $comparison = null)
	{
		return $this
			->addUsingAlias(CcShowInstancesPeer::ID, $ccShowInstances->getDbOriginalShow(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcShowInstancesRelatedByDbId relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcShowInstancesQuery The current query, for fluid interface
	 */
	public function joinCcShowInstancesRelatedByDbId($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('CcShowInstancesRelatedByDbId');
		
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
			$this->addJoinObject($join, 'CcShowInstancesRelatedByDbId');
		}
		
		return $this;
	}

	/**
	 * Use the CcShowInstancesRelatedByDbId relation CcShowInstances object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcShowInstancesQuery A secondary query class using the current class as primary query
	 */
	public function useCcShowInstancesRelatedByDbIdQuery($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinCcShowInstancesRelatedByDbId($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CcShowInstancesRelatedByDbId', 'CcShowInstancesQuery');
	}

	/**
	 * Filter the query by a related CcSchedule object
	 *
	 * @param     CcSchedule $ccSchedule  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcShowInstancesQuery The current query, for fluid interface
	 */
	public function filterByCcSchedule($ccSchedule, $comparison = null)
	{
		return $this
			->addUsingAlias(CcShowInstancesPeer::ID, $ccSchedule->getDbInstanceId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcSchedule relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcShowInstancesQuery The current query, for fluid interface
	 */
	public function joinCcSchedule($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('CcSchedule');
		
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
			$this->addJoinObject($join, 'CcSchedule');
		}
		
		return $this;
	}

	/**
	 * Use the CcSchedule relation CcSchedule object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcScheduleQuery A secondary query class using the current class as primary query
	 */
	public function useCcScheduleQuery($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinCcSchedule($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CcSchedule', 'CcScheduleQuery');
	}

	/**
	 * Filter the query by a related CcPlayoutHistory object
	 *
	 * @param     CcPlayoutHistory $ccPlayoutHistory  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcShowInstancesQuery The current query, for fluid interface
	 */
	public function filterByCcPlayoutHistory($ccPlayoutHistory, $comparison = null)
	{
		return $this
			->addUsingAlias(CcShowInstancesPeer::ID, $ccPlayoutHistory->getDbInstanceId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcPlayoutHistory relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcShowInstancesQuery The current query, for fluid interface
	 */
	public function joinCcPlayoutHistory($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('CcPlayoutHistory');
		
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
			$this->addJoinObject($join, 'CcPlayoutHistory');
		}
		
		return $this;
	}

	/**
	 * Use the CcPlayoutHistory relation CcPlayoutHistory object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcPlayoutHistoryQuery A secondary query class using the current class as primary query
	 */
	public function useCcPlayoutHistoryQuery($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinCcPlayoutHistory($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CcPlayoutHistory', 'CcPlayoutHistoryQuery');
	}

	/**
	 * Exclude object from result
	 *
	 * @param     CcShowInstances $ccShowInstances Object to remove from the list of results
	 *
	 * @return    CcShowInstancesQuery The current query, for fluid interface
	 */
	public function prune($ccShowInstances = null)
	{
		if ($ccShowInstances) {
			$this->addUsingAlias(CcShowInstancesPeer::ID, $ccShowInstances->getDbId(), Criteria::NOT_EQUAL);
	  }
	  
		return $this;
	}

} // BaseCcShowInstancesQuery
