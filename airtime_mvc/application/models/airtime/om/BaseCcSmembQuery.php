<?php


/**
 * Base class that represents a query for the 'cc_smemb' table.
 *
 * 
 *
 * @method     CcSmembQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     CcSmembQuery orderByUid($order = Criteria::ASC) Order by the uid column
 * @method     CcSmembQuery orderByGid($order = Criteria::ASC) Order by the gid column
 * @method     CcSmembQuery orderByLevel($order = Criteria::ASC) Order by the level column
 * @method     CcSmembQuery orderByMid($order = Criteria::ASC) Order by the mid column
 *
 * @method     CcSmembQuery groupById() Group by the id column
 * @method     CcSmembQuery groupByUid() Group by the uid column
 * @method     CcSmembQuery groupByGid() Group by the gid column
 * @method     CcSmembQuery groupByLevel() Group by the level column
 * @method     CcSmembQuery groupByMid() Group by the mid column
 *
 * @method     CcSmembQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     CcSmembQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     CcSmembQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     CcSmemb findOne(PropelPDO $con = null) Return the first CcSmemb matching the query
 * @method     CcSmemb findOneOrCreate(PropelPDO $con = null) Return the first CcSmemb matching the query, or a new CcSmemb object populated from the query conditions when no match is found
 *
 * @method     CcSmemb findOneById(int $id) Return the first CcSmemb filtered by the id column
 * @method     CcSmemb findOneByUid(int $uid) Return the first CcSmemb filtered by the uid column
 * @method     CcSmemb findOneByGid(int $gid) Return the first CcSmemb filtered by the gid column
 * @method     CcSmemb findOneByLevel(int $level) Return the first CcSmemb filtered by the level column
 * @method     CcSmemb findOneByMid(int $mid) Return the first CcSmemb filtered by the mid column
 *
 * @method     array findById(int $id) Return CcSmemb objects filtered by the id column
 * @method     array findByUid(int $uid) Return CcSmemb objects filtered by the uid column
 * @method     array findByGid(int $gid) Return CcSmemb objects filtered by the gid column
 * @method     array findByLevel(int $level) Return CcSmemb objects filtered by the level column
 * @method     array findByMid(int $mid) Return CcSmemb objects filtered by the mid column
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcSmembQuery extends ModelCriteria
{

	/**
	 * Initializes internal state of BaseCcSmembQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'airtime', $modelName = 'CcSmemb', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new CcSmembQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    CcSmembQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof CcSmembQuery) {
			return $criteria;
		}
		$query = new CcSmembQuery();
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
	 * @return    CcSmemb|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = CcSmembPeer::getInstanceFromPool((string) $key))) && $this->getFormatter()->isObjectFormatter()) {
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
	 * @return    CcSmembQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(CcSmembPeer::ID, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    CcSmembQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(CcSmembPeer::ID, $keys, Criteria::IN);
	}

	/**
	 * Filter the query on the id column
	 * 
	 * @param     int|array $id The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcSmembQuery The current query, for fluid interface
	 */
	public function filterById($id = null, $comparison = null)
	{
		if (is_array($id) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(CcSmembPeer::ID, $id, $comparison);
	}

	/**
	 * Filter the query on the uid column
	 * 
	 * @param     int|array $uid The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcSmembQuery The current query, for fluid interface
	 */
	public function filterByUid($uid = null, $comparison = null)
	{
		if (is_array($uid)) {
			$useMinMax = false;
			if (isset($uid['min'])) {
				$this->addUsingAlias(CcSmembPeer::UID, $uid['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($uid['max'])) {
				$this->addUsingAlias(CcSmembPeer::UID, $uid['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcSmembPeer::UID, $uid, $comparison);
	}

	/**
	 * Filter the query on the gid column
	 * 
	 * @param     int|array $gid The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcSmembQuery The current query, for fluid interface
	 */
	public function filterByGid($gid = null, $comparison = null)
	{
		if (is_array($gid)) {
			$useMinMax = false;
			if (isset($gid['min'])) {
				$this->addUsingAlias(CcSmembPeer::GID, $gid['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($gid['max'])) {
				$this->addUsingAlias(CcSmembPeer::GID, $gid['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcSmembPeer::GID, $gid, $comparison);
	}

	/**
	 * Filter the query on the level column
	 * 
	 * @param     int|array $level The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcSmembQuery The current query, for fluid interface
	 */
	public function filterByLevel($level = null, $comparison = null)
	{
		if (is_array($level)) {
			$useMinMax = false;
			if (isset($level['min'])) {
				$this->addUsingAlias(CcSmembPeer::LEVEL, $level['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($level['max'])) {
				$this->addUsingAlias(CcSmembPeer::LEVEL, $level['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcSmembPeer::LEVEL, $level, $comparison);
	}

	/**
	 * Filter the query on the mid column
	 * 
	 * @param     int|array $mid The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcSmembQuery The current query, for fluid interface
	 */
	public function filterByMid($mid = null, $comparison = null)
	{
		if (is_array($mid)) {
			$useMinMax = false;
			if (isset($mid['min'])) {
				$this->addUsingAlias(CcSmembPeer::MID, $mid['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($mid['max'])) {
				$this->addUsingAlias(CcSmembPeer::MID, $mid['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcSmembPeer::MID, $mid, $comparison);
	}

	/**
	 * Exclude object from result
	 *
	 * @param     CcSmemb $ccSmemb Object to remove from the list of results
	 *
	 * @return    CcSmembQuery The current query, for fluid interface
	 */
	public function prune($ccSmemb = null)
	{
		if ($ccSmemb) {
			$this->addUsingAlias(CcSmembPeer::ID, $ccSmemb->getId(), Criteria::NOT_EQUAL);
	  }
	  
		return $this;
	}

} // BaseCcSmembQuery
