<?php


/**
 * Base class that represents a query for the 'cc_pref' table.
 *
 * 
 *
 * @method     CcPrefQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     CcPrefQuery orderBySubjid($order = Criteria::ASC) Order by the subjid column
 * @method     CcPrefQuery orderByKeystr($order = Criteria::ASC) Order by the keystr column
 * @method     CcPrefQuery orderByValstr($order = Criteria::ASC) Order by the valstr column
 *
 * @method     CcPrefQuery groupById() Group by the id column
 * @method     CcPrefQuery groupBySubjid() Group by the subjid column
 * @method     CcPrefQuery groupByKeystr() Group by the keystr column
 * @method     CcPrefQuery groupByValstr() Group by the valstr column
 *
 * @method     CcPrefQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     CcPrefQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     CcPrefQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     CcPrefQuery leftJoinCcSubjs($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcSubjs relation
 * @method     CcPrefQuery rightJoinCcSubjs($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcSubjs relation
 * @method     CcPrefQuery innerJoinCcSubjs($relationAlias = '') Adds a INNER JOIN clause to the query using the CcSubjs relation
 *
 * @method     CcPref findOne(PropelPDO $con = null) Return the first CcPref matching the query
 * @method     CcPref findOneOrCreate(PropelPDO $con = null) Return the first CcPref matching the query, or a new CcPref object populated from the query conditions when no match is found
 *
 * @method     CcPref findOneById(int $id) Return the first CcPref filtered by the id column
 * @method     CcPref findOneBySubjid(int $subjid) Return the first CcPref filtered by the subjid column
 * @method     CcPref findOneByKeystr(string $keystr) Return the first CcPref filtered by the keystr column
 * @method     CcPref findOneByValstr(string $valstr) Return the first CcPref filtered by the valstr column
 *
 * @method     array findById(int $id) Return CcPref objects filtered by the id column
 * @method     array findBySubjid(int $subjid) Return CcPref objects filtered by the subjid column
 * @method     array findByKeystr(string $keystr) Return CcPref objects filtered by the keystr column
 * @method     array findByValstr(string $valstr) Return CcPref objects filtered by the valstr column
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcPrefQuery extends ModelCriteria
{

	/**
	 * Initializes internal state of BaseCcPrefQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'airtime', $modelName = 'CcPref', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new CcPrefQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    CcPrefQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof CcPrefQuery) {
			return $criteria;
		}
		$query = new CcPrefQuery();
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
	 * @return    CcPref|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = CcPrefPeer::getInstanceFromPool((string) $key))) && $this->getFormatter()->isObjectFormatter()) {
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
	 * @return    CcPrefQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(CcPrefPeer::ID, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    CcPrefQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(CcPrefPeer::ID, $keys, Criteria::IN);
	}

	/**
	 * Filter the query on the id column
	 * 
	 * @param     int|array $id The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcPrefQuery The current query, for fluid interface
	 */
	public function filterById($id = null, $comparison = null)
	{
		if (is_array($id) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(CcPrefPeer::ID, $id, $comparison);
	}

	/**
	 * Filter the query on the subjid column
	 * 
	 * @param     int|array $subjid The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcPrefQuery The current query, for fluid interface
	 */
	public function filterBySubjid($subjid = null, $comparison = null)
	{
		if (is_array($subjid)) {
			$useMinMax = false;
			if (isset($subjid['min'])) {
				$this->addUsingAlias(CcPrefPeer::SUBJID, $subjid['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($subjid['max'])) {
				$this->addUsingAlias(CcPrefPeer::SUBJID, $subjid['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcPrefPeer::SUBJID, $subjid, $comparison);
	}

	/**
	 * Filter the query on the keystr column
	 * 
	 * @param     string $keystr The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcPrefQuery The current query, for fluid interface
	 */
	public function filterByKeystr($keystr = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($keystr)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $keystr)) {
				$keystr = str_replace('*', '%', $keystr);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcPrefPeer::KEYSTR, $keystr, $comparison);
	}

	/**
	 * Filter the query on the valstr column
	 * 
	 * @param     string $valstr The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcPrefQuery The current query, for fluid interface
	 */
	public function filterByValstr($valstr = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($valstr)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $valstr)) {
				$valstr = str_replace('*', '%', $valstr);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcPrefPeer::VALSTR, $valstr, $comparison);
	}

	/**
	 * Filter the query by a related CcSubjs object
	 *
	 * @param     CcSubjs $ccSubjs  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcPrefQuery The current query, for fluid interface
	 */
	public function filterByCcSubjs($ccSubjs, $comparison = null)
	{
		return $this
			->addUsingAlias(CcPrefPeer::SUBJID, $ccSubjs->getDbId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcSubjs relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcPrefQuery The current query, for fluid interface
	 */
	public function joinCcSubjs($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('CcSubjs');
		
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
			$this->addJoinObject($join, 'CcSubjs');
		}
		
		return $this;
	}

	/**
	 * Use the CcSubjs relation CcSubjs object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcSubjsQuery A secondary query class using the current class as primary query
	 */
	public function useCcSubjsQuery($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinCcSubjs($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CcSubjs', 'CcSubjsQuery');
	}

	/**
	 * Exclude object from result
	 *
	 * @param     CcPref $ccPref Object to remove from the list of results
	 *
	 * @return    CcPrefQuery The current query, for fluid interface
	 */
	public function prune($ccPref = null)
	{
		if ($ccPref) {
			$this->addUsingAlias(CcPrefPeer::ID, $ccPref->getId(), Criteria::NOT_EQUAL);
	  }
	  
		return $this;
	}

} // BaseCcPrefQuery
