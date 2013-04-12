<?php


/**
 * Base class that represents a query for the 'cc_webstream' table.
 *
 * 
 *
 * @method     CcWebstreamQuery orderByDbId($order = Criteria::ASC) Order by the id column
 * @method     CcWebstreamQuery orderByDbName($order = Criteria::ASC) Order by the name column
 * @method     CcWebstreamQuery orderByDbDescription($order = Criteria::ASC) Order by the description column
 * @method     CcWebstreamQuery orderByDbUrl($order = Criteria::ASC) Order by the url column
 * @method     CcWebstreamQuery orderByDbLength($order = Criteria::ASC) Order by the length column
 * @method     CcWebstreamQuery orderByDbCreatorId($order = Criteria::ASC) Order by the creator_id column
 * @method     CcWebstreamQuery orderByDbMtime($order = Criteria::ASC) Order by the mtime column
 * @method     CcWebstreamQuery orderByDbUtime($order = Criteria::ASC) Order by the utime column
 * @method     CcWebstreamQuery orderByDbLPtime($order = Criteria::ASC) Order by the lptime column
 * @method     CcWebstreamQuery orderByDbMime($order = Criteria::ASC) Order by the mime column
 *
 * @method     CcWebstreamQuery groupByDbId() Group by the id column
 * @method     CcWebstreamQuery groupByDbName() Group by the name column
 * @method     CcWebstreamQuery groupByDbDescription() Group by the description column
 * @method     CcWebstreamQuery groupByDbUrl() Group by the url column
 * @method     CcWebstreamQuery groupByDbLength() Group by the length column
 * @method     CcWebstreamQuery groupByDbCreatorId() Group by the creator_id column
 * @method     CcWebstreamQuery groupByDbMtime() Group by the mtime column
 * @method     CcWebstreamQuery groupByDbUtime() Group by the utime column
 * @method     CcWebstreamQuery groupByDbLPtime() Group by the lptime column
 * @method     CcWebstreamQuery groupByDbMime() Group by the mime column
 *
 * @method     CcWebstreamQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     CcWebstreamQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     CcWebstreamQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     CcWebstreamQuery leftJoinCcSchedule($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcSchedule relation
 * @method     CcWebstreamQuery rightJoinCcSchedule($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcSchedule relation
 * @method     CcWebstreamQuery innerJoinCcSchedule($relationAlias = '') Adds a INNER JOIN clause to the query using the CcSchedule relation
 *
 * @method     CcWebstream findOne(PropelPDO $con = null) Return the first CcWebstream matching the query
 * @method     CcWebstream findOneOrCreate(PropelPDO $con = null) Return the first CcWebstream matching the query, or a new CcWebstream object populated from the query conditions when no match is found
 *
 * @method     CcWebstream findOneByDbId(int $id) Return the first CcWebstream filtered by the id column
 * @method     CcWebstream findOneByDbName(string $name) Return the first CcWebstream filtered by the name column
 * @method     CcWebstream findOneByDbDescription(string $description) Return the first CcWebstream filtered by the description column
 * @method     CcWebstream findOneByDbUrl(string $url) Return the first CcWebstream filtered by the url column
 * @method     CcWebstream findOneByDbLength(string $length) Return the first CcWebstream filtered by the length column
 * @method     CcWebstream findOneByDbCreatorId(int $creator_id) Return the first CcWebstream filtered by the creator_id column
 * @method     CcWebstream findOneByDbMtime(string $mtime) Return the first CcWebstream filtered by the mtime column
 * @method     CcWebstream findOneByDbUtime(string $utime) Return the first CcWebstream filtered by the utime column
 * @method     CcWebstream findOneByDbLPtime(string $lptime) Return the first CcWebstream filtered by the lptime column
 * @method     CcWebstream findOneByDbMime(string $mime) Return the first CcWebstream filtered by the mime column
 *
 * @method     array findByDbId(int $id) Return CcWebstream objects filtered by the id column
 * @method     array findByDbName(string $name) Return CcWebstream objects filtered by the name column
 * @method     array findByDbDescription(string $description) Return CcWebstream objects filtered by the description column
 * @method     array findByDbUrl(string $url) Return CcWebstream objects filtered by the url column
 * @method     array findByDbLength(string $length) Return CcWebstream objects filtered by the length column
 * @method     array findByDbCreatorId(int $creator_id) Return CcWebstream objects filtered by the creator_id column
 * @method     array findByDbMtime(string $mtime) Return CcWebstream objects filtered by the mtime column
 * @method     array findByDbUtime(string $utime) Return CcWebstream objects filtered by the utime column
 * @method     array findByDbLPtime(string $lptime) Return CcWebstream objects filtered by the lptime column
 * @method     array findByDbMime(string $mime) Return CcWebstream objects filtered by the mime column
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcWebstreamQuery extends ModelCriteria
{

	/**
	 * Initializes internal state of BaseCcWebstreamQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'airtime', $modelName = 'CcWebstream', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new CcWebstreamQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    CcWebstreamQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof CcWebstreamQuery) {
			return $criteria;
		}
		$query = new CcWebstreamQuery();
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
	 * @return    CcWebstream|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = CcWebstreamPeer::getInstanceFromPool((string) $key))) && $this->getFormatter()->isObjectFormatter()) {
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
	 * @return    CcWebstreamQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(CcWebstreamPeer::ID, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    CcWebstreamQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(CcWebstreamPeer::ID, $keys, Criteria::IN);
	}

	/**
	 * Filter the query on the id column
	 * 
	 * @param     int|array $dbId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcWebstreamQuery The current query, for fluid interface
	 */
	public function filterByDbId($dbId = null, $comparison = null)
	{
		if (is_array($dbId) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(CcWebstreamPeer::ID, $dbId, $comparison);
	}

	/**
	 * Filter the query on the name column
	 * 
	 * @param     string $dbName The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcWebstreamQuery The current query, for fluid interface
	 */
	public function filterByDbName($dbName = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbName)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbName)) {
				$dbName = str_replace('*', '%', $dbName);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcWebstreamPeer::NAME, $dbName, $comparison);
	}

	/**
	 * Filter the query on the description column
	 * 
	 * @param     string $dbDescription The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcWebstreamQuery The current query, for fluid interface
	 */
	public function filterByDbDescription($dbDescription = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbDescription)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbDescription)) {
				$dbDescription = str_replace('*', '%', $dbDescription);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcWebstreamPeer::DESCRIPTION, $dbDescription, $comparison);
	}

	/**
	 * Filter the query on the url column
	 * 
	 * @param     string $dbUrl The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcWebstreamQuery The current query, for fluid interface
	 */
	public function filterByDbUrl($dbUrl = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbUrl)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbUrl)) {
				$dbUrl = str_replace('*', '%', $dbUrl);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcWebstreamPeer::URL, $dbUrl, $comparison);
	}

	/**
	 * Filter the query on the length column
	 * 
	 * @param     string $dbLength The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcWebstreamQuery The current query, for fluid interface
	 */
	public function filterByDbLength($dbLength = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbLength)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbLength)) {
				$dbLength = str_replace('*', '%', $dbLength);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcWebstreamPeer::LENGTH, $dbLength, $comparison);
	}

	/**
	 * Filter the query on the creator_id column
	 * 
	 * @param     int|array $dbCreatorId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcWebstreamQuery The current query, for fluid interface
	 */
	public function filterByDbCreatorId($dbCreatorId = null, $comparison = null)
	{
		if (is_array($dbCreatorId)) {
			$useMinMax = false;
			if (isset($dbCreatorId['min'])) {
				$this->addUsingAlias(CcWebstreamPeer::CREATOR_ID, $dbCreatorId['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbCreatorId['max'])) {
				$this->addUsingAlias(CcWebstreamPeer::CREATOR_ID, $dbCreatorId['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcWebstreamPeer::CREATOR_ID, $dbCreatorId, $comparison);
	}

	/**
	 * Filter the query on the mtime column
	 * 
	 * @param     string|array $dbMtime The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcWebstreamQuery The current query, for fluid interface
	 */
	public function filterByDbMtime($dbMtime = null, $comparison = null)
	{
		if (is_array($dbMtime)) {
			$useMinMax = false;
			if (isset($dbMtime['min'])) {
				$this->addUsingAlias(CcWebstreamPeer::MTIME, $dbMtime['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbMtime['max'])) {
				$this->addUsingAlias(CcWebstreamPeer::MTIME, $dbMtime['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcWebstreamPeer::MTIME, $dbMtime, $comparison);
	}

	/**
	 * Filter the query on the utime column
	 * 
	 * @param     string|array $dbUtime The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcWebstreamQuery The current query, for fluid interface
	 */
	public function filterByDbUtime($dbUtime = null, $comparison = null)
	{
		if (is_array($dbUtime)) {
			$useMinMax = false;
			if (isset($dbUtime['min'])) {
				$this->addUsingAlias(CcWebstreamPeer::UTIME, $dbUtime['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbUtime['max'])) {
				$this->addUsingAlias(CcWebstreamPeer::UTIME, $dbUtime['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcWebstreamPeer::UTIME, $dbUtime, $comparison);
	}

	/**
	 * Filter the query on the lptime column
	 * 
	 * @param     string|array $dbLPtime The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcWebstreamQuery The current query, for fluid interface
	 */
	public function filterByDbLPtime($dbLPtime = null, $comparison = null)
	{
		if (is_array($dbLPtime)) {
			$useMinMax = false;
			if (isset($dbLPtime['min'])) {
				$this->addUsingAlias(CcWebstreamPeer::LPTIME, $dbLPtime['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbLPtime['max'])) {
				$this->addUsingAlias(CcWebstreamPeer::LPTIME, $dbLPtime['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcWebstreamPeer::LPTIME, $dbLPtime, $comparison);
	}

	/**
	 * Filter the query on the mime column
	 * 
	 * @param     string $dbMime The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcWebstreamQuery The current query, for fluid interface
	 */
	public function filterByDbMime($dbMime = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbMime)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbMime)) {
				$dbMime = str_replace('*', '%', $dbMime);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcWebstreamPeer::MIME, $dbMime, $comparison);
	}

	/**
	 * Filter the query by a related CcSchedule object
	 *
	 * @param     CcSchedule $ccSchedule  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcWebstreamQuery The current query, for fluid interface
	 */
	public function filterByCcSchedule($ccSchedule, $comparison = null)
	{
		return $this
			->addUsingAlias(CcWebstreamPeer::ID, $ccSchedule->getDbStreamId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcSchedule relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcWebstreamQuery The current query, for fluid interface
	 */
	public function joinCcSchedule($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
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
	public function useCcScheduleQuery($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinCcSchedule($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CcSchedule', 'CcScheduleQuery');
	}

	/**
	 * Exclude object from result
	 *
	 * @param     CcWebstream $ccWebstream Object to remove from the list of results
	 *
	 * @return    CcWebstreamQuery The current query, for fluid interface
	 */
	public function prune($ccWebstream = null)
	{
		if ($ccWebstream) {
			$this->addUsingAlias(CcWebstreamPeer::ID, $ccWebstream->getDbId(), Criteria::NOT_EQUAL);
	  }
	  
		return $this;
	}

} // BaseCcWebstreamQuery
