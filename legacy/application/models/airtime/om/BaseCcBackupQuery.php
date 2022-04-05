<?php


/**
 * Base class that represents a query for the 'cc_backup' table.
 *
 *
 *
 * @method     CcBackupQuery orderByToken($order = Criteria::ASC) Order by the token column
 * @method     CcBackupQuery orderBySessionid($order = Criteria::ASC) Order by the sessionid column
 * @method     CcBackupQuery orderByStatus($order = Criteria::ASC) Order by the status column
 * @method     CcBackupQuery orderByFromtime($order = Criteria::ASC) Order by the fromtime column
 * @method     CcBackupQuery orderByTotime($order = Criteria::ASC) Order by the totime column
 *
 * @method     CcBackupQuery groupByToken() Group by the token column
 * @method     CcBackupQuery groupBySessionid() Group by the sessionid column
 * @method     CcBackupQuery groupByStatus() Group by the status column
 * @method     CcBackupQuery groupByFromtime() Group by the fromtime column
 * @method     CcBackupQuery groupByTotime() Group by the totime column
 *
 * @method     CcBackupQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     CcBackupQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     CcBackupQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     CcBackup findOne(PropelPDO $con = null) Return the first CcBackup matching the query
 * @method     CcBackup findOneOrCreate(PropelPDO $con = null) Return the first CcBackup matching the query, or a new CcBackup object populated from the query conditions when no match is found
 *
 * @method     CcBackup findOneByToken(string $token) Return the first CcBackup filtered by the token column
 * @method     CcBackup findOneBySessionid(string $sessionid) Return the first CcBackup filtered by the sessionid column
 * @method     CcBackup findOneByStatus(string $status) Return the first CcBackup filtered by the status column
 * @method     CcBackup findOneByFromtime(string $fromtime) Return the first CcBackup filtered by the fromtime column
 * @method     CcBackup findOneByTotime(string $totime) Return the first CcBackup filtered by the totime column
 *
 * @method     array findByToken(string $token) Return CcBackup objects filtered by the token column
 * @method     array findBySessionid(string $sessionid) Return CcBackup objects filtered by the sessionid column
 * @method     array findByStatus(string $status) Return CcBackup objects filtered by the status column
 * @method     array findByFromtime(string $fromtime) Return CcBackup objects filtered by the fromtime column
 * @method     array findByTotime(string $totime) Return CcBackup objects filtered by the totime column
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcBackupQuery extends ModelCriteria
{

	/**
	 * Initializes internal state of BaseCcBackupQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'airtime', $modelName = 'CcBackup', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new CcBackupQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    CcBackupQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof CcBackupQuery) {
			return $criteria;
		}
		$query = new CcBackupQuery();
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
	 * @return    CcBackup|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = CcBackupPeer::getInstanceFromPool((string) $key))) && $this->getFormatter()->isObjectFormatter()) {
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
	 * @return    CcBackupQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(CcBackupPeer::TOKEN, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    CcBackupQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(CcBackupPeer::TOKEN, $keys, Criteria::IN);
	}

	/**
	 * Filter the query on the token column
	 *
	 * @param     string $token The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcBackupQuery The current query, for fluid interface
	 */
	public function filterByToken($token = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($token)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $token)) {
				$token = str_replace('*', '%', $token);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcBackupPeer::TOKEN, $token, $comparison);
	}

	/**
	 * Filter the query on the sessionid column
	 *
	 * @param     string $sessionid The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcBackupQuery The current query, for fluid interface
	 */
	public function filterBySessionid($sessionid = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($sessionid)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $sessionid)) {
				$sessionid = str_replace('*', '%', $sessionid);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcBackupPeer::SESSIONID, $sessionid, $comparison);
	}

	/**
	 * Filter the query on the status column
	 *
	 * @param     string $status The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcBackupQuery The current query, for fluid interface
	 */
	public function filterByStatus($status = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($status)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $status)) {
				$status = str_replace('*', '%', $status);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcBackupPeer::STATUS, $status, $comparison);
	}

	/**
	 * Filter the query on the fromtime column
	 *
	 * @param     string|array $fromtime The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcBackupQuery The current query, for fluid interface
	 */
	public function filterByFromtime($fromtime = null, $comparison = null)
	{
		if (is_array($fromtime)) {
			$useMinMax = false;
			if (isset($fromtime['min'])) {
				$this->addUsingAlias(CcBackupPeer::FROMTIME, $fromtime['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($fromtime['max'])) {
				$this->addUsingAlias(CcBackupPeer::FROMTIME, $fromtime['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcBackupPeer::FROMTIME, $fromtime, $comparison);
	}

	/**
	 * Filter the query on the totime column
	 *
	 * @param     string|array $totime The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcBackupQuery The current query, for fluid interface
	 */
	public function filterByTotime($totime = null, $comparison = null)
	{
		if (is_array($totime)) {
			$useMinMax = false;
			if (isset($totime['min'])) {
				$this->addUsingAlias(CcBackupPeer::TOTIME, $totime['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($totime['max'])) {
				$this->addUsingAlias(CcBackupPeer::TOTIME, $totime['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcBackupPeer::TOTIME, $totime, $comparison);
	}

	/**
	 * Exclude object from result
	 *
	 * @param     CcBackup $ccBackup Object to remove from the list of results
	 *
	 * @return    CcBackupQuery The current query, for fluid interface
	 */
	public function prune($ccBackup = null)
	{
		if ($ccBackup) {
			$this->addUsingAlias(CcBackupPeer::TOKEN, $ccBackup->getToken(), Criteria::NOT_EQUAL);
	  }

		return $this;
	}

} // BaseCcBackupQuery
