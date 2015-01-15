<?php


/**
 * Base class that represents a query for the 'cc_country' table.
 *
 * 
 *
 * @method     CcCountryQuery orderByDbIsoCode($order = Criteria::ASC) Order by the isocode column
 * @method     CcCountryQuery orderByDbName($order = Criteria::ASC) Order by the name column
 *
 * @method     CcCountryQuery groupByDbIsoCode() Group by the isocode column
 * @method     CcCountryQuery groupByDbName() Group by the name column
 *
 * @method     CcCountryQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     CcCountryQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     CcCountryQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     CcCountry findOne(PropelPDO $con = null) Return the first CcCountry matching the query
 * @method     CcCountry findOneOrCreate(PropelPDO $con = null) Return the first CcCountry matching the query, or a new CcCountry object populated from the query conditions when no match is found
 *
 * @method     CcCountry findOneByDbIsoCode(string $isocode) Return the first CcCountry filtered by the isocode column
 * @method     CcCountry findOneByDbName(string $name) Return the first CcCountry filtered by the name column
 *
 * @method     array findByDbIsoCode(string $isocode) Return CcCountry objects filtered by the isocode column
 * @method     array findByDbName(string $name) Return CcCountry objects filtered by the name column
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcCountryQuery extends ModelCriteria
{

	/**
	 * Initializes internal state of BaseCcCountryQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'airtime', $modelName = 'CcCountry', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new CcCountryQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    CcCountryQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof CcCountryQuery) {
			return $criteria;
		}
		$query = new CcCountryQuery();
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
	 * @return    CcCountry|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = CcCountryPeer::getInstanceFromPool((string) $key))) && $this->getFormatter()->isObjectFormatter()) {
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
	 * @return    CcCountryQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(CcCountryPeer::ISOCODE, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    CcCountryQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(CcCountryPeer::ISOCODE, $keys, Criteria::IN);
	}

	/**
	 * Filter the query on the isocode column
	 * 
	 * @param     string $dbIsoCode The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcCountryQuery The current query, for fluid interface
	 */
	public function filterByDbIsoCode($dbIsoCode = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbIsoCode)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbIsoCode)) {
				$dbIsoCode = str_replace('*', '%', $dbIsoCode);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcCountryPeer::ISOCODE, $dbIsoCode, $comparison);
	}

	/**
	 * Filter the query on the name column
	 * 
	 * @param     string $dbName The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcCountryQuery The current query, for fluid interface
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
		return $this->addUsingAlias(CcCountryPeer::NAME, $dbName, $comparison);
	}

	/**
	 * Exclude object from result
	 *
	 * @param     CcCountry $ccCountry Object to remove from the list of results
	 *
	 * @return    CcCountryQuery The current query, for fluid interface
	 */
	public function prune($ccCountry = null)
	{
		if ($ccCountry) {
			$this->addUsingAlias(CcCountryPeer::ISOCODE, $ccCountry->getDbIsoCode(), Criteria::NOT_EQUAL);
	  }
	  
		return $this;
	}

} // BaseCcCountryQuery
