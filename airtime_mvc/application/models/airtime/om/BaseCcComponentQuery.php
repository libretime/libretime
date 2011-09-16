<?php


/**
 * Base class that represents a query for the 'cc_component' table.
 *
 * 
 *
 * @method     CcComponentQuery orderByDbName($order = Criteria::ASC) Order by the name column
 * @method     CcComponentQuery orderByDbIp($order = Criteria::ASC) Order by the ip column
 *
 * @method     CcComponentQuery groupByDbName() Group by the name column
 * @method     CcComponentQuery groupByDbIp() Group by the ip column
 *
 * @method     CcComponentQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     CcComponentQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     CcComponentQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     CcComponent findOne(PropelPDO $con = null) Return the first CcComponent matching the query
 * @method     CcComponent findOneOrCreate(PropelPDO $con = null) Return the first CcComponent matching the query, or a new CcComponent object populated from the query conditions when no match is found
 *
 * @method     CcComponent findOneByDbName(string $name) Return the first CcComponent filtered by the name column
 * @method     CcComponent findOneByDbIp(string $ip) Return the first CcComponent filtered by the ip column
 *
 * @method     array findByDbName(string $name) Return CcComponent objects filtered by the name column
 * @method     array findByDbIp(string $ip) Return CcComponent objects filtered by the ip column
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcComponentQuery extends ModelCriteria
{

	/**
	 * Initializes internal state of BaseCcComponentQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'airtime', $modelName = 'CcComponent', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new CcComponentQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    CcComponentQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof CcComponentQuery) {
			return $criteria;
		}
		$query = new CcComponentQuery();
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
	 * @return    CcComponent|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = CcComponentPeer::getInstanceFromPool((string) $key))) && $this->getFormatter()->isObjectFormatter()) {
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
	 * @return    CcComponentQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(CcComponentPeer::NAME, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    CcComponentQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(CcComponentPeer::NAME, $keys, Criteria::IN);
	}

	/**
	 * Filter the query on the name column
	 * 
	 * @param     string $dbName The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcComponentQuery The current query, for fluid interface
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
		return $this->addUsingAlias(CcComponentPeer::NAME, $dbName, $comparison);
	}

	/**
	 * Filter the query on the ip column
	 * 
	 * @param     string $dbIp The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcComponentQuery The current query, for fluid interface
	 */
	public function filterByDbIp($dbIp = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbIp)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbIp)) {
				$dbIp = str_replace('*', '%', $dbIp);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcComponentPeer::IP, $dbIp, $comparison);
	}

	/**
	 * Exclude object from result
	 *
	 * @param     CcComponent $ccComponent Object to remove from the list of results
	 *
	 * @return    CcComponentQuery The current query, for fluid interface
	 */
	public function prune($ccComponent = null)
	{
		if ($ccComponent) {
			$this->addUsingAlias(CcComponentPeer::NAME, $ccComponent->getDbName(), Criteria::NOT_EQUAL);
	  }
	  
		return $this;
	}

} // BaseCcComponentQuery
