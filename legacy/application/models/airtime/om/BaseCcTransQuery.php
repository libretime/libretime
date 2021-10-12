<?php


/**
 * Base class that represents a query for the 'cc_trans' table.
 *
 *
 *
 * @method     CcTransQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     CcTransQuery orderByTrtok($order = Criteria::ASC) Order by the trtok column
 * @method     CcTransQuery orderByDirection($order = Criteria::ASC) Order by the direction column
 * @method     CcTransQuery orderByState($order = Criteria::ASC) Order by the state column
 * @method     CcTransQuery orderByTrtype($order = Criteria::ASC) Order by the trtype column
 * @method     CcTransQuery orderByLock($order = Criteria::ASC) Order by the lock column
 * @method     CcTransQuery orderByTarget($order = Criteria::ASC) Order by the target column
 * @method     CcTransQuery orderByRtrtok($order = Criteria::ASC) Order by the rtrtok column
 * @method     CcTransQuery orderByMdtrtok($order = Criteria::ASC) Order by the mdtrtok column
 * @method     CcTransQuery orderByGunid($order = Criteria::ASC) Order by the gunid column
 * @method     CcTransQuery orderByPdtoken($order = Criteria::ASC) Order by the pdtoken column
 * @method     CcTransQuery orderByUrl($order = Criteria::ASC) Order by the url column
 * @method     CcTransQuery orderByLocalfile($order = Criteria::ASC) Order by the localfile column
 * @method     CcTransQuery orderByFname($order = Criteria::ASC) Order by the fname column
 * @method     CcTransQuery orderByTitle($order = Criteria::ASC) Order by the title column
 * @method     CcTransQuery orderByExpectedsum($order = Criteria::ASC) Order by the expectedsum column
 * @method     CcTransQuery orderByRealsum($order = Criteria::ASC) Order by the realsum column
 * @method     CcTransQuery orderByExpectedsize($order = Criteria::ASC) Order by the expectedsize column
 * @method     CcTransQuery orderByRealsize($order = Criteria::ASC) Order by the realsize column
 * @method     CcTransQuery orderByUid($order = Criteria::ASC) Order by the uid column
 * @method     CcTransQuery orderByErrmsg($order = Criteria::ASC) Order by the errmsg column
 * @method     CcTransQuery orderByJobpid($order = Criteria::ASC) Order by the jobpid column
 * @method     CcTransQuery orderByStart($order = Criteria::ASC) Order by the start column
 * @method     CcTransQuery orderByTs($order = Criteria::ASC) Order by the ts column
 *
 * @method     CcTransQuery groupById() Group by the id column
 * @method     CcTransQuery groupByTrtok() Group by the trtok column
 * @method     CcTransQuery groupByDirection() Group by the direction column
 * @method     CcTransQuery groupByState() Group by the state column
 * @method     CcTransQuery groupByTrtype() Group by the trtype column
 * @method     CcTransQuery groupByLock() Group by the lock column
 * @method     CcTransQuery groupByTarget() Group by the target column
 * @method     CcTransQuery groupByRtrtok() Group by the rtrtok column
 * @method     CcTransQuery groupByMdtrtok() Group by the mdtrtok column
 * @method     CcTransQuery groupByGunid() Group by the gunid column
 * @method     CcTransQuery groupByPdtoken() Group by the pdtoken column
 * @method     CcTransQuery groupByUrl() Group by the url column
 * @method     CcTransQuery groupByLocalfile() Group by the localfile column
 * @method     CcTransQuery groupByFname() Group by the fname column
 * @method     CcTransQuery groupByTitle() Group by the title column
 * @method     CcTransQuery groupByExpectedsum() Group by the expectedsum column
 * @method     CcTransQuery groupByRealsum() Group by the realsum column
 * @method     CcTransQuery groupByExpectedsize() Group by the expectedsize column
 * @method     CcTransQuery groupByRealsize() Group by the realsize column
 * @method     CcTransQuery groupByUid() Group by the uid column
 * @method     CcTransQuery groupByErrmsg() Group by the errmsg column
 * @method     CcTransQuery groupByJobpid() Group by the jobpid column
 * @method     CcTransQuery groupByStart() Group by the start column
 * @method     CcTransQuery groupByTs() Group by the ts column
 *
 * @method     CcTransQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     CcTransQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     CcTransQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     CcTrans findOne(PropelPDO $con = null) Return the first CcTrans matching the query
 * @method     CcTrans findOneOrCreate(PropelPDO $con = null) Return the first CcTrans matching the query, or a new CcTrans object populated from the query conditions when no match is found
 *
 * @method     CcTrans findOneById(int $id) Return the first CcTrans filtered by the id column
 * @method     CcTrans findOneByTrtok(string $trtok) Return the first CcTrans filtered by the trtok column
 * @method     CcTrans findOneByDirection(string $direction) Return the first CcTrans filtered by the direction column
 * @method     CcTrans findOneByState(string $state) Return the first CcTrans filtered by the state column
 * @method     CcTrans findOneByTrtype(string $trtype) Return the first CcTrans filtered by the trtype column
 * @method     CcTrans findOneByLock(string $lock) Return the first CcTrans filtered by the lock column
 * @method     CcTrans findOneByTarget(string $target) Return the first CcTrans filtered by the target column
 * @method     CcTrans findOneByRtrtok(string $rtrtok) Return the first CcTrans filtered by the rtrtok column
 * @method     CcTrans findOneByMdtrtok(string $mdtrtok) Return the first CcTrans filtered by the mdtrtok column
 * @method     CcTrans findOneByGunid(string $gunid) Return the first CcTrans filtered by the gunid column
 * @method     CcTrans findOneByPdtoken(string $pdtoken) Return the first CcTrans filtered by the pdtoken column
 * @method     CcTrans findOneByUrl(string $url) Return the first CcTrans filtered by the url column
 * @method     CcTrans findOneByLocalfile(string $localfile) Return the first CcTrans filtered by the localfile column
 * @method     CcTrans findOneByFname(string $fname) Return the first CcTrans filtered by the fname column
 * @method     CcTrans findOneByTitle(string $title) Return the first CcTrans filtered by the title column
 * @method     CcTrans findOneByExpectedsum(string $expectedsum) Return the first CcTrans filtered by the expectedsum column
 * @method     CcTrans findOneByRealsum(string $realsum) Return the first CcTrans filtered by the realsum column
 * @method     CcTrans findOneByExpectedsize(int $expectedsize) Return the first CcTrans filtered by the expectedsize column
 * @method     CcTrans findOneByRealsize(int $realsize) Return the first CcTrans filtered by the realsize column
 * @method     CcTrans findOneByUid(int $uid) Return the first CcTrans filtered by the uid column
 * @method     CcTrans findOneByErrmsg(string $errmsg) Return the first CcTrans filtered by the errmsg column
 * @method     CcTrans findOneByJobpid(int $jobpid) Return the first CcTrans filtered by the jobpid column
 * @method     CcTrans findOneByStart(string $start) Return the first CcTrans filtered by the start column
 * @method     CcTrans findOneByTs(string $ts) Return the first CcTrans filtered by the ts column
 *
 * @method     array findById(int $id) Return CcTrans objects filtered by the id column
 * @method     array findByTrtok(string $trtok) Return CcTrans objects filtered by the trtok column
 * @method     array findByDirection(string $direction) Return CcTrans objects filtered by the direction column
 * @method     array findByState(string $state) Return CcTrans objects filtered by the state column
 * @method     array findByTrtype(string $trtype) Return CcTrans objects filtered by the trtype column
 * @method     array findByLock(string $lock) Return CcTrans objects filtered by the lock column
 * @method     array findByTarget(string $target) Return CcTrans objects filtered by the target column
 * @method     array findByRtrtok(string $rtrtok) Return CcTrans objects filtered by the rtrtok column
 * @method     array findByMdtrtok(string $mdtrtok) Return CcTrans objects filtered by the mdtrtok column
 * @method     array findByGunid(string $gunid) Return CcTrans objects filtered by the gunid column
 * @method     array findByPdtoken(string $pdtoken) Return CcTrans objects filtered by the pdtoken column
 * @method     array findByUrl(string $url) Return CcTrans objects filtered by the url column
 * @method     array findByLocalfile(string $localfile) Return CcTrans objects filtered by the localfile column
 * @method     array findByFname(string $fname) Return CcTrans objects filtered by the fname column
 * @method     array findByTitle(string $title) Return CcTrans objects filtered by the title column
 * @method     array findByExpectedsum(string $expectedsum) Return CcTrans objects filtered by the expectedsum column
 * @method     array findByRealsum(string $realsum) Return CcTrans objects filtered by the realsum column
 * @method     array findByExpectedsize(int $expectedsize) Return CcTrans objects filtered by the expectedsize column
 * @method     array findByRealsize(int $realsize) Return CcTrans objects filtered by the realsize column
 * @method     array findByUid(int $uid) Return CcTrans objects filtered by the uid column
 * @method     array findByErrmsg(string $errmsg) Return CcTrans objects filtered by the errmsg column
 * @method     array findByJobpid(int $jobpid) Return CcTrans objects filtered by the jobpid column
 * @method     array findByStart(string $start) Return CcTrans objects filtered by the start column
 * @method     array findByTs(string $ts) Return CcTrans objects filtered by the ts column
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcTransQuery extends ModelCriteria
{

	/**
	 * Initializes internal state of BaseCcTransQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'airtime', $modelName = 'CcTrans', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new CcTransQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    CcTransQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof CcTransQuery) {
			return $criteria;
		}
		$query = new CcTransQuery();
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
	 * @return    CcTrans|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = CcTransPeer::getInstanceFromPool((string) $key))) && $this->getFormatter()->isObjectFormatter()) {
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
	 * @return    CcTransQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(CcTransPeer::ID, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    CcTransQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(CcTransPeer::ID, $keys, Criteria::IN);
	}

	/**
	 * Filter the query on the id column
	 *
	 * @param     int|array $id The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcTransQuery The current query, for fluid interface
	 */
	public function filterById($id = null, $comparison = null)
	{
		if (is_array($id) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(CcTransPeer::ID, $id, $comparison);
	}

	/**
	 * Filter the query on the trtok column
	 *
	 * @param     string $trtok The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcTransQuery The current query, for fluid interface
	 */
	public function filterByTrtok($trtok = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($trtok)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $trtok)) {
				$trtok = str_replace('*', '%', $trtok);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcTransPeer::TRTOK, $trtok, $comparison);
	}

	/**
	 * Filter the query on the direction column
	 *
	 * @param     string $direction The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcTransQuery The current query, for fluid interface
	 */
	public function filterByDirection($direction = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($direction)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $direction)) {
				$direction = str_replace('*', '%', $direction);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcTransPeer::DIRECTION, $direction, $comparison);
	}

	/**
	 * Filter the query on the state column
	 *
	 * @param     string $state The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcTransQuery The current query, for fluid interface
	 */
	public function filterByState($state = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($state)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $state)) {
				$state = str_replace('*', '%', $state);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcTransPeer::STATE, $state, $comparison);
	}

	/**
	 * Filter the query on the trtype column
	 *
	 * @param     string $trtype The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcTransQuery The current query, for fluid interface
	 */
	public function filterByTrtype($trtype = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($trtype)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $trtype)) {
				$trtype = str_replace('*', '%', $trtype);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcTransPeer::TRTYPE, $trtype, $comparison);
	}

	/**
	 * Filter the query on the lock column
	 *
	 * @param     string $lock The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcTransQuery The current query, for fluid interface
	 */
	public function filterByLock($lock = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($lock)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $lock)) {
				$lock = str_replace('*', '%', $lock);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcTransPeer::LOCK, $lock, $comparison);
	}

	/**
	 * Filter the query on the target column
	 *
	 * @param     string $target The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcTransQuery The current query, for fluid interface
	 */
	public function filterByTarget($target = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($target)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $target)) {
				$target = str_replace('*', '%', $target);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcTransPeer::TARGET, $target, $comparison);
	}

	/**
	 * Filter the query on the rtrtok column
	 *
	 * @param     string $rtrtok The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcTransQuery The current query, for fluid interface
	 */
	public function filterByRtrtok($rtrtok = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($rtrtok)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $rtrtok)) {
				$rtrtok = str_replace('*', '%', $rtrtok);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcTransPeer::RTRTOK, $rtrtok, $comparison);
	}

	/**
	 * Filter the query on the mdtrtok column
	 *
	 * @param     string $mdtrtok The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcTransQuery The current query, for fluid interface
	 */
	public function filterByMdtrtok($mdtrtok = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($mdtrtok)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $mdtrtok)) {
				$mdtrtok = str_replace('*', '%', $mdtrtok);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcTransPeer::MDTRTOK, $mdtrtok, $comparison);
	}

	/**
	 * Filter the query on the gunid column
	 *
	 * @param     string $gunid The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcTransQuery The current query, for fluid interface
	 */
	public function filterByGunid($gunid = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($gunid)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $gunid)) {
				$gunid = str_replace('*', '%', $gunid);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcTransPeer::GUNID, $gunid, $comparison);
	}

	/**
	 * Filter the query on the pdtoken column
	 *
	 * @param     string|array $pdtoken The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcTransQuery The current query, for fluid interface
	 */
	public function filterByPdtoken($pdtoken = null, $comparison = null)
	{
		if (is_array($pdtoken)) {
			$useMinMax = false;
			if (isset($pdtoken['min'])) {
				$this->addUsingAlias(CcTransPeer::PDTOKEN, $pdtoken['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($pdtoken['max'])) {
				$this->addUsingAlias(CcTransPeer::PDTOKEN, $pdtoken['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcTransPeer::PDTOKEN, $pdtoken, $comparison);
	}

	/**
	 * Filter the query on the url column
	 *
	 * @param     string $url The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcTransQuery The current query, for fluid interface
	 */
	public function filterByUrl($url = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($url)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $url)) {
				$url = str_replace('*', '%', $url);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcTransPeer::URL, $url, $comparison);
	}

	/**
	 * Filter the query on the localfile column
	 *
	 * @param     string $localfile The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcTransQuery The current query, for fluid interface
	 */
	public function filterByLocalfile($localfile = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($localfile)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $localfile)) {
				$localfile = str_replace('*', '%', $localfile);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcTransPeer::LOCALFILE, $localfile, $comparison);
	}

	/**
	 * Filter the query on the fname column
	 *
	 * @param     string $fname The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcTransQuery The current query, for fluid interface
	 */
	public function filterByFname($fname = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($fname)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $fname)) {
				$fname = str_replace('*', '%', $fname);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcTransPeer::FNAME, $fname, $comparison);
	}

	/**
	 * Filter the query on the title column
	 *
	 * @param     string $title The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcTransQuery The current query, for fluid interface
	 */
	public function filterByTitle($title = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($title)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $title)) {
				$title = str_replace('*', '%', $title);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcTransPeer::TITLE, $title, $comparison);
	}

	/**
	 * Filter the query on the expectedsum column
	 *
	 * @param     string $expectedsum The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcTransQuery The current query, for fluid interface
	 */
	public function filterByExpectedsum($expectedsum = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($expectedsum)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $expectedsum)) {
				$expectedsum = str_replace('*', '%', $expectedsum);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcTransPeer::EXPECTEDSUM, $expectedsum, $comparison);
	}

	/**
	 * Filter the query on the realsum column
	 *
	 * @param     string $realsum The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcTransQuery The current query, for fluid interface
	 */
	public function filterByRealsum($realsum = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($realsum)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $realsum)) {
				$realsum = str_replace('*', '%', $realsum);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcTransPeer::REALSUM, $realsum, $comparison);
	}

	/**
	 * Filter the query on the expectedsize column
	 *
	 * @param     int|array $expectedsize The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcTransQuery The current query, for fluid interface
	 */
	public function filterByExpectedsize($expectedsize = null, $comparison = null)
	{
		if (is_array($expectedsize)) {
			$useMinMax = false;
			if (isset($expectedsize['min'])) {
				$this->addUsingAlias(CcTransPeer::EXPECTEDSIZE, $expectedsize['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($expectedsize['max'])) {
				$this->addUsingAlias(CcTransPeer::EXPECTEDSIZE, $expectedsize['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcTransPeer::EXPECTEDSIZE, $expectedsize, $comparison);
	}

	/**
	 * Filter the query on the realsize column
	 *
	 * @param     int|array $realsize The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcTransQuery The current query, for fluid interface
	 */
	public function filterByRealsize($realsize = null, $comparison = null)
	{
		if (is_array($realsize)) {
			$useMinMax = false;
			if (isset($realsize['min'])) {
				$this->addUsingAlias(CcTransPeer::REALSIZE, $realsize['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($realsize['max'])) {
				$this->addUsingAlias(CcTransPeer::REALSIZE, $realsize['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcTransPeer::REALSIZE, $realsize, $comparison);
	}

	/**
	 * Filter the query on the uid column
	 *
	 * @param     int|array $uid The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcTransQuery The current query, for fluid interface
	 */
	public function filterByUid($uid = null, $comparison = null)
	{
		if (is_array($uid)) {
			$useMinMax = false;
			if (isset($uid['min'])) {
				$this->addUsingAlias(CcTransPeer::UID, $uid['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($uid['max'])) {
				$this->addUsingAlias(CcTransPeer::UID, $uid['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcTransPeer::UID, $uid, $comparison);
	}

	/**
	 * Filter the query on the errmsg column
	 *
	 * @param     string $errmsg The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcTransQuery The current query, for fluid interface
	 */
	public function filterByErrmsg($errmsg = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($errmsg)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $errmsg)) {
				$errmsg = str_replace('*', '%', $errmsg);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcTransPeer::ERRMSG, $errmsg, $comparison);
	}

	/**
	 * Filter the query on the jobpid column
	 *
	 * @param     int|array $jobpid The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcTransQuery The current query, for fluid interface
	 */
	public function filterByJobpid($jobpid = null, $comparison = null)
	{
		if (is_array($jobpid)) {
			$useMinMax = false;
			if (isset($jobpid['min'])) {
				$this->addUsingAlias(CcTransPeer::JOBPID, $jobpid['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($jobpid['max'])) {
				$this->addUsingAlias(CcTransPeer::JOBPID, $jobpid['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcTransPeer::JOBPID, $jobpid, $comparison);
	}

	/**
	 * Filter the query on the start column
	 *
	 * @param     string|array $start The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcTransQuery The current query, for fluid interface
	 */
	public function filterByStart($start = null, $comparison = null)
	{
		if (is_array($start)) {
			$useMinMax = false;
			if (isset($start['min'])) {
				$this->addUsingAlias(CcTransPeer::START, $start['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($start['max'])) {
				$this->addUsingAlias(CcTransPeer::START, $start['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcTransPeer::START, $start, $comparison);
	}

	/**
	 * Filter the query on the ts column
	 *
	 * @param     string|array $ts The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcTransQuery The current query, for fluid interface
	 */
	public function filterByTs($ts = null, $comparison = null)
	{
		if (is_array($ts)) {
			$useMinMax = false;
			if (isset($ts['min'])) {
				$this->addUsingAlias(CcTransPeer::TS, $ts['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($ts['max'])) {
				$this->addUsingAlias(CcTransPeer::TS, $ts['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcTransPeer::TS, $ts, $comparison);
	}

	/**
	 * Exclude object from result
	 *
	 * @param     CcTrans $ccTrans Object to remove from the list of results
	 *
	 * @return    CcTransQuery The current query, for fluid interface
	 */
	public function prune($ccTrans = null)
	{
		if ($ccTrans) {
			$this->addUsingAlias(CcTransPeer::ID, $ccTrans->getId(), Criteria::NOT_EQUAL);
	  }

		return $this;
	}

} // BaseCcTransQuery
