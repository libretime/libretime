<?php


/**
 * Base class that represents a query for the 'cc_stream_setting' table.
 *
 * 
 *
 * @method     CcStreamSettingQuery orderByDbKeyName($order = Criteria::ASC) Order by the keyname column
 * @method     CcStreamSettingQuery orderByDbValue($order = Criteria::ASC) Order by the value column
 * @method     CcStreamSettingQuery orderByDbType($order = Criteria::ASC) Order by the type column
 *
 * @method     CcStreamSettingQuery groupByDbKeyName() Group by the keyname column
 * @method     CcStreamSettingQuery groupByDbValue() Group by the value column
 * @method     CcStreamSettingQuery groupByDbType() Group by the type column
 *
 * @method     CcStreamSettingQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     CcStreamSettingQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     CcStreamSettingQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     CcStreamSetting findOne(PropelPDO $con = null) Return the first CcStreamSetting matching the query
 * @method     CcStreamSetting findOneOrCreate(PropelPDO $con = null) Return the first CcStreamSetting matching the query, or a new CcStreamSetting object populated from the query conditions when no match is found
 *
 * @method     CcStreamSetting findOneByDbKeyName(string $keyname) Return the first CcStreamSetting filtered by the keyname column
 * @method     CcStreamSetting findOneByDbValue(string $value) Return the first CcStreamSetting filtered by the value column
 * @method     CcStreamSetting findOneByDbType(string $type) Return the first CcStreamSetting filtered by the type column
 *
 * @method     array findByDbKeyName(string $keyname) Return CcStreamSetting objects filtered by the keyname column
 * @method     array findByDbValue(string $value) Return CcStreamSetting objects filtered by the value column
 * @method     array findByDbType(string $type) Return CcStreamSetting objects filtered by the type column
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcStreamSettingQuery extends ModelCriteria
{

	/**
	 * Initializes internal state of BaseCcStreamSettingQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'airtime', $modelName = 'CcStreamSetting', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new CcStreamSettingQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    CcStreamSettingQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof CcStreamSettingQuery) {
			return $criteria;
		}
		$query = new CcStreamSettingQuery();
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
	 * @return    CcStreamSetting|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = CcStreamSettingPeer::getInstanceFromPool((string) $key))) && $this->getFormatter()->isObjectFormatter()) {
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
	 * @return    CcStreamSettingQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(CcStreamSettingPeer::KEYNAME, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    CcStreamSettingQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(CcStreamSettingPeer::KEYNAME, $keys, Criteria::IN);
	}

	/**
	 * Filter the query on the keyname column
	 * 
	 * @param     string $dbKeyName The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcStreamSettingQuery The current query, for fluid interface
	 */
	public function filterByDbKeyName($dbKeyName = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbKeyName)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbKeyName)) {
				$dbKeyName = str_replace('*', '%', $dbKeyName);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcStreamSettingPeer::KEYNAME, $dbKeyName, $comparison);
	}

	/**
	 * Filter the query on the value column
	 * 
	 * @param     string $dbValue The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcStreamSettingQuery The current query, for fluid interface
	 */
	public function filterByDbValue($dbValue = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbValue)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbValue)) {
				$dbValue = str_replace('*', '%', $dbValue);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcStreamSettingPeer::VALUE, $dbValue, $comparison);
	}

	/**
	 * Filter the query on the type column
	 * 
	 * @param     string $dbType The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcStreamSettingQuery The current query, for fluid interface
	 */
	public function filterByDbType($dbType = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbType)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbType)) {
				$dbType = str_replace('*', '%', $dbType);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcStreamSettingPeer::TYPE, $dbType, $comparison);
	}

	/**
	 * Exclude object from result
	 *
	 * @param     CcStreamSetting $ccStreamSetting Object to remove from the list of results
	 *
	 * @return    CcStreamSettingQuery The current query, for fluid interface
	 */
	public function prune($ccStreamSetting = null)
	{
		if ($ccStreamSetting) {
			$this->addUsingAlias(CcStreamSettingPeer::KEYNAME, $ccStreamSetting->getDbKeyName(), Criteria::NOT_EQUAL);
	  }
	  
		return $this;
	}

} // BaseCcStreamSettingQuery
