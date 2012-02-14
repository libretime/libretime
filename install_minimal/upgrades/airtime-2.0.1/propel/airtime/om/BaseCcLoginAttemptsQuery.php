<?php


/**
 * Base class that represents a query for the 'cc_login_attempts' table.
 *
 * 
 *
 * @method     CcLoginAttemptsQuery orderByDbIP($order = Criteria::ASC) Order by the ip column
 * @method     CcLoginAttemptsQuery orderByDbAttempts($order = Criteria::ASC) Order by the attempts column
 *
 * @method     CcLoginAttemptsQuery groupByDbIP() Group by the ip column
 * @method     CcLoginAttemptsQuery groupByDbAttempts() Group by the attempts column
 *
 * @method     CcLoginAttemptsQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     CcLoginAttemptsQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     CcLoginAttemptsQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     CcLoginAttempts findOne(PropelPDO $con = null) Return the first CcLoginAttempts matching the query
 * @method     CcLoginAttempts findOneOrCreate(PropelPDO $con = null) Return the first CcLoginAttempts matching the query, or a new CcLoginAttempts object populated from the query conditions when no match is found
 *
 * @method     CcLoginAttempts findOneByDbIP(string $ip) Return the first CcLoginAttempts filtered by the ip column
 * @method     CcLoginAttempts findOneByDbAttempts(int $attempts) Return the first CcLoginAttempts filtered by the attempts column
 *
 * @method     array findByDbIP(string $ip) Return CcLoginAttempts objects filtered by the ip column
 * @method     array findByDbAttempts(int $attempts) Return CcLoginAttempts objects filtered by the attempts column
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcLoginAttemptsQuery extends ModelCriteria
{

	/**
	 * Initializes internal state of BaseCcLoginAttemptsQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'airtime', $modelName = 'CcLoginAttempts', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new CcLoginAttemptsQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    CcLoginAttemptsQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof CcLoginAttemptsQuery) {
			return $criteria;
		}
		$query = new CcLoginAttemptsQuery();
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
	 * @return    CcLoginAttempts|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = CcLoginAttemptsPeer::getInstanceFromPool((string) $key))) && $this->getFormatter()->isObjectFormatter()) {
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
	 * @return    CcLoginAttemptsQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(CcLoginAttemptsPeer::IP, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    CcLoginAttemptsQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(CcLoginAttemptsPeer::IP, $keys, Criteria::IN);
	}

	/**
	 * Filter the query on the ip column
	 * 
	 * @param     string $dbIP The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcLoginAttemptsQuery The current query, for fluid interface
	 */
	public function filterByDbIP($dbIP = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbIP)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbIP)) {
				$dbIP = str_replace('*', '%', $dbIP);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcLoginAttemptsPeer::IP, $dbIP, $comparison);
	}

	/**
	 * Filter the query on the attempts column
	 * 
	 * @param     int|array $dbAttempts The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcLoginAttemptsQuery The current query, for fluid interface
	 */
	public function filterByDbAttempts($dbAttempts = null, $comparison = null)
	{
		if (is_array($dbAttempts)) {
			$useMinMax = false;
			if (isset($dbAttempts['min'])) {
				$this->addUsingAlias(CcLoginAttemptsPeer::ATTEMPTS, $dbAttempts['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbAttempts['max'])) {
				$this->addUsingAlias(CcLoginAttemptsPeer::ATTEMPTS, $dbAttempts['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcLoginAttemptsPeer::ATTEMPTS, $dbAttempts, $comparison);
	}

	/**
	 * Exclude object from result
	 *
	 * @param     CcLoginAttempts $ccLoginAttempts Object to remove from the list of results
	 *
	 * @return    CcLoginAttemptsQuery The current query, for fluid interface
	 */
	public function prune($ccLoginAttempts = null)
	{
		if ($ccLoginAttempts) {
			$this->addUsingAlias(CcLoginAttemptsPeer::IP, $ccLoginAttempts->getDbIP(), Criteria::NOT_EQUAL);
	  }
	  
		return $this;
	}

} // BaseCcLoginAttemptsQuery
