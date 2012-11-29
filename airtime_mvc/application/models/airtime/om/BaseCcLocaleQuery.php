<?php


/**
 * Base class that represents a query for the 'cc_locale' table.
 *
 * 
 *
 * @method     CcLocaleQuery orderByDbId($order = Criteria::ASC) Order by the id column
 * @method     CcLocaleQuery orderByDbLocaleCode($order = Criteria::ASC) Order by the locale_code column
 * @method     CcLocaleQuery orderByDbLocaleLang($order = Criteria::ASC) Order by the locale_lang column
 *
 * @method     CcLocaleQuery groupByDbId() Group by the id column
 * @method     CcLocaleQuery groupByDbLocaleCode() Group by the locale_code column
 * @method     CcLocaleQuery groupByDbLocaleLang() Group by the locale_lang column
 *
 * @method     CcLocaleQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     CcLocaleQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     CcLocaleQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     CcLocale findOne(PropelPDO $con = null) Return the first CcLocale matching the query
 * @method     CcLocale findOneOrCreate(PropelPDO $con = null) Return the first CcLocale matching the query, or a new CcLocale object populated from the query conditions when no match is found
 *
 * @method     CcLocale findOneByDbId(int $id) Return the first CcLocale filtered by the id column
 * @method     CcLocale findOneByDbLocaleCode(string $locale_code) Return the first CcLocale filtered by the locale_code column
 * @method     CcLocale findOneByDbLocaleLang(string $locale_lang) Return the first CcLocale filtered by the locale_lang column
 *
 * @method     array findByDbId(int $id) Return CcLocale objects filtered by the id column
 * @method     array findByDbLocaleCode(string $locale_code) Return CcLocale objects filtered by the locale_code column
 * @method     array findByDbLocaleLang(string $locale_lang) Return CcLocale objects filtered by the locale_lang column
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcLocaleQuery extends ModelCriteria
{

	/**
	 * Initializes internal state of BaseCcLocaleQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'airtime', $modelName = 'CcLocale', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new CcLocaleQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    CcLocaleQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof CcLocaleQuery) {
			return $criteria;
		}
		$query = new CcLocaleQuery();
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
	 * @return    CcLocale|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = CcLocalePeer::getInstanceFromPool((string) $key))) && $this->getFormatter()->isObjectFormatter()) {
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
	 * @return    CcLocaleQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(CcLocalePeer::ID, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    CcLocaleQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(CcLocalePeer::ID, $keys, Criteria::IN);
	}

	/**
	 * Filter the query on the id column
	 * 
	 * @param     int|array $dbId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcLocaleQuery The current query, for fluid interface
	 */
	public function filterByDbId($dbId = null, $comparison = null)
	{
		if (is_array($dbId) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(CcLocalePeer::ID, $dbId, $comparison);
	}

	/**
	 * Filter the query on the locale_code column
	 * 
	 * @param     string $dbLocaleCode The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcLocaleQuery The current query, for fluid interface
	 */
	public function filterByDbLocaleCode($dbLocaleCode = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbLocaleCode)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbLocaleCode)) {
				$dbLocaleCode = str_replace('*', '%', $dbLocaleCode);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcLocalePeer::LOCALE_CODE, $dbLocaleCode, $comparison);
	}

	/**
	 * Filter the query on the locale_lang column
	 * 
	 * @param     string $dbLocaleLang The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcLocaleQuery The current query, for fluid interface
	 */
	public function filterByDbLocaleLang($dbLocaleLang = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbLocaleLang)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbLocaleLang)) {
				$dbLocaleLang = str_replace('*', '%', $dbLocaleLang);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcLocalePeer::LOCALE_LANG, $dbLocaleLang, $comparison);
	}

	/**
	 * Exclude object from result
	 *
	 * @param     CcLocale $ccLocale Object to remove from the list of results
	 *
	 * @return    CcLocaleQuery The current query, for fluid interface
	 */
	public function prune($ccLocale = null)
	{
		if ($ccLocale) {
			$this->addUsingAlias(CcLocalePeer::ID, $ccLocale->getDbId(), Criteria::NOT_EQUAL);
	  }
	  
		return $this;
	}

} // BaseCcLocaleQuery
